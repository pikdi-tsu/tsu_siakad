<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MiddlewareController;
use App\Models\MasterData\Master_Negara;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class NegaraController extends MiddlewareController
{
    public function __construct() {
        $this->registerPermissions('system:master_negara');
    }
    public function index(Request $request)
    {
        $data['title'] = 'Master Negara';
        $data['menu']  = 'Negara';

        if ($request->ajax()) {
            $query = Master_Negara::query()->orderBy('nama_negara', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $this->getActionButtons($row, 'system:master_negara');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('system::masterdata.negara.index', $data);
    }

    public function store(Request $request)
    {
        $this->guardStore($request->id, 'system:master_negara');

        $validator = Validator::make($request->all(), [
            'kode_negara' => 'required|string|max:10|unique:siakad_master_negara,kode_negara,' . $request->id,
            'nama_negara' => 'required|string|max:100',
            'kode_emis' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_Negara::updateOrCreate(
            ['id' => $request->id],
            [
                'kode_negara' => $request->kode_negara,
                'nama_negara' => $request->nama_negara,
                'kode_emis' => $request->kode_emis,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Data Negara berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $this->guard('edit', 'system:master_negara');

        $data = Master_Negara::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function update(Request $request, $id)
    {
        $this->guard('edit', 'system:master_negara');

        $masterNegara = Master_Negara::query()->findOrFail($id);
        $tablePermission = config('app.module.name');

        $request->validate([
            'name' => ['required', Rule::unique($tablePermission . '_master_negara', 'name')->ignore($id)->where('guard_name', 'web')]
        ]);

        $masterNegara->update(['name' => $request->name]);

        return back()->with('success', 'Nama Permission berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->guard('delete', 'system:master_negara');

        $data = Master_Negara::find($id);

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
