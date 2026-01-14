<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_KebutuhanKhusus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class KebutuhanKhususController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Master Kebutuhan Khusus";
        $data['menu']  = "Kebutuhan Khusus";

        if ($request->ajax()) {
            // Urutkan berdasarkan Kode (Ascending)
            $query = Master_KebutuhanKhusus::query()->orderBy('kode_kebutuhan', 'asc');

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

        return view('system::masterdata.kebutuhanKhusus.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_kebutuhan' => 'required|max:10|unique:siakad_master_kebutuhan_khusus,kode_kebutuhan,' . $request->id,
            'nama_kebutuhan' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        Master_KebutuhanKhusus::updateOrCreate(
            ['id' => $request->id],
            [
                'kode_kebutuhan' => strtoupper($request->kode_kebutuhan), // Paksa huruf besar (a -> A)
                'nama_kebutuhan' => $request->nama_kebutuhan,
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'Data Kebutuhan Khusus berhasil disimpan!']);
    }

    public function edit($id)
    {
        $data = Master_KebutuhanKhusus::find($id);
        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_KebutuhanKhusus::find($id);
        if ($data) {
            $data->delete();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
}
