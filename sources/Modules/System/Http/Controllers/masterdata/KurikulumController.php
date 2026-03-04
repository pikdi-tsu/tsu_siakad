<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_Kurikulum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class KurikulumController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Master Kurikulum";
        $data['menu']  = "Kurikulum";

        if ($request->ajax()) {
            $query = Master_Kurikulum::query()->orderBy('nama_kurikulum', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('isactive', function ($row) {
                    return $row->isactive
                        ? '<span class="badge bg-success">Aktif</span>'
                        : '<span class="badge bg-danger">Nonaktif</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-sm btn_edit" title="Edit"><i class="fas fa-pencil-alt"></i></button>';
                    $btn .= ' <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-sm btn_hapus" title="Hapus"><i class="fas fa-trash"></i></button>';
                    return '<div class="text-center">' . $btn . '</div>';
                })
                ->rawColumns(['action', 'isactive'])
                ->make(true);
        }

        return view('system::masterdata.kurikulum.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kurikulum' => 'required|string|max:150',
            'deskripsi'      => 'nullable|string',
            'isactive'       => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_Kurikulum::updateOrCreate(
            ['id' => $request->id],
            [
                'nama_kurikulum' => $request->nama_kurikulum,
                'deskripsi'      => $request->deskripsi,
                'isactive'       => $request->isactive,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Data Kurikulum berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $data = Master_Kurikulum::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_Kurikulum::find($id);

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
