<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_Pekerjaan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PekerjaanController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Master Pekerjaan";
        $data['menu']  = "Pekerjaan";

        if ($request->ajax()) {
            // Urutkan berdasarkan Kode (Ascending)
            $query = Master_Pekerjaan::query()->orderBy('kode_pekerjaan', 'asc');

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

        return view('system::masterdata.pekerjaan.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_pekerjaan' => 'required|max:5|unique:siakad_master_pekerjaan,kode_pekerjaan,' . $request->id,
            'nama_pekerjaan' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        Master_Pekerjaan::updateOrCreate(
            ['id' => $request->id],
            [
                'kode_pekerjaan' => $request->kode_pekerjaan,
                'nama_pekerjaan' => $request->nama_pekerjaan,
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'Data Master_Pekerjaan berhasil disimpan!']);
    }

    public function edit($id)
    {
        $data = Master_Pekerjaan::find($id);
        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_Pekerjaan::find($id);
        if ($data) {
            $data->delete();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
}
