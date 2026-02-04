<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MiddlewareController;
use App\Models\MasterData\Master_Instansi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class InstansiController extends MiddlewareController
{
    public function __construct()
    {
        $this->registerPermissions('system:master_instansi');
    }

    public function index(Request $request)
    {
        $data['title'] = "Master Instansi";
        $data['menu']  = "Instansi";

        if ($request->ajax()) {
            $query = Master_Instansi::query()
                ->orderBy('nama_instansi', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $this->getActionButtons($row, 'system:master_instansi');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('system::masterdata.instansi.index', $data);
    }

    public function store(Request $request)
    {
        $this->guardStore($request->id, 'system:master_instansi');

        $validator = Validator::make($request->all(), [
            'no'            => 'required|string|max:50|unique:siakad_master_instansi,no,' . $request->id,
            'nama_instansi' => 'required|string|max:200',
            'alamat'        => 'nullable|string|max:255',
            'no_telepon'    => 'nullable|string|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_Instansi::updateOrCreate(
            ['id' => $request->id],
            [
                'no'            => $request->no,
                'nama_instansi' => $request->nama_instansi,
                'alamat'        => $request->alamat,
                'no_telepon'    => $request->no_telepon,
            ]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Instansi berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $this->guard('edit', 'system:master_instansi');

        $data = Master_Instansi::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function update(Request $request, $id)
    {
        $this->guard('edit', 'system:master_instansi');

        $masterInstansi = Master_Instansi::query()->findOrFail($id);
        $tablePermission = config('app.module.name');

        $request->validate([
            'name' => ['required', Rule::unique($tablePermission . '_master_instansi', 'name')->ignore($id)->where('guard_name', 'web')]
        ]);

        $masterInstansi->update(['name' => $request->name]);

        return back()->with('success', 'Nama Permission berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->guard('delete', 'system:master_instansi');

        $data = Master_Instansi::find($id);

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
