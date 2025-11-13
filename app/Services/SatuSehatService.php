<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SatuSehatService
{
    protected string $authUrl;
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected string $orgId;

    public function __construct()
    {
        $this->authUrl = config('services.satusehat.auth_url');
        $this->baseUrl = config('services.satusehat.base_url');
        $this->clientId = config('services.satusehat.client_id');
        $this->clientSecret = config('services.satusehat.client_secret');
        $this->orgId = config('services.satusehat.org_id');
    }

    /**
     * Ambil access token dari cache atau API
     */
    public function getAccessToken(): string
    {
        return Cache::remember('satusehat_access_token', 3500, function () {
            $response = Http::asForm()->post("{$this->authUrl}/accesstoken?grant_type=client_credentials", [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            if ($response->failed()) {
                throw new \Exception('Gagal mendapatkan token SATUSEHAT: ' . $response->body());
            }
            Log::info('SATUSEHAT Access Token Response', ['body' => $response->json()]);

            return $response->json('access_token');
        });
    }

    /**
     * Request ke API SATUSEHAT
     */
    public function request(string $method, string $endpoint, array $data = [])
    {
        $token = $this->getAccessToken();

        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');

        $response = Http::withToken($token)->$method($url, $data);

        if ($response->failed()) {
            throw new \Exception('Error API SATUSEHAT: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Contoh fungsi khusus untuk get patient
     */
    public function getPatientByNik(string $nik)
    {
        // $nikFhir = "https://fhir.kemkes.go.id/id/nik|{$nik}";
        // $url = "Patient?identifier={$nikFhir}";
        $url = 'Patient?identifier=' . urlencode("https://fhir.kemkes.go.id/id/nik|{$nik}");

        Log::info('SATUSEHAT Patient Request', ['url' => $url]);

        $response = $this->request('get', $url);

        if (isset($response['resourceType']) && $response['resourceType'] === 'OperationOutcome') {
            throw new \Exception('OperationOutcome dari SATUSEHAT: ' . json_encode($response));
        }

        return $response;
    }
}