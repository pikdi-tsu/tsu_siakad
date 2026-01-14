<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_JenisKegiatanPendukung;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class JenisKegiatanPendukungController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Master Jenis Kegiatan Pendukung";
        $data['menu']  = "Jenis Kegiatan Pendukung";

        if ($request->ajax()) {
            $query = Master_JenisKegiatanPendukung::query()->orderBy('kode_kegiatan', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    // LOGIC PENGAMAN: Cek awalan 'A' atau flag is_system
                    // Sesuai screenshot, data 'A' tidak punya tombol aksi
                    if (Str::startsWith($row->kode_kegiatan, 'A') || $row->is_system) {
                        return '<span class="badge badge-secondary"><i class="fas fa-lock"></i> System</span>';
                    }

                    $btn = '<button type="button" data-id="'.$row->id.'" class="btn btn-warning btn-sm btn_edit" title="Edit"><i class="fas fa-pencil-alt"></i></button>';
                    $btn .= ' <button type="button" data-id="'.$row->id.'" class="btn btn-danger btn-sm btn_hapus" title="Hapus"><i class="fas fa-trash"></i></button>';
                    return '<div class="text-center">'.$btn.'</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('system::masterdata.jenisKegiatanPendukung.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_kegiatan' => 'required|max:10|unique:siakad_master_jenis_kegiatan_pendukung,kode_kegiatan,' . $request->id,
            'nama_kegiatan' => 'required|string|max:255',
        ]);

        // LOGIC VALIDASI INPUT: User tidak boleh input kode awalan 'A'
        // Kecuali update data yang memang sudah ada (walaupun di UI di-hide)
        $validator->after(function ($validator) use ($request) {
            if (!$request->id && Str::startsWith(strtoupper($request->kode_kegiatan), 'A')) {
                $validator->errors()->add('kode_kegiatan', 'Kode dengan awalan A dilindungi oleh sistem.');
            }
        });

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        Master_JenisKegiatanPendukung::updateOrCreate(
            ['id' => $request->id],
            [
                'kode_kegiatan' => strtoupper($request->kode_kegiatan), // Paksa Uppercase
                'nama_kegiatan' => $request->nama_kegiatan,
                'is_system'     => 0, // Data user selalu 0
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan!']);
    }

    public function edit($id)
    {
        $data = Master_JenisKegiatanPendukung::find($id);

        // Proteksi Tambahan: Jangan kasih edit kalau system
        if($data && Str::startsWith($data->kode_kegiatan, 'A')) {
            return response()->json(['status' => 'error', 'message' => 'Data Sistem tidak dapat diedit.']);
        }

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_JenisKegiatanPendukung::find($id);

        // Proteksi Tambahan
        if($data && Str::startsWith($data->kode_kegiatan, 'A')) {
            return response()->json(['status' => 'error', 'message' => 'Data Sistem tidak dapat dihapus.']);
        }

        if ($data) {
            $data->delete();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
}
