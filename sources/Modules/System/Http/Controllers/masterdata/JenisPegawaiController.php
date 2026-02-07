<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MiddlewareController;
use App\Models\MasterData\Master_JenisPegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class JenisPegawaiController extends MiddlewareController
{
    public function __construct() {
        $this->registerPermissions('system:master_jenispegawai');
    }

    public function index(Request $request)
    {
        $data['title'] = 'Master Jenis Pegawai';
        $data['menu']  = 'Jenis Pegawai';

        if ($request->ajax()) {
            $query = Master_JenisPegawai::query()->orderBy('nama_jenis_pegawai', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $this->getActionButtons($row, 'system:master_jenispegawai');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('system::masterdata.jenisPegawai.index', $data);
    }

    public function store(Request $request)
    {
        $this->guardStore($request->id, 'system:master_jenispegawai');

        $validator = Validator::make($request->all(), [
            'kode_jenis_pegawai' => 'required|string|max:20|unique:siakad_master_jenis_pegawai,kode_jenis_pegawai,' . $request->id,
            'nama_jenis_pegawai' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_JenisPegawai::updateOrCreate(
            ['id' => $request->id],
            [
                'kode_jenis_pegawai' => $request->kode_jenis_pegawai,
                'nama_jenis_pegawai' => $request->nama_jenis_pegawai,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Data Jenis Pegawai berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $this->guard('edit', 'system:master_jenispegawai');

        $data = Master_JenisPegawai::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function update(Request $request, $id)
    {
        $this->guard('edit', 'system:master_jenispegawai');

        $masterJenisPegawai = Master_JenisPegawai::query()->findOrFail($id);
        $tablePermission = config('app.module.name');

        $request->validate([
            'name' => ['required', Rule::unique($tablePermission . '_master_jenis_pegawai', 'name')->ignore($id)->where('guard_name', 'web')]
        ]);

        $masterJenisPegawai->update(['name' => $request->name]);

        return back()->with('success', 'Nama Permission berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->guard('delete', 'system:master_jenispegawai');

        $data = Master_JenisPegawai::find($id);

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
