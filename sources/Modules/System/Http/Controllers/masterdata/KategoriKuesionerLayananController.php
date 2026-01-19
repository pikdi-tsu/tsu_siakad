<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_KategoriKuesionerLayanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class KategoriKuesionerLayananController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Kategori Kuesioner Layanan";
        $data['menu']  = "Kategori Kuesioner";

        if ($request->ajax()) {
            $query = Master_KategoriKuesionerLayanan::query()->orderBy('created_at', 'asc');

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

        return view('system::masterdata.kategoriKuesionerLayanan.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        Master_KategoriKuesionerLayanan::updateOrCreate(
            ['id' => $request->id],
            [
                'nama_kategori' => $request->nama_kategori,
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'Data Kategori berhasil disimpan!']);
    }

    public function edit($id)
    {
        $data = Master_KategoriKuesionerLayanan::find($id);
        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_KategoriKuesionerLayanan::find($id);
        if ($data) {
            $data->delete();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
}
