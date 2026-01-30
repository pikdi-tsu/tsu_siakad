<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_GroupMkWajibPilihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class GroupMkWajibPilihanController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = 'Master Group MK Wajib/Pilihan';
        $data['menu']  = 'Group MK Wajib/Pilihan';

        if ($request->ajax()) {
            $query = Master_GroupMkWajibPilihan::query()->orderBy('nama_group_mk', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn  = '<button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-sm btn_edit" title="Edit">';
                    $btn .= '<i class="fas fa-pencil-alt"></i></button> ';
                    $btn .= '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-sm btn_hapus" title="Hapus">';
                    $btn .= '<i class="fas fa-trash"></i></button>';

                    return '<div class="text-center">' . $btn . '</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('system::masterdata.groupMkWajibPilihan.index', $data);
    }

    public function store(Request $request)
    {
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
        $data = Master_GroupMkWajibPilihan::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
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
