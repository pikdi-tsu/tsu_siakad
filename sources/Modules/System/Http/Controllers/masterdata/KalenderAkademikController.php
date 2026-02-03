<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_KalenderAkademik;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\Facades\DataTables;

class KalenderAkademikController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Kalender Akademik";
        $data['menu']  = "Kalender Akademik";

        // Data untuk Dropdown Filter & Form
        // Nanti bisa diganti MasterPeriode::all()
        $data['list_periode'] = [
            '20251' => '2025 Ganjil',
            '20252' => '2025 Genap',
            '20261' => '2026 Ganjil'
        ];
        $data['list_kegiatan'] = Master_KalenderAkademik::all();

        if ($request->ajax()) {
            // Eager Load 'kegiatan' biar query ringan
            $query = Master_KalenderAkademik::with('kegiatan')->select('siakad_kalender_akademik.*');

            // --- LOGIC FILTER ---
            if ($request->periode) {
                $query->where('id_periode', $request->periode);
            }
            if ($request->kegiatan) {
                $query->where('id_kegiatan', $request->kegiatan);
            }
            if ($request->libur_nasional == 'true') {
                $query->where('is_libur_nasional', 1);
            }
            if ($request->libur_akademik == 'true') {
                $query->where('is_libur_akademik', 1);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('tgl_mulai', function ($row) {
                    return $row->tgl_mulai_formatted;
                })
                ->editColumn('tgl_selesai', function ($row) {
                    return $row->tgl_selesai_formatted;
                })
                ->addColumn('nama_kegiatan', function ($row) {
                    // Ambil nama dari relasi
                    return $row->kegiatan ? $row->kegiatan->nama_kegiatan : '-';
                })
                ->addColumn('status_libur', function ($row) {
                    $badges = [];
                    if ($row->is_libur_nasional) $badges[] = '<span class="badge badge-danger">Libur Nasional</span>';
                    if ($row->is_libur_akademik) $badges[] = '<span class="badge badge-warning">Libur Akademik</span>';
                    return implode(' ', $badges);
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button type="button" data-id="'.$row->id.'" class="btn btn-warning btn-sm btn_edit" title="Edit"><i class="fas fa-pencil-alt"></i></button>';
                    $btn .= ' <button type="button" data-id="'.$row->id.'" class="btn btn-danger btn-sm btn_hapus" title="Hapus"><i class="fas fa-trash"></i></button>';
                    return '<div class="text-center">'.$btn.'</div>';
                })
                ->rawColumns(['status_libur', 'action'])
                ->make(true);
        }

        return view('system::masterdata.kalenderAkademik.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_periode'  => 'required',
            'id_kegiatan' => 'required',
            'tgl_mulai'   => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'keterangan'  => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        KalenderAkademik::updateOrCreate(
            ['id' => $request->id],
            [
                'id_periode'        => $request->id_periode,
                'id_kegiatan'       => $request->id_kegiatan,
                'tgl_mulai'         => $request->tgl_mulai,
                'tgl_selesai'       => $request->tgl_selesai,
                'keterangan'        => $request->keterangan,
                // Checkbox value handling (kadang string "on", kadang 1)
                'is_libur_nasional' => $request->has('is_libur_nasional') ? 1 : 0,
                'is_libur_akademik' => $request->has('is_libur_akademik') ? 1 : 0,
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'Data Kalender berhasil disimpan!']);
    }

    public function edit($id)
    {
        $data = KalenderAkademik::find($id);
        return $data
            ? response()->json(['status' => 'success', 'data' => $data])
            : response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = KalenderAkademik::find($id);
        if ($data) {
            $data->delete();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
}
