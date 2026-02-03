<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MiddlewareController;
use App\Models\MasterData\Master_StatusKeaktifan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class StatusKeaktifanController extends MiddlewareController
{
    public function __construct() {
        $this->registerPermissions('system:master_statuskeaktifan');
    }
    public function index(Request $request)
    {
        $data['title'] = 'Master Status Keaktifan';
        $data['menu']  = 'Status Keaktifan';

        if ($request->ajax()) {
            $query = Master_StatusKeaktifan::query()->orderBy('nama_status_keaktifan', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $this->getActionButtons($row, 'system:master_statuskeaktifan');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('system::masterdata.status_keaktifan.index', $data);
    }

    public function store(Request $request)
    {
        $this->guardStore($request->id, 'system:master_statuskeaktifan');

        $validator = Validator::make($request->all(), [
            'nama_status_keaktifan' => 'required|string|max:150',
            'status_keluar' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_StatusKeaktifan::updateOrCreate(
            ['id' => $request->id],
            [
                'nama_status_keaktifan' => $request->nama_status_keaktifan,
                'status_keluar' => $request->status_keluar,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Data Status Keaktifan berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $this->guard('edit', 'system:master_statuskeaktifan');

        $data = Master_StatusKeaktifan::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function update(Request $request, $id)
    {
        $this->guard('edit', 'system:master_statuskeaktifan');

        $masterStatusKeaktifan = Master_StatusKeaktifan::query()->findOrFail($id);
        $tablePermission = config('app.module.name');

        $request->validate([
            'name' => ['required', Rule::unique($tablePermission . '_master_status_keaktifan', 'name')->ignore($id)->where('guard_name', 'web')]
        ]);

        $masterStatusKeaktifan->update(['name' => $request->name]);

        return back()->with('success', 'Nama Permission berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->guard('delete', 'system:master_statuskeaktifan');

        $data = Master_StatusKeaktifan::find($id);

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
