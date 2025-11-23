<?php

namespace App\Services;

use Exception;
use App\Services\getAccessToken;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class GetLocation
{
    protected string $baseUrl;
    protected getAccessToken $tokenService;

    public function __construct(getAccessToken $tokenService)
    {
        $this->baseUrl = config('services.satusehat.base_url') . '/Location';
        $this->tokenService = $tokenService;
    }

    /**
     * SEARCH Location berdasarkan organization (uuid)
     */
    public function byOrganization(string $orgId): array
    {
        return $this->search(['organization' => $orgId]);
    }

    /**
     * GENERIC Search
     */
    protected function search(array $params): array
    {
        try {
            $token = $this->tokenService->getAccessToken();

            Log::info('SATUSEHAT Location Request', [
                'url' => $this->baseUrl,
                'params' => $params
            ]);

            $response = Http::withToken($token)
                ->get($this->baseUrl, $params);

            if ($response->failed()) {
                Log::error('Error mengambil Location', [
                    'message' => $response->body()
                ]);

                throw new Exception("Error API SATUSEHAT: " . $response->body());
            }

            $json = $response->json();

            if (!isset($json['entry'])) {
                throw new Exception("Location tidak ditemukan.");
            }

            return array_map(function ($entry) {
                $loc = $entry['resource'];
                // dd($loc);

                return [
                    'id'            => $loc['id'] ?? null,
                    'name'          => $loc['name'] ?? null,
                    'status'        => $loc['status'] ?? null,
                    'description'   => $loc['description'] ?? null,

                    'type'          => $loc['physicalType']['text'] ?? null,
                    'organization'  => $loc['managingOrganization']['reference'] ?? null,
                    'partOf'        => $loc['partOf']['reference'] ?? null,

                    'longitude'     => $loc['position']['longitude'] ?? null,
                    'latitude'      => $loc['position']['latitude'] ?? null,

                    'address'       => $loc['address']['line'][0] ?? null,
                    'city'          => $loc['address']['city'] ?? null,

                    'raw'           => $loc,
                ];
            }, $json['entry']);

        } catch (Exception $e) {

            Log::error('Gagal search Location SATUSEHAT', [
                'params' => $params,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
