<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_UnsurNilai;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class UnsurNilaiController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Master Unsur Nilai";
        $data['menu']  = "Unsur Nilai";

        if ($request->ajax()) {
            $query = Master_UnsurNilai::query()->orderBy('kode_unsur', 'asc');

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

        return view('system::masterdata.unsurNilai.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_unsur' => 'required|max:20|unique:siakad_master_unsur_nilai,kode_unsur,' . $request->id,
            'nama_unsur' => 'required|string|max:255',
            'nama_singkat' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        Master_UnsurNilai::updateOrCreate(
            ['id' => $request->id],
            [
                'kode_unsur'      => $request->kode_unsur,
                'nama_unsur'      => $request->nama_unsur,
                'nama_singkat'    => $request->nama_singkat,
                'kelompok_unsur'  => $request->kelompok_unsur,
                'metode_evaluasi' => $request->metode_evaluasi,
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan!']);
    }

    public function edit($id)
    {
        $data = Master_UnsurNilai::find($id);
        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_UnsurNilai::find($id);
        if ($data) {
            $data->delete();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
}
