<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_WaktuKuliah;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class SistemKuliahController extends Controller
{
    public function index(Request $request)
    {
        // JUDUL HALAMAN
        $data['title'] = "Master Sistem Kuliah";
        $data['menu']  = "Sistem Kuliah";

        // JIKA AJAX (Load DataTables)
        if ($request->ajax()) {
            $query = Master_WaktuKuliah::query()->orderBy('id', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('isactive', function ($row) {
                    // Badge Status biar cantik
                    return $row->isactive == 1
                        ? '<span class="badge badge-success">Aktif</span>'
                        : '<span class="badge badge-danger">Tidak Aktif</span>';
                })
                ->addColumn('action', function ($row) {
                    // Tombol Edit & Hapus
                    $btn = '<button type="button" data-id="'.$row->id.'" class="btn btn-warning btn-sm btn_edit" title="Edit"><i class="fas fa-edit"></i></button>';
                    $btn .= ' <button type="button" data-id="'.$row->id.'" class="btn btn-danger btn-sm btn_hapus" title="Hapus"><i class="fas fa-trash"></i></button>';
                    return $btn;
                })
                ->rawColumns(['isactive', 'action']) // Render HTML
                ->make(true);
        }

        // LOAD VIEW
        return view('system::masterdata.sistemKuliah.index', $data);
    }

    public function store(Request $request)
    {
        // VALIDASI
        $validator = Validator::make($request->all(), [
            'waktu' => 'required|string|max:255',
            'isactive' => 'required|in:1,0',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        // LOGIC UPDATE / CREATE (Single Route)
        // Cek apakah ada ID (Edit) atau Kosong (Create)
        $sistem = Master_WaktuKuliah::updateOrCreate(
            ['id' => $request->id], // Kunci pencarian (Hidden Input)
            [
                'waktu'    => $request->waktu,
                'isactive' => $request->isactive
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Data Sistem Kuliah berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $data = Master_WaktuKuliah::find($id);
        if ($data) {
            return response()->json(['status' => 'success', 'data' => $data]);
        }
        return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_WaktuKuliah::find($id);
        if ($data) {
            $data->delete();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
}
