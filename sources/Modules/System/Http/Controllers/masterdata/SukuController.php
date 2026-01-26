<?php

namespace Modules\System\Http\Controllers\masterdata;


use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_Suku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SukuController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Master Suku";
        $data['menu']  = "Suku";

        if ($request->ajax()) {
            $query = Master_Suku::query()->orderBy('nama_suku', 'asc');

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

        return view('system::masterdata.suku.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_suku' => 'required|string|max:100|unique:siakad_master_suku,nama_suku,' . $request->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_Suku::updateOrCreate(
            ['id' => $request->id],
            [
                'nama_suku' => $request->nama_suku,
            ]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Master Suku berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $data = Master_Suku::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_Suku::find($id);

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
