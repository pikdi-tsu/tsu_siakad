<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_Instansi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class InstansiController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Master Instansi";
        $data['menu']  = "Instansi";

        if ($request->ajax()) {
            $query = Master_Instansi::query()
                ->orderBy('nama_instansi', 'asc');

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

        return view('system::masterdata.instansi.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'no'            => 'required|string|max:50|unique:siakad_master_instansi,no,' . $request->id,
            'nama_instansi' => 'required|string|max:200',
            'alamat'        => 'nullable|string|max:255',
            'no_telepon'    => 'nullable|string|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_Instansi::updateOrCreate(
            ['id' => $request->id],
            [
                'no'            => $request->no,
                'nama_instansi' => $request->nama_instansi,
                'alamat'        => $request->alamat,
                'no_telepon'    => $request->no_telepon,
            ]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Instansi berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $data = Master_Instansi::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_Instansi::find($id);

        if ($data) {
            $data->delete();
            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil dihapus!'
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Gagal menghapus data'
        ]);
    }
}
