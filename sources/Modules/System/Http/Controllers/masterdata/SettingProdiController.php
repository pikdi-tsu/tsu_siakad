<?php

namespace Modules\System\Http\Controllers\masterdata;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Master_SettingProdi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SettingProdiController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = "Setting Program Studi";
        $data['menu']  = "Setting Prodi";

        // 1. Ambil Filter Periode (Default ke Periode Aktif jika ada)
        // $periode_aktif = MasterPeriode::where('is_active', 1)->first();
        // $id_periode = $request->periode ?? ($periode_aktif->id ?? null);
        $data['id_periode_selected'] = '20251'; // Hardcode contoh, ganti dengan logic di atas

        // Dummy Data Periode untuk Dropdown
        $data['list_periode'] = [
            '20251' => '2025 Ganjil',
            '20252' => '2025 Genap',
        ];

        // 2. Ambil Data Hirarki (Fakultas -> Prodi)
        // Di real case: MasterFakultas::with(['prodi.setting_prodi' => function($q) use($id_periode) { ... }])->get();

        // MOCKUP DATA (Karena saya tidak punya DB Master kamu)
        // Nanti diganti dengan Query Eloquent yang mengambil Fakultas beserta Prodinya
        $data['fakultas_data'] = [
            (object)[
                'nama_fakultas' => 'Fakultas Teknik',
                'prodi' => [
                    (object)['id' => 'p1', 'nama_prodi' => 'S1 - Informatika'],
                    (object)['id' => 'p2', 'nama_prodi' => 'S1 - Sistem Informasi'],
                ]
            ],
            (object)[
                'nama_fakultas' => 'Fakultas Ekonomi',
                'prodi' => [
                    (object)['id' => 'p3', 'nama_prodi' => 'S1 - Manajemen'],
                    (object)['id' => 'p4', 'nama_prodi' => 'S1 - Akuntansi'],
                ]
            ]
        ];

        // 3. Ambil Settingan yang sudah tersimpan untuk periode ini
        $settings = Master_SettingProdi::where('id_periode', $data['id_periode_selected'])->get();
        // Mapping biar mudah diakses di view: $mapped_settings['id_prodi'] = object;
        $data['settings_map'] = $settings->keyBy('id_prodi');

        return view('system::masterdata.settingProdi.index', $data);
    }

    // AJAX Handler untuk Auto-Save Checkbox & Dropdown
    public function update(Request $request)
    {
        $request->validate([
            'id_periode' => 'required',
            'id_prodi'   => 'required',
            'field'      => 'required', // Nama kolom (misal: is_krs)
            'value'      => 'required'  // Nilai (1/0 atau id_kurikulum)
        ]);

        try {
            Master_SettingProdi::updateOrCreate(
                [
                    'id_periode' => $request->id_periode,
                    'id_prodi'   => $request->id_prodi
                ],
                [
                    $request->field => $request->value
                ]
            );

            return response()->json(['status' => 'success', 'message' => 'Update berhasil']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
