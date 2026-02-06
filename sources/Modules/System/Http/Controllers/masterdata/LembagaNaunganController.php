<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_LembagaNaungan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class LembagaNaunganController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Master Lembaga Naungan";
        $data['menu']  = "Lembaga Naungan";

        if ($request->ajax()) {
            return DataTables::of(Master_LembagaNaungan::query())
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

        return view('system::masterdata.lembagaNaungan.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_lembaga' => 'required|string|max:100|unique:siakad_master_lembaga_naungan,nama_lembaga,' . $request->id,
            'isactive'     => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_LembagaNaungan::updateOrCreate(
            ['id' => $request->id],
            [
                'nama_lembaga' => $request->nama_lembaga,
                'isactive'     => $request->isactive,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Data Lembaga Naungan berhasil disimpan'
        ]);
    }

    public function edit($id)
    {
        $data = Master_LembagaNaungan::find($id);

        return response()->json([
            'status' => $data ? 'success' : 'error',
            'data'   => $data
        ]);
    }

    public function destroy($id)
    {
        Master_LembagaNaungan::destroy($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus'
        ]);
    }
}
