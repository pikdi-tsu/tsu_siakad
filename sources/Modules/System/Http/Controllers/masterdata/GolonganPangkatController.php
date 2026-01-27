<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_GolonganPangkat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class GolonganPangkatController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = 'Master Golongan Pangkat';
        $data['menu']  = 'Golongan Pangkat';

        if ($request->ajax()) {
            $query = Master_GolonganPangkat::query()->orderBy('nama_golongan_pangkat', 'asc');

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

        return view('system::masterdata.golongan_pangkat.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_golongan_pangkat' => 'required|string|max:20|unique:siakad_master_golongan_pangkat,kode_golongan_pangkat,' . $request->id,
            'nama_golongan_pangkat' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_GolonganPangkat::updateOrCreate(
            ['id' => $request->id],
            [
                'kode_golongan_pangkat' => $request->kode_golongan_pangkat,
                'nama_golongan_pangkat' => $request->nama_golongan_pangkat,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Data Golongan Pangkat berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $data = Master_GolonganPangkat::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_GolonganPangkat::find($id);

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
