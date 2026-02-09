<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MiddlewareController;
use App\Models\MasterData\Master_JabatanFungsional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class JabatanFungsionalController extends MiddlewareController
{
    public function __construct() {
        $this->registerPermissions('system:master_golonganpangkat',);
    }

    public function index(Request $request)
    {
        $data['title'] = 'Master Jabatan Fungsional';
        $data['menu']  = 'Jabatan Fungsional';

        if ($request->ajax()) {
            $query = Master_JabatanFungsional::query()->orderBy('nama_jabatan_fungsional', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $this->getActionButtons($row, 'system:master_jabatanfungsional');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('system::masterdata.jabatanFungsional.index', $data);
    }

    public function store(Request $request)
    {
        $this->guardStore($request->id, 'system:master_jabatanfungsional');

        $validator = Validator::make($request->all(), [
            'kode_jabatan_fungsional' => 'required|string|max:20|unique:siakad_master_jabatan_fungsional,kode_jabatan_fungsional,' . $request->id,
            'nama_jabatan_fungsional' => 'required|string|max:150',
            'sks_maksimal' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_JabatanFungsional::updateOrCreate(
            ['id' => $request->id],
            [
                'kode_jabatan_fungsional' => $request->kode_jabatan_fungsional,
                'nama_jabatan_fungsional' => $request->nama_jabatan_fungsional,
                'sks_maksimal' => $request->sks_maksimal,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Data Jabatan Fungsional berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $this->guard('edit', 'system:master_jabatanfungsional');

        $data = Master_JabatanFungsional::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function update(Request $request, $id)
    {
        $this->guard('edit', 'system:master_jabatanfungsional');

        $masterJabatanFungsional = Master_JabatanFungsional::query()->findOrFail($id);
        $tablePermission = config('app.module.name');

        $request->validate([
            'name' => ['required', Rule::unique($tablePermission . '_master_jabatan_fungsional', 'name')->ignore($id)->where('guard_name', 'web')]
        ]);

        $masterJabatanFungsional->update(['name' => $request->name]);

        return back()->with('success', 'Nama Permission berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->guard('delete', 'system:master_jabatanfungsional');

        $data = Master_JabatanFungsional::find($id);

        if ($data) {
            $data->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil dihapus!'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal menghapus data'
        ]);
    }
}
