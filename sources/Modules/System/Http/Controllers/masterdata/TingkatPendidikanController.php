<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_TingkatPendidikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TingkatPendidikanController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = 'Master Tingkat Pendidikan';
        $data['menu']  = 'Tingkat Pendidikan';

        if ($request->ajax()) {
            $query = Master_TingkatPendidikan::query()->orderBy('urutan_jenjang_pendidikan', 'asc');

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

        return view('system::masterdata.tingkat_pendidikan.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenjang' => 'required|string|max:10',
            'nama_jenjang_pendidikan' => 'required|string|max:100',
            'nama_jenjang_pendidikan_en' => 'nullable|string|max:100',
            'urutan_jenjang_pendidikan' => 'required|numeric',
            'perguruan_tinggi' => 'required|boolean',
            'pasca_sarjana' => 'required|boolean',
            'jenjang_rpl' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }

        Master_TingkatPendidikan::updateOrCreate(
            ['id' => $request->id],
            [
                'jenjang' => $request->jenjang,
                'nama_jenjang_pendidikan' => $request->nama_jenjang_pendidikan,
                'nama_jenjang_pendidikan_en' => $request->nama_jenjang_pendidikan_en,
                'urutan_jenjang_pendidikan' => $request->urutan_jenjang_pendidikan,
                'perguruan_tinggi' => $request->perguruan_tinggi,
                'pasca_sarjana' => $request->pasca_sarjana,
                'jenjang_rpl' => $request->jenjang_rpl,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Data Tingkat Pendidikan berhasil disimpan!'
        ]);
    }

    public function edit($id)
    {
        $data = Master_TingkatPendidikan::find($id);

        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_TingkatPendidikan::find($id);

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
