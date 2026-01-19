<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_KegiatanAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class KegiatanAkademikController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Master Kegiatan Akademik";
        $data['menu']  = "Kegiatan Akademik";

        if ($request->ajax()) {
            $query = Master_KegiatanAkademik::query()->orderBy('kode_kegiatan', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('warna_background', function ($row) {
                    // Render Visual Warna di Tabel (Persis Screenshot)
                    if($row->warna_background) {
                        return '<span class="badge p-2" style="background-color: '.$row->warna_background.'; color: #fff; border: 1px solid #ddd;">'.$row->warna_background.'</span>';
                    }
                    return '-';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button type="button" data-id="'.$row->id.'" class="btn btn-warning btn-sm btn_edit" title="Edit"><i class="fas fa-pencil-alt"></i></button>';
                    $btn .= ' <button type="button" data-id="'.$row->id.'" class="btn btn-danger btn-sm btn_hapus" title="Hapus"><i class="fas fa-trash"></i></button>';
                    return '<div class="text-center">'.$btn.'</div>';
                })
                ->rawColumns(['warna_background', 'action'])
                ->make(true);
        }

        return view('system::masterdata.kegiatanAkademik.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_kegiatan'    => 'required|max:20|unique:siakad_master_kegiatan_akademik,kode_kegiatan,' . $request->id,
            'nama_kegiatan'    => 'required|string|max:255',
            'warna_background' => 'nullable|string|max:7', // Validasi panjang Hex Color
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        Master_KegiatanAkademik::updateOrCreate(
            ['id' => $request->id],
            [
                'kode_kegiatan'    => $request->kode_kegiatan,
                'nama_kegiatan'    => $request->nama_kegiatan,
                'warna_background' => $request->warna_background ?? '#ffffff', // Default putih jika kosong
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'Data Kegiatan Akademik berhasil disimpan!']);
    }

    public function edit($id)
    {
        $data = Master_KegiatanAkademik::find($id);
        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_KegiatanAkademik::find($id);
        if ($data) {
            $data->delete();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
}
