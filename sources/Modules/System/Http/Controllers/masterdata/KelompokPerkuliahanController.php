<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MiddlewareController;
use App\Models\MasterData\Master_KelompokPerkuliahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class KelompokPerkuliahanController extends MiddlewareController
{
    public function __construct() {
        $this->registerPermissions('system:master_kelompokperkuliahan');
    }

    public function index(Request $request)
    {
        $data['title'] = 'Master Kelompok Perkuliahan';
        $data['menu']  = 'Kelompok Perkuliahan';

        if ($request->ajax()) {
            $query = Master_KelompokPerkuliahan::query()->orderBy('urutan', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $this->getActionButtons($row, 'system:master_kelompokperkuliahan');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('system::masterdata.kelompok_perkuliahan.index', $data);
    }

    public function store(Request $request)
    {
        $this->guardStore($request->id, 'system:master_kelompokperkuliahan');

        $validator = Validator::make($request->all(), [
            'nama_kelompok_perkuliahan' => 'required|string|max:150',
            'urutan' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_KelompokPerkuliahan::updateOrCreate(
            ['id' => $request->id],
            [
                'nama_kelompok_perkuliahan' => $request->nama_kelompok_perkuliahan,
                'urutan' => $request->urutan,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Data Kelompok Perkuliahan berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $this->guard('edit', 'system:master_kelompokperkuliahan');

        $data = Master_KelompokPerkuliahan::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function update(Request $request, $id)
    {
        $this->guard('edit', 'system:master_kelompokperkuliahan');

        $masterKelompokPerkuliahan = Master_KelompokPerkuliahan::query()->findOrFail($id);
        $tablePermission = config('app.module.name');

        $request->validate([
            'name' => ['required', Rule::unique($tablePermission . '_master_kelompok_perkuliahan', 'name')->ignore($id)->where('guard_name', 'web')]
        ]);

        $masterKelompokPerkuliahan->update(['name' => $request->name]);

        return back()->with('success', 'Nama Permission berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->guard('delete', 'system:master_kelompokperkuliahan');

        $data = Master_KelompokPerkuliahan::find($id);

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
