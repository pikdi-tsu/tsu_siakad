<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_DataPerguruanTinggi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class DataPerguruanTinggiController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Master Data Perguruan Tinggi";
        $data['menu']  = "Perguruan Tinggi";

        if ($request->ajax()) {
            $query = Master_DataPerguruanTinggi::query()
                ->orderBy('nama_unit', 'asc');

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

        return view('system::masterdata.dataPerguruanTinggi.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_unit'   => 'required|string|max:50|unique:siakad_master_data_perguruan_tinggi,kode_unit,' . $request->id,
            'nama_unit'   => 'required|string|max:200',
            'nama_singkat' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_DataPerguruanTinggi::updateOrCreate(
            ['id' => $request->id],
            $request->only([
                'kode_unit',
                'nama_unit',
                'nama_unit_en',
                'nama_singkat',
                'jenis_perguruan_tinggi',
                'lembaga_naungan',
                'unit_satuan_kerja',
                'periode_berdiri',
                'no_sk_pendirian',
                'tanggal_sk_pendirian',
                'rektor',
                'wakil_rektor1',
                'wakil_rektor2',
                'wakil_rektor3',
                'wakil_rektor4',
                'lembaga_akreditasi',
                'peringkat_akreditasi',
                'nilai_akreditasi',
                'no_sk_akreditasi',
                'tanggal_sk_akreditasi',
                'tanggal_berlaku_akreditasi',
                'tanggal_berakhir_akreditasi',
                'file_sertifikat_akreditasi',
                'visi',
                'misi',
                'alamat',
                'telepon',
                'alamat_email',
                'alamat_website',
                'fax'
            ])

        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Perguruan Tinggi berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $data = Master_DataPerguruanTinggi::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_DataPerguruanTinggi::find($id);

        if ($data) {
            $data->delete();
            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil dihapus!'
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Gagal menghapus data'
        ]);
    }
}
