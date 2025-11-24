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
                    'description'   => $loc['description'] ?? null,

                    'organization'  => $loc['managingOrganization']['reference'] ?? null,
                    // 'partOf'        => $loc['partOf']['reference'] ?? null,

                    'longitude'     => $loc['position']['longitude'] ?? null,
                    'latitude'      => $loc['position']['latitude'] ?? null,
                    'altitude'      => $loc['position']['altitude'] ?? null,

                    'address'       => $loc['address']['line'][0] ?? null,
                    'city'          => $loc['address']['city'] ?? null,
                    'postalCode'    => $loc['address']['postalCode'] ?? null,
                    'country'       => $loc['address']['country'] ?? null,
                    'address_use'   => $loc['address']['use'] ?? null,

                    'province_code' => $loc['address']['extension'][0]['extension'][0]['valueCode'] ?? null,
                    'city_code'     => $loc['address']['extension'][0]['extension'][1]['valueCode'] ?? null,
                    'district_code' => $loc['address']['extension'][0]['extension'][2]['valueCode'] ?? null,
                    'village_code'  => $loc['address']['extension'][0]['extension'][3]['valueCode'] ?? null,
                    'rt'            => $loc['address']['extension'][0]['extension'][4]['valueCode'] ?? null,
                    'rw'            => $loc['address']['extension'][0]['extension'][5]['valueCode'] ?? null,

                    'phone'         => collect($loc['telecom'] ?? [])
                                        ->firstWhere('system', 'phone')['value'] ?? null,
                    'email'         => collect($loc['telecom'] ?? [])
                                        ->firstWhere('system', 'email')['value'] ?? null,
                    'website'       => collect($loc['telecom'] ?? [])
                                        ->firstWhere('system', 'url')['value'] ?? null,

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
