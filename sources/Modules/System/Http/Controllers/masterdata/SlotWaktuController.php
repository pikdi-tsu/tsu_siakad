<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_SlotWaktu;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class SlotWaktuController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Master Slot Waktu";
        $data['menu']  = "Slot Waktu";

        // Ambil semua data, urutkan dari pagi ke malam
        $slots = Master_SlotWaktu::orderBy('waktu', 'asc')->get();

        // Grouping Data untuk 3 Kolom
        $data['pagi'] = $slots->filter(function ($item) {
            $jam = (int) substr($item->waktu, 0, 2);
            return $jam >= 6 && $jam < 12; // 06:00 - 11:59
        });

        $data['siang'] = $slots->filter(function ($item) {
            $jam = (int) substr($item->waktu, 0, 2);
            return $jam >= 12 && $jam < 18; // 12:00 - 17:59
        });

        $data['malam'] = $slots->filter(function ($item) {
            $jam = (int) substr($item->waktu, 0, 2);
            return $jam >= 18 || $jam < 6; // 18:00 - 05:59 (Malam sampai Subuh)
        });

        return view('system::masterdata.slotWaktu.index', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Validasi format jam HH:MM
            'waktu' => 'required|date_format:H:i|unique:siakad_master_slot_waktu,waktu,' . $request->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        Master_SlotWaktu::updateOrCreate(
            ['id' => $request->id],
            [
                'waktu' => $request->waktu,
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'Data Waktu berhasil disimpan!']);
    }

    public function edit($id)
    {
        $data = Master_SlotWaktu::find($id);
        if ($data) {
            // Kirim format H:i untuk input type="time" atau text
            $data->waktu_input = Carbon::parse($data->waktu)->format('H:i');
            return response()->json(['status' => 'success', 'data' => $data]);
        }
        return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function destroy($id)
    {
        $data = Master_SlotWaktu::find($id);
        if ($data) {
            $data->delete();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
}
