<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_JenisModulMataKuliah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class JenisModulMataKuliahController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Master Jenis Modul Mata Kuliah";
        $data['menu']  = "Jenis Modul MK";

        if ($request->ajax()) {
            $query = Master_JenisModulMataKuliah::query()->orderBy('kode_modul', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<button type="button" data-id="'.$row->id.'" class="btn btn-warning btn-sm btn_edit" title="Edit"><i class="fas fa-pencil-alt"></i></button>';
                    // Tombol Hapus disertakan (bisa dihapus jika mau persis screenshot)
                    $btn .= ' <button type="button" data-id="'.$row->id.'" class="btn btn-danger btn-sm btn_hapus" title="Hapus"><i class="fas fa-trash"></i></button>';
                    return '<div class="text-center">'.$btn.'</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('system::masterdata.jenisModulMataKuliah.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_modul' => 'required|max:10|unique:siakad_master_jenis_modul,kode_modul,' . $request->id,
            'nama_modul' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        Master_JenisModulMataKuliah::updateOrCreate(
            ['id' => $request->id],
            [
                'kode_modul' => $request->kode_modul,
                'nama_modul' => $request->nama_modul,
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan!']);
    }

    public function edit($id)
    {
        $data = Master_JenisModulMataKuliah::find($id);
        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_JenisModulMataKuliah::find($id);
        if ($data) {
            $data->delete();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
}
