<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Models\MasterData\Master_RuangKuliah;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class RuangKuliahController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Master Ruang Kuliah";
        $data['menu']  = "Ruang Kuliah";

        // Filter Unit (Hardcode dulu atau ambil dari Master Fakultas/Unit)
        // Nanti bisa diganti: MasterFakultas::all();
        $data['list_unit'] = ['Universitas Tiga Serangkai', 'Fakultas Teknik', 'Fakultas Ekonomi'];

        if ($request->ajax()) {
            $query = Master_RuangKuliah::query();

            // Logic Filter Unit dari Dropdown
            if ($request->has('filter_unit') && !empty($request->filter_unit)) {
                $query->where('unit', $request->filter_unit);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('is_active', function ($row) {
                    // Icon Centang Hijau ala SEVIMA
                    return $row->is_active
                        ? '<div class="text-center text-success"><i class="fas fa-check-circle"></i></div>'
                        : '<div class="text-center text-secondary"><i class="fas fa-times-circle"></i></div>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button type="button" data-id="'.$row->id.'" class="btn btn-warning btn-sm btn_edit" title="Edit"><i class="fas fa-pencil-alt"></i></button>';
                    $btn .= ' <button type="button" data-id="'.$row->id.'" class="btn btn-danger btn-sm btn_hapus" title="Hapus"><i class="fas fa-trash"></i></button>';
                    return '<div class="text-center">'.$btn.'</div>';
                })
                ->rawColumns(['is_active', 'action'])
                ->make(true);
        }

        return view('system::masterdata.ruangKuliah.index', $data);
    }

    public function store(Request $request)
    {
        // Validasi
        $validator = Validator::make($request->all(), [
            'kode_ruang' => 'required|max:50|unique:siakad_master_ruang_kuliah,kode_ruang,' . $request->id, // Ignore unique kalau update
            'nama_ruang' => 'required|string|max:255',
            'unit'       => 'required',
            'kapasitas'  => 'required|numeric|min:0',
            'is_active'  => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        Master_RuangKuliah::updateOrCreate(
            ['id' => $request->id],
            [
                'kode_ruang' => $request->kode_ruang,
                'nama_ruang' => $request->nama_ruang,
                'unit'       => $request->unit,
                'lokasi'     => $request->lokasi,
                'kapasitas'  => $request->kapasitas,
                'is_active'  => $request->is_active,
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'Data Ruang Kuliah berhasil disimpan!']);
    }

    public function edit($id)
    {
        $data = Master_RuangKuliah::find($id);
        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_RuangKuliah::find($id);
        if ($data) {
            $data->delete();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
}
