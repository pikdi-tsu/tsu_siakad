<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_StatusMahasiswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class StatusMahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Master Status Mahasiswa";
        $data['menu']  = "Status Mahasiswa";

        if ($request->ajax()) {
            $query = Master_StatusMahasiswa::query()->orderBy('kode_status', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                // Render 3 Icon Boolean
                ->editColumn('is_pengajuan_mhs', function ($row) {
                    return $this->renderBooleanIcon($row->is_pengajuan_mhs);
                })
                ->editColumn('is_aktif', function ($row) {
                    return $this->renderBooleanIcon($row->is_aktif);
                })
                ->editColumn('is_sks', function ($row) {
                    return $this->renderBooleanIcon($row->is_sks);
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-sm btn_edit" title="Edit"><i class="fas fa-pencil-alt"></i></button>';

                    // Proteksi: Tombol Hapus hilang jika data Sistem (A, C, L, dll)
                    if (!$row->is_system) {
                        $btn .= ' <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-sm btn_hapus" title="Hapus"><i class="fas fa-trash"></i></button>';
                    }

                    return '<div class="text-center">' . $btn . '</div>';
                })
                ->rawColumns(['is_pengajuan_mhs', 'is_aktif', 'is_sks', 'action'])
                ->make(true);
        }

        return view('system::masterdata.statusMahasiswa.index', $data);
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
            'kode_status' => 'required|max:10|unique:siakad_master_status_mahasiswa,kode_status,' . $request->id,
            'nama_status' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        Master_StatusMahasiswa::updateOrCreate(
            ['id' => $request->id],
            [
                'kode_status'      => strtoupper($request->kode_status),
                'nama_status'      => $request->nama_status,
                // Handle Checkbox
                'is_pengajuan_mhs' => $request->boolean('is_pengajuan_mhs'),
                'is_aktif'         => $request->boolean('is_aktif'),
                'is_sks'           => $request->boolean('is_sks'),
                // Default data baru bukan system
                'is_system'        => 0
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan!']);
    }

    public function edit($id)
    {
        $data = Master_StatusMahasiswa::find($id);
        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_StatusMahasiswa::find($id);

        // Double Protection Backend
        if ($data && $data->is_system) {
            return response()->json(['status' => 'error', 'message' => 'Data Sistem tidak dapat dihapus!']);
        }

        if ($data) {
            $data->delete();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
}
