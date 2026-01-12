<?php

use Illuminate\Support\Facades\Route;
use Modules\System\Http\Controllers\DashboardController;
use Modules\System\Http\Controllers\HomeController;
use Modules\System\Http\Controllers\LoginController;
use Modules\System\Http\Controllers\masterdata\BatchPendaftaranController;
use Modules\System\Http\Controllers\masterdata\FakultasController;
use Modules\System\Http\Controllers\masterdata\JenjangController;
use Modules\System\Http\Controllers\masterdata\JurusanController;
use Modules\System\Http\Controllers\masterdata\KabupatenController;
use Modules\System\Http\Controllers\masterdata\KecamatanController;
use Modules\System\Http\Controllers\masterdata\KelurahanController;
use Modules\System\Http\Controllers\masterdata\ProvinsiController;
use Modules\System\Http\Controllers\masterdata\TarifUKTController;
use Modules\System\Http\Controllers\SettingController;

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

Route::prefix('')->group(function() {
    Route::get('/', [HomeController::class, 'index'])->name('indexing')->middleware('guest');
    Route::middleware(['web'])->group(function () {
        Route::get('login/mahasiswa', [LoginController::class, 'indexMahasiswa'])->name('login.mahasiswa')->middleware('guest');
        Route::get('login/dosen-tendik', [LoginController::class, 'indexDosenTendik'])->name('login.dosen-tendik')->middleware('guest');
        Route::post('login/mahasiswa', [LoginController::class, 'loginActionMahasiswa'])->name('login.action.mahasiswa');
        Route::post('login/dosen-tendik', [LoginController::class, 'loginActionDosenTendik'])->name('login.action.dosen-tendik');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('loginChance', [LoginController::class, 'loginChance'])->name('loginchance');
        Route::get('NewPassword', [LoginController::class, 'newPassword'])->name('NewPassword');
        Route::post('NewPasswordAction', [LoginController::class, 'newPasswordAction'])->name('NewPasswordAction');
        Route::get('checkbirthday', [LoginController::class, 'checkbirthday']);
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');

        //forgot password
        Route::get('mahasiswa/forgot-password', [LoginController::class, 'forgotPasswordMahasiswa'])->name('forgot_password.mahasiswa')->middleware('guest');
        Route::post('mahasiswa/forgot-password', [LoginController::class, 'actionSendLinkMahasiswa'])->name('forgot_password.send.mahasiswa');
        Route::get('dosen-tendik/forgot-password', [LoginController::class, 'forgotPasswordDosenTendik'])->name('forgot_password.dosen_tendik')->middleware('guest');
        Route::post('dosen-tendik/forgot-password', [LoginController::class, 'actionSendLinkDosenTendik'])->name('forgot_password.send.dosen_tendik');
        Route::get('reset-password/{type}', [LoginController::class, 'FormForgotPassword'])->name('forgot_password.form_reset')->middleware('guest');
        Route::post('reset-password/{type}', [LoginController::class, 'ForgotPasswordAction'])->name('forgot_password.action')->middleware('guest');
//        Route::get('/form_ForgotPassword/{params}', [LoginController::class, 'FormForgotPassword'])->name('ForgotPassword.formreset');
//        Route::post('/Action_ForgotPassword/{params}', [LoginController::class, 'ForgotPasswordAction'])->name('ForgotPassword.ActionReset');

//        Route::middleware(['checkadmin'])->group(function () {
//            Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

            Route::prefix('MasterData')->group(function() {
                Route::prefix('BatchPendaftaran')->group(function(){
                    Route::get('/', [BatchPendaftaranController::class, 'index'])->name('admin.BatchPendaftaran.show');
                    Route::get('/TabelBatch', [BatchPendaftaranController::class, 'TabelBatch'])->name('admin.BatchPendaftaran.Tabel');
                    Route::post('/Store', [BatchPendaftaranController::class, 'StoreBatch'])->name('admin.BatchPendaftaran.Store');
                    Route::get('/EditBatch/{params}', [BatchPendaftaranController::class, 'ShowBatch'])->name('admin.BatchPendaftaran.Edit');
                    Route::get('/Status/{params1}/{params2}', [BatchPendaftaranController::class, 'delete'])->name('admin.BatchPendaftaran.delete');
                });
                Route::prefix('Provinsi')->group(function(){
                    Route::get('/', [ProvinsiController::class, 'index'])->name('admin.Provinsi.show');
                    Route::get('/TabelProvinsi', [ProvinsiController::class, 'TabelProvinsi'])->name('admin.Provinsi.Tabel');
                });
                Route::prefix('Kabupaten')->group(function(){
                    Route::get('/', [KabupatenController::class, 'index'])->name('admin.Kabupaten.show');
                    Route::get('/TabelKabupaten', [KabupatenController::class, 'TabelKabupaten'])->name('admin.Kabupaten.Tabel');
                });
                Route::prefix('Kecamatan')->group(function(){
                    Route::get('/', [KecamatanController::class, 'index'])->name('admin.Kecamatan.show');
                    Route::get('/TabelKecamatan', [KecamatanController::class, 'TabelKecamatan'])->name('admin.Kecamatan.Tabel');
                });
                Route::prefix('Kelurahan')->group(function(){
                    Route::get('/', [KelurahanController::class, 'index'])->name('admin.Kelurahan.show');
                    Route::get('/TabelKelurahan', [KelurahanController::class, 'TabelKelurahan'])->name('admin.Kelurahan.Tabel');
                });
                Route::prefix('TarifUKT')->group(function(){
                    Route::get('/', [TarifUKTController::class, 'index'])->name('admin.TarifUKT.show');
                    Route::get('/TabelUKT', [TarifUKTController::class, 'TabelUKT'])->name('admin.TarifUKT.Tabel');
                    Route::post('/Store', [TarifUKTController::class, 'StoreUKT'])->name('admin.TarifUKT.Store');
                    Route::get('/EditUKT/{params}', [TarifUKTController::class, 'ShowUKT'])->name('admin.TarifUKT.Edit');
                    Route::get('/Status/{params1}/{params2}', [TarifUKTController::class, 'delete'])->name('admin.TarifUKT.delete');
                });
                Route::prefix('Fakultas')->group(function() {
                    Route::get('/', [FakultasController::class, 'index'])->name('admin.fakultas.show');
                    Route::get('/TabelFakultas', [FakultasController::class, 'table_fakultas'])->name('admin.fakultas.Tabel');
                    Route::post('/Store', [FakultasController::class, 'StoreFakultas'])->name('admin.fakultas.Store');
                    Route::get('/EditFakultas/{params}', [FakultasController::class, 'ShowFakultas'])->name('admin.fakultas.Edit');
                    Route::get('/Status/{params1}/{params2}', [FakultasController::class, 'delete'])->name('admin.fakultas.delete');
                });
                Route::prefix('Jurusan')->group(function() {
                    Route::get('/', [JurusanController::class, 'index'])->name('admin.Jurusan.show');
                    Route::get('/TabelJurusan', [JurusanController::class, 'table_jurusan'])->name('admin.Jurusan.Tabel');
                    Route::post('/Store', [JurusanController::class, 'StoreJurusan'])->name('admin.Jurusan.Store');
                    Route::get('/EditJurusan/{params}', [JurusanController::class, 'ShowJurusan'])->name('admin.Jurusan.Edit');
                    Route::get('/Status/{params1}/{params2}', [JurusanController::class, 'delete'])->name('admin.Jurusan.delete');
                });
                Route::prefix('JenjangPendidikan')->group(function() {
                    Route::get('/', [JenjangController::class, 'index'])->name('admin.Jenjang.show');
                    Route::get('/TabelJenjang', [JenjangController::class, 'table_Pendaftaran'])->name('admin.Jenjang.Tabel');
                    Route::post('/Store', [JenjangController::class, 'StoreJurusan'])->name('admin.Jenjang.Store');
                    Route::get('/EditJenjang/{params}', [JenjangController::class, 'ShowJurusan'])->name('admin.Jenjang.Edit');
                    Route::get('/Status/{params1}/{params2}', [JenjangController::class, 'delete'])->name('admin.Jenjang.delete');
                });
            });

            //Setting
            Route::prefix('setting')->group(function(){
                //Change Password
                Route::get('/changepassword', [SettingController::class, 'showChangePassword'])->name('show.changepassword');
                Route::post('/changepasswordsave', [SettingController::class, 'saveChangePassword'])->name('save.changepassword');

                //Edit Profile
                Route::get('/changeprofile', [SettingController::class, 'showEditProfile'])->name('show.changeprofile');
                Route::post('/changeprofilesave', [SettingController::class, 'saveEditProfile'])->name('save.changeprofile');

                //User Management
                Route::get('/usermanagement', [SettingController::class, 'userManagement'])->name('show.userManagement');
                Route::get('/tabelPegawai', [SettingController::class, 'table_pegawai'])->name('show.tabelPegawai');
                Route::get('/tabelMahasiswa', [SettingController::class, 'table_mahasiswa'])->name('show.tabelMahasiswa');
                Route::get('/finduser', [SettingController::class, 'searchNama'])->name('show.finduser');
                Route::post('/StoreUser', [SettingController::class, 'StoreUser'])->name('show.saveUser');
                Route::get('/detailuser/{params}', [SettingController::class, 'DetailUser'])->name('show.detailuser');
                Route::get('/deleteuser/{params}', [SettingController::class, 'DeleteUser'])->name('show.deleteuser');

                //User Reset
                Route::get('/userreset', [SettingController::class, 'UserReset'])->name('UserReset.show');
                Route::get('/userreset_tabelPegawai', [SettingController::class, 'UserReset_TablePegawai'])->name('UserReset.tabelPegawai');
                Route::get('/userreset_tabelMahasiswa', [SettingController::class, 'UserReset_TableMahasiswa'])->name('UserReset.tabelMahasiswa');
                Route::get('/ResetPassword/{params}', [SettingController::class, 'ResetPassword'])->name('UserReset.ResetPassword');
                Route::get('/ResetQA/{params}', [SettingController::class, 'ResetQA'])->name('UserReset.ResetQA');

                //List Menu
                Route::get('/ShowMenu', [SettingController::class, 'ShowMenu'])->name('menu.show');
                Route::get('/LisMenu', [SettingController::class, 'table_menu'])->name('menu.TabelMenu');
                Route::post('/SaveUpdateMenu', [SettingController::class, 'SaveUpdateMenu'])->name('menu.SaveMenu');
                Route::get('/GetMenu/{params}', [SettingController::class, 'GetMenu'])->name('menu.GetMenu');
                Route::get('/DeleteAktif/{params1}/{params2}', [SettingController::class, 'DeleteMenu'])->name('menu.DeleteAktif');

                //Group User
                Route::get('/ShowGroupUser', [SettingController::class, 'ShowGroupUser'])->name('gruopuser.show');
                Route::get('/LisGroupUser', [SettingController::class, 'table_groupuser'])->name('gruopuser.TabelGroupUser');
                Route::post('/SaveUpdateGroupUser', [SettingController::class, 'SaveUpdateGroupUser'])->name('gruopuser.Save');
                Route::get('/GetGroupUser/{params}', [SettingController::class, 'GetGroupUser'])->name('gruopuser.GetGroupUser');
                Route::get('/ShowPrivilege/{params}', [SettingController::class, 'ShowPrivilege'])->name('gruopuser.ShowPrivilege');
                Route::post('/SavePrivilege/{params}', [SettingController::class, 'StorePrivilege'])->name('gruopuser.SavePrivilege');
            });
        });
//    });
});
