<?php

namespace App\Http\Controllers;

use App\Models\DataMahasiswa;
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
    public function jsonLokal()
    {
        $data = DataMahasiswa::with('user')->latest();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('nama_mahasiswa', function($row){
                return $row->user ? $row->user->name : 'Belum Ada Akun/Nama';
            })
            ->addColumn('status_sync', function($row){
                // Cek apakah NIM ini sudah ada di tabel cermin Feeder
                $isSynced = NeoFeederMahasiswas::where('nim', $row->nim)->exists();

                if($isSynced) {
                    return '<span class="badge badge-success"><i class="fas fa-check"></i> Tersinkron</span>';
                }
                return '<span class="badge badge-warning"><i class="fas fa-exclamation-circle"></i> Belum Sync</span>';
            })
            ->rawColumns(['status_sync'])
            ->make(true);
    }

    /**
     * TAB 2: BELUM SINKRON (Data Lokal yang belum ada di Feeder)
     */
    public function jsonSync()
    {
        // Ambil daftar NIM yang SUDAH ada di cermin Feeder
        $syncedNims = NeoFeederMahasiswas::whereNotNull('nim')->pluck('nim')->toArray();

        // Tarik data mahasiswa lokal yang NIM-nya TIDAK ADA di daftar Feeder tadi
        $data = DataMahasiswa::with('user')->whereNotIn('nim', $syncedNims)->latest();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('nama_mahasiswa', function($row){
                return $row->user ? $row->user->name : 'Belum Ada Akun/Nama';
            })
            ->make(true);
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
    public function pushExec()
    {
        $limit = 5;

        try {
            $syncedNims = NeoFeederMahasiswas::whereNotNull('nim')->pluck('nim')->toArray();

            $mahasiswas = DataMahasiswa::with('user')
                ->whereNotIn('nim', $syncedNims)
                ->take($limit)
                ->get();

            $processed = 0;

            foreach ($mahasiswas as $mhs) {
                // Dictionary Feeder InsertBiodataMahasiswa
                $record = [
                    'nama_mahasiswa' => $mhs->user ? $mhs->user->name : 'TANPA NAMA',
                    'jenis_kelamin'  => $mhs->jenis_kelamin ?? 'L',
                    'tempat_lahir'   => $mhs->tempat_lahir ?? 'Tidak Diketahui',
                    'tanggal_lahir'  => $mhs->tgl_lahir ? Carbon::parse($mhs->tgl_lahir)->format('Y-m-d') : '2000-01-01',
                    'id_agama'       => $mhs->id_agama ?? 1, // 1=Islam, sesuaikan kamus Feeder
                    'nik'            => $mhs->nik_ktp ?? '0000000000000000',
                    'kewarganegaraan'=> 'ID',
                    'kelurahan'      => $mhs->alamat_lengkap ?? 'Tidak Diketahui',
                    'id_wilayah'     => '000000', // WAJIB DIGANTI NANTI: ID Wilayah Kecamatan dari Feeder
                    'penerima_kps'   => 0,
                    'nama_ibu_kandung' => $mhs->nama_ibu ?? 'TIDAK DIKETAHUI',
                ];

                $response = $this->feeder->execute('InsertBiodataMahasiswa', ['record' => $record]);

                // Get Hasli
                if (($response['error_code'] ?? 1) === 0) {
                    NeoFeederMahasiswas::updateOrCreate(
                        ['nim' => $mhs->nim],
                        [
                            'id_mahasiswa_feeder' => $response['data']['id_mahasiswa'] ?? null,
                            'nama_mahasiswa'      => $record['nama_mahasiswa'],
                            'jenis_kelamin'       => $record['jenis_kelamin'],
                            'tanggal_lahir'       => $record['tanggal_lahir'],
                            'last_synced_at'      => now(),
                        ]
                    );
                } else {
                    // GAGAL KARENA VALIDASI FEEDER (Misal: NIK sudah dipakai kampus lain)
                    \Log::error("GAGAL PUSH NIM {$mhs->nim}: " . ($response['error_desc'] ?? 'Unknown Error'));

                    // Tabel cermin dengan tanda error
                    NeoFeederMahasiswas::updateOrCreate(
                        ['nim' => $mhs->nim],
                        [
                            'status_sync' => 'error_push', // Menandakan ditolak feeder
                            'nama_mahasiswa' => $response['error_desc'] // Simpan pesan error sementara
                        ]
                    );
                }
                $processed++;
            }

            return response()->json([
                'status' => 'success',
                'processed' => $processed
            ]);

        } catch (\Exception $e) {
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
