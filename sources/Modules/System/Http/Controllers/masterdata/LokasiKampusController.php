<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MiddlewareController;
use App\Models\MasterData\Master_LokasiKampus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class LokasiKampusController extends MiddlewareController
{
    public function __construct() {
        $this->registerPermissions('system:master_lokasikampus');
    }

    public function index(Request $request)
    {
        $data['title'] = 'Master Lokasi Kampus';
        $data['menu']  = 'Lokasi Kampus';

        if ($request->ajax()) {
            $query = Master_LokasiKampus::query()->orderBy('nama', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $this->getActionButtons($row, 'system:master_lokasikampus');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('system::masterdata.lokasi_kampus.index', $data);
    }

    public function store(Request $request)
    {
        $this->guardStore($request->id, 'system:master_lokasikampus');

        $validator = Validator::make($request->all(), [
            'kode' => 'required|string|max:20|unique:siakad_master_lokasi_kampus,kode,' . $request->id,
            'nama' => 'required|string|max:200',
            'alamat' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_LokasiKampus::updateOrCreate(
            ['id' => $request->id],
            [
                'kode' => $request->kode,
                'nama' => $request->nama,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Data Lokasi Kampus berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $this->guard('edit', 'system:master_lokasikampus');

        $data = Master_LokasiKampus::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function update(Request $request, $id)
    {
        $this->guard('edit', 'system:master_lokasikampus');

        $masterLokasiKampus = Master_LokasiKampus::query()->findOrFail($id);
        $tablePermission = config('app.module.name');

        $request->validate([
            'name' => ['required', Rule::unique($tablePermission . '_master_lokasi_kampus', 'name')->ignore($id)->where('guard_name', 'web')]
        ]);

        $masterLokasiKampus->update(['name' => $request->name]);

        return back()->with('success', 'Nama Permission berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->guard('delete', 'system:master_lokasikampus');

        $data = Master_LokasiKampus::find($id);

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
