<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\MiddlewareController;
use App\Models\MasterData\Master_Fakultas;
use App\Models\MasterData\Master_JurusanKuliah;
use App\Models\NeoFeederFakultas;
use App\Services\NeoFeederService;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Symfony\Component\HttpFoundation\Response;
use DB;

class FakultasController extends MiddlewareController
{
    protected $feeder;

    public function __construct(NeoFeederService $feederService)
    {
        $this->registerPermissions('system:master_fakultas');
        $this->feeder = $feederService;
    }

    public function index()
    {
        $last = Master_Fakultas::where('isactive',1)->orderBy('IdFakultas','desc')->select('KodeFakultas')->first();
        $newCode = 'F001';
        if ($last && preg_match('/F(\d+)/', $last->KodeFakultas, $matches)) {
            $newCode = 'F' . str_pad((int)$matches[1] + 1, 3, '0', STR_PAD_LEFT);
        }

        return view('system::masterdata.fakultas.index', [
            'title' => 'Master Data Fakultas',
            'menu'  => 'Fakultas',
            'kdfakultas' => $newCode
        ]);
    }

    public function table_fakultas()
    {
        $data = Master_Fakultas::get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('kode', function ($d) { return $d->KodeFakultas; })
            ->addColumn('nama', function ($d) { return $d->namafakultas; })
            ->addColumn('singkatan', function ($d) { return $d->singkatan; })
            ->addColumn('aktif', function ($d) {
                $warna = $d->isactive === 1 ? 'success' : 'danger';
                $teks = $d->isactive === 1 ? 'Aktif' : 'Tidak Aktif';
                return '<span class="badge bg-'.$warna.'">'.$teks.'</span>';
            })
            ->addColumn('action', function ($d) {
                $id = encrypt($d->KodeFakultas);
                $edit = '<a href="#" data-id="'.$id.'" class="btn_edit"><i title="Edit" class="fa fa-edit text-orange"></i></a>';

                $aktifBtn = $d->isactive === 1
                    ? '<a href="'.route('perguruan_tinggi.fakultas.delete',[$id,encrypt('0')]).'" class="btn_delete"><i title="Nonaktifkan" class="fa fa-trash text-red"></i></a>'
                    : '<a href="'.route('perguruan_tinggi.fakultas.delete',[$id,encrypt('1')]).'" class="btn_delete"><i title="Aktifkan" class="fas fa-check-circle text-green"></i></a>';

                return $edit.' '.$aktifBtn;
            })
            ->rawColumns(['action','aktif'])
            ->make(true);
    }

    public function syncFeeder()
    {
        try {
            // Tembak API GetFakultas
            $response = $this->feeder->execute('GetFakultas');

            if (($response['error_code'] ?? 1) !== 0 || empty($response['data'])) {
                return response()->json(['status' => 'error', 'message' => 'Gagal menarik data atau data kosong dari Feeder.']);
            }

            $countInserted = 0;
            $countUpdated = 0;

            foreach ($response['data'] as $row) {
                // Cek apakah data feeder ini sudah ada di tabel cermin kita
                $feederData = NeoFeederFakultas::where('id_fakultas', $row['id_fakultas'])->first();

                if (!$feederData) {
                    NeoFeederFakultas::create([
                        'id' => Str::uuid(),
                        'id_fakultas' => $row['id_fakultas'],
                        'nama_fakultas' => $row['nama_fakultas'],
                        'status' => $row['status'],
                        'id_jenjang_pendidikan' => $row['id_jenjang_pendidikan'],
                        'nama_jenjang_pendidikan' => $row['nama_jenjang_pendidikan'],
                    ]);
                    $countInserted++;
                } else {
                    $feederData->update([
                        'nama_fakultas' => $row['nama_fakultas'],
                        'status' => $row['status'],
                        'id_jenjang_pendidikan' => $row['id_jenjang_pendidikan'],
                        'nama_jenjang_pendidikan' => $row['nama_jenjang_pendidikan'],
                    ]);
                    $countUpdated++;
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => "Berhasil menarik data! ($countInserted Data Baru, $countUpdated Diperbarui)"
            ]);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // Datatable NEO FEEDER
    public function table_feeder()
    {
        $data = NeoFeederFakultas::latest()->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('id_fakultas', function ($d) { return $d->id_fakultas; })
            ->addColumn('nama_fakultas', function ($d) { return $d->nama_fakultas; })
            ->addColumn('jenjang', function ($d) { return $d->nama_jenjang_pendidikan; })
            ->addColumn('status', function ($d) {
                $warna = $d->status === 'A' ? 'success' : 'danger';
                $teks = $d->status === 'A' ? 'Aktif' : 'Tidak Aktif';
                return '<span class="badge bg-'.$warna.'">'.$teks.'</span>';
            })
            ->rawColumns(['status'])
            ->make(true);
    }

    // ============================================================
    // FUNGSI MANUAL (CREATE, UPDATE, DELETE)
    // ============================================================
    public function StoreFakultas(Request $post)
    {
        $cek = Master_Fakultas::where('namafakultas',$post->namafakultas)->orwhere('singkatan',$post->singkatanfakultas)->first();
        if($cek && $post->IdFakultas == null){
            return redirect()->back()->with('alert', ['title' => 'Gagal!','message' => 'Nama / Singkatan Sudah Ada !','status' => 'warning']);
        }

        if($post->IdFakultas == null){
            Master_Fakultas::insert([
                'KodeFakultas' => $post->kdfakultas,
                'namafakultas' => $post->namafakultas,
                'singkatan'    => $post->singkatanfakultas,
                'created_at'   => now(),
                'created_by'   => session('session')->nip ?? 'Admin',
            ]);
            $msg = 'Disimpan';
        } else {
            $id = decrypt($post->IdFakultas);
            Master_Fakultas::where('IdFakultas', $id)->update([
                'namafakultas' => $post->namafakultas,
                'singkatan'    => $post->singkatanfakultas,
                'updated_at'   => now(),
                'updated_by'   => session('session')->nip ?? 'Admin',
            ]);
            $msg = 'Diperbarui';
        }

        return redirect()->back()->with('alert', ['title' => 'Berhasil!','message' => "Data Fakultas $msg!",'status' => 'success']);
    }

    public function ShowFakultas($params)
    {
        $id = decrypt($params);
        $check = Master_Fakultas::where('KodeFakultas',$id)->first();
        return response()->json(['hasil' => $check ? 1 : 0, 'fakultas' => $check, 'IdFakultas' => $params], Response::HTTP_OK);
    }

    public function delete($params1,$params2)
    {
        $id = decrypt($params1);
        $aktif = decrypt($params2);

        if ($aktif == 0 && Master_JurusanKuliah::where('fakultas',$id)->exists()) {
            return redirect()->back()->with('alert', ['title' => 'Gagal','message' => 'Fakultas sedang digunakan di tabel Jurusan!','status' => 'error']);
        }

        Master_Fakultas::where('KodeFakultas',$id)->update([
            'isactive' => $aktif,
            'updated_at' => now(),
            'updated_by' => session('session')->nip ?? 'Admin'
        ]);

        return redirect()->back()->with('alert', ['title' => 'Berhasil','message' => 'Status Fakultas Diperbarui','status' => 'success']);
    }
}
