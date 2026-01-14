<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_Jas_Almamater;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class JasAlmamaterController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Master Jas Almamater";
        $data['menu']  = "Jas Almamater";

        if ($request->ajax()) {
            // Urutkan biar rapi (opsional, sorting ukuran baju agak tricky kalau string)
            // Kita urutkan by created_at atau kode saja sementara
            $query = Master_Jas_Almamater::query()->orderBy('created_at', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<button type="button" data-id="'.$row->id.'" class="btn btn-warning btn-sm btn_edit" title="Edit"><i class="fas fa-pencil-alt"></i></button>';
                    $btn .= ' <button type="button" data-id="'.$row->id.'" class="btn btn-danger btn-sm btn_hapus" title="Hapus"><i class="fas fa-trash"></i></button>';
                    return '<div class="text-center">'.$btn.'</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('system::masterdata.jasAlmamater.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_ukuran' => 'required|max:10|unique:siakad_master_jas_almamater,kode_ukuran,' . $request->id,
            'nama_ukuran' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        Master_Jas_Almamater::updateOrCreate(
            ['id' => $request->id],
            [
                'kode_ukuran' => strtoupper($request->kode_ukuran), // Paksa huruf besar (L, XL)
                'nama_ukuran' => $request->nama_ukuran,
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'Data Ukuran berhasil disimpan!']);
    }

    public function edit($id)
    {
        $data = Master_Jas_Almamater::find($id);
        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_Jas_Almamater::find($id);
        if ($data) {
            $data->delete();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
}
