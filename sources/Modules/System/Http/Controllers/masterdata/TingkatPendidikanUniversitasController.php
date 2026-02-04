<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MiddlewareController;
use App\Models\MasterData\Master_TingkatPendidikanUniversitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class TingkatPendidikanUniversitasController extends MiddlewareController
{
    public function __construct()
    {
        $this->registerPermissions('system:master_tingkatpendidikanuniv');
    }

    public function index(Request $request)
    {
        $data['title'] = "Master Tingkat Pendidikan Universitas";
        $data['menu']  = "Tingkat Pendidikan Universitas";

        if ($request->ajax()) {
            $query = Master_TingkatPendidikanUniversitas::query()
                ->orderBy('jenjang', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $this->getActionButtons($row, 'system:master_tingkatpendidikanuniv');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('system::masterdata.tingkat_pendidikan_universitas.index', $data);
    }

    public function store(Request $request)
    {
        $this->guardStore($request->id, 'system:master_tingkatpendidikanuniv');

        $validator = Validator::make($request->all(), [
            'jenjang'    => 'required|string|max:20|unique:siakad_master_tingkat_pendidikan_universitas,jenjang,' . $request->id,
            'masa_studi' => 'required|string|max:10',
            'max_cuti'  => 'required|string|max:10',
            'max_studi' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_TingkatPendidikanUniversitas::updateOrCreate(
            ['id' => $request->id],
            [
                'jenjang'     => $request->jenjang,
                'masa_studi' => $request->masa_studi,
                'max_cuti'   => $request->max_cuti,
                'max_studi'  => $request->max_studi,
            ]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Tingkat Pendidikan Universitas berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $this->guard('edit', 'system:master_tingkatpendidikanuniv');

        $data = Master_TingkatPendidikanUniversitas::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function update(Request $request, $id)
    {
        $this->guard('edit', 'system:master_tingkatpendidikanuniv');

        $masterTingkatPendidikanUniversitas = Master_TingkatPendidikanUniversitas::query()->findOrFail($id);
        $tablePermission = config('app.module.name');

        $request->validate([
            'name' => ['required', Rule::unique($tablePermission . '_master_tingkat_pendidikan_universitas', 'name')->ignore($id)->where('guard_name', 'web')]
        ]);

        $masterTingkatPendidikanUniversitas->update(['name' => $request->name]);

        return back()->with('success', 'Nama Permission berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->guard('delete', 'system:master_tingkatpendidikanuniv');

        $data = Master_TingkatPendidikanUniversitas::find($id);

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
