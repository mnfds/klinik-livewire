<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class getAccessToken
{
    protected string $authUrl;
    protected string $clientId;
    protected string $clientSecret;

    public function __construct()
    {
        $this->authUrl = config('services.satusehat.auth_url');
        $this->clientId = config('services.satusehat.client_id');
        $this->clientSecret = config('services.satusehat.client_secret');
    }

    /**
     * Ambil access token dari cache atau refresh jika sudah kedaluwarsa
     */
    public function getAccessToken(): string
    {
        return Cache::remember('satusehat_access_token', 3500, function () {
            $url = "{$this->authUrl}/accesstoken?grant_type=client_credentials";

            $response = Http::asForm()->post($url, [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            if ($response->failed()) {
                Log::error('Gagal mendapatkan token SATUSEHAT', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new \Exception('Gagal mendapatkan token SATUSEHAT: ' . $response->body());
            }

            $token = $response->json('access_token');

            Log::info('SATUSEHAT Access Token diperbarui', ['token_preview' => substr($token, 0, 20) . '...']);

            return $token;
        });
    }
}