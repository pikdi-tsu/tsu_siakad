<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MiddlewareController;
use App\Models\MasterData\Master_JabatanStruktural;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class JabatanStrukturalController extends MiddlewareController
{
    public function __construct() {
        $this->registerPermissions('system:master_jabatanstruktural');
    }
    public function index(Request $request)
    {
        $data['title'] = 'Master Jabatan Struktural';
        $data['menu']  = 'Jabatan Struktural';

        if ($request->ajax()) {
            $query = Master_JabatanStruktural::query()->orderBy('nama_jabatan_struktural', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $this->getActionButtons($row, 'system:master_jabatanstruktural');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('system::masterdata.jabatan_struktural.index', $data);
    }

    public function store(Request $request)
    {
        $this->guardStore($request->id, 'system:master_jabatanstruktural');

        $validator = Validator::make($request->all(), [
            'nama_jabatan_struktural' => 'required|string|max:150',
            'parent_jabatan_struktural' => 'nullable|string|max:150',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_JabatanStruktural::updateOrCreate(
            ['id' => $request->id],
            [
                'nama_jabatan_struktural' => $request->nama_jabatan_struktural,
                'parent_jabatan_struktural' => $request->parent_jabatan_struktural,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Data Jabatan Struktural berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $this->guard('edit', 'system:master_jabatanstruktural');

        $data = Master_JabatanStruktural::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function update(Request $request, $id)
    {
        $this->guard('edit', 'system:master_jabatanstruktural');

        $masterJabatanStruktural = Master_JabatanStruktural::query()->findOrFail($id);
        $tablePermission = config('app.module.name');

        $request->validate([
            'name' => ['required', Rule::unique($tablePermission . '_master_jabatan_struktural', 'name')->ignore($id)->where('guard_name', 'web')]
        ]);

        $masterJabatanStruktural->update(['name' => $request->name]);

        return back()->with('success', 'Nama Permission berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->guard('delete', 'system:master_jabatanstruktural');

        $data = Master_JabatanStruktural::find($id);

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
