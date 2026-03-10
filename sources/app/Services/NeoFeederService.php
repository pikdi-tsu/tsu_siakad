<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class NeoFeederService
{
    protected $url;

    public function __construct()
    {
        $this->url = config('app.neo_feeder.url');
    }

    /**
     * LOGIC LOGIN (Manual by User Input)
     */
    public function login($username, $password)
    {
        try {
            $response = Http::post($this->url, [
                'act' => 'GetToken',
                'username' => $username,
                'password' => $password
            ]);

            $json = $response->json();

            if (($json['error_code'] ?? 1) === 0) {
                $token = $json['data']['token'];

                // Simpan Token & Info User di Session
                Session::put('neofeeder_token', $token);
                Session::put('neofeeder_username', $username); // Biar tau siapa yg login

                return ['success' => true];
            } else {
                return [
                    'success' => false,
                    'message' => $json['error_desc'] ?? 'Username/Password Salah'
                ];
            }

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Koneksi Gagal: ' . $e->getMessage()];
        }
    }

    public function execute(string $act, array $params = [])
    {
        // Ambil Token dari Session
        $token = Session::get('neofeeder_token');

        if (!$token) {
            throw new \Exception("Token tidak ditemukan. Silakan login Feeder ulang.");
        }

        // Gabungkan payload standar (act & token) dengan params spesifik
        $payload = array_merge([
            'act'   => $act,
            'token' => $token,
        ], $params);

        // Tembak API
        try {
            $response = Http::timeout(10)->post($this->url, $payload)->json();

            // Cek Error Code level API Feeder
            if (isset($response['error_code']) && $response['error_code'] !== 0) {
                throw new \Exception("Feeder Error ({$response['error_code']}): " . ($response['error_desc'] ?? 'Unknown'));
            }

            return $response;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new \Exception("Gagal terhubung ke Server Feeder (Timeout/Down).");
        } catch (\Exception $e) {
            // Lempar error biar ditangkap Controller
            throw $e;
        }
    }
}
