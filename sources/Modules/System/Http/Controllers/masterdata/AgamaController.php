<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_Agama;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AgamaController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Master Agama";
        $data['menu']  = "Agama";

        if ($request->ajax()) {
            // Urutkan berdasarkan Kode Agama (Biar urut 1, 2, 3)
            $query = Master_Agama::query()->orderBy('kode_agama', 'asc');

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

        return view('system::masterdata.agama.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_agama' => 'required|max:5|unique:siakad_master_agama,kode_agama,' . $request->id,
            'nama_agama' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        Master_Agama::updateOrCreate(
            ['id' => $request->id],
            [
                'kode_agama' => $request->kode_agama,
                'nama_agama' => $request->nama_agama,
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'Data Master_Agama berhasil disimpan!']);
    }

    public function edit($id)
    {
        $data = Master_Agama::find($id);
        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_Agama::find($id);
        if ($data) {
            $data->delete();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
}
