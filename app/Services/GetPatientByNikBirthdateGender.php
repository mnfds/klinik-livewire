<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GetPatientByNikBirthdateGender
{
    protected string $baseUrl;
    protected getAccessToken $tokenService;

    public function __construct(getAccessToken $tokenService)
    {
        $this->baseUrl = config('services.satusehat.base_url') . '/Patient';
        $this->tokenService = $tokenService;
    }

    /**
     * Cari pasien dari SATUSEHAT berdasarkan:
     * Nama + Tanggal Lahir + Gender
     */
    public function handle(string $name, string $birthdate, string $gender): array
    {
        try {
            $token = $this->tokenService->getAccessToken();

            $query = [
                'name'      => $name,
                'gender'    => strtolower($gender), // female / male
                'birthdate' => $birthdate, // format: YYYY-MM-DD
            ];

            Log::info('SATUSEHAT Patient Request', [
                'url'   => $this->baseUrl,
                'query' => $query,
            ]);

            $response = Http::withToken($token)->get($this->baseUrl, $query);

            if ($response->failed()) {
                Log::error('Gagal mencari pasien', ['message' => $response->body()]);
                throw new Exception("Error API SATUSEHAT: {$response->body()}");
            }

            $data = $response->json();

            if (!isset($data['entry'][0]['resource'])) {
                throw new Exception('Data pasien tidak ditemukan.');
            }

            $patient = $data['entry'][0]['resource'];

            Log::info('BERHASIL GET PASIEN DARI SATUSEHAT', [
                'nama'   => $patient['name'][0]['text'] ?? null,
                'no_ihs' => $patient['id'] ?? null,
            ]);

            return [
                'no_ihs' => $patient['id'] ?? null,
                'nama'   => $patient['name'][0]['text'] ?? null,
                'raw'    => $patient,
            ];
        } catch (Exception $e) {
            Log::error('Gagal mengambil data pasien SATUSEHAT', [
                'params' => compact('name', 'birthdate', 'gender'),
                'error'  => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
