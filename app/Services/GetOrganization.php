<?php

namespace App\Services;

use Exception;
use App\Services\getAccessToken;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class GetOrganization
{
    protected string $baseUrl;
    protected getAccessToken $tokenService;

    public function __construct(getAccessToken $tokenService)
    {
        $this->baseUrl = config('services.satusehat.base_url') . '/Organization';
        $this->tokenService = $tokenService;
    }

    /**
     * Ambil data organization berdasarkan partof
     */
    public function byPartOf(string $partOf): array
    {
        try {
            $token = $this->tokenService->getAccessToken();

            Log::info('SATUSEHAT Organization Request', [
                'url' => "{$this->baseUrl}?partof={$partOf}",
            ]);

            $response = Http::withToken($token)
                ->get($this->baseUrl, [
                    'partof' => $partOf
                ]);

            if ($response->failed()) {
                Log::error('Gagal mencari Organization', [
                    'message' => $response->body()
                ]);
                throw new Exception("Error API SATUSEHAT: {$response->body()}");
            }

            $data = $response->json();

            // Validasi hasil
            if (!isset($data['entry']) || count($data['entry']) === 0) {
                throw new Exception("Organization dengan partOf {$partOf} tidak ditemukan.");
            }

            // Mapping hasil menjadi array rapi
            $organizations = array_map(function ($entry) {
                $org = $entry['resource'];

                return [
                    'id'   => $org['id'] ?? null,
                    'name' => $org['name'] ?? null,
                    'city' => $org['address'][0]['city'] ?? null,
                    'line' => $org['address'][0]['line'][0] ?? null,
                    'postalCode' => $org['address'][0]['postalCode'] ?? null,
                    'telecom' => [
                        'phone' => $org['telecom'][0]['value'] ?? null,
                        'email' => $org['telecom'][1]['value'] ?? null,
                        'url' => $org['telecom'][2]['value'] ?? null,
                    ],
                    'active' => $org['active'] ?? null,
                    'raw' => $org, // opsional untuk debugging
                ];
            }, $data['entry']);

            return $organizations;

        } catch (Exception $e) {

            Log::error('Gagal mengambil data Organization SATUSEHAT', [
                'partOf' => $partOf,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
