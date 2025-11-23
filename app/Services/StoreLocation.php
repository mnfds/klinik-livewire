<?php

namespace App\Services;

use App\Services\getAccessToken;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class StoreLocation
{
    protected string $baseUrl;
    protected getAccessToken $tokenService;

    public function __construct(getAccessToken $tokenService)
    {
        $this->baseUrl = config('services.satusehat.base_url');
        $this->tokenService = $tokenService;
    }

    /**
     * POST data Location ke SATUSEHAT
     */

    public function createLocation(array $payload)
    {
        try {
            $token = $this->tokenService->getAccessToken();

            $response = Http::withToken($token)
                ->withHeaders([
                    'Content-Type' => 'application/fhir+json',
                    'Accept' => 'application/fhir+json',
                    'x-sandbox' => 'true', // penting!
                ])
                ->post($this->baseUrl . '/Location', $payload);

            Log::info('SATUSEHAT Create Location Request', [
                'payload' => $payload,
                'response' => $response->json()
            ]);

            return $response->json();

        } catch (\Exception $e) {
            Log::error('SATUSEHAT Create Location Error', [
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
