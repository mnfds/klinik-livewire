<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class StoreOrganization
{
    protected string $baseUrl;
    protected getAccessToken $tokenService;

    public function __construct(getAccessToken $tokenService)
    {
        $this->baseUrl = config('services.satusehat.base_url');
        $this->tokenService = $tokenService;
    }

    /**
     * POST data Organisasi pada Satu Sehat
     */
    public function createOrganization(array $payload)
    {
        try {
            $token = $this->tokenService->getAccessToken();

            $response = Http::withToken($token)
                ->withHeaders([
                    'Content-Type' => 'application/fhir+json',
                    'Accept' => 'application/fhir+json',
                    'x-sandbox' => 'true', // penting!
                ])
                ->post($this->baseUrl . '/Organization', $payload);

            Log::info('SATUSEHAT Create Organization Request', [
                'payload' => $payload,
                'response' => $response->json()
            ]);

            return $response->json();

        } catch (\Exception $e) {
            Log::error('SATUSEHAT Create Organization Error', [
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}