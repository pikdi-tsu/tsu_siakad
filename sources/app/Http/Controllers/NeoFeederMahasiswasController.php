<?php

namespace App\Http\Controllers;

use App\Models\DataMahasiswa;
use App\Models\MasterData\Master_PeriodeAkademik;
use App\Models\NeoFeederMahasiswas;
use App\Services\NeoFeederService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class NeoFeederMahasiswasController extends MiddlewareController
{
    protected $feeder;

    public function __construct(NeoFeederService $feederService)
    {
        // Pasang Gembok Permission Komandan
        $this->registerPermissions('system:neo_feeder_mahasiswa');
        $this->feeder = $feederService;
    }

    /**
     * Halaman Utama
     */
    public function index()
    {
        return view('system::neofeeder.mahasiswa.index', [
            'title' => 'Data Mahasiswa Feeder'
        ]);
    }

    /**
     * TAB 1: DATA MAHASISWA TSU SIAKAD
     */
    public function jsonLokal(Request $request)
    {
        if ($request->ajax()) {
            $data = DataMahasiswa::with(['user', 'prodi'])
                ->where('nim', 'not like', 'XX%')
                ->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama_mahasiswa', function($row){
                    if ($row->user && $row->user->name) {
                        return $row->user->name;
                    }
                    return '<span class="text-muted"><i>Belum Ada Nama</i></span>';
                })
                ->addColumn('nama_mahasiswa', function($row){
                    return $row->user ? $row->user->name : 'Belum Ada Akun/Nama';
                })
                ->addColumn('prodi', function ($row) {
                    if ($row->prodi && $row->prodi->nama_prodi) {
                        return '<span class="badge badge-info">' . $row->prodi->jenjang . ' ' . $row->prodi->nama_prodi . '</span>';
                    }
                    return '<span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> Belum Set Prodi</span>';
                })
                ->addColumn('status_sync', function($row){
                    $isSynced = NeoFeederMahasiswas::where('nim', $row->nim)->exists();

                    if($isSynced) {
                        return '<span class="badge badge-success"><i class="fas fa-check"></i> Tersinkron</span>';
                    }
                    return '<span class="badge badge-warning"><i class="fas fa-exclamation-circle"></i> Belum Sync</span>';
                })
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';

                    if (!empty($row->nim)) {
                        $btn .= '<button data-id="'.$row->id.'" class="btn btn-sm btn-info mx-1 btn-pull-single-bio" title="Tarik Biodata dari Feeder">
                                    <i class="fas fa-cloud-download-alt"></i>
                                 </button>';
                    }

                    return $btn;
                })
                ->rawColumns(['nama_mahasiswa', 'prodi', 'status_sync', 'action'])
                ->make(true);
        }
    }

    /**
     * TAB 2: BELUM SINKRON (Data Lokal yang belum ada di Feeder)
     */
    public function jsonSync(Request $request)
    {
        if ($request->ajax()) {
            $syncedNims = NeoFeederMahasiswas::whereNotNull('nim')->pluck('nim')->toArray();

            $query = DataMahasiswa::with(['user', 'prodi'])
                ->whereNotIn('nim', $syncedNims)
                ->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('nama_mahasiswa', function($row){
                    return $row->user ? $row->user->name : 'Belum Ada Akun/Nama';

                })
                ->addColumn('prodi', function ($row) {
                    if ($row->prodi) {
                        return '<span class="badge badge-info">' . $row->prodi->jenjang . ' ' . $row->prodi->nama_prodi . '</span>';
                    }
                    return '<span class="badge badge-danger">Belum Set Prodi</span>';
                })
                ->rawColumns(['nama_mahasiswa', 'prodi'])
                ->make(true);
        }
    }

    /**
     * TAB 3: DATA NEO FEEDER (Cermin Lokal dari API)
     */
    public function jsonFeeder(Request $request)
    {
        $data = NeoFeederMahasiswas::query()->latest();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('last_synced_at', function($row){
                return $row->last_synced_at ? Carbon::parse($row->last_synced_at)->diffForHumans() : '-';
            })
            ->orderColumn('DT_RowIndex', function ($query, $order) {
                $query->orderBy('id', $order);
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Smart Pull Biodata (Fill-in-the-blanks)
     */
    public function pullBiodata(Request $request)
    {
        $id_lokal = $request->input('id');
        $mode = $request->input('mode', 'fill');

        try {
            // Kunci Target di Database Lokal
            $mhsLokal = DataMahasiswa::with('user')->find($id_lokal);
            if (!$mhsLokal) {
                return response()->json([
                    'status' => 'error',
                    'html_error' => 'Data mahasiswa lokal tidak ditemukan atau tidak memiliki NIM!'
                ], 404);
            }

            // Cek from list mahasiswa
            $mirror = NeoFeederMahasiswas::where('nim', $mhsLokal->nim)->first();
            if (!$mirror || empty($mirror->id_mahasiswa_feeder)) {
                return response()->json([
                    'status' => 'warning',
                    'message' => "Mahasiswa dengan NIM {$mhsLokal->nim} belum tercatat di Feeder pusat. Silakan 'Push' data terlebih dahulu!"
                ]);
            }

            $idFeeder = $mirror->id_mahasiswa_feeder;

            // Fetch data from API
            $response = $this->feeder->execute('GetBiodataMahasiswa', [
                'filter' => "id_mahasiswa = '{$idFeeder}'",
                'limit'  => 1
            ]);

            if (($response['error_code'] ?? 1) !== 0) {
                throw new \Exception("[TSU_FEEDER_02] Gagal tarik biodata: " . ($response['error_desc'] ?? 'Unknown Error'));
            }

            $dataFeeder = $response['data'][0] ?? null;
            if (!$dataFeeder) {
                return response()->json(['status' => 'warning', 'message' => 'Biodata tidak ditemukan di server PDDIKTI!']);
            }

            $this->applyBiodata($mhsLokal, $dataFeeder, $mode);
            Log::info("SNIPER EXECUTED - Mode: {$mode} | Negara Akhir: {$mhsLokal->kewarganegaraan}");
            $mhsLokal->save();

            return response()->json([
                'status' => 'success',
                'message' => "Biodata <b>{$mhsLokal->nama_mahasiswa}</b> berhasil ditarik dan dilengkapi!"
            ]);

        } catch (\Exception $e) {
            // ERROR HANDLING GLOBAL
            $rawMessage = $e->getMessage();
            $errorCode  = "[TSU_SYS_CRITICAL]";
            $userMsg    = "Terjadi kesalahan sistem saat menarik Biodata.";

            if (preg_match('/\[TSU_.*?\]/', $rawMessage, $matches)) {
                $errorCode = $matches[0];
                $userMsg = str_replace($errorCode, '', $rawMessage);
            }

            Log::error("$errorCode Gagal Smart Pull Biodata.", [
                'original_error' => $rawMessage, 'file' => $e->getFile(), 'line' => $e->getLine()
            ]);

            $finalErrorMsg = "<div class='text-center'>";
            $finalErrorMsg .= "<h4 class='text-bold text-danger mb-2'>$errorCode</h4>";
            $finalErrorMsg .= "<p class='mb-2 text-bold' style='font-size: 1.1em;'>$userMsg</p>";
            $finalErrorMsg .= "</div>";

            return response()->json(['status' => 'error', 'html_error' => $finalErrorMsg], 500);
        }
    }

    /**
     * Batch Pull Biodata
     */
    public function pullBiodataBatch(Request $request)
    {
        $limit = 50;
        $offset = $request->input('offset', 0);
        $batchUpdated = 0;
        $batchNoId = 0;
        $batchAlreadyFull = 0;

        try {
            // Fetch 50 (abaikan NIM XX)
            $mahasiswas = DataMahasiswa::with('user')
                ->where('nim', 'not like', 'XX%')
                ->orderBy('nim', 'ASC')
                ->skip($offset)
                ->take($limit)
                ->get();

            if ($mahasiswas->isEmpty()) {
                return response()->json(['status' => 'success', 'finished' => true, 'fetched' => 0]);
            }

            // Cari ID Feeder di tabel Neo Feeder lokal
            $nims = $mahasiswas->pluck('nim')->toArray();
            $mirrors = NeoFeederMahasiswas::whereIn('nim', $nims)->get()->keyBy('nim');

            $idFeeders = [];
            foreach ($mahasiswas as $mhs) {
                if (isset($mirrors[$mhs->nim]) && !empty($mirrors[$mhs->nim]->id_mahasiswa_feeder)) {
                    $idFeeders[] = "'" . $mirrors[$mhs->nim]->id_mahasiswa_feeder . "'";
                }
            }

            // Hit API Feeder
            if (!empty($idFeeders)) {
                $filterIn = implode(',', $idFeeders);
                $response = $this->feeder->execute('GetBiodataMahasiswa', [
                    'filter' => "id_mahasiswa IN ({$filterIn})"
                ]);

                // Mengubah readable response Feeder
                $dataFeeder = [];
                if (($response['error_code'] ?? 1) === 0 && !empty($response['data'])) {
                    foreach ($response['data'] as $bio) {
                        $dataFeeder[$bio['id_mahasiswa']] = $bio;
                    }
                }

                // Logic isi attribute data kosong
                foreach ($mahasiswas as $mhs) {
                    $mirror = $mirrors[$mhs->nim] ?? null;

                    // Cek ID Neo Feeder Lokal
                    if (!$mirror || empty($mirror->id_mahasiswa_feeder)) {
                        $batchNoId++;
                        continue; // Tidak punya ID
                    }

                    // Cek data di balasan Feeder
                    if (isset($dataFeeder[$mirror->id_mahasiswa_feeder])) {
                        $bio = $dataFeeder[$mirror->id_mahasiswa_feeder];

                        $this->applyBiodata($mhs, $bio);

                        // Cek ada perubahan
                        if ($mhs->isDirty()) {
                            $mhs->save();
                            $batchUpdated++; // Sukses
                        } else {
                            $batchAlreadyFull++; // Lengkap / Feeder kosong
                        }
                    }
                }
            } else {
                // Jika di batch tidak ada ID Feeder
                $batchNoId = $mahasiswas->count();
            }

            return response()->json([
                'status' => 'success',
                'fetched' => $mahasiswas->count(),
                'updated' => $batchUpdated,
                'no_id'   => $batchNoId,
                'next_offset' => $offset + $limit,
                'finished' => false
            ]);

        } catch (\Exception $e) {
            // ERROR HANDLING GLOBAL TSU
            $rawMessage = $e->getMessage();
            $errorCode  = "[TSU_SYS_CRITICAL]";
            $userMsg    = "Terjadi kesalahan saat mengeksekusi Mode Sapu Jagat Biodata.";

            if (preg_match('/\[TSU_.*?\]/', $rawMessage, $matches)) {
                $errorCode = $matches[0];
                $userMsg = str_replace($errorCode, '', $rawMessage);
            }

            Log::error("$errorCode Gagal Smart Pull Batch Biodata.", [
                'original_error' => $rawMessage, 'file' => $e->getFile(), 'line' => $e->getLine()
            ]);

            return response()->json([
                'status' => 'error',
                'html_error' => "<div class='text-center'><h4 class='text-danger'>$errorCode</h4><p>$userMsg</p></div>"
            ], 500);
        }
    }

    /**
     * HELPER: Sinkronisasi Biodata (Dual-Mode)
     */
    private function applyBiodata($mhs, $bio, $mode = 'fill')
    {
        $isOverwrite = ($mode === 'overwrite');

        // Closure cerdas untuk menentukan nilai mana yang dipakai
        $syncField = function($local, $feeder) use ($isOverwrite) {
            $cleanLocal = is_string($local) ? trim($local) : $local;

            if ($isOverwrite) {
                return $feeder ?? $local; // Overwrite
            }
            if (empty($cleanLocal)) {
                return $feeder ?? null; // Fill in blank
            }
            return $local;
        };

        // Closure untuk Tanggal
        $syncDate = function($localDate, $feederDate) use ($syncField) {
            // Feeder = DD-MM-YYYY, MySQL = YYYY-MM-DD
            $parsedFeeder = !empty($feederDate) ? Carbon::parse($feederDate)->format('Y-m-d') : null;
            return $syncField($localDate, $parsedFeeder);
        };

        // UPDATE USER (Akun Login)
        if ($mhs->user) {
            if ($isOverwrite || empty(trim($mhs->user->name))) {
                $mhs->user->name = $bio['nama_mahasiswa'] ?? $mhs->user->name;
                $mhs->user->save();
            }
        }

        // UPDATE BIODATA LOKAL
        $mhs->nik_ktp = $syncField($mhs->nik_ktp, $bio['nik'] ?? null);
        $mhs->nisn = $syncField($mhs->nisn, $bio['nisn'] ?? null);
        $mhs->npwp = $syncField($mhs->npwp, $bio['npwp'] ?? null);
        $mhs->kewarganegaraan = $syncField($mhs->kewarganegaraan, $bio['kewarganegaraan'] ?? null);
        $mhs->tempat_lahir = $syncField($mhs->tempat_lahir, $bio['tempat_lahir'] ?? null);
        $mhs->jenis_kelamin = $syncField($mhs->jenis_kelamin, $bio['jenis_kelamin'] ?? null);
        $mhs->tgl_lahir = $syncDate($mhs->tgl_lahir, $bio['tanggal_lahir'] ?? null);

        // Alamat
        $mhs->alamat_lengkap = $syncField($mhs->alamat_lengkap, $bio['jalan'] ?? null);
        $mhs->dusun = $syncField($mhs->dusun, $bio['dusun'] ?? null);
        $mhs->rt = $syncField($mhs->rt, $bio['rt'] ?? null);
        $mhs->rw = $syncField($mhs->rw, $bio['rw'] ?? null);
        $mhs->kelurahan = $syncField($mhs->kelurahan, $bio['kelurahan'] ?? null);
        $mhs->kodepos = $syncField($mhs->kodepos, $bio['kode_pos'] ?? null);

        // Kontak
        $mhs->no_hp = $syncField($mhs->no_hp, $bio['handphone'] ?? ($bio['telepon'] ?? null));
        $mhs->email_pribadi = $syncField($mhs->email_pribadi, $bio['email'] ?? null);

        // Orang Tua (Aman dari null tanggal)
        $mhs->nik_ayah = $syncField($mhs->nik_ayah, $bio['nik_ayah'] ?? null);
        $mhs->nama_ayah = $syncField($mhs->nama_ayah, $bio['nama_ayah'] ?? null);
        $mhs->tgl_lahir_ayah = $syncDate($mhs->tgl_lahir_ayah, $bio['tanggal_lahir_ayah'] ?? null);

        $mhs->nik_ibu = $syncField($mhs->nik_ibu, $bio['nik_ibu'] ?? null);
        $mhs->nama_ibu = $syncField($mhs->nama_ibu, $bio['nama_ibu_kandung'] ?? null);
        $mhs->tgl_lahir_ibu = $syncDate($mhs->tgl_lahir_ibu, $bio['tanggal_lahir_ibu'] ?? null);

        $mhs->nama_wali = $syncField($mhs->nama_wali, $bio['nama_wali'] ?? null);
        $mhs->tgl_lahir_wali = $syncDate($mhs->tgl_lahir_wali, $bio['tanggal_lahir_wali'] ?? null);

        // Lain-lain
        $mhs->penerima_kps = $syncField($mhs->penerima_kps, $bio['penerima_kps'] ?? 0);
    }

    /**
     * Total Data Sync
     */
    public function syncInit()
    {
        try {
            $response = $this->feeder->execute('GetCountMahasiswa');

            // Logic normalisasi output (kadang Feeder return array, kadang int langsung)
            $rawData = $response['data'] ?? 0;
            $total = is_array($rawData) ? ($rawData['count'] ?? 0) : $rawData;

            return response()->json([
                'status' => 'success',
                'total'  => (int) $total
            ]);

        } catch (\Exception $e) {
            // Tangkap error dari Service (Token expired / Koneksi putus)
            return response()->json([
                'status' => 'error',
                'error'  => $e->getMessage()
            ]);
        }
    }

    /**
     * Eksekusi Per Batch
     */
    public function syncExec(Request $request)
    {
        $limit = 500;
        $offset = $request->input('offset', 0);

        try {
            // Ambil Data dari Feeder
            $response = $this->feeder->execute('GetListMahasiswa', [
                'filter' => '', 'order' => '', 'limit' => $limit, 'offset' => $offset,
            ]);

            if (($response['error_code'] ?? 1) !== 0) {
                throw new \Exception("[TSU_FEEDER_01] Gagal mengambil data: " . ($response['error_desc'] ?? 'Unknown Error'));
            }

            $records = $response['data'] ?? [];
            $processed = count($records);

            // Jika tidak ada data lagi, kembalikan laporan selesai
            if (empty($records)) {
                $msg = "<h6 class='font-weight-bold mb-2'>Laporan Sinkronisasi Mahasiswa Selesai</h6>";
                $msg .= "<ul class='mb-0 pl-3' style='text-align: left; list-style-type: disc;'>";
                $msg .= "<li>Total Batch Terakhir: <b>0</b> data</li>";
                $msg .= "<li>Status: <b class='text-success'>Up to Date</b></li>";
                $msg .= "</ul>";

                return response()->json([
                    'status' => 'success', 'fetched' => 0, 'next_offset' => $offset, 'finished' => true, 'html_message' => $msg
                ]);
            }

            // Array untuk Bulk Upsert
            $upsertData = [];

            foreach ($records as $row) {
                $tgl_lahir = null;
                if (!empty($row['tanggal_lahir'])) {
                    $tgl_lahir = Carbon::createFromFormat('d-m-Y', $row['tanggal_lahir'])->format('Y-m-d');
                }

                $upsertData[] = [
                    'id'                  => Str::uuid()->toString(),
                    'id_mahasiswa_feeder' => $row['id_mahasiswa'],
                    'nim'                 => $row['nim'] ?? ($row['nipd'] ?? null),
                    'nama_mahasiswa'      => $row['nama_mahasiswa'] ?? null,
                    'jenis_kelamin'       => $row['jenis_kelamin'] ?? null,
                    'tanggal_lahir'       => $tgl_lahir,
                    'id_prodi_feeder'     => $row['id_prodi'] ?? null,
                    'nama_program_studi'  => $row['nama_program_studi'] ?? null,
                    'id_periode_masuk'    => $row['id_periode'] ?? null,
                    'nama_periode_masuk'  => $row['nama_periode_masuk'] ?? null,
                    'id_status_mahasiswa' => $row['id_status_mahasiswa'] ?? null,
                    'nama_status_mahasiswa'=> $row['nama_status_mahasiswa'] ?? null,
                    'id_agama'            => $row['id_agama'] ?? null,
                    'nama_agama'          => $row['nama_agama'] ?? null,
                    'ipk'                 => isset($row['ipk']) ? (float) $row['ipk'] : null,
                    'total_sks'           => isset($row['total_sks']) ? (int) $row['total_sks'] : null,
                    'id_registrasi_mahasiswa_feeder' => $row['id_registrasi_mahasiswa'] ?? null,
                    'last_synced_at'      => now()->format('Y-m-d H:i:s'),
                ];
            }

            // Eksekusi Bulk Upsert
            NeoFeederMahasiswas::upsert(
                $upsertData,
                ['id_mahasiswa_feeder'],
                [
                    'nim', 'nama_mahasiswa', 'jenis_kelamin', 'tanggal_lahir',
                    'id_prodi_feeder', 'nama_program_studi', 'id_periode_masuk', 'nama_periode_masuk',
                    'id_status_mahasiswa', 'nama_status_mahasiswa', 'id_agama', 'nama_agama',
                    'ipk', 'total_sks', 'id_registrasi_mahasiswa_feeder', 'last_synced_at'
                ]
            );

            // Cek batch
            $isFinished = $processed < $limit;

            // KEMBALIKAN JSON KE FRONTEND AJAX
            return response()->json([
                'status'       => 'success',
                'fetched'      => $processed,
                'next_offset'  => $offset + $limit,
                'finished'     => $isFinished,
            ]);

        } catch (\Exception $e) {
            // LOG ERROR
            $rawMessage = $e->getMessage();
            $errorCode  = "[TSU_SYS_CRITICAL]";
            $userMsg    = "Terjadi kesalahan sistem yang tidak terduga saat menarik data Feeder.";

            // Cek throw error message
            if (preg_match('/\[TSU_.*?\]/', $rawMessage, $matches)) {
                $errorCode = $matches[0];
                $userMsg = str_replace($errorCode, '', $rawMessage);
            } else {
                $userMsg = "Terjadi gangguan teknis koneksi ke API PDDIKTI.";
            }

            Log::error("$errorCode Gagal Sync Feeder Mahasiswa.", [
                'original_error' => $rawMessage,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            $finalErrorMsg = "<div class='text-center'>";
            $finalErrorMsg .= "<h4 class='text-bold text-danger mb-2'>$errorCode</h4>";
            $finalErrorMsg .= "<p class='mb-2 text-bold' style='font-size: 1.1em;'>$userMsg</p>";
            $finalErrorMsg .= "<p class='text-muted small mb-0'>Silakan screenshot pesan ini dan laporkan ke PIKDI jika masalah berlanjut.</p>";
            $finalErrorMsg .= "</div>";

            // Kembalikan JSON dengan status HTTP 500
            return response()->json([
                'status'     => 'error',
                'html_error' => $finalErrorMsg
            ], 500);
        }
    }

    /**
     * Total Data Push
     */
    public function pushInit()
    {
        try {
            $syncedNims = NeoFeederMahasiswas::whereNotNull('nim')->pluck('nim')->toArray();
            $total = DataMahasiswa::whereNotIn('nim', $syncedNims)->count();

            return response()->json(['status' => 'success', 'total'  => $total]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'error' => $e->getMessage()]);
        }
    }

    public function pushExec(Request $request)
    {
        $limit = 2;
        $sukses = 0;
        $gagal = 0;

        try {
            // Data lokal Feeder
            $syncedNims = NeoFeederMahasiswas::whereNotNull('nim')->pluck('nim')->toArray();

            // Not yet sync data mahasiswa
            $mahasiswas = DataMahasiswa::whereNotIn('nim', $syncedNims)
                ->take($limit)
                ->get();

            foreach ($mahasiswas as $mhs) {
                // Data Mahasiswa Array
                $recordBiodata = [
                    'nama_mahasiswa' => $mhs->nama_mahasiswa ?? 'TANPA NAMA',
                    'jenis_kelamin'  => $mhs->jenis_kelamin ?? 'L',
                    'tempat_lahir'   => $mhs->tempat_lahir ?? 'Tidak Diketahui',
                    'tanggal_lahir'  => $mhs->tgl_lahir ? Carbon::parse($mhs->tgl_lahir)->format('Y-m-d') : '2000-01-01',
                    'id_agama'       => (int) ($mhs->id_agama ?? 1),
                    'nik'            => $mhs->nik_ktp,
                    'nisn'           => $mhs->nisn,
                    'npwp'           => $mhs->npwp,
                    'kewarganegaraan'=> $mhs->kewarganegaraan ?? 'ID',
                    'jalan'          => $mhs->alamat_lengkap,
                    'dusun'          => $mhs->dusun,
                    'rt'             => $mhs->rt,
                    'rw'             => $mhs->rw,
                    'kelurahan'      => $mhs->kelurahan,
                    'kode_pos'       => $mhs->kodepos,
                    'id_wilayah'     => $mhs->id_wilayah ?? '999999', // 999999 = Default / Luar Negeri
                    'id_jenis_tinggal' => null,
                    'id_alat_transportasi' => null,
                    'telepon'        => null,
                    'handphone'      => $mhs->no_hp,
                    'email'          => $mhs->email_pribadi,
                    'penerima_kps'   => (int) ($mhs->penerima_kps ?? 0),
                    'nomor_kps'      => null,

                    // --- Data Ayah ---
                    'nik_ayah'            => $mhs->nik_ayah,
                    'nama_ayah'           => $mhs->nama_ayah,
                    'tanggal_lahir_ayah'  => $mhs->tgl_lahir_ayah ? Carbon::parse($mhs->tgl_lahir_ayah)->format('Y-m-d') : null,
                    'id_pendidikan_ayah'  => $mhs->id_pendidikan_ayah,
                    'id_pekerjaan_ayah'   => $mhs->id_pekerjaan_ayah,
                    'id_penghasilan_ayah' => $mhs->id_penghasilan_ayah,

                    // --- Data Ibu Kandung (Wajib Feeder) ---
                    'nik_ibu'             => $mhs->nik_ibu,
                    'nama_ibu_kandung'    => $mhs->nama_ibu ?? 'TIDAK DIKETAHUI',
                    'tanggal_lahir_ibu'   => $mhs->tgl_lahir_ibu ? Carbon::parse($mhs->tgl_lahir_ibu)->format('Y-m-d') : null,
                    'id_pendidikan_ibu'   => $mhs->id_pendidikan_ibu,
                    'id_pekerjaan_ibu'    => $mhs->id_pekerjaan_ibu,
                    'id_penghasilan_ibu'  => $mhs->id_penghasilan_ibu,

                    // --- Data Wali ---
                    'nama_wali'           => $mhs->nama_wali,
                    'tanggal_lahir_wali'  => $mhs->tgl_lahir_wali ? Carbon::parse($mhs->tgl_lahir_wali)->format('Y-m-d') : null,
                    'id_pendidikan_wali'  => $mhs->id_pendidikan_wali,
                    'id_pekerjaan_wali'   => $mhs->id_pekerjaan_wali,
                    'id_penghasilan_wali' => $mhs->id_penghasilan_wali,

                    // --- Kebutuhan Khusus ---
                    'id_kebutuhan_khusus_mahasiswa' => 0,
                    'id_kebutuhan_khusus_ayah'      => 0,
                    'id_kebutuhan_khusus_ibu'       => 0,
                ];

                // API Biodata Mahasiswa
                $resBiodata = $this->feeder->execute('InsertBiodataMahasiswa', ['record' => $recordBiodata]);

                // Cek Status API
                if (($resBiodata['error_code'] ?? 1) === 0) {
                    // TANGKAP ID MAHASISWA DARI FEEDER
                    $idMahasiswaFeeder = $resBiodata['data']['id_mahasiswa'];

                    // Riwayat Pendidikan Array
                    $periode = Master_PeriodeAkademik::find($mhs->id_periode_masuk);
                    $kodePeriode = $periode ? preg_replace('/[^0-9]/', '', $periode->kode_periode) : date('Y').'1';

                    $recordRiwayat = [
                        'id_mahasiswa'             => $idMahasiswaFeeder,
                        'nim'                      => $mhs->nim,
                        'id_jenis_daftar'          => 1,
                        'id_jalur_daftar'          => null,
                        'id_periode_masuk'         => $kodePeriode,
                        'tanggal_daftar'           => $mhs->created_at ? $mhs->created_at->format('Y-m-d') : date('Y-m-d'),

                        // GANTI DENGAN UUID KAMPUS (Bisa pakai env('FEEDER_ID_PT'))
                        'id_perguruan_tinggi'      => env('FEEDER_ID_PT', 'MASUKKAN_UUID_KAMPUS_DISINI'),

                        'id_prodi'                 => $mhs->id_prodi,
                        'id_bidang_minat'          => null,
                        'sks_diakui'               => null,
                        'id_perguruan_tinggi_asal' => null,
                        'id_prodi_asal'            => null,
                        'id_pembiayaan'            => 1, // 1 = Mandiri
                        'biaya_masuk'              => 0,
                    ];

                    $resRiwayat = $this->feeder->execute('InsertRiwayatPendidikanMahasiswa', ['record' => $recordRiwayat]);

                    // Cek Status Tembakan 2
                    if (($resRiwayat['error_code'] ?? 1) === 0) {
                        NeoFeederMahasiswas::updateOrCreate(
                            ['nim' => $mhs->nim],
                            [
                                'id_mahasiswa_feeder'     => $idMahasiswaFeeder,
                                'id_registrasi_mahasiswa' => $resRiwayat['data']['id_registrasi_mahasiswa'] ?? null,
                                'nama_mahasiswa'          => $recordBiodata['nama_mahasiswa'],
                                'status_sync'             => 'sudah sync',
                                'last_synced_at'          => now(),
                            ]
                        );
                        $sukses++;
                    } else {
                        Log::error("GAGAL RIWAYAT NIM {$mhs->nim}: " . ($resRiwayat['error_desc'] ?? 'Unknown'));
                        \App\Models\NeoFeederMahasiswas::updateOrCreate(
                            ['nim' => $mhs->nim],
                            [
                                'id_mahasiswa_feeder' => $idMahasiswaFeeder, // Simpan ID manusianya agar tidak hilang
                                'nama_mahasiswa'      => 'Gagal Riwayat: ' . ($resRiwayat['error_desc'] ?? ''),
                                'status_sync'         => 'error_push',
                            ]
                        );
                        $gagal++;
                    }
                } else {
                    Log::error("GAGAL BIODATA NIM {$mhs->nim}: " . ($resBiodata['error_desc'] ?? 'Unknown'));
                    \App\Models\NeoFeederMahasiswas::updateOrCreate(
                        ['nim' => $mhs->nim],
                        [
                            'nama_mahasiswa' => 'Gagal Biodata: ' . ($resBiodata['error_desc'] ?? ''),
                            'status_sync'    => 'error_push',
                        ]
                    );
                    $gagal++;
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => "Proses Push Selesai! Berhasil: $sukses | Gagal: $gagal"
            ]);

        } catch (\Exception $e) {
            Log::error("CRITICAL ERROR PUSH MAHASISWA: " . $e->getMessage());
            return response()->json(['status' => 'error', 'error' => $e->getMessage()]);
        }
    }

    // Helper error
    private function emptyDataTable($errorMsg = '') {
        return response()->json([
            'draw' => 1, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => [], 'error' => $errorMsg
        ]);
    }

    /**
     * Store (Simpan Data Baru ke Lokal TSU)
     */
    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'nama_mahasiswa' => 'required',
            'nim' => 'required|unique:mahasiswas,nim',
            'id_prodi_feeder' => 'required',
            // ... validasi lain ...
        ]);

        NeoFeederMahasiswas::create($request->all());

        return response()->json(['success' => 'Data berhasil disimpan!']);
    }

    /**
     * Hapus Data
     */
    public function destroy($id)
    {
        $mhs = NeoFeederMahasiswas::findOrFail($id);
        $mhs->delete();
        return redirect()->back()->with('success', 'Data dihapus dari TSU (Feeder aman).');
    }
}
