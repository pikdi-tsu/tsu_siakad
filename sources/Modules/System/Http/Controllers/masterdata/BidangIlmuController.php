<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_BidangIlmu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class BidangIlmuController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Master Bidang Ilmu";
        $data['menu']  = "Bidang Ilmu";

        if ($request->ajax()) {
            $query = Master_BidangIlmu::query()->orderBy('kode_bidang_ilmu', 'asc');

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

        return view('system::masterdata.bidangIlmu.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_bidang_ilmu' => 'required|max:20|unique:siakad_master_bidang_ilmu,kode_bidang_ilmu,' . $request->id,
            'nama_bidang_ilmu' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        Master_BidangIlmu::updateOrCreate(
            ['id' => $request->id],
            [
                'kode_bidang_ilmu' => $request->kode_bidang_ilmu,
                'nama_bidang_ilmu' => $request->nama_bidang_ilmu,
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan!']);
    }

    public function edit($id)
    {
        $data = Master_BidangIlmu::find($id);
        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_BidangIlmu::find($id);
        if ($data) {
            $data->delete();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
}
