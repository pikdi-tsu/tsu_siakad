<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MiddlewareController;
use App\Models\MasterData\Master_PenyelenggaraSertifikat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class PenyelenggaraSertifikatController extends MiddlewareController
{
    public function __construct() {
        $this->registerPermissions('system:master_penyelenggarasertifikat');
    }

    public function index(Request $request)
    {
        $data['title'] = 'Master Penyelenggara Sertifikat';
        $data['menu']  = 'Penyelenggara Sertifikat';

        if ($request->ajax()) {
            $query = Master_PenyelenggaraSertifikat::query()->orderBy('nama_penyelenggara_sertifikat', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $this->getActionButtons($row, 'system:master_penyelenggarasertifikat');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('system::masterdata.penyelenggaraSertifikat.index', $data);
    }

    public function store(Request $request)
    {
        $this->guardStore($request->id, 'system:master_penyelenggarasertifikat');
        $validator = Validator::make($request->all(), [
            'nama_penyelenggara_sertifikat' => 'required|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_PenyelenggaraSertifikat::updateOrCreate(
            ['id' => $request->id],
            ['nama_penyelenggara_sertifikat' => $request->nama_penyelenggara_sertifikat]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Data Penyelenggara Sertifikat berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $this->guard('edit', 'system:master_penyelenggarasertifikat');

        $data = Master_PenyelenggaraSertifikat::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function update(Request $request, $id)
    {
        $this->guard('edit', 'system:master_penyelenggarasertifikat');

        $masterPenyelenggaraSertifikat = Master_PenyelenggaraSertifikat::query()->findOrFail($id);
        $tablePermission = config('app.module.name');

        $request->validate([
            'name' => ['required', Rule::unique($tablePermission . '_master_penyelenggara_sertifikat', 'name')->ignore($id)->where('guard_name', 'web')]
        ]);

        $masterPenyelenggaraSertifikat->update(['name' => $request->name]);

        return back()->with('success', 'Nama Permission berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->guard('delete', 'system:master_penyelenggarasertifikat');

        $data = Master_PenyelenggaraSertifikat::find($id);

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
