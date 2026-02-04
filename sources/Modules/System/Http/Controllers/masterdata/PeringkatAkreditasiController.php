<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_PeringkatAkreditasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PeringkatAkreditasiController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Master Peringkat Akreditasi";
        $data['menu']  = "Peringkat Akreditasi";

        if ($request->ajax()) {
            return DataTables::of(Master_PeringkatAkreditasi::query())
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

        return view('system::masterdata.peringkatAkreditasi.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'peringkat_akreditasi' =>
            'required|string|max:50|unique:siakad_master_peringkat_akreditasi,peringkat_akreditasi,' . $request->id,
            'isactive' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_PeringkatAkreditasi::updateOrCreate(
            ['id' => $request->id],
            [
                'peringkat_akreditasi' => $request->peringkat_akreditasi,
                'isactive'             => $request->isactive,
            ]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Peringkat Akreditasi berhasil disimpan'
        ]);
    }

    public function edit($id)
    {
        $data = Master_PeringkatAkreditasi::find($id);

        return response()->json([
            'status' => $data ? 'success' : 'error',
            'data'   => $data
        ]);
    }

    public function destroy($id)
    {
        Master_PeringkatAkreditasi::destroy($id);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data berhasil dihapus'
        ]);
    }
}
