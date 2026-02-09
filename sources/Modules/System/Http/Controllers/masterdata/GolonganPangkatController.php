<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MiddlewareController;
use App\Models\MasterData\Master_GolonganPangkat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class GolonganPangkatController extends MiddlewareController
{
    public function __construct() {
        $this->registerPermissions('system:master_golonganpangkat');
    }
    public function index(Request $request)
    {
        $data['title'] = 'Master Golongan Pangkat';
        $data['menu']  = 'Golongan Pangkat';

        if ($request->ajax()) {
            $query = Master_GolonganPangkat::query()->orderBy('nama_golongan_pangkat', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $this->getActionButtons($row, 'system:master_golonganpangkat');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('system::masterdata.golonganPangkat.index', $data);
    }

    public function store(Request $request)
    {
        $this->guardStore($request->id, 'system:master_golonganpangkat');

        $validator = Validator::make($request->all(), [
            'kode_golongan_pangkat' => 'required|string|max:20|unique:siakad_master_golongan_pangkat,kode_golongan_pangkat,' . $request->id,
            'nama_golongan_pangkat' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_GolonganPangkat::updateOrCreate(
            ['id' => $request->id],
            [
                'kode_golongan_pangkat' => $request->kode_golongan_pangkat,
                'nama_golongan_pangkat' => $request->nama_golongan_pangkat,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Data Golongan Pangkat berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $this->guard('edit', 'system:master_golonganpangkat');

        $data = Master_GolonganPangkat::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function update(Request $request, $id)
    {
        $this->guard('edit', 'system:master_golonganpangkat');

        $masterGolonganPangkat = Master_GolonganPangkat::query()->findOrFail($id);
        $tablePermission = config('app.module.name');

        $request->validate([
            'name' => ['required', Rule::unique($tablePermission . '_master_golongan_pangkat', 'name')->ignore($id)->where('guard_name', 'web')]
        ]);

        $masterGolonganPangkat->update(['name' => $request->name]);

        return back()->with('success', 'Nama Permission berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->guard('delete', 'system:master_golonganpangkat');

        $data = Master_GolonganPangkat::find($id);

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
