<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_JabatanFungsional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class JabatanFungsionalController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = 'Master Jabatan Fungsional';
        $data['menu']  = 'Jabatan Fungsional';

        if ($request->ajax()) {
            $query = Master_JabatanFungsional::query()->orderBy('nama_jabatan_fungsional', 'asc');

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

        return view('system::masterdata.jabatanFungsional.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_jabatan_fungsional' => 'required|string|max:20|unique:siakad_master_jabatan_fungsional,kode_jabatan_fungsional,' . $request->id,
            'nama_jabatan_fungsional' => 'required|string|max:150',
            'sks_maksimal' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_JabatanFungsional::updateOrCreate(
            ['id' => $request->id],
            [
                'kode_jabatan_fungsional' => $request->kode_jabatan_fungsional,
                'nama_jabatan_fungsional' => $request->nama_jabatan_fungsional,
                'sks_maksimal' => $request->sks_maksimal,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Data Jabatan Fungsional berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $data = Master_JabatanFungsional::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_JabatanFungsional::find($id);

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
