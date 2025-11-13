<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GetPatientByNik
{
    protected string $baseUrl;
    protected getAccessToken $tokenService;

    public function __construct(getAccessToken $tokenService)
    {
        $this->baseUrl = config('services.satusehat.base_url') . '/Patient';
        $this->tokenService = $tokenService;
    }

    /**
     * Ambil data pasien dari SATUSEHAT berdasarkan NIK
     */
    public function handle(string $nik): array
    {
        try {
            $token = $this->tokenService->getAccessToken();
            $identifier = "https://fhir.kemkes.go.id/id/nik|{$nik}";

            Log::info('SATUSEHAT Patient Request', [
                'url' => "{$this->baseUrl}?identifier={$identifier}",
            ]);

            $response = Http::withToken($token)
                ->get($this->baseUrl, ['identifier' => $identifier]);

            if ($response->failed()) {
                Log::error('Gagal mencari pasien', ['message' => $response->body()]);
                throw new Exception("Error API SATUSEHAT: {$response->body()}");
            }

            $data = $response->json();

            // Cek apakah hasilnya ada
            if (!isset($data['entry'][0]['resource'])) {
                throw new Exception("Data pasien dengan NIK {$nik} tidak ditemukan.");
            }

            $patient = $data['entry'][0]['resource'];

            return [
                'no_ihs' => $patient['id'] ?? null,
                'nama'   => $patient['name'][0]['text'] ?? null,
                'raw'    => $patient, // opsional: data lengkap
            ];
            Log::info('Berhasil GET Pasien Dari SATUSEHAT', [
                'nama' => $patient['name'][0]['text'] ?? null,
                'nik'  => $nik,
                'no_ihs' => $patient['id'] ?? null,
            ]);
        } catch (Exception $e) {
            Log::error('Gagal mengambil data pasien SATUSEHAT', [
                'nik' => $nik,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
