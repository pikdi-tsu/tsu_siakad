<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MiddlewareController;
use App\Models\MasterData\Master_ContactPerson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class ContactPersonController extends MiddlewareController
{
    public function __construct()
    {
        $this->registerPermissions('system:master_contactperson');
    }

    public function index(Request $request)
    {
        $data['title'] = "Master Contact Person";
        $data['menu']  = "Contact Person";

        if ($request->ajax()) {
            $query = Master_ContactPerson::query()->orderBy('nama', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return $this->getActionButtons($row, 'system:master_contactperson');

                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('system::masterdata.contactPerson.index', $data);
    }

    public function store(Request $request)
    {
        $this->guardStore($request->id, 'system:master_contactperson');

        // VALIDASI
        $validator = Validator::make($request->all(), [
            'nama'          => 'required|string|max:150',
            'jenis_kelamin' => 'required|string|in:L,P',
            'no_telepon'    => 'required|string|max:30',
            'alamat_email'  => 'required|email|max:150',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        // SIMPAN DATA
        Master_ContactPerson::updateOrCreate(
            ['id' => $request->id],
            [
                'nama'          => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'no_telepon'    => $request->no_telepon,
                'alamat_email'  => $request->alamat_email,
            ]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Contact Person berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $this->guard('edit', 'system:master_contactperson');

        $data = Master_ContactPerson::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function update(Request $request, $id)
    {
        $this->guard('edit', 'system:master_contactperson');

        $masterContactPerson = Master_ContactPerson::query()->findOrFail($id);
        $tablePermission = config('app.module.name');

        $request->validate([
            'name' => ['required', Rule::unique($tablePermission . '_master_contactperson', 'name')->ignore($id)->where('guard_name', 'web')]
        ]);

        $masterContactPerson->update(['name' => $request->name]);

        return back()->with('success', 'Nama Permission berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->guard('delete', 'system:master_contactperson');

        $data = Master_ContactPerson::find($id);

        if ($data) {
            $data->delete();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus!']);
        }

        return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
}
