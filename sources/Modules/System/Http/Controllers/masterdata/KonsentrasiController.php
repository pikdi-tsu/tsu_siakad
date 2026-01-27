<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_Konsentrasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class KonsentrasiController extends Controller
{
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
                    $btn  = '<button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-sm btn_edit" title="Edit">';
                    $btn .= '<i class="fas fa-pencil-alt"></i></button> ';
                    $btn .= '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-sm btn_hapus" title="Hapus">';
                    $btn .= '<i class="fas fa-trash"></i></button>';

                    return '<div class="text-center">' . $btn . '</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('system::masterdata.konsentrasi.index', $data);
    }

    public function store(Request $request)
    {
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
        $data = Master_Konsentrasi::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
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
