<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_StatusKeaktifan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class StatusKeaktifanController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = 'Master Status Keaktifan';
        $data['menu']  = 'Status Keaktifan';

        if ($request->ajax()) {
            $query = Master_StatusKeaktifan::query()->orderBy('nama_status_keaktifan', 'asc');

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

        return view('system::masterdata.status_keaktifan.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_status_keaktifan' => 'required|string|max:150',
            'status_keluar' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_StatusKeaktifan::updateOrCreate(
            ['id' => $request->id],
            [
                'nama_status_keaktifan' => $request->nama_status_keaktifan,
                'status_keluar' => $request->status_keluar,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Data Status Keaktifan berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $data = Master_StatusKeaktifan::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_StatusKeaktifan::find($id);

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
