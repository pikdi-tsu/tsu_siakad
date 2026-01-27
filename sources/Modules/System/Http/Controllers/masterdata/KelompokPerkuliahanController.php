<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_KelompokPerkuliahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class KelompokPerkuliahanController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = 'Master Kelompok Perkuliahan';
        $data['menu']  = 'Kelompok Perkuliahan';

        if ($request->ajax()) {
            $query = Master_KelompokPerkuliahan::query()->orderBy('urutan', 'asc');

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

        return view('system::masterdata.kelompok_perkuliahan.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kelompok_perkuliahan' => 'required|string|max:150',
            'urutan' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_KelompokPerkuliahan::updateOrCreate(
            ['id' => $request->id],
            [
                'nama_kelompok_perkuliahan' => $request->nama_kelompok_perkuliahan,
                'urutan' => $request->urutan,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Data Kelompok Perkuliahan berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $data = Master_KelompokPerkuliahan::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_KelompokPerkuliahan::find($id);

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
