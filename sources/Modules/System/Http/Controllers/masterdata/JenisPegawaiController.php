<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_JenisPegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class JenisPegawaiController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = 'Master Jenis Pegawai';
        $data['menu']  = 'Jenis Pegawai';

        if ($request->ajax()) {
            $query = Master_JenisPegawai::query()->orderBy('nama_jenis_pegawai', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn  = '<button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-sm btn_edit" title="Edit">';
                    $btn .= '<i class="fas fa-pencil-alt"></i></button> ';
                    $btn .= '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-sm btn_hapus" title="Hapus">';
                    $btn .= '<i class="fas fa-trash"></i></button>';

                    return '<div class="text-center">' . $btn . '</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('system::masterdata.jenisPegawai.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_jenis_pegawai' => 'required|string|max:20|unique:siakad_master_jenis_pegawai,kode_jenis_pegawai,' . $request->id,
            'nama_jenis_pegawai' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_JenisPegawai::updateOrCreate(
            ['id' => $request->id],
            [
                'kode_jenis_pegawai' => $request->kode_jenis_pegawai,
                'nama_jenis_pegawai' => $request->nama_jenis_pegawai,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Data Jenis Pegawai berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $data = Master_JenisPegawai::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_JenisPegawai::find($id);

        if ($data) {
            $data->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil dihapus!'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal menghapus data'
        ]);
    }
}
