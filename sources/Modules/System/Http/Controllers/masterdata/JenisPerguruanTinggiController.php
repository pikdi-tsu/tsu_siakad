<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_JenisPerguruanTinggi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class JenisPerguruanTinggiController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Master Jenis Perguruan Tinggi";
        $data['menu']  = "Jenis Perguruan Tinggi";

        if ($request->ajax()) {
            return DataTables::of(Master_JenisPerguruanTinggi::query())
                ->addIndexColumn()
                ->editColumn('isactive', function ($row) {
                    return $row->isactive
                        ? '<span class="badge badge-success">Aktif</span>'
                        : '<span class="badge badge-danger">Tidak Aktif</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="text-center">
                            <button class="btn btn-warning btn-sm btn_edit" data-id="' . $row->id . '">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <button class="btn btn-danger btn-sm btn_hapus" data-id="' . $row->id . '">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>';
                })
                ->rawColumns(['isactive', 'action'])
                ->make(true);
        }

        return view('system::masterdata.jenisPerguruanTinggi.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_pt' => 'required|string|max:100|unique:siakad_master_jenis_perguruan_tinggi,jenis_pt,' . $request->id,
            'isactive' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_JenisPerguruanTinggi::updateOrCreate(
            ['id' => $request->id],
            [
                'jenis_pt' => $request->jenis_pt,
                'isactive' => $request->isactive,
            ]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Jenis Perguruan Tinggi berhasil disimpan'
        ]);
    }

    public function edit($id)
    {
        $data = Master_JenisPerguruanTinggi::find($id);

        return response()->json([
            'status' => $data ? 'success' : 'error',
            'data'   => $data
        ]);
    }

    public function destroy($id)
    {
        Master_JenisPerguruanTinggi::destroy($id);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data berhasil dihapus'
        ]);
    }
}
