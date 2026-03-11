<?php

namespace App\Http\Controllers;

use App\Models\DataMahasiswa;
use App\Models\MasterData\Master_PeriodeAkademik;
use App\Models\NeoFeederMahasiswas;
use App\Services\NeoFeederService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;

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
     * 1. Halaman Utama (Cuma nampilin wrapper view)
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
            $data = DataMahasiswa::with('user')->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama_mahasiswa', function($row){
                    return $row->user ? $row->user->name : 'Belum Ada Akun/Nama';
                })
                ->addColumn('prodi', function ($row) {
                    if ($row->nama_prodi_lengkap) {
                        return '<span class="badge badge-info">' . $row->nama_prodi_lengkap . '</span>';
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
                ->rawColumns(['prodi', 'status_sync'])
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
     * Total Data
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
        $limit = 50;
        $offset = $request->input('offset', 0);

        try {
            // 1. Ambil Data dari Feeder
            $response = $this->feeder->execute('GetListMahasiswa', [
                'filter' => '',
                'order'  => '',
                'limit'  => $limit,
                'offset' => $offset,
            ]);

            if (($response['error_code'] ?? 1) !== 0) {
                return response()->json(['error' => $response['error_desc']]);
            }

            $records = $response['data'] ?? [];


            // Simpan ke Database Lokal (Mirror)
            foreach ($records as $row) {

                // Format Tanggal Lahir
                $tgl_lahir = null;
                if (!empty($row['tanggal_lahir'])) {
                    try {
                        $tgl_lahir = Carbon::createFromFormat('d-m-Y', $row['tanggal_lahir'])->format('Y-m-d');
                    } catch (\Exception $e) {
                        $tgl_lahir = null;
                    }
                }

                NeoFeederMahasiswas::updateOrCreate(
                    [
                        // Kunci Utama Pencarian
                        'id_mahasiswa_feeder' => $row['id_mahasiswa']
                    ],
                    [
                        'nim'            => $row['nim'] ?? ($row['nipd'] ?? null),
                        'nama_mahasiswa' => $row['nama_mahasiswa'] ?? null,
                        'jenis_kelamin'  => $row['jenis_kelamin'] ?? null,
                        'tanggal_lahir'  => $tgl_lahir,
                        'tempat_lahir'   => null,

                        'id_prodi_feeder'    => $row['id_prodi'] ?? null,
                        'nama_program_studi' => $row['nama_program_studi'] ?? null,
                        'id_periode_masuk'   => $row['id_periode'] ?? null,
                        'nama_periode_masuk' => $row['nama_periode_masuk'] ?? null,

                        'id_status_mahasiswa'   => $row['id_status_mahasiswa'] ?? null,
                        'nama_status_mahasiswa' => $row['nama_status_mahasiswa'] ?? null,
                        'id_agama'   => $row['id_agama'] ?? null,
                        'nama_agama' => $row['nama_agama'] ?? null,

                        'ipk'       => isset($row['ipk']) ? (float) $row['ipk'] : null,
                        'total_sks' => isset($row['total_sks']) ? (int) $row['total_sks'] : null,

                        'id_registrasi_mahasiswa_feeder' => $row['id_registrasi_mahasiswa'] ?? null,
                        'last_synced_at' => now(),
                    ]
                );
            }

            return response()->json([
                'status' => 'success',
                'fetched' => count($records),
                'next_offset' => $offset + $limit
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'error'  => $e->getMessage()]);
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

    /**
     * Eksekusi Per Batch ke Neo Feeder
     */
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Log;

// ...

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

                        // ⚠️ VITAL: GANTI DENGAN UUID KAMPUS KOMANDAN (Bisa pakai env('FEEDER_ID_PT'))
                        'id_perguruan_tinggi'      => env('FEEDER_ID_PT', 'MASUKKAN_UUID_KAMPUS_DISINI'),

                        'id_prodi'                 => $mhs->id_prodi,
                        'id_bidang_minat'          => null,
                        'sks_diakui'               => null,
                        'id_perguruan_tinggi_asal' => null,
                        'id_prodi_asal'            => null,
                        'id_pembiayaan'            => 1, // 1 = Mandiri
                        'biaya_masuk'              => 0,
                    ];

                    // 🔥 TEMBAKKAN PELURU 2 (RIWAYAT)
                    $resRiwayat = $this->feeder->execute('InsertRiwayatPendidikanMahasiswa', ['record' => $recordRiwayat]);

                    // Cek Status Tembakan 2
                    if (($resRiwayat['error_code'] ?? 1) === 0) {
                        // 🎉 SUKSES COMBO 2 HIT!
                        \App\Models\NeoFeederMahasiswas::updateOrCreate(
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
                        // ❌ GAGAL DI HIT 2 (Riwayat Pendidikan ditolak)
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
                    // ❌ GAGAL DI HIT 1 (Biodata Ditolak)
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
