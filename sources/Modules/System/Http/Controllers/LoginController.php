<?php

namespace Modules\System\Http\Controllers;

use App\Models\DataDosenTendik;
use App\Models\DataMahasiswa;
use App\Models\GroupUserModel;
use App\Models\MasterGroupModel;
use App\Models\ModulModel;
use App\Models\MahasiswaModel;
use App\Models\PegawaiModel;
use App\Models\SiakadMahasiswa;
use App\Models\UserDosenTendik;
use App\Models\UserMahasiswa;
use App\Models\UserResetPasswordModel;
use App\Models\User;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

use Illuminate\Support\Facades\Session, Crypt, DB;

class LoginController extends Controller
{
    use ThrottlesLogins;

    protected $maxAttempts = 5;
    protected $decayMinutes = 1;

    public function username()
    {
        return 'email';
    }

    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard')
                ->with('alert', ['title' => 'Info', 'message' => 'Anda sudah login.', 'status' => 'info']);
        }

        // SSO Block (IP Based)
        $ssoThrottleKey = 'sso-attempt:' . request()->ip();
        $ssoSeconds = 0;
        if (RateLimiter::tooManyAttempts($ssoThrottleKey, 5)) {
            $ssoSeconds = RateLimiter::availableIn($ssoThrottleKey);
            session()->now('error', "SECURITY LOCKDOWN: Tunggu <b id='sso-alert-timer'>$ssoSeconds</b> detik lagi.");
        }

        // Manual Block (Session Based)
        $manualSeconds = 0;
        if (session()->has('manual_block_until')) {
            $timeLeft = session('manual_block_until') - now()->timestamp;

            if ($timeLeft > 0) {
                $manualSeconds = $timeLeft;
                session()->now('error', "SECURITY LOCKDOWN: Tunggu <b id='sso-alert-timer'>$manualSeconds</b> detik lagi.");
            } else {
                session()->forget('manual_block_until');
            }
        }

        $data = [
            'title' => 'Login Administrator (Local)',
            'app_name' => config('app.name', 'Siakad TSU'),
            'existing_sso_seconds' => $ssoSeconds,
            'existing_manual_seconds' => $manualSeconds,
        ];

        return view('system::login.loginform', $data);
    }

    public function login(Request $request)
    {
        // Validasi Input
        $request->validate([
            'identity' => ['required'], // Bisa Email atau Username
            'password' => ['required'],
        ]);

        // Cek Throttling
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            $seconds = $this->limiter()->availableIn($this->throttleKey($request));
            session()->put('manual_block_until', now()->addSeconds($seconds)->timestamp);
            return back()
                ->with('error', "SECURITY LOCKDOWN: Tunggu <b id='sso-alert-timer'>$seconds</b> detik lagi.")
                ->with('retry_seconds_manual', $seconds)
                ->withInput($request->only('identity'));
        }

        // Tentukan Login Pakai Email atau Username
        $loginType = filter_var($request->identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Credentials
        $credentials = [
            $loginType  => $request->identity,
            'password'  => $request->password,
            'isactive' => 1 // Hanya user aktif yang boleh masuk
        ];

        // Eksekusi Login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $superAdminModule = 'super admin ' . config('app.module.name');
            $user = Auth::user();
            $roles = $user->getRoleNames()->toArray();
            $isMahasiswa = in_array('mahasiswa', $roles, true);

            $profil = null;
            if ($isMahasiswa) {
                $profil = DataMahasiswa::query()->where('user_id', $user->id)->first();
                $roleLabel = 'mahasiswa';
            } else {
                $profil = DataDosenTendik::query()->where('user_id', $user->id)->first();
                $roleLabel = $roles[0] ?? 'user';
            }

            if ($profil) {
                Session::put('active_role', $roleLabel);
                Session::put('active_profile_id', $profil->id);
                Session::put('active_identity', $profil->nim ?? $profil->nik ?? $profil->nidn ?? '-');
            } elseif ($user->email === config('app.pikdi.email') || in_array($superAdminModule, $roles, true)) {
                // Biarkan masuk mode darurat tanpa profil
                Session::put('active_role', 'super admin');
                Session::put('active_identity', 'ADMIN-PUSAT');
            } else {
                Auth::logout();
                return back()->with('error', 'Data Profil (Dosen/Mhs) tidak ditemukan. Hubungi Admin.');
            }

            // Bersihkan rate limiter dan session block
            session()->forget('manual_block_until');
            $this->clearLoginAttempts($request);

            return redirect()->route('dashboard')
                ->with('success', 'Login Berhasil!');
        }

        $this->incrementLoginAttempts($request);
        return back()->with('error', 'Username atau Password salah / Akun tidak aktif.')->withInput($request->only('identity'));
    }

    public function logout(Request $req)
    {
        Auth::logout();

        $req->session()->invalidate();
        $req->session()->regenerateToken();

        return redirect()->route('login')
            ->with('alert', ['title' => 'Success', 'message' => 'Anda berhasil logout.', 'status' => 'success']);
    }

    public function loginChance()
    {
        if(Session::has('login_chance')){
            $login_chance = Session::get('login_chance');

            if ($login_chance['chance'] > 0) {
                $chance = $login_chance['chance'] - 1;
                $data = array(
                    'chance'        => $chance,
                    'time_start'    => time(),
                );
                Session::put('login_chance', $data);

                return $chance;
            } elseif ($login_chance['time_start'] > (15)) {
                Session::forget('login_chance');
            } elseif ($login_chance['chance'] == 0) {
                $data = array(
                    'chance'        => 0,
                    'time_start'    => time(),
                );
                Session::put('login_chance', $data);
            }
        }else{
            $data = array(
                'chance'        => 5,
                'time_start'    => time(),
            );
            Session::put('login_chance', $data);

            return 5;
        }
    }

    public function newPassword()
    {
        $session = Session::get('tmp');
        if (!$session) return redirect(route('login.mahasiswa'));

        $data = [
            'title'      => 'New Password',
            'action'     => '#',
            'nik'        => $session['tmp_nik'],
            'nama'       => $session['tmp_nama'],
            'role'       => $session['tmp_role'],
            'question_1' => $this->question_1,
            'question_2' => $this->question_2,
        ];
        return view('system::login/newpassword', $data);
    }

    public function newPasswordAction(Request $post)
    {
        $session = Session::get('tmp');
        if (!$session) return redirect(route('login.mahasiswa'));

        $data = [
            'password'  => Hash::make($post->password),
            'q1' => $post->q_1, 'a1' => $post->a_1,
            'q2' => $post->q_2, 'a2' => $post->a_2,
        ];

        // Validasi manual sederhana
        if ($post->nik && $post->password && $post->q_1 && $post->a_1 && $post->q_2 && $post->a_2) {
            $update = false;

            // Gunakan Session role untuk menentukan tabel target
            if ($session['tmp_role'] === 'MAHASISWA') {
                $update = UserMahasiswa::where('nim', $post->nik)->update($data);
            } elseif ($session['tmp_role'] === 'DOSEN_TENDIK') {
                $update = UserDosenTendik::where('nik', $post->nik)->update($data);
            }

            if ($update) {
                Session::forget('tmp');
                return redirect(route('login.mahasiswa'))->with('alert', ['title' => 'Berhasil', 'message' => 'Password Berhasil Diganti, Silahkan Login ulang!', 'status' => 'success']);
            }
        }

        return redirect()->route('NewPassword')->with('alert', ['title' => 'Gagal', 'message' => 'Gagal mengganti password. Silakan lengkapi data.', 'status' => 'danger']);
    }

//    function checkTimeChance(){
//        if(Session::has('login_chance')){
//            $login_chance = Session::get('login_chance');
//            if ($login_chance['chance'] == 0) {
//                $chance = date('H:i:s', strtotime('+30 second', $login_chance['time_start']));
//                if (time() >= strtotime($chance)) {
//                    Session::forget('login_chance');
//                }else{
//                    Session::put('time_chance', strtotime('+30 second', $login_chance['time_start']) - time());
//                }
//            }
//        }
//    }

    public function checkbirthday(Request $get){
        $birthday = $get->birthday;
        $nik      = $get->nik;
        $role     = $get->role;

        $cekrole = MasterGroupModel::where('KodeGroupUser', $role)->first();

        if ($cekrole->NamaGroup === 'MAHASISWA') {
            $cek1 = MahasiswaModel::where('nim', $nik)->first();
            $tgl = $cek1->tgl_lahir;
        } else {
            $cek1 = PegawaiModel::where('nip', $nik)->first();
            $tgl = $cek1->tgl_lahir;
        }
        if (strtotime($tgl) == strtotime($birthday)) {
            return '1';
        } else {
            return '0';
        }
    }

    public function forgotPasswordMahasiswa()
    {
        return view('system::login.Mahasiswa.forgot_password', [
            'title' => 'Lupa Password Mahasiswa',
            'action_url' => route('forgot_password.send.mahasiswa') // Pastikan route ini ada
        ]);
    }

    public function forgotPasswordDosenTendik()
    {
        return view('system::login.DosenTendik.forgot_password', [
            'title' => 'Lupa Password Dosen & Tendik',
            'action_url' => route('forgot_password.send.dosen_tendik')
        ]);
    }

    public function actionSendLinkMahasiswa(Request $post)
    {
        return $this->handleSendLink($post, 'mahasiswa');
    }

    public function actionSendLinkDosenTendik(Request $post)
    {
        return $this->handleSendLink($post, 'dosen_tendik');
    }

    private function handleSendLink(Request $post, string $type)
    {
        $post->validate(['email' => 'required|email']);

        // Tentukan konfigurasi
        if ($type === 'mahasiswa') {
            $model = UserMahasiswa::class;
            $id_col = 'nim';
            $redirectBack = route('forgot_password.mahasiswa');
            $loginRoute = route('login.mahasiswa');
        } else {
            $model = UserDosenTendik::class;
            $id_col = 'nik';
            $redirectBack = route('forgot_password.dosen_tendik');
            $loginRoute = route('login.dosen-tendik');
        }

        $user = $model::query()->where('email', $post->email)->where('isactive', 1)->first();

        if (!$user) {
            return redirect($redirectBack)->with('alert', ['title' => 'Gagal', 'message' => 'Email tidak ditemukan di data ' . ucfirst($type), 'status' => 'danger']);
        }

        $id_val = $user->$id_col;

        // Token Tetap Terenkripsi (Untuk Validasi Keamanan)
        $enc_param = encrypt($id_val . '##' . $type);

        // route('name', ['type' => ..., 'params' => ...])
        $resetLink = route('forgot_password.form_reset', ['type' => $type, 'token' => $enc_param]);

        UserResetPasswordModel::query()->insert([
            'email'    => $user->email,
            'token'    => $post->_token,
            'activity' => 'Forgot Password ' . ucfirst($type)
        ]);

        $nama = $user->name;
        if ($type === 'mahasiswa' && $user->profil) {
            $nama = $user->profil->nama_lengkap;
        }

        $emailData = $nama . '##' . $id_val . '##' . $resetLink . '##' . $loginRoute;

        SendEmail($user->email, $nama, $emailData, 'Reset Password', 'Reset Password Siakad');

        $model::query()->where($id_col, $id_val)->update([
            'forgot_password_send_email' => 1,
            'updated_at' => now(),
            'updated_by' => $id_val
        ]);

        return redirect($redirectBack)->with('alert', ['title' => 'Sukses', 'message' => 'Silahkan cek email Anda untuk reset password.', 'status' => 'success']);
    }

    public function FormForgotPassword(Request $request, $type)
    {
        // Tentukan Route Login untuk Redirect Error
        $loginRoute = ($type === 'mahasiswa') ? route('login.mahasiswa') : route('login.dosen-tendik');

        // Ambil token dari URL (?token=...)
        $params = $request->query('token');

        if (!$params) {
            return redirect($loginRoute)->with('alert', ['title' => 'Error', 'message' => 'Token tidak ditemukan.', 'status' => 'danger']);
        }

        try {
            // Bongkar Token
            $decrypted = decrypt($params);
            [$id_val, $token_type] = explode('##', $decrypted);

            // Validasi Tipe
            if ($type !== $token_type) {
                throw new \RuntimeException("Type mismatch");
            }

            // Cari User
            $model = ($type === 'mahasiswa') ? UserMahasiswa::class : UserDosenTendik::class;
            $col   = ($type === 'mahasiswa') ? 'nim' : 'nik';

            $user = $model::query()->where($col, $id_val)->select('email', "$col as nik_or_nim", 'forgot_password_send_email')->first();

            if (!$user || $user->forgot_password_send_email != 1) {
                return redirect($loginRoute)->with('alert', ['title' => 'Error', 'message' => 'Link sudah kedaluwarsa.', 'status' => 'danger']);
            }

            $viewName = ($type === 'mahasiswa')
                ? 'system::login/Mahasiswa/form_forgot_password'
                : 'system::login/DosenTendik/form_forgot_password';

            return view($viewName, [
                'title'  => 'Form Reset Password',
                'data'   => $user,
                'params' => $params, // Kirim token ke view buat di-submit lagi
                'type'   => $type
            ]);

        } catch (\Exception $e) {
            return redirect($loginRoute)->with('alert', ['title' => 'Error', 'message' => 'Link tidak valid.', 'status' => 'danger']);
        }
    }

    public function ForgotPasswordAction(Request $request, $type)
    {
        $loginRoute = ($type === 'mahasiswa') ? route('login.mahasiswa') : route('login.dosen-tendik');

        $params = $request->input('params') ?? $request->query('token');

        try {
            [$id_val, $token_type] = explode('##', decrypt($params));

            if ($type !== $token_type) {
                throw new \RuntimeException("Type mismatch");
            }

            $model = ($type === 'mahasiswa') ? UserMahasiswa::class : UserDosenTendik::class;
            $col   = ($type === 'mahasiswa') ? 'nim' : 'nik';

            $update = $model::where($col, $id_val)->where('email', $request->email)->update([
                'password' => Hash::make($request->password),
                'forgot_password_send_email' => '0',
                'updated_at' => now()
            ]);

            if ($update) {
                return redirect($loginRoute)->with('alert', ['title' => 'Sukses', 'message' => 'Password berhasil diubah. Silahkan Login.', 'status' => 'success']);
            }

            return back()->with('alert', ['title' => 'Gagal', 'message' => 'Gagal update password. Email tidak cocok.', 'status' => 'danger']);

        } catch (\Exception $e) {
            return redirect($loginRoute)->with('alert', ['title' => 'Error', 'message' => 'Terjadi kesalahan token.', 'status' => 'danger']);
        }
    }

//    public function FormForgotPassword($params)
//    {
//        try {
//            list($id_val, $type) = explode('##', decrypt($params));
//
//            $model = ($type === 'mahasiswa') ? UserMahasiswa::class : UserDosenTendik::class;
//            $col   = ($type === 'mahasiswa') ? 'nim' : 'nik';
//
//            $user = $model::where($col, $id_val)->select('email', "$col as nik_or_nim", 'forgotpassword_sendemail')->first();
//
//            if (!$user || $user->forgotpassword_sendemail != 1) throw new \Exception("Invalid Token");
//
//            return view('system::login/form_forgotpassword', ['title' => 'Form Reset', 'data' => $user, 'params' => $params]);
//
//        } catch (\Exception $e) {
//            return redirect(route('login.mahasiswa'))->with('alert', ['title' => 'Error', 'message' => 'Link tidak valid.', 'status' => 'error']);
//        }
//    }

//    public function ForgotPasswordAction(Request $post, $params)
//    {
//        try {
//            list($id_val, $type) = explode('##', decrypt($params));
//
//            $model = ($type === 'mahasiswa') ? UserMahasiswa::class : UserDosenTendik::class;
//            $col   = ($type === 'mahasiswa') ? 'nim' : 'nik';
//
//            $update = $model::where($col, $id_val)->where('email', $post->email)->update([
//                'password' => Hash::make($post->password),
//                'forgotpassword_sendemail' => '0',
//                'updated_at' => now()
//            ]);
//
//            if ($update) return redirect(route('login.mahasiswa'))->with('alert', ['title' => 'Sukses', 'message' => 'Password berhasil diubah.', 'status' => 'success']);
//
//            return back()->with('alert', ['title' => 'Gagal', 'message' => 'Gagal update password.', 'status' => 'error']);
//
//        } catch (\Exception $e) {
//            return redirect(route('login.mahasiswa'))->with('alert', ['title' => 'Error', 'message' => 'Link expired.', 'status' => 'error']);
//        }
//    }

}
