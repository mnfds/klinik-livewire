<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GetPractitionerByNik
{
    protected string $baseUrl;
    protected getAccessToken $tokenService;

    public function __construct(getAccessToken $tokenService)
    {
        $this->baseUrl = config('services.satusehat.base_url') . '/Practitioner';
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

            Log::info('SATUSEHAT Practitioner Request', [
                'url' => "{$this->baseUrl}?identifier={$identifier}",
            ]);

            $response = Http::withToken($token)
                ->get($this->baseUrl, ['identifier' => $identifier]);

            if ($response->failed()) {
                Log::error('Gagal mencari Praktisi', ['message' => $response->body()]);
                throw new Exception("Error API SATUSEHAT: {$response->body()}");
            }

            $data = $response->json();

            if (!isset($data['entry'][0]['resource'])) {
                throw new Exception("Data Practitioner dengan NIK {$nik} tidak ditemukan.");
            }

            $resource = $data['entry'][0]['resource'];
            $identifiers = collect($resource['identifier'] ?? []);

            // Ambil IHS dari nakes-his-number
            $ihs = optional(
                $identifiers->firstWhere('system', 'https://fhir.kemkes.go.id/id/nakes-his-number')
            )['value'];

            // Jika masih kosong, ambil dari sys-ids.kemkes.go.id/practitioner
            if (!$ihs) {
                $ihs = optional(
                    $identifiers->firstWhere('system', 'http://sys-ids.kemkes.go.id/practitioner')
                )['value'];
            }

            // Jika masih kosong, fallback ke UUID (resource.id)
            $ihs = $ihs ?: ($resource['id'] ?? null);
            $nama = $resource['name'][0]['text'] ?? null;
            $gender = $resource['gender'] ?? null;
            $birthdate = $resource['birthDate'] ?? null;
            $id_satusehat = $resource['id'] ?? null;
            $address    = $resource['address'][0] ?? [];
            $city = $address['city'] ?? null;
            $address_line = $address['line'][0] ?? null;

            Log::info('Berhasil GET Praktisi Dari SATUSEHAT', [
                'nik'    => $nik,
                'no_ihs' => $ihs,
                'nama'   => $nama,
            ]);

            return [
                'no_ihs' => $ihs,
                'nama'   => $nama,
                'gender'   => $gender,
                'birthdate'   => $birthdate,
                'id_satusehat'   => $id_satusehat,
                'city'   => $city,
                'address_line'   => $address_line,
                'raw'    => $resource,
            ];

        } catch (Exception $e) {
            Log::error('Gagal mengambil data Praktisi SATUSEHAT', [
                'nik'   => $nik,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
