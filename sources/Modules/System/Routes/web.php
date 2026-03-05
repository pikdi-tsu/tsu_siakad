<?php

use App\Http\Controllers\NeoFeederAuthController;
use App\Http\Controllers\NeoFeederMahasiswasController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SsoController;
use App\Http\Controllers\EmergencyLoginController;
use Modules\System\Http\Controllers\HomeController;
use Modules\System\Http\Controllers\MenuController;
use Modules\System\Http\Controllers\LoginController;
use Modules\System\Http\Controllers\PermissionController;
use Modules\System\Http\Controllers\RoleController;
use Modules\System\Http\Controllers\SettingController;
use Modules\System\Http\Controllers\DashboardController;
use Modules\System\Http\Controllers\UserController;
use Modules\System\Http\Controllers\UserProfileController;
use Modules\System\Http\Controllers\masterdata\SukuController;
use Modules\System\Http\Controllers\masterdata\AgamaController;
use Modules\System\Http\Controllers\masterdata\JenjangController;
use Modules\System\Http\Controllers\masterdata\JurusanController;
use Modules\System\Http\Controllers\masterdata\FakultasController;
use Modules\System\Http\Controllers\masterdata\ProvinsiController;
use Modules\System\Http\Controllers\masterdata\TarifUKTController;
use Modules\System\Http\Controllers\masterdata\KabupatenController;
use Modules\System\Http\Controllers\masterdata\KecamatanController;
use Modules\System\Http\Controllers\masterdata\KelurahanController;
use Modules\System\Http\Controllers\masterdata\PekerjaanController;
use Modules\System\Http\Controllers\masterdata\SlotWaktuController;
use Modules\System\Http\Controllers\masterdata\BidangIlmuController;
use Modules\System\Http\Controllers\masterdata\UnsurNilaiController;
use Modules\System\Http\Controllers\masterdata\PenghasilanController;
use Modules\System\Http\Controllers\masterdata\RuangKuliahController;
use Modules\System\Http\Controllers\masterdata\StatusHadirController;
use Modules\System\Http\Controllers\masterdata\JasAlmamaterController;
use Modules\System\Http\Controllers\masterdata\JenisTinggalController;
use Modules\System\Http\Controllers\masterdata\SettingProdiController;
use Modules\System\Http\Controllers\masterdata\SistemKuliahController;
use Modules\System\Http\Controllers\masterdata\TransportasiController;
use Modules\System\Http\Controllers\masterdata\JenisPertemuanController;
use Modules\System\Http\Controllers\masterdata\JenisMataKuliahController;
use Modules\System\Http\Controllers\masterdata\KebutuhanKhususController;
use Modules\System\Http\Controllers\masterdata\PeriodeAkademikController;
use Modules\System\Http\Controllers\masterdata\StatusMahasiswaController;
use Modules\System\Http\Controllers\masterdata\BatchPendaftaranController;
use Modules\System\Http\Controllers\masterdata\KalenderAkademikController;
use Modules\System\Http\Controllers\masterdata\KegiatanAkademikController;
use Modules\System\Http\Controllers\masterdata\KelasPerkuliahanController;
use Modules\System\Http\Controllers\masterdata\KelompokMataKuliahController;
use Modules\System\Http\Controllers\masterdata\JenisModulMataKuliahController;
use Modules\System\Http\Controllers\masterdata\JenisKegiatanPendukungController;
use Modules\System\Http\Controllers\masterdata\KategoriKuesionerLayananController;
use Modules\System\Http\Controllers\masterdata\{
    DataPerguruanTinggiController,
    ProgramStudiController,
    KonsentrasiController,
    TingkatPendidikanUniversitasController,
    InstansiController,
    ContactPersonController,
    TingkatPendidikanController,
    GedungController,
    LokasiKampusController,
    KelompokPerkuliahanController,
    GroupMkWajibPilihanController,
    JenisSertifikatController,
    PenyelenggaraSertifikatController,
    NegaraController,
    JenisPegawaiController,
    GolonganPangkatController,
    JabatanFungsionalController,
    JabatanStrukturalController,
    StatusKeaktifanController,
    LembagaNaunganController,
    PeringkatAkreditasiController,
    JenisPerguruanTinggiController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('indexing')->middleware('web', 'guest');
    Route::middleware(['web'])->group(function () {
        // Dashboard
        Route::prefix('dashboard')->group(function() {
            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        });

        // Auth
        Route::prefix('login')->group(function() {
            // Login PIKDI
            Route::get('/', [LoginController::class, 'index'])->name('login')->middleware('guest');
            Route::post('/', [LoginController::class, 'login'])->name('login.action');

            // Login SSO User
            Route::get('/sso', [SsoController::class, 'redirect'])->name('sso.login');
            Route::get('/sso/callback', [SsoController::class, 'callback'])->name('sso.callback');
        });

        // Auth Emergency Login
        Route::prefix('emergency-login')->group(function() {
            Route::get('/', [EmergencyLoginController::class, 'login'])->name('emergency-login');
        });

        // Auth Rescue Login
        Route::prefix('rescue-login')->group(function() {
            Route::get('/', [EmergencyLoginController::class, 'showRescueForm'])->name('rescue');
            Route::post('/', [EmergencyLoginController::class, 'processRescueLogin'])->name('rescue.post');
        });

        // Logout
        Route::prefix('logout')->group(function() {
            Route::post('/', [LoginController::class, 'logout'])->name('logout');
//            Route::post('/neofeeder', [NeoFeederAuthController::class, 'logout'])->name('neofeeder.logout');
        });

        // Profile & Password
        Route::prefix('profile')->middleware(['auth'])->name('profile.')->group(function() {
            Route::get('/', [UserProfileController::class, 'index'])->name('index');
            Route::post('/profile/photo', [UserProfileController::class, 'updatePhoto'])->name('save.change-profile');
            Route::put('/profile/password', [UserProfileController::class, 'updatePassword'])->name('update-password');
        });

        // Master Data
        Route::prefix('MasterData')->middleware(['auth'])->group(function() {
            // Perguruan Tinggi
            Route::prefix('PerguruanTinggi')->name('perguruan_tinggi.')->group(function () {
                // Data Perguruan Tinggi
                Route::prefix('DataPerguruanTinggi')->group(function () {
                    Route::get('/', [DataPerguruanTinggiController::class, 'index'])->name('perguruan_tinggi.index');
                    Route::get('/searchPegawai', [DataPerguruanTinggiController::class, 'search'])->name('perguruan_tinggi.caripegawai');
                    Route::post('/save', [DataPerguruanTinggiController::class, 'save'])->name('perguruan_tinggi.save');
                    Route::get('/edit/{id}', [DataPerguruanTinggiController::class, 'edit'])->name('perguruan_tinggi.edit');
                    Route::delete('/delete/{id}', [DataPerguruanTinggiController::class, 'destroy'])->name('perguruan_tinggi.delete');
                });

                // Ruang Kuliah
                Route::prefix('RuangKuliah')->middleware(['permission:system:master_ruangkuliah:view'])->group(function() {
                    Route::get('/', [RuangKuliahController::class, 'index'])->name('ruang_kuliah.index');
                    Route::post('/ruang-kuliah/store', [RuangKuliahController::class, 'store'])->name('ruang_kuliah.store');
                    Route::get('/ruang-kuliah/edit/{id}', [RuangKuliahController::class, 'edit'])->name('ruang_kuliah.edit');
                    Route::delete('/ruang-kuliah/delete/{id}', [RuangKuliahController::class, 'destroy'])->name('ruang_kuliah.delete');
                });

                // Kegiatan Akademik
                Route::prefix('KegiatanAkademik')->middleware(['permission:system:master_kegiatanakademik:view'])->group(function() {
                    Route::get('/', [KegiatanAkademikController::class, 'index'])->name('kegiatan_akademik.index');
                    Route::post('/kegiatan-akademik/store', [KegiatanAkademikController::class, 'store'])->name('kegiatan_akademik.store');
                    Route::get('/kegiatan-akademik/edit/{id}', [KegiatanAkademikController::class, 'edit'])->name('kegiatan_akademik.edit');
                    Route::delete('/kegiatan-akademik/delete/{id}', [KegiatanAkademikController::class, 'destroy'])->name('kegiatan_akademik.delete');
                });

                // Kalender Akademik
                Route::prefix('KalenderAkademik')->middleware(['permission:system:master_kalenderakademik:view'])->group(function() {
                    Route::get('/', [KalenderAkademikController::class, 'index'])->name('kalender_akademik.index');
                    Route::post('/kalender-akademik/store', [KalenderAkademikController::class, 'store'])->name('kalender_akademik.store');
                    Route::get('/kalender-akademik/edit/{id}', [KalenderAkademikController::class, 'edit'])->name('kalender_akademik.edit');
                    Route::delete('/kalender-akademik/delete/{id}', [KalenderAkademikController::class, 'destroy'])->name('kalender_akademik.delete');
                });

                // Program Studi
                Route::prefix('ProgramStudi')->middleware(['permission:system:master_programstudi:view'])->group(function () {
                    Route::get('/', [ProgramStudiController::class, 'index'])->name('program_studi.index');
                    Route::post('/store', [ProgramStudiController::class, 'store'])->name('program_studi.store');
                    Route::get('/edit/{id}', [ProgramStudiController::class, 'edit'])->name('program_studi.edit');
                    Route::delete('/delete/{id}', [ProgramStudiController::class, 'destroy'])->name('program_studi.delete');
                });

                // Konsentrasi
                Route::prefix('Konsentrasi')->middleware(['permission:system:master_konsentrasi:view'])->group(function () {
                    Route::get('/', [KonsentrasiController::class, 'index'])->name('konsentrasi.index');
                    Route::post('/store', [KonsentrasiController::class, 'store'])->name('konsentrasi.store');
                    Route::get('/edit/{id}', [KonsentrasiController::class, 'edit'])->name('konsentrasi.edit');
                    Route::delete('/delete/{id}', [KonsentrasiController::class, 'destroy'])->name('konsentrasi.delete');
                });

                // Tingkat Pendidikan Universitas
                Route::prefix('TingkatPendidikanUniversitas')->middleware(['permission:system:master_tingkatpendidikanuniv:view'])->group(function () {
                    Route::get('/', [TingkatPendidikanUniversitasController::class, 'index'])->name('tingkat_pendidikan_univ.index');
                    Route::post('/store', [TingkatPendidikanUniversitasController::class, 'store'])->name('tingkat_pendidikan_univ.store');
                    Route::get('/edit/{id}', [TingkatPendidikanUniversitasController::class, 'edit'])->name('tingkat_pendidikan_univ.edit');
                    Route::delete('/delete/{id}', [TingkatPendidikanUniversitasController::class, 'destroy'])->name('tingkat_pendidikan_univ.delete');
                });

                // Instansi
                Route::prefix('Instansi')->middleware(['permission:system:master_instansi:view'])->group(function () {
                    Route::get('/', [InstansiController::class, 'index'])->name('instansi.index');
                    Route::post('/store', [InstansiController::class, 'store'])->name('instansi.store');
                    Route::get('/edit/{id}', [InstansiController::class, 'edit'])->name('instansi.edit');
                    Route::delete('/delete/{id}', [InstansiController::class, 'destroy'])->name('instansi.delete');
                });

                // Tingkat Pendidikan
                Route::prefix('TingkatPendidikan')->middleware(['permission:system:master_tingkatpendidikan:view'])->group(function () {
                    Route::get('/', [TingkatPendidikanController::class, 'index'])->name('tingkat_pendidikan.index');
                    Route::post('/store', [TingkatPendidikanController::class, 'store'])->name('tingkat_pendidikan.store');
                    Route::get('/edit/{id}', [TingkatPendidikanController::class, 'edit'])->name('tingkat_pendidikan.edit');
                    Route::delete('/delete/{id}', [TingkatPendidikanController::class, 'destroy'])->name('tingkat_pendidikan.delete');
                });

                // Contact Person
                Route::prefix('ContactPerson')->middleware(['permission:system:master_contactperson:view'])->group(function () {
                    Route::get('/', [ContactPersonController::class, 'index'])->name('contact_person.index');
                    Route::post('/store', [ContactPersonController::class, 'store'])->name('contact_person.store');
                    Route::get('/edit/{id}', [ContactPersonController::class, 'edit'])->name('contact_person.edit');
                    Route::delete('/delete/{id}', [ContactPersonController::class, 'destroy'])->name('contact_person.delete');
                });

                // Gedung
                Route::prefix('Gedung')->middleware(['permission:system:master_gedung:view'])->group(function () {
                    Route::get('/', [GedungController::class, 'index'])->name('gedung.index');
                    Route::post('/store', [GedungController::class, 'store'])->name('gedung.store');
                    Route::get('/edit/{id}', [GedungController::class, 'edit'])->name('gedung.edit');
                    Route::delete('/delete/{id}', [GedungController::class, 'destroy'])->name('gedung.delete');
                });

                // Lokasi Kampus
                Route::prefix('LokasiKampus')->middleware(['permission:system:master_lokasikampus:view'])->group(function () {
                    Route::get('/', [LokasiKampusController::class, 'index'])->name('lokasi_kampus.index');
                    Route::post('/store', [LokasiKampusController::class, 'store'])->name('lokasi_kampus.store');
                    Route::get('/edit/{id}', [LokasiKampusController::class, 'edit'])->name('lokasi_kampus.edit');
                    Route::delete('/delete/{id}', [LokasiKampusController::class, 'destroy'])->name('lokasi_kampus.delete');
                });

                // Lembaga Naungan
                Route::prefix('lembaga-naungan')->group(function () {
                    Route::get('/', [LembagaNaunganController::class, 'index'])->name('lembaga_naungan.index');
                    Route::post('/store', [LembagaNaunganController::class, 'store'])->name('lembaga_naungan.store');
                    Route::get('/edit/{id}', [LembagaNaunganController::class, 'edit'])->name('lembaga_naungan.edit');
                    Route::delete('/delete/{id}', [LembagaNaunganController::class, 'destroy'])->name('lembaga_naungan.delete');
                });

                // Jenis Perguruan Tinggi
                Route::prefix('jenis-perguruan-tinggi')->group(function () {
                    Route::get('/', [JenisPerguruanTinggiController::class, 'index'])->name('jenis_pt.index');
                    Route::post('/store', [JenisPerguruanTinggiController::class, 'store'])->name('jenis_pt.store');
                    Route::get('/edit/{id}', [JenisPerguruanTinggiController::class, 'edit'])->name('jenis_pt.edit');
                    Route::delete('/delete/{id}', [JenisPerguruanTinggiController::class, 'destroy'])->name('jenis_pt.delete');
                });

                // Peringkat Akreditasi
                Route::prefix('peringkat-akreditasi')->group(function () {
                    Route::get('/', [PeringkatAkreditasiController::class, 'index'])
                        ->name('peringkat_akreditasi.index');
                    Route::post('/store', [PeringkatAkreditasiController::class, 'store'])
                        ->name('peringkat_akreditasi.store');
                    Route::get('/edit/{id}', [PeringkatAkreditasiController::class, 'edit'])
                        ->name('peringkat_akreditasi.edit');
                    Route::delete('/delete/{id}', [PeringkatAkreditasiController::class, 'destroy'])
                        ->name('peringkat_akreditasi.delete');
                });

                // Univ Luar

                // ====================================== Route Semesntara ===================================
                // Batch Pendaftaran
                Route::prefix('BatchPendaftaran')->middleware(['permission:system:master_batchpendaftaran:view'])->group(function(){
                    Route::get('/', [BatchPendaftaranController::class, 'index'])->name('admin.BatchPendaftaran.show');
                    Route::get('/TabelBatch', [BatchPendaftaranController::class, 'TabelBatch'])->name('admin.BatchPendaftaran.Tabel');
                    Route::post('/Store', [BatchPendaftaranController::class, 'StoreBatch'])->name('admin.BatchPendaftaran.Store');
                    Route::get('/EditBatch/{params}', [BatchPendaftaranController::class, 'ShowBatch'])->name('admin.BatchPendaftaran.Edit');
                    Route::get('/Status/{params1}/{params2}', [BatchPendaftaranController::class, 'delete'])->name('admin.BatchPendaftaran.delete');
                });

                // Fakultas
                Route::prefix('Fakultas')->middleware(['permission:system:master_fakultas:view'])->group(function() {
                    Route::get('/', [FakultasController::class, 'index'])->name('admin.fakultas.show');
                    Route::get('/TabelFakultas', [FakultasController::class, 'table_fakultas'])->name('admin.fakultas.Tabel');
                    Route::post('/Store', [FakultasController::class, 'StoreFakultas'])->name('admin.fakultas.Store');
                    Route::get('/EditFakultas/{params}', [FakultasController::class, 'ShowFakultas'])->name('admin.fakultas.Edit');
                    Route::get('/Status/{params1}/{params2}', [FakultasController::class, 'delete'])->name('admin.fakultas.delete');
                });

                // Program Studi
                Route::prefix('ProgramStudi')->middleware(['permission:system:master_programstudi:view'])->group(function() {
                    Route::get('/', [JurusanController::class, 'index'])->name('admin.Jurusan.show');
                    Route::get('/TabelJurusan', [JurusanController::class, 'table_jurusan'])->name('admin.Jurusan.Tabel');
                    Route::post('/Store', [JurusanController::class, 'StoreJurusan'])->name('admin.Jurusan.Store');
                    Route::get('/EditJurusan/{params}', [JurusanController::class, 'ShowJurusan'])->name('admin.Jurusan.Edit');
                    Route::get('/Status/{params1}/{params2}', [JurusanController::class, 'delete'])->name('admin.Jurusan.delete');
                });

                // Jenjang Pendidikan Universitas
                Route::prefix('JenjangPendidikanUniversitas')->middleware(['permission:system:master_jenjangpendidikanuniv:view'])->group(function() {
                    Route::get('/', [JenjangController::class, 'index'])->name('admin.Jenjang.show');
                    Route::get('/TabelJenjang', [JenjangController::class, 'table_Pendaftaran'])->name('admin.Jenjang.Tabel');
                    Route::post('/Store', [JenjangController::class, 'StoreJurusan'])->name('admin.Jenjang.Store');
                    Route::get('/EditJenjang/{params}', [JenjangController::class, 'ShowJurusan'])->name('admin.Jenjang.Edit');
                    Route::get('/Status/{params1}/{params2}', [JenjangController::class, 'delete'])->name('admin.Jenjang.delete');
                });

                // Sistem Kuliah
                Route::prefix('SistemKuliah')->middleware(['permission:system:master_sistemkuliah:view'])->group(function() {
                    Route::get('/', [SistemKuliahController::class, 'index'])->name('sistem_kuliah.index');
                    Route::post('/sistem-kuliah/store', [SistemKuliahController::class, 'store'])->name('sistem_kuliah.store');
                    Route::get('/sistem-kuliah/edit/{id}', [SistemKuliahController::class, 'edit'])->name('sistem_kuliah.edit');
                    Route::delete('/sistem-kuliah/delete/{id}', [SistemKuliahController::class, 'destroy'])->name('sistem_kuliah.delete');
                });

                // Tarif UKT
                Route::prefix('TarifUKT')->middleware(['permission:system:master_tarifukt:view'])->group(function(){
                    Route::get('/', [TarifUKTController::class, 'index'])->name('admin.TarifUKT.show');
                    Route::get('/TabelUKT', [TarifUKTController::class, 'TabelUKT'])->name('admin.TarifUKT.Tabel');
                    Route::post('/Store', [TarifUKTController::class, 'StoreUKT'])->name('admin.TarifUKT.Store');
                    Route::get('/EditUKT/{params}', [TarifUKTController::class, 'ShowUKT'])->name('admin.TarifUKT.Edit');
                    Route::get('/Status/{params1}/{params2}', [TarifUKTController::class, 'delete'])->name('admin.TarifUKT.delete');
                });
                // ====================================== End Route Semesntara ===================================
            });

            // Pegawai
            Route::prefix('Pegawai')->name('pegawai.')->group(function() {
                // Jenis Pegawai
                Route::prefix('JenisPegawai')->middleware(['permission:system:master_jenispegawai:view'])->group(function () {
                    Route::get('/', [JenisPegawaiController::class, 'index'])->name('jenis_pegawai.index');
                    Route::post('/store', [JenisPegawaiController::class, 'store'])->name('jenis_pegawai.store');
                    Route::get('/edit/{id}', [JenisPegawaiController::class, 'edit'])->name('jenis_pegawai.edit');
                    Route::delete('/delete/{id}', [JenisPegawaiController::class, 'destroy'])->name('jenis_pegawai.delete');
                });

                // Golongan Pangkat
                Route::prefix('GolonganPangkat')->middleware(['permission:system:master_golonganpangkat:view'])->group(function () {
                    Route::get('/', [GolonganPangkatController::class, 'index'])->name('golongan_pangkat.index');
                    Route::post('/store', [GolonganPangkatController::class, 'store'])->name('golongan_pangkat.store');
                    Route::get('/edit/{id}', [GolonganPangkatController::class, 'edit'])->name('golongan_pangkat.edit');
                    Route::delete('/delete/{id}', [GolonganPangkatController::class, 'destroy'])->name('golongan_pangkat.delete');
                });

                // Jabatan Fungsional
                Route::prefix('JabatanFungsional')->middleware(['permission:system:master_jabatanfungsional:view'])->group(function () {
                    Route::get('/', [JabatanFungsionalController::class, 'index'])->name('jabatan_fungsional.index');
                    Route::post('/store', [JabatanFungsionalController::class, 'store'])->name('jabatan_fungsional.store');
                    Route::get('/edit/{id}', [JabatanFungsionalController::class, 'edit'])->name('jabatan_fungsional.edit');
                    Route::delete('/delete/{id}', [JabatanFungsionalController::class, 'destroy'])->name('jabatan_fungsional.delete');
                });

                // Jabatan Struktural
                Route::prefix('JabatanStruktural')->middleware(['permission:system:master_jabatanstruktural:view'])->group(function () {
                    Route::get('/', [JabatanStrukturalController::class, 'index'])->name('jabatan_struktural.index');
                    Route::post('/store', [JabatanStrukturalController::class, 'store'])->name('jabatan_struktural.store');
                    Route::get('/edit/{id}', [JabatanStrukturalController::class, 'edit'])->name('jabatan_struktural.edit');
                    Route::delete('/delete/{id}', [JabatanStrukturalController::class, 'destroy'])->name('jabatan_struktural.delete');
                });

                // Status Keaktifan
                Route::prefix('StatusKeaktifan')->middleware(['permission:system:master_statuskeaktifan:view'])->group(function () {
                    Route::get('/', [StatusKeaktifanController::class, 'index'])->name('status_keaktifan.index');
                    Route::post('/store', [StatusKeaktifanController::class, 'store'])->name('status_keaktifan.store');
                    Route::get('/edit/{id}', [StatusKeaktifanController::class, 'edit'])->name('status_keaktifan.edit');
                    Route::delete('/delete/{id}', [StatusKeaktifanController::class, 'destroy'])->name('status_keaktifan.delete');
                });

                // Tarif Honor

                // Jenis Honor
            });

            // Perkuliahan
            Route::prefix('Perkuliahan')->name('perkuliahan.')->group(function() {
                // Jenis Matakuliah
                Route::prefix('JenisMataKuliah')->middleware(['permission:system:master_jenismatakuliah:view'])->group(function() {
                    Route::get('/', [JenisMataKuliahController::class, 'index'])->name('jenis_matakuliah.index');
                    Route::post('/jenis-matakuliah/store', [JenisMataKuliahController::class, 'store'])->name('jenis_matakuliah.store');
                    Route::get('/jenis-matakuliah/edit/{id}', [JenisMataKuliahController::class, 'edit'])->name('jenis_matakuliah.edit');
                    Route::delete('/jenis-matakuliah/delete/{id}', [JenisMataKuliahController::class, 'destroy'])->name('jenis_matakuliah.delete');
                });

                // Kelompok Matakuliah
                Route::prefix('KelompokMataKuliah')->middleware(['permission:system:master_kelompokmatakuliah:view'])->group(function() {
                    Route::get('/', [KelompokMataKuliahController::class, 'index'])->name('kelompok_matakuliah.index');
                    Route::post('/kelompok-matakuliah/store', [KelompokMataKuliahController::class, 'store'])->name('kelompok_matakuliah.store');
                    Route::get('/kelompok-matakuliah/edit/{id}', [KelompokMataKuliahController::class, 'edit'])->name('kelompok_matakuliah.edit');
                    Route::delete('/kelompok-matakuliah/delete/{id}', [KelompokMataKuliahController::class, 'destroy'])->name('kelompok_matakuliah.delete');
                });

                // Bidang Ilmu
                Route::prefix('BidangIlmu')->middleware(['permission:system:master_bidangilmu:view'])->group(function() {
                    Route::get('/', [BidangIlmuController::class, 'index'])->name('bidang_ilmu.index');
                    Route::post('/bidang-ilmu/store', [BidangIlmuController::class, 'store'])->name('bidang_ilmu.store');
                    Route::get('/bidang-ilmu/edit/{id}', [BidangIlmuController::class, 'edit'])->name('bidang_ilmu.edit');
                    Route::delete('/bidang-ilmu/delete/{id}', [BidangIlmuController::class, 'destroy'])->name('bidang_ilmu.delete');
                });

                // Unsur Nilai
                Route::prefix('UnsurNilai')->middleware(['permission:system:master_unsurnilai:view'])->group(function() {
                    Route::get('/', [UnsurNilaiController::class, 'index'])->name('unsur_nilai.index');
                    Route::post('/unsur-nilai/store', [UnsurNilaiController::class, 'store'])->name('unsur_nilai.store');
                    Route::get('/unsur-nilai/edit/{id}', [UnsurNilaiController::class, 'edit'])->name('unsur_nilai.edit');
                    Route::delete('/unsur-nilai/delete/{id}', [UnsurNilaiController::class, 'destroy'])->name('unsur_nilai.delete');
                });

                // Kelas Perkuliahan
                Route::prefix('KelasPerkuliahan')->middleware(['permission:system:master_kelasperkuliahan:view'])->group(function() {
                    Route::get('/', [KelasPerkuliahanController::class, 'index'])->name('kelas_perkuliahan.index');
                    Route::post('/kelas-perkuliahan/store', [KelasPerkuliahanController::class, 'store'])->name('kelas_perkuliahan.store');
                    Route::get('/kelas-perkuliahan/edit/{id}', [KelasPerkuliahanController::class, 'edit'])->name('kelas_perkuliahan.edit');
                    Route::delete('/kelas-perkuliahan/delete/{id}', [KelasPerkuliahanController::class, 'destroy'])->name('kelas_perkuliahan.delete');
                });

                // Slot Waktu
                Route::prefix('SlotWaktu')->middleware(['permission:system:master_slotwaktu:view'])->group(function() {
                    Route::get('/', [SlotWaktuController::class, 'index'])->name('slot_waktu.index');
                    Route::post('/slot-waktu/store', [SlotWaktuController::class, 'store'])->name('slot_waktu.store');
                    Route::get('/slot-waktu/edit/{id}', [SlotWaktuController::class, 'edit'])->name('slot_waktu.edit');
                    Route::delete('/slot-waktu/delete/{id}', [SlotWaktuController::class, 'destroy'])->name('slot_waktu.delete');
                });

                // Status Hadir
                Route::prefix('StatusHadir')->middleware(['permission:system:master_statushadir:view'])->group(function() {
                    Route::get('/', [StatusHadirController::class, 'index'])->name('status_hadir.index');
                    Route::post('/status-hadir/store', [StatusHadirController::class, 'store'])->name('status_hadir.store');
                    Route::get('/status-hadir/edit/{id}', [StatusHadirController::class, 'edit'])->name('status_hadir.edit');
                    Route::delete('/status-hadir/delete/{id}', [StatusHadirController::class, 'destroy'])->name('status_hadir.delete');
                });

                // Jenis Pertemuan
                Route::prefix('JenisPertemuan')->group(function() {
                    Route::get('/', [JenisPertemuanController::class, 'index'])->name('jenis_pertemuan.index');
                    Route::post('/jenis-pertemuan/store', [JenisPertemuanController::class, 'store'])->name('jenis_pertemuan.store');
                    Route::get('/jenis-pertemuan/edit/{id}', [JenisPertemuanController::class, 'edit'])->name('jenis_pertemuan.edit');
                    Route::delete('/jenis-pertemuan/delete/{id}', [JenisPertemuanController::class, 'destroy'])->name('jenis_pertemuan.delete');
                });

                // Jenis Modul Mata Kuliah
                Route::prefix('JenisModulMataKuliah')->middleware(['permission:system:master_jenismodulmatakuliah:view'])->group(function() {
                    Route::get('/', [JenisModulMataKuliahController::class, 'index'])->name('jenis_modul_mata_kuliah.index');
                    Route::post('/jenis-modul-mata-kuliah/store', [JenisModulMataKuliahController::class, 'store'])->name('jenis_modul_mata_kuliah.store');
                    Route::get('/jenis-modul-mata-kuliah/edit/{id}', [JenisModulMataKuliahController::class, 'edit'])->name('jenis_modul_mata_kuliah.edit');
                    Route::delete('/jenis-modul-mata-kuliah/delete/{id}', [JenisModulMataKuliahController::class, 'destroy'])->name('jenis_modul_mata_kuliah.delete');
                });

                // Kelompok Perkuliahan
                Route::prefix('KelompokPerkuliahan')->middleware(['permission:system:master_kelompokperkuliahan:view'])->group(function () {
                    Route::get('/', [KelompokPerkuliahanController::class, 'index'])->name('kelompok_perkuliahan.index');
                    Route::post('/store', [KelompokPerkuliahanController::class, 'store'])->name('kelompok_perkuliahan.store');
                    Route::get('/edit/{id}', [KelompokPerkuliahanController::class, 'edit'])->name('kelompok_perkuliahan.edit');
                    Route::delete('/delete/{id}', [KelompokPerkuliahanController::class, 'destroy'])->name('kelompok_perkuliahan.delete');
                });

                // Group MK Wajib / Pilihan
                Route::prefix('GroupMkWajibPilihan')->middleware(['permission:system:master_groupmkwajibpilihan:view'])->group(function () {
                    Route::get('/', [GroupMkWajibPilihanController::class, 'index'])->name('group_mk.index');
                    Route::post('/store', [GroupMkWajibPilihanController::class, 'store'])->name('group_mk.store');
                    Route::get('/edit/{id}', [GroupMkWajibPilihanController::class, 'edit'])->name('group_mk.edit');
                    Route::delete('/delete/{id}', [GroupMkWajibPilihanController::class, 'destroy'])->name('group_mk.delete');
                });

                // Jenis Kegiatan Pendukung
                Route::prefix('JenisKegiatanPendukung')->middleware(['permission:system:master_jeniskegiatanpendukung:view'])->group(function() {
                    Route::get('/', [JenisKegiatanPendukungController::class, 'index'])->name('jenis_kegiatan_pendukung.index');
                    Route::post('/jenis-kegiatan-pendukung/store', [JenisKegiatanPendukungController::class, 'store'])->name('jenis_kegiatan_pendukung.store');
                    Route::get('/jenis-kegiatan-pendukung/edit/{id}', [JenisKegiatanPendukungController::class, 'edit'])->name('jenis_kegiatan_pendukung.edit');
                    Route::delete('/jenis-kegiatan-pendukung/delete/{id}', [JenisKegiatanPendukungController::class, 'destroy'])->name('jenis_kegiatan_pendukung.delete');
                });

                // Jenis Sertifikat
                Route::prefix('JenisSertifikat')->middleware(['permission:system:master_jenissertifikat:view'])->group(function () {
                    Route::get('/', [JenisSertifikatController::class, 'index'])->name('jenis_sertifikat.index');
                    Route::post('/store', [JenisSertifikatController::class, 'store'])->name('jenis_sertifikat.store');
                    Route::get('/edit/{id}', [JenisSertifikatController::class, 'edit'])->name('jenis_sertifikat.edit');
                    Route::delete('/delete/{id}', [JenisSertifikatController::class, 'destroy'])->name('jenis_sertifikat.delete');
                });

                // Penyelenggara Sertifikat
                Route::prefix('PenyelenggaraSertifikat')->middleware(['permission:system:master_penyelenggarasertifikat:view'])->group(function () {
                    Route::get('/', [PenyelenggaraSertifikatController::class, 'index'])->name('penyelenggara_sertifikat.index');
                    Route::post('/store', [PenyelenggaraSertifikatController::class, 'store'])->name('penyelenggara_sertifikat.store');
                    Route::get('/edit/{id}', [PenyelenggaraSertifikatController::class, 'edit'])->name('penyelenggara_sertifikat.edit');
                    Route::delete('/delete/{id}', [PenyelenggaraSertifikatController::class, 'destroy'])->name('penyelenggara_sertifikat.delete');
                });
            });

            // Biodata
            Route::prefix('Biodata')->name('biodata.')->group(function() {
                // Agama
                Route::prefix('Agama')->middleware(['permission:system:master_agama:view'])->group(function() {
                    Route::get('/', [AgamaController::class, 'index'])->name('agama.index');
                    Route::post('/agama/store', [AgamaController::class, 'store'])->name('agama.store');
                    Route::get('/agama/edit/{id}', [AgamaController::class, 'edit'])->name('agama.edit');
                    Route::delete('/agama/delete/{id}', [AgamaController::class, 'destroy'])->name('agama.delete');
                });

                // Pekerjaan
                Route::prefix('Pekerjaan')->middleware(['permission:system:master_pekerjaan:view'])->group(function() {
                    Route::get('/', [PekerjaanController::class, 'index'])->name('pekerjaan.index');
                    Route::post('/pekerjaan/store', [PekerjaanController::class, 'store'])->name('pekerjaan.store');
                    Route::get('/pekerjaan/edit/{id}', [PekerjaanController::class, 'edit'])->name('pekerjaan.edit');
                    Route::delete('/pekerjaan/delete/{id}', [PekerjaanController::class, 'destroy'])->name('pekerjaan.delete');
                });

                // Penghasilan
                Route::prefix('Penghasilan')->middleware(['permission:system:master_penghasilan:view'])->group(function() {
                    Route::get('/', [PenghasilanController::class, 'index'])->name('penghasilan.index');
                    Route::post('/penghasilan/store', [PenghasilanController::class, 'store'])->name('penghasilan.store');
                    Route::get('/penghasilan/edit/{id}', [PenghasilanController::class, 'edit'])->name('penghasilan.edit');
                    Route::delete('/penghasilan/delete/{id}', [PenghasilanController::class, 'destroy'])->name('penghasilan.delete');
                });

                // Suku
                Route::prefix('Suku')->middleware(['permission:system:master_suku:view'])->group(function() {
                    Route::get('/', [SukuController::class, 'index'])->name('suku.index');
                    Route::post('/suku/store', [SukuController::class, 'store'])->name('suku.store');
                    Route::get('/suku/edit/{id}', [SukuController::class, 'edit'])->name('suku.edit');
                    Route::delete('/suku/delete/{id}', [SukuController::class, 'destroy'])->name('suku.delete');
                });

                // Jas Almamater
                Route::prefix('JasAlmamater')->middleware(['permission:system:master_jasalmamater:view'])->group(function() {
                    Route::get('/', [JasAlmamaterController::class, 'index'])->name('jas_almamater.index');
                    Route::post('/jas-almamater/store', [JasAlmamaterController::class, 'store'])->name('jas_almamater.store');
                    Route::get('/jas-almamater/edit/{id}', [JasAlmamaterController::class, 'edit'])->name('jas_almamater.edit');
                    Route::delete('/jas-almamater/delete/{id}', [JasAlmamaterController::class, 'destroy'])->name('jas_almamater.delete');
                });
            });

            // Mahasiswa
            Route::prefix('Mahasiswa')->name('mahasiswa.')->group(function() {
                // Status Mahasiswa
                Route::prefix('StatusMahasiswa')->middleware(['permission:system:master_statusmahasiswa:view'])->group(function() {
                    Route::get('/', [StatusMahasiswaController::class, 'index'])->name('status_mahasiswa.index');
                    Route::post('/status-mahasiswa/store', [StatusMahasiswaController::class, 'store'])->name('status_mahasiswa.store');
                    Route::get('/status-mahasiswa/edit/{id}', [StatusMahasiswaController::class, 'edit'])->name('status_mahasiswa.edit');
                    Route::delete('/status-mahasiswa/delete/{id}', [StatusMahasiswaController::class, 'destroy'])->name('status_mahasiswa.delete');
                });

                // Jenis Tinggal
                Route::prefix('JenisTinggal')->middleware(['permission:system:master_jenistinggal:view'])->group(function() {
                    Route::get('/', [JenisTinggalController::class, 'index'])->name('jenis_tinggal.index');
                    Route::post('/jenis-tinggal/store', [JenisTinggalController::class, 'store'])->name('jenis_tinggal.store');
                    Route::get('/jenis-tinggal/edit/{id}', [JenisTinggalController::class, 'edit'])->name('jenis_tinggal.edit');
                    Route::delete('/jenis-tinggal/delete/{id}', [JenisTinggalController::class, 'destroy'])->name('jenis_tinggal.delete');
                });

                // Transportasi
                Route::prefix('Transportasi')->middleware(['permission:system:master_transportasi:view'])->group(function() {
                    Route::get('/', [TransportasiController::class, 'index'])->name('transportasi.index');
                    Route::post('/transportasi/store', [TransportasiController::class, 'store'])->name('transportasi.store');
                    Route::get('/transportasi/edit/{id}', [TransportasiController::class, 'edit'])->name('transportasi.edit');
                    Route::delete('/transportasi/delete/{id}', [TransportasiController::class, 'destroy'])->name('transportasi.delete');
                });

                // Kebutuhan Khusus
                Route::prefix('KebutuhanKhusus')->middleware(['permission:system:master_kebutuhankhusus:view'])->group(function() {
                    Route::get('/', [KebutuhanKhususController::class, 'index'])->name('kebutuhan_khusus.index');
                    Route::post('/kebutuhan-khusus/store', [KebutuhanKhususController::class, 'store'])->name('kebutuhan_khusus.store');
                    Route::get('/kebutuhan-khusus/edit/{id}', [KebutuhanKhususController::class, 'edit'])->name('kebutuhan_khusus.edit');
                    Route::delete('/kebutuhan-khusus/delete/{id}', [KebutuhanKhususController::class, 'destroy'])->name('kebutuhan_khusus.delete');
                });
            });

            // Wilayah
            Route::prefix('Wilayah')->name('wilayah.')->group(function() {
                // Negara
                Route::prefix('Negara')->middleware(['permission:system:master_negara:view'])->group(function () {
                    Route::get('/', [NegaraController::class, 'index'])->name('negara.index');
                    Route::post('/store', [NegaraController::class, 'store'])->name('negara.store');
                    Route::get('/edit/{id}', [NegaraController::class, 'edit'])->name('negara.edit');
                    Route::delete('/delete/{id}', [NegaraController::class, 'destroy'])->name('negara.delete');
                });

                // Provinsi
                Route::prefix('Provinsi')->middleware(['permission:system:master_provinsi:view'])->group(function(){
                    Route::get('/', [ProvinsiController::class, 'index'])->name('provinsi.index');
                    Route::get('/TabelProvinsi', [ProvinsiController::class, 'TabelProvinsi'])->name('admin.Provinsi.Tabel');
                });

                // ====================================== Route Semesntara ===================================
                // Kabupaten
                Route::prefix('Kabupaten')->middleware(['permission:system:master_kabupaten:view'])->group(function(){
                    Route::get('/', [KabupatenController::class, 'index'])->name('kabupaten.index');
                    Route::get('/TabelKabupaten', [KabupatenController::class, 'TabelKabupaten'])->name('admin.Kabupaten.Tabel');
                });
                // ===========================================================================================

                // kecamatan
                Route::prefix('Kecamatan')->middleware(['permission:system:master_kecamatan:view'])->group(function(){
                    Route::get('/', [KecamatanController::class, 'index'])->name('kecamatan.index');
                    Route::get('/TabelKecamatan', [KecamatanController::class, 'TabelKecamatan'])->name('admin.Kecamatan.Tabel');
                });

                //Kelurahan
                Route::prefix('Kelurahan')->middleware(['permission:system:master_kelurahan:view'])->group(function(){
                    Route::get('/', [KelurahanController::class, 'index'])->name('kelurahan.index');
                    Route::get('/TabelKelurahan', [KelurahanController::class, 'TabelKelurahan'])->name('admin.Kelurahan.Tabel');
                });
            });

            // Settings
            Route::prefix('Settings')->name('setting.')->group(function() {
                // Kategori Kuesioner Layanan
                Route::prefix('KategoriKuesionerLayanan')->middleware(['permission:system:master_kategorikuesionerlayanan:view'])->group(function() {
                    Route::get('/', [KategoriKuesionerLayananController::class, 'index'])->name('kategori_kuesioner_layanan.index');
                    Route::post('/kategori-kuesioner-layanan/store', [KategoriKuesionerLayananController::class, 'store'])->name('kategori_kuesioner_layanan.store');
                    Route::get('/kategori-kuesioner-layanan/edit/{id}', [KategoriKuesionerLayananController::class, 'edit'])->name('kategori_kuesioner_layanan.edit');
                    Route::delete('/kategori-kuesioner-layanan/delete/{id}', [KategoriKuesionerLayananController::class, 'destroy'])->name('kategori_kuesioner_layanan.delete');
                });

                // Setting Prodi
                Route::prefix('SettingProdi')->middleware(['permission:system:master_settingprodi:view'])->group(function() {
                    Route::get('/', [SettingProdiController::class, 'index'])->name('setting_prodi.index');
                    Route::post('/setting-prodi/update', [SettingProdiController::class, 'update'])->name('setting_prodi.update');
                });

                // Periode Akademik
                Route::prefix('PeriodeAkademik')->middleware(['permission:system:master_periodeakademik:view'])->group(function() {
                    Route::get('/', [PeriodeAkademikController::class, 'index'])->name('periode_akademik.index');
                    Route::post('/periode-akademik/store', [PeriodeAkademikController::class, 'store'])->name('periode_akademik.store');
                    Route::get('/periode-akademik/edit/{id}', [PeriodeAkademikController::class, 'edit'])->name('periode_akademik.edit');
                    Route::delete('/periode-akademik/delete/{id}', [PeriodeAkademikController::class, 'destroy'])->name('periode_akademik.delete');

                    // Route Khusus Set Aktif
                    Route::post('/periode-akademik/set-active/{id}', [PeriodeAkademikController::class, 'setActive'])->name('periode_akademik.active');
                });
            });
        });

        // Neo Feeder
        Route::prefix('neo-feeder')->middleware(['auth'])->name('neo_feeder.')->group(function() {
            // Neo Feeder Auth
            Route::get('/login', [NeoFeederAuthController::class, 'showLoginForm'])->name('login');
            Route::post('/login', [NeoFeederAuthController::class, 'login'])->name('login.post');
            Route::post('/logout', [NeoFeederAuthController::class, 'logout'])->name('logout');

            // Landing Page Feeder
            Route::get('/home', [NeoFeederAuthController::class, 'dashboard'])->name('dashboard');


            // Neo Feeder Mahasiswa
            Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
                Route::get('/', [NeoFeederMahasiswasController::class, 'index'])->name('index');
                Route::prefix('json')->name('json.')->group(function () {
                    Route::get('/lokal', [NeoFeederMahasiswasController::class, 'jsonLokal'])->name('lokal');
                    Route::get('/sync', [NeoFeederMahasiswasController::class, 'jsonSync'])->name('sync');
                    Route::get('/feeder', [NeoFeederMahasiswasController::class, 'jsonFeeder'])->name('feeder');
                });

                // Sync Total Data (Start) & Eksekusi Per Batch (Looping)
                Route::prefix('sync')->name('sync.')->group(function () {
                    Route::post('init', [NeoFeederMahasiswasController::class, 'syncInit'])->name('init');
                    Route::post('exec', [NeoFeederMahasiswasController::class, 'syncExec'])->name('exec');

                });

                // Push Data Sync ke Neo Feeder (Start) & Eksekusi Per Batch (Looping)
                Route::prefix('push')->name('push.')->group(function () {
                    Route::post('init', [NeoFeederMahasiswasController::class, 'pushInit'])->name('init');
                    Route::post('exec', [NeoFeederMahasiswasController::class, 'pushExec'])->name('exec');
                });

                Route::delete('/{id}', [NeoFeederMahasiswasController::class, 'destroy'])->name('destroy');
            });
        });

        // System Navigation
        Route::prefix('system')->middleware(['auth'])->name('system.')->group(function() {
            // User
            Route::middleware(['permission:system:user:view'])->group(function() {
                Route::get('users/json', [UserController::class, 'datatable'])->name('user.json');
                Route::post('user/sync', [UserController::class, 'sync'])->name('user.sync'); // Route Sync
                Route::resource('user', UserController::class);
            });

            // Role
            Route::middleware(['permission:system:role:view'])->group(function() {
                Route::get('role/json', [RoleController::class, 'datatable'])->name('role.json');
                Route::post('role/sync', [RoleController::class, 'sync'])->name('role.sync'); // Route Sync
                Route::resource('role', RoleController::class);
            });

            // Permissions
            Route::middleware(['permission:system:permission:view'])->group(function() {
                Route::get('permission/json', [PermissionController::class, 'datatable'])->name('permission.json');
                Route::resource('permission', PermissionController::class)->except(['create', 'edit', 'show']);
            });

            // Menu
            Route::middleware(['permission:system:menu:view'])->group(function() {
                Route::get('menu/json', [MenuController::class, 'datatable'])->name('menu.json');
                Route::resource('menu', MenuController::class);
            });
        });

//        //Setting
//        Route::prefix('setting')->middleware(['auth'])->name('setting.')->group(function(){
//            //User Management
//            Route::get('/usermanagement', [SettingController::class, 'userManagement'])->name('show.userManagement');
//            Route::get('/tabelPegawai', [SettingController::class, 'table_pegawai'])->name('show.tabelPegawai');
//            Route::get('/tabelMahasiswa', [SettingController::class, 'table_mahasiswa'])->name('show.tabelMahasiswa');
//            Route::get('/finduser', [SettingController::class, 'searchNama'])->name('show.finduser');
//            Route::post('/StoreUser', [SettingController::class, 'StoreUser'])->name('show.saveUser');
//            Route::get('/detailuser/{params}', [SettingController::class, 'DetailUser'])->name('show.detailuser');
//            Route::get('/deleteuser/{params}', [SettingController::class, 'DeleteUser'])->name('show.deleteuser');
//
//            //User Reset
//            Route::get('/userreset', [SettingController::class, 'UserReset'])->name('UserReset.show');
//            Route::get('/userreset_tabelPegawai', [SettingController::class, 'UserReset_TablePegawai'])->name('UserReset.tabelPegawai');
//            Route::get('/userreset_tabelMahasiswa', [SettingController::class, 'UserReset_TableMahasiswa'])->name('UserReset.tabelMahasiswa');
//            Route::get('/ResetPassword/{params}', [SettingController::class, 'ResetPassword'])->name('UserReset.ResetPassword');
//            Route::get('/ResetQA/{params}', [SettingController::class, 'ResetQA'])->name('UserReset.ResetQA');
//
//            //List Menu
//            Route::get('/ShowMenu', [SettingController::class, 'ShowMenu'])->name('menu.show');
//            Route::get('/LisMenu', [SettingController::class, 'table_menu'])->name('menu.TabelMenu');
//            Route::post('/SaveUpdateMenu', [SettingController::class, 'SaveUpdateMenu'])->name('menu.SaveMenu');
//            Route::get('/GetMenu/{params}', [SettingController::class, 'GetMenu'])->name('menu.GetMenu');
//            Route::get('/DeleteAktif/{params1}/{params2}', [SettingController::class, 'DeleteMenu'])->name('menu.DeleteAktif');
//
//            //Group User
//            Route::get('/ShowGroupUser', [SettingController::class, 'ShowGroupUser'])->name('gruopuser.show');
//            Route::get('/LisGroupUser', [SettingController::class, 'table_groupuser'])->name('gruopuser.TabelGroupUser');
//            Route::post('/SaveUpdateGroupUser', [SettingController::class, 'SaveUpdateGroupUser'])->name('gruopuser.Save');
//            Route::get('/GetGroupUser/{params}', [SettingController::class, 'GetGroupUser'])->name('gruopuser.GetGroupUser');
//            Route::get('/ShowPrivilege/{params}', [SettingController::class, 'ShowPrivilege'])->name('gruopuser.ShowPrivilege');
//            Route::post('/SavePrivilege/{params}', [SettingController::class, 'StorePrivilege'])->name('gruopuser.SavePrivilege');
//        });
    });
});
