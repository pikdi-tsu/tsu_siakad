<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MiddlewareController;
use App\Models\MasterData\Master_ProgramStudi;
use App\Models\MasterData\NeoFeederProdi;
use App\Services\NeoFeederService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ProgramStudiController extends MiddlewareController
{
    private NeoFeederService $neoFeederService;

    public function __construct(NeoFeederService $neoFeederService)
    {
        $this->registerPermissions('system:master_programstudi');
        $this->neoFeederService = $neoFeederService;
    }

    public function index(Request $request)
    {
        $data['title'] = "Master Program Studi";
        $data['menu']  = "Program Studi";
        $data['feeder_prodis'] = NeoFeederProdi::where('status', 'A')->get();

        if ($request->ajax()) {
            $query = Master_ProgramStudi::get();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $this->getActionButtons($row, 'system:master_programstudi');
                })
                ->rawColumns(['action', 'kode_prodi', 'nama_prodi', 'fakultas_id', 'ketua_prodi', 'status_prodi'])
                ->make(true);
        }

        return view('system::masterdata.programStudi.index', $data);
    }

    public function store(Request $request)
    {
        $this->guardStore($request->id, 'system:master_programstudi');

        $validator = Validator::make($request->all(), [
            'kode_prodi'   => 'required|string|max:20|unique:siakad_master_program_studi,kode_prodi,' . $request->id,
            'nama_prodi'   => 'required|string|max:200',
            'fakultas_id'  => 'required|string',
            'status_prodi' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_ProgramStudi::updateOrCreate(
            ['id' => $request->id],
            [
                'kode_prodi'   => $request->kode_prodi,
                'nama_prodi'   => $request->nama_prodi,
                'ketua_prodi'  => $request->ketua_prodi,
                'fakultas_id'  => $request->fakultas_id,
                'status_prodi' => $request->status_prodi,
            ]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Program Studi berhasil disimpan!'
        ]);
    }

    // TARIK DATA KE TABEL NEO FEEDER PRODI
    public function syncFeeder()
    {
        try {
            // Tembak API GetProdi (atau sesuaikan jika di versi Feeder Komandan namanya GetSms)
            $response = $this->neoFeederService->execute('GetProdi');

            if (($response['error_code'] ?? 1) !== 0 || empty($response['data'])) {
                return response()->json(['status' => 'error', 'message' => 'Gagal menarik data atau data kosong dari Feeder.']);
            }

            $countInserted = 0; $countUpdated = 0;

            foreach ($response['data'] as $row) {
                $feederData = NeoFeederProdi::where('id_prodi', $row['id_prodi'])->first();

                if (!$feederData) {
                    NeoFeederProdi::create([
                        'id' => Str::uuid(),
                        'id_prodi' => $row['id_prodi'],
                        'kode_program_studi' => $row['kode_program_studi'],
                        'nama_program_studi' => $row['nama_program_studi'],
                        'status' => $row['status'],
                        'id_jenjang_pendidikan' => $row['id_jenjang_pendidikan'],
                        'nama_jenjang_pendidikan' => $row['nama_jenjang_pendidikan'],
                    ]);
                    $countInserted++;
                } else {
                    $feederData->update([
                        'kode_program_studi' => $row['kode_program_studi'],
                        'nama_program_studi' => $row['nama_program_studi'],
                        'status' => $row['status'],
                        'id_jenjang_pendidikan' => $row['id_jenjang_pendidikan'],
                        'nama_jenjang_pendidikan' => $row['nama_jenjang_pendidikan'],
                    ]);
                    $countUpdated++;
                }
            }
            return response()->json(['status' => 'success', 'message' => "Berhasil menarik data! ($countInserted Data Baru, $countUpdated Diperbarui)"]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // FUNGSI UNTUK DATATABLE TAB 2 (DATA NEO FEEDER)
    public function tableFeeder()
    {
        $data = NeoFeederProdi::latest()->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('kode_prodi', function ($d) { return $d->kode_program_studi; })
            ->addColumn('nama_prodi', function ($d) { return $d->nama_program_studi; })
            ->addColumn('jenjang', function ($d) { return $d->nama_jenjang_pendidikan; })
            ->addColumn('status', function ($d) {
                $warna = $d->status === 'A' ? 'success' : 'danger';
                $teks = $d->status === 'A' ? 'Aktif' : 'Tidak Aktif';
                return '<span class="badge bg-'.$warna.'">'.$teks.'</span>';
            })
            ->rawColumns(['status'])
            ->make(true);
    }

    public function edit($id)
    {
        $this->guard('edit', 'system:master_programstudi');

        $data = Master_ProgramStudi::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function update(Request $request, $id)
    {
        $this->guard('edit', 'system:master_programstudi');

        $masterProgramStudi = Master_ProgramStudi::query()->findOrFail($id);
        $tablePermission = config('app.module.name');

        $request->validate([
            'name' => ['required', Rule::unique($tablePermission . '_master_program_studi', 'name')->ignore($id)->where('guard_name', 'web')]
        ]);

        $masterProgramStudi->update(['name' => $request->name]);

        return back()->with('success', 'Nama Permission berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->guard('delete', 'system:master_programstudi');

        $data = Master_ProgramStudi::find($id);

        if ($data) {
            $data->delete();
            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil dihapus!'
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Gagal menghapus data'
        ]);
    }
}
