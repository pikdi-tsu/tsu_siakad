<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MiddlewareController;
use App\Models\MasterData\Master_GroupMkWajibPilihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class GroupMkWajibPilihanController extends MiddlewareController
{
    public function __construct() {
        $this->registerPermissions('system:master_groupmkwajibpilihan');
    }

    public function index(Request $request)
    {
        $data['title'] = 'Master Group MK Wajib/Pilihan';
        $data['menu']  = 'Group MK Wajib/Pilihan';

        if ($request->ajax()) {
            $query = Master_GroupMkWajibPilihan::query()->orderBy('nama_group_mk', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $this->getActionButtons($row, 'system:master_groupmkwajibpilihan');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('system::masterdata.group_mk_wajib_pilihan.index', $data);
    }

    public function store(Request $request)
    {
        $this->guardStore($request->id, 'system:master_groupmkwajibpilihan');

        $validator = Validator::make($request->all(), [
            'nama_group_mk' => 'required|string|max:150',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_GroupMkWajibPilihan::updateOrCreate(
            ['id' => $request->id],
            ['nama_group_mk' => $request->nama_group_mk]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Data Group MK Wajib/Pilihan berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $this->guard('edit', 'system:master_groupmkwajibpilihan');

        $data = Master_GroupMkWajibPilihan::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function update(Request $request, $id)
    {
        $this->guard('edit', 'system:master_groupmkwajibpilihan');

        $masterGroupMKWajibPilihan = Master_GroupMKWajibPilihan::query()->findOrFail($id);
        $tablePermission = config('app.module.name');

        $request->validate([
            'name' => ['required', Rule::unique($tablePermission . '_master_group_mk_wajib_pilihan', 'name')->ignore($id)->where('guard_name', 'web')]
        ]);

        $masterGroupMKWajibPilihan->update(['name' => $request->name]);

        return back()->with('success', 'Nama Permission berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->guard('delete', 'system:master_groupmkwajibpilihan');

        $data = Master_GroupMkWajibPilihan::find($id);

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
