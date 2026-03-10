<?php

namespace App\Http\Controllers;

use App\Services\NeoFeederService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class NeoFeederAuthController extends Controller
{
    // Tambahkan Method Ini
    public function dashboard()
    {
        return view('system::neofeeder.dashboard', [
            'title' => 'Dashboard Integrasi',
            'token_info' => session('neofeeder_token')
        ]);
    }

    public function showLoginForm()
    {
        return view('system::neofeeder.auth.login', [
            'title' => 'Login Neo Feeder'
        ]);
    }

    public function login(Request $request, NeoFeederService $service)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        // Panggil Service untuk hit API GetToken
        $result = $service->login($request->username, $request->password);

        if ($result['success']) {
            $tujuan = session('url.intended') ?? route('neo_feeder.dashboard');
            session()->forget('url.intended');

            return redirect($tujuan)->with('success', 'Berhasil terhubung ke Neo Feeder!');
        }

        Log::error($result['message']);
        return back()->with('error', 'Gagal terhubung ke Neo Feeder! Silahkan hubungi PIKDI untuk tindak lanjut!');
    }

    public function logout()
    {
        Session::forget(['neofeeder_token', 'neofeeder_username']);
        return redirect()->route('neo_feeder.login')->with('success', 'Sesi Feeder diputus.');
    }
}
