<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_JabatanStruktural;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class JabatanStrukturalController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = 'Master Jabatan Struktural';
        $data['menu']  = 'Jabatan Struktural';

        if ($request->ajax()) {
            $query = Master_JabatanStruktural::query()->orderBy('nama_jabatan_struktural', 'asc');

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

        return view('system::masterdata.jabatan_struktural.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_jabatan_struktural' => 'required|string|max:150',
            'parent_jabatan_struktural' => 'nullable|string|max:150',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_JabatanStruktural::updateOrCreate(
            ['id' => $request->id],
            [
                'nama_jabatan_struktural' => $request->nama_jabatan_struktural,
                'parent_jabatan_struktural' => $request->parent_jabatan_struktural,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Data Jabatan Struktural berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $data = Master_JabatanStruktural::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_JabatanStruktural::find($id);

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
