<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MiddlewareController;
use App\Models\MasterData\Master_ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ProgramStudiController extends MiddlewareController
{
    public function __construct()
    {
        $this->registerPermissions('system:master_programstudi');
    }

    public function index(Request $request)
    {
        $data['title'] = "Master Program Studi";
        $data['menu']  = "Program Studi";

        if ($request->ajax()) {
            $query = Master_ProgramStudi::query()
                ->orderBy('nama_prodi', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $this->getActionButtons($row, 'system:master_programstudi');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('system::masterdata.program_studi.index', $data);
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
