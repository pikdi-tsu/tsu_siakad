<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MiddlewareController;
use App\Models\MasterData\Master_Konsentrasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class KonsentrasiController extends MiddlewareController
{
    public function __construct()
    {
        $this->registerPermissions('system:master_konsentrasi');
    }

    public function index(Request $request)
    {
        $data['title'] = "Master Konsentrasi";
        $data['menu']  = "Konsentrasi";

        if ($request->ajax()) {
            $query = Master_Konsentrasi::query()
                ->orderBy('nama_konsentrasi', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $this->getActionButtons($row, 'system:master_konsentrasi');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('system::masterdata.konsentrasi.index', $data);
    }

    public function store(Request $request)
    {
        $this->guardStore($request->id, 'system:master_konsentrasi');

        $validator = Validator::make($request->all(), [
            'kode'              => 'required|string|max:20|unique:siakad_master_konsentrasi,kode,' . $request->id,
            'nama_konsentrasi'  => 'required|string|max:200',
            'nama_konsentrasi_en' => 'nullable|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_Konsentrasi::updateOrCreate(
            ['id' => $request->id],
            [
                'kode'               => $request->kode,
                'nama_konsentrasi'   => $request->nama_konsentrasi,
                'nama_konsentrasi_en' => $request->nama_konsentrasi_en,
            ]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Konsentrasi berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $this->guard('edit', 'system:master_konsentrasi');

        $data = Master_Konsentrasi::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function update(Request $request, $id)
    {
        $this->guard('edit', 'system:master_konsentrasi');

        $masterKonsentrasi = Master_Konsentrasi::query()->findOrFail($id);
        $tablePermission = config('app.module.name');

        $request->validate([
            'name' => ['required', Rule::unique($tablePermission . '_master_konsentrasi', 'name')->ignore($id)->where('guard_name', 'web')]
        ]);

        $masterKonsentrasi->update(['name' => $request->name]);

        return back()->with('success', 'Nama Permission berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->guard('delete', 'system:master_konsentrasi');

        $data = Master_Konsentrasi::find($id);

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
