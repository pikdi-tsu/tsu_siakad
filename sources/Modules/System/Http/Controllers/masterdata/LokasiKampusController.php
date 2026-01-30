<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_LokasiKampus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class LokasiKampusController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = 'Master Lokasi Kampus';
        $data['menu']  = 'Lokasi Kampus';

        if ($request->ajax()) {
            $query = Master_LokasiKampus::query()->orderBy('nama', 'asc');

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

        return view('system::masterdata.lokasiKampus.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode' => 'required|string|max:20|unique:siakad_master_lokasi_kampus,kode,' . $request->id,
            'nama' => 'required|string|max:200',
            'alamat' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_LokasiKampus::updateOrCreate(
            ['id' => $request->id],
            [
                'kode' => $request->kode,
                'nama' => $request->nama,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Data Lokasi Kampus berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $data = Master_LokasiKampus::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_LokasiKampus::find($id);

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
