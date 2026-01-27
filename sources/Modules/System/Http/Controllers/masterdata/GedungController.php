<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_Gedung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class GedungController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = 'Master Gedung';
        $data['menu']  = 'Gedung';

        if ($request->ajax()) {
            $query = Master_Gedung::query()->orderBy('nama_gedung', 'asc');

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

        return view('system::masterdata.gedung.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_gedung' => 'required|string|max:20|unique:siakad_master_gedung,kode_gedung,' . $request->id,
            'nama_gedung' => 'required|string|max:100',
            'lokasi_kampus' => 'required|string|max:100',
            'telepon' => 'nullable|string|max:30',
            'jml_lantai' => 'required|numeric|min:0',
            'jml_ruang' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_Gedung::updateOrCreate(
            ['id' => $request->id],
            [
                'kode_gedung' => $request->kode_gedung,
                'nama_gedung' => $request->nama_gedung,
                'lokasi_kampus' => $request->lokasi_kampus,
                'telepon' => $request->telepon,
                'jml_lantai' => $request->jml_lantai,
                'jml_ruang' => $request->jml_ruang,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Data Gedung berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $data = Master_Gedung::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_Gedung::find($id);

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
