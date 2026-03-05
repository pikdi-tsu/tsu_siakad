<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_JenisPertemuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class JenisPertemuanController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Master Jenis Pertemuan";
        $data['menu']  = "Jenis Pertemuan";

        if ($request->ajax()) {
            $query = Master_JenisPertemuan::query()->orderBy('kode_jenis', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                // Render Icon Centang/Silang
                ->editColumn('is_hitung_presensi', function ($row) {
                    return $this->renderBooleanIcon($row->is_hitung_presensi);
                })
                ->editColumn('is_ujian', function ($row) {
                    return $this->renderBooleanIcon($row->is_ujian);
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-sm btn_edit" title="Edit"><i class="fas fa-pencil-alt"></i></button>';
                    $btn .= ' <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-sm btn_hapus" title="Hapus"><i class="fas fa-trash"></i></button>';
                    return '<div class="text-center">' . $btn . '</div>';
                })
                ->rawColumns(['is_hitung_presensi', 'is_ujian', 'action'])
                ->make(true);
        }

        return view('system::masterdata.jenisPertemuan.index', $data);
    }

    private function renderBooleanIcon($value)
    {
        if ($value) {
            return '<div class="text-center text-success"><i class="fas fa-check"></i></div>';
        }
        return '<div class="text-center text-danger"><i class="fas fa-times"></i></div>';
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_jenis' => 'required|max:10|unique:siakad_master_jenis_pertemuan,kode_jenis,' . $request->id,
            'nama_jenis' => 'required|string|max:255',
            'nama_singkat' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        Master_JenisPertemuan::updateOrCreate(
            ['id' => $request->id],
            [
                'kode_jenis'     => $request->kode_jenis,
                'nama_jenis'     => $request->nama_jenis,
                'nama_singkat'   => $request->nama_singkat,
                'kelompok_jenis' => $request->kelompok_jenis,
                // Handle Checkbox
                'is_hitung_presensi' => $request->boolean('is_hitung_presensi'),
                'is_ujian'           => $request->boolean('is_ujian'),
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan!']);
    }

    public function edit($id)
    {
        $data = Master_JenisPertemuan::find($id);
        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_JenisPertemuan::find($id);
        if ($data) {
            $data->delete();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
}
