<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_PenyelenggaraSertifikat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PenyelenggaraSertifikatController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = 'Master Penyelenggara Sertifikat';
        $data['menu']  = 'Penyelenggara Sertifikat';

        if ($request->ajax()) {
            $query = Master_PenyelenggaraSertifikat::query()->orderBy('nama_penyelenggara_sertifikat', 'asc');

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

        return view('system::masterdata.penyelenggaraSertifikat.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_penyelenggara_sertifikat' => 'required|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_PenyelenggaraSertifikat::updateOrCreate(
            ['id' => $request->id],
            ['nama_penyelenggara_sertifikat' => $request->nama_penyelenggara_sertifikat]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Data Penyelenggara Sertifikat berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $data = Master_PenyelenggaraSertifikat::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_PenyelenggaraSertifikat::find($id);

        if ($data) {
            $data->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil dihapus!'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal menghapus data'
        ]);
    }
}
