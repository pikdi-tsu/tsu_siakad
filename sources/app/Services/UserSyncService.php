<?php

namespace App\Services;

use App\Models\DataMahasiswa;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;

class UserSyncService
{
    /**
     * Menangani sinkronisasi user (Create/Update & Sync Roles)
     * @param array $userData Data user mentah (format array dari JSON Homebase)
     * @param string|null $accessToken Token OAuth (Opsional, null jika dari Emergency Login)
     * @return User
     * @throws Exception Jika role tidak diizinkan
     */
    public function handle(array $userData, ?string $accessToken = null)
    {
        // LOGIC FILTER ALLOWED ROLE
        $allowedRoles = config('app.roles.allowed', []);

        if (!empty($allowedRoles)) {
            $incomingRoles = [];
            // Normalisasi data role (antisipasi format beda)
            if (!empty($userData['roles']) && is_array($userData['roles'])) {
                foreach ($userData['roles'] as $role) {
                    // Cek apakah formatnya string langsung atau array object
                    $rName = is_string($role) ? $role : ($role['name'] ?? null);
                    if ($rName) {
                        $incomingRoles[] = strtolower($rName);
                    }
                }
            }

            $allowedRoles = array_map('strtolower', $allowedRoles);

            // Cek intersection
            $hasAccess = !empty(array_intersect($incomingRoles, $allowedRoles));
            $isSuperAdminRole = in_array('super admin', $incomingRoles, true);

            if (!$hasAccess && !$isSuperAdminRole) {
                // Kita lempar Exception biar Controller yang nangkep
                throw new \RuntimeException('AKSES DITOLAK: Role Anda ' . implode(', ', $incomingRoles) . ' tidak diizinkan.');
            }
        }

        // LOGIC UPDATE / CREATE USER
        try {
            return DB::transaction(static function () use ($userData, $accessToken) {
                $user = User::query()->updateOrCreate(
                    ['tsu_homebase_id' => $userData['id'] ?? $userData['tsu_homebase_id']], // Kunci pakai ID Homebase
                    [
                        'name' => $userData['name'],
                        'username' => $userData['username'] ?? null,
                        'email' => $userData['email'],
                        'password' => null,
                        'avatar_url' => $userData['profile_photo_url'] ?? null,
                        'isactive' => $userData['isactive'] ?? true,
                        'sso_access_token' => $accessToken, // Bisa null kalau emergency
                    ]
                );

                // LOGIC SYNC ROLE
                $rolesToSync = [];

                if (isset($userData['roles']) && is_array($userData['roles'])) {
                    foreach ($userData['roles'] as $rolePayload) {
                        // Handle jika payload roles cuma array string ['dosen', 'admin']
                        $roleName = is_string($rolePayload) ? $rolePayload : $rolePayload['name'];

                        // Auto-create Role di lokal
                        Role::query()->firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
                        $rolesToSync[] = $roleName;
                    }
                }

                // Logika Pengaman Super Admin (Email PIKDI)
                if ($user->email === config('app.pikdi.email') || $user->hasRole('super admin')) {
                    $rolesToSync[] = 'super admin';
                    Role::query()->firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);
                }

                // Logika Preservation
                if ($user->hasRole('super admin')) {
                    $rolesToSync[] = 'super admin';
                }

                // Eksekusi Sync
                $user->syncRoles(array_unique($rolesToSync));

                // 4. LOGIC SPOKE (BARU: ISI PROFIL MAHASISWA / DOSEN) 🚀
                // Ini bagian yang membedakan Siakad dengan aplikasi lain

                // A. JIKA MAHASISWA
                if ($user->hasRole('mahasiswa')) {
                    DataMahasiswa::query()->updateOrCreate(
                        ['user_id' => $user->id], // Cari berdasarkan User ID
                        [
                            'nim' => $userData['username'], // Username Homebase = NIM

                            // Data pelengkap default (biar gak error SQL)
                            'nama_lengkap' => $userData['name'],
                            'email_pribadi' => $userData['email'],
                            'angkatan' => substr($userData['username'], 0, 4) ?? date('Y'),
                        ]
                    );
                }

                // B. JIKA DOSEN / TENDIK
                // Sesuaikan nama role Homebase, misal: 'dosen', 'tendik', 'staf', dll
                elseif ($user->hasRole(['dosen', 'tendik', 'admin_prodi'])) {
                    Dosen::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'nik' => $userData['username'], // Username Homebase = NIK

                            'nama_lengkap' => $userData['name'],
                            'nidn' => $userData['nidn'] ?? null, // Kalau Homebase kirim NIDN
                        ]
                    );
                }

                return $user;
            });
        } catch (\Throwable $e) {
            throw new \RuntimeException('Terjadi Kesalahan Login di '. config('app.module.name') .' .Silahkan hubungi PIKDI!');
        }
    }
}
