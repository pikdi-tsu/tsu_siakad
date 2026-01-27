<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_Negara;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class NegaraController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = 'Master Negara';
        $data['menu']  = 'Negara';

        if ($request->ajax()) {
            $query = Master_Negara::query()->orderBy('nama_negara', 'asc');

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

        return view('system::masterdata.negara.index', $data);
    }

    public function store(Request $request)
    {
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
        $data = Master_Negara::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
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
