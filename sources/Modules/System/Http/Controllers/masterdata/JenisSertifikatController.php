<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_JenisSertifikat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class JenisSertifikatController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = 'Master Jenis Sertifikat';
        $data['menu']  = 'Jenis Sertifikat';

        if ($request->ajax()) {
            $query = Master_JenisSertifikat::query()->orderBy('nama_jenis_sertifikat', 'asc');

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

        return view('system::masterdata.jenis_sertifikat.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_jenis_sertifikat' => 'required|string|max:150',
            'ukom' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_JenisSertifikat::updateOrCreate(
            ['id' => $request->id],
            [
                'nama_jenis_sertifikat' => $request->nama_jenis_sertifikat,
                'ukom' => $request->ukom,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Data Jenis Sertifikat berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $data = Master_JenisSertifikat::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_JenisSertifikat::find($id);

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
