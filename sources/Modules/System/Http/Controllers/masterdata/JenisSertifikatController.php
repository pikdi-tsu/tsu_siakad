<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MiddlewareController;
use App\Models\MasterData\Master_JenisSertifikat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class JenisSertifikatController extends MiddlewareController
{
    public function __construct() {
        $this->registerPermissions('system:master_jenissertifikat');
    }
    public function index(Request $request)
    {
        $data['title'] = 'Master Jenis Sertifikat';
        $data['menu']  = 'Jenis Sertifikat';

        if ($request->ajax()) {
            $query = Master_JenisSertifikat::query()->orderBy('nama_jenis_sertifikat', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $this->getActionButtons($row, 'system:master_jenissertifikat');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('system::masterdata.jenisSertifikat.index', $data);
    }

    public function store(Request $request)
    {
        $this->guardStore($request->id, 'system:master_jenissertifikat');

        $validator = Validator::make($request->all(), [
            'nama_jenis_sertifikat' => 'required|string|max:150',
            'ukom' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_JenisSertifikat::updateOrCreate(
            ['id' => $request->id],
            [
                'nama_jenis_sertifikat' => $request->nama_jenis_sertifikat,
                'ukom' => $request->ukom,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Data Jenis Sertifikat berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $this->guard('edit', 'system:master_jenissertifikat');

        $data = Master_JenisSertifikat::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function update(Request $request, $id)
    {
        $this->guard('edit', 'system:master_jenissertifikat');

        $masterJenisSertifikat = Master_JenisSertifikat::query()->findOrFail($id);
        $tablePermission = config('app.module.name');

        $request->validate([
            'name' => ['required', Rule::unique($tablePermission . '_master_jenis_sertifikat', 'name')->ignore($id)->where('guard_name', 'web')]
        ]);

        $masterJenisSertifikat->update(['name' => $request->name]);

        return back()->with('success', 'Nama Permission berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->guard('delete', 'system:master_jenissertifikat');

        $data = Master_JenisSertifikat::find($id);

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
