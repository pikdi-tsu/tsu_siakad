<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, HasRoles;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sso_id',           // ID dari Homebase
        'username',         // NIM atau NIK
        'name',
        'email',
        'password',
        'avatar_url',       // Foto Profil URL
        'isactive',        // Status Aktif (1/0)
        'sso_access_token', // Token OAuth
        'sso_refresh_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'sso_access_token',
        'sso_refresh_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'isactive' => 'boolean',
    ];

    public function getTable()
    {
        return config('app.module.name' . '_data_dosen_tendiks');
    }

    // Relasi ke Profil Mahasiswa
    public function mahasiswa()
    {
        return $this->hasOne(DataMahasiswa::class, 'user_id');
    }

    // Relasi ke Profil Dosen/Tendik
    public function dosen()
    {
        return $this->hasOne(DataDosenTendik::class, 'user_id');
    }

    /**
     * Cek apakah User ini Mahasiswa
     * Cara pakai: if ($user->isMahasiswa()) { ... }
     */
    public function isMahasiswa()
    {
        return $this->hasRole('mahasiswa');
    }

    /**
     * Cek apakah User ini Dosen atau Tendik
     */
    public function isDosen()
    {
        return $this->hasRole(['dosen', 'tendik', 'admin_prodi', 'dekan']);
    }

    /**
     * Magic Accessor: Ambil data profil aktif secara otomatis
     * Cara pakai: $user->profil->nim atau $user->profil->nama_lengkap
     */
    public function getProfilAttribute()
    {
        if ($this->isMahasiswa()) {
            return $this->mahasiswa;
        }

        if ($this->isDosen()) {
            return $this->dosen;
        }
        return null;
    }

    public function getProfilePhotoUrlAttribute()
    {
        // Cek foto dari SSO (avatar_url)
        if (!empty($this->attributes['avatar_url'])) {
            return $this->attributes['avatar_url'];
        }

        // Cek foto manual upload (profile_photo_path - bawaan template)
        // (Jaga-jaga fitur upload foto manual)
        if (!empty($this->attributes['profile_photo_path'])) {
            return asset('storage/' . $this->attributes['profile_photo_path']);
        }

        // Default Avatar Huruf (Inisial Nama)
        $name = trim($this->name);
        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=7F9CF5&background=2d394a';
    }
}
