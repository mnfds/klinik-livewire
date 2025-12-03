<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class StoreCondition
{
    protected string $baseUrl;
    protected getAccessToken $tokenService;

    public function __construct(getAccessToken $tokenService)
    {
        $this->baseUrl = config('services.satusehat.base_url') . '/Condition';
        $this->tokenService = $tokenService;
    }

    /**
     * POST Condition ke Satu Sehat
     */
    public function handle($encounterId, $pasienNama, $pasienIhs, $icdCode, $icdName)
    {
        try {
            $token = $this->tokenService->getAccessToken();
            $payload = [
                "resourceType" => "Condition",
                "clinicalStatus" => [
                    "coding" => [
                        [
                            "system" => "http://terminology.hl7.org/CodeSystem/condition-clinical",
                            "code" => "active",
                            "display" => "Active"
                        ]
                    ]
                ],
                "category" => [
                    [
                        "coding" => [
                            [
                                "system" => "http://terminology.hl7.org/CodeSystem/condition-category",
                                "code" => "encounter-diagnosis",
                                "display" => "Encounter Diagnosis"
                            ]
                        ]
                    ]
                ],
                "code" => [
                    "coding" => [
                        [
                            "system" => "http://hl7.org/fhir/sid/icd-10",
                            "code" => $icdCode,
                            "display" => $icdName
                        ]
                    ]
                ],
                "subject" => [
                    "reference" => "Patient/" . $pasienIhs, // Mengambil dari input no_ihs
                    "display" => $pasienNama// Mengambil dari input nama
                ],
                "encounter" => [
                    "reference" => "Encounter/" . $encounterId, // Sesuaikan dengan encounter ID yang benar
                ],
            ];

            Log::info("POST Condition ICD Payload", $payload);

            $response = Http::withToken($token)
                ->post($this->baseUrl, $payload);

            if ($response->failed()) {
                Log::error("Error POST Condition", ['body' => $response->body()]);
                throw new Exception("Gagal POST Condition: " . $response->body());
            }
            
            return $response->json('id');
            // return true;

        } catch (\Exception $e) {
            Log::error("StoreCondition Error: " . $e->getMessage());
            throw $e;
        }
    }
}