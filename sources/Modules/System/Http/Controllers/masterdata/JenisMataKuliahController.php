<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_JenisMataKuliah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class JenisMataKuliahController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Master Jenis Mata Kuliah";
        $data['menu']  = "Jenis Mata Kuliah";

        if ($request->ajax()) {
            $query = Master_JenisMataKuliah::query()->orderBy('kode_jenis', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<button type="button" data-id="'.$row->id.'" class="btn btn-warning btn-sm btn_edit" title="Edit"><i class="fas fa-pencil-alt"></i></button>';
                    $btn .= ' <button type="button" data-id="'.$row->id.'" class="btn btn-danger btn-sm btn_hapus" title="Hapus"><i class="fas fa-trash"></i></button>';
                    return '<div class="text-center">'.$btn.'</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('system::masterdata.jenisMataKuliah.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_jenis' => 'required|max:10|unique:siakad_master_jenis_matakuliah,kode_jenis,' . $request->id,
            'nama_jenis' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        Master_JenisMataKuliah::updateOrCreate(
            ['id' => $request->id],
            [
                'kode_jenis' => $request->kode_jenis,
                'nama_jenis' => $request->nama_jenis,
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan!']);
    }

    public function edit($id)
    {
        $data = Master_JenisMataKuliah::find($id);
        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_JenisMataKuliah::find($id);
        if ($data) {
            $data->delete();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
}
