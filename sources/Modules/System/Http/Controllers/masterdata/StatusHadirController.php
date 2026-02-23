<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_StatusHadir;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class StatusHadirController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Master Status Hadir";
        $data['menu']  = "Status Hadir";

        if ($request->ajax()) {
            $query = Master_StatusHadir::query()->orderBy('kode_status', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                // Render Icon Boolean
                ->editColumn('is_hitung_hadir', function ($row) {
                    return $this->renderBooleanIcon($row->is_hitung_hadir);
                })
                ->editColumn('is_untuk_dosen', function ($row) {
                    return $this->renderBooleanIcon($row->is_untuk_dosen);
                })
                ->editColumn('is_untuk_mahasiswa', function ($row) {
                    return $this->renderBooleanIcon($row->is_untuk_mahasiswa);
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-sm btn_edit" title="Edit"><i class="fas fa-pencil-alt"></i></button>';
                    $btn .= ' <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-sm btn_hapus" title="Hapus"><i class="fas fa-trash"></i></button>';
                    return '<div class="text-center">' . $btn . '</div>';
                })
                ->rawColumns(['is_hitung_hadir', 'is_untuk_dosen', 'is_untuk_mahasiswa', 'action'])
                ->make(true);
        }

        return view('system::masterdata.statusHadir.index', $data);
    }

    // Helper render icon biar kodingan rapi
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
            'kode_status' => 'required|max:10|unique:siakad_master_status_hadir,kode_status,' . $request->id,
            'nama_status' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        Master_StatusHadir::updateOrCreate(
            ['id' => $request->id],
            [
                'kode_status'        => $request->kode_status,
                'nama_status'        => $request->nama_status,
                // Handle Checkbox (dikirim string "on" atau null)
                // 'is_hitung_hadir'    => $request->has('is_hitung_hadir') ? 1 : 0,
                // 'is_untuk_dosen'     => $request->has('is_untuk_dosen') ? 1 : 0,
                // 'is_untuk_mahasiswa' => $request->has('is_untuk_mahasiswa') ? 1 : 0,
                'is_hitung_hadir'    => $request->boolean('is_hitung_hadir'),
                'is_untuk_dosen'     => $request->boolean('is_untuk_dosen'),
                'is_untuk_mahasiswa' => $request->boolean('is_untuk_mahasiswa'),

            ]
        );

        return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan!']);
    }

    public function edit($id)
    {
        $data = Master_StatusHadir::find($id);
        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_StatusHadir::find($id);
        if ($data) {
            $data->delete();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
}
