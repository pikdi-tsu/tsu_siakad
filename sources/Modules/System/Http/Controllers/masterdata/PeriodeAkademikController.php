<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_PeriodeAkademik;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PeriodeAkademikController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Periode Akademik";
        $data['menu']  = "Periode Akademik";

        if ($request->ajax()) {
            $query = Master_PeriodeAkademik::query()->orderBy('kode_periode', 'desc'); // Biasanya periode terbaru di atas

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('checkbox', function($row){
                    return '<input type="checkbox" name="ids[]" class="check-item" value="'.$row->id.'">';
                })
                ->editColumn('tgl_awal_kuliah', function ($row) {
                    return $row->tgl_awal_kuliah_formatted;
                })
                ->editColumn('tgl_akhir_kuliah', function ($row) {
                    return $row->tgl_akhir_kuliah_formatted;
                })
                ->editColumn('is_active', function ($row) {
                    return $row->is_active
                        ? '<div class="text-center text-success"><i class="fas fa-check-circle"></i></div>'
                        : '<div class="text-center text-danger"><i class="fas fa-times"></i></div>';
                })
                ->addColumn('action', function ($row) {
                    // Tombol Set Aktif (Hijau)
                    $btn = '<button type="button" onclick="setActive(\''.$row->id.'\')" class="btn btn-success btn-sm" title="Set Aktif"><i class="fas fa-check"></i></button>';
                    // Tombol Edit/Detail (Biru)
                    $btn .= ' <button type="button" data-id="'.$row->id.'" class="btn btn-info btn-sm btn_edit" title="Detail/Edit"><i class="fas fa-eye"></i></button>';
                    // Tombol Hapus (Merah)
                    $btn .= ' <button type="button" data-id="'.$row->id.'" class="btn btn-danger btn-sm btn_hapus" title="Hapus"><i class="fas fa-trash"></i></button>';
                    return '<div class="text-center">'.$btn.'</div>';
                })
                ->rawColumns(['checkbox', 'is_active', 'action'])
                ->make(true);
        }

        return view('system::masterdata.periodeAkademik.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_periode' => 'required|unique:siakad_master_periode,kode_periode,' . $request->id,
            'nama_periode' => 'required|string',
            'tgl_awal_kuliah' => 'required|date',
            'tgl_akhir_kuliah' => 'required|date|after:tgl_awal_kuliah',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        Master_PeriodeAkademik::updateOrCreate(
            ['id' => $request->id],
            [
                'kode_periode'     => $request->kode_periode,
                'nama_periode'     => $request->nama_periode,
                'tgl_awal_kuliah'  => $request->tgl_awal_kuliah,
                'tgl_akhir_kuliah' => $request->tgl_akhir_kuliah,
                'tgl_awal_uts'     => $request->tgl_awal_uts,
                'tgl_awal_uas'     => $request->tgl_awal_uas,
                // is_active tidak diupdate di sini, ada tombol khusus
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'Data Periode berhasil disimpan!']);
    }

    // LOGIC PENTING: SET AKTIF
    public function setActive($id)
    {
        DB::beginTransaction();
        try {
            // 1. Non-aktifkan semua periode
            Master_PeriodeAkademik::query()->update(['is_active' => 0]);

            // 2. Aktifkan periode yang dipilih
            $periode = Master_PeriodeAkademik::find($id);
            $periode->is_active = 1;
            $periode->save();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Periode '.$periode->nama_periode.' berhasil diaktifkan!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'Gagal mengaktifkan periode.']);
        }
    }

    public function edit($id)
    {
        $data = Master_PeriodeAkademik::find($id);
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function destroy($id)
    {
        $data = Master_PeriodeAkademik::find($id);
        if($data->is_active) {
            return response()->json(['status' => 'error', 'message' => 'Tidak boleh menghapus Periode yang sedang AKTIF!']);
        }
        $data->delete();
        return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus!']);
    }
}
