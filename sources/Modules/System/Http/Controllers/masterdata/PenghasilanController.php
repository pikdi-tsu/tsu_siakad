<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_Penghasilan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PenghasilanController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Master Penghasilan";
        $data['menu']  = "Penghasilan";

        if ($request->ajax()) {
            // Urutkan berdasarkan Kode (Ascending)
            $query = Master_Penghasilan::query()->orderBy('kode_penghasilan', 'asc');

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

        return view('system::masterdata.penghasilan.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_penghasilan' => 'required|max:5|unique:siakad_master_penghasilan,kode_penghasilan,' . $request->id,
            'nama_penghasilan' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        Master_Penghasilan::updateOrCreate(
            ['id' => $request->id],
            [
                'kode_penghasilan' => $request->kode_penghasilan,
                'nama_penghasilan' => $request->nama_penghasilan,
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'Data Master_Penghasilan berhasil disimpan!']);
    }

    public function edit($id)
    {
        $data = Master_Penghasilan::find($id);
        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_Penghasilan::find($id);
        if ($data) {
            $data->delete();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
}
