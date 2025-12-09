<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class StoreObatNonRacik
{
    protected string $baseUrl;
    protected getAccessToken $tokenService;

    public function __construct(getAccessToken $tokenService)
    {
        $this->baseUrl = config('services.satusehat.base_url') . '/Medication';
        $this->tokenService = $tokenService;
    }

    /**
     * POST Medication Obat Non Racik
     */
    public function handle($kfaKodeAktual, $kfaNamaDagang, $kfaKodeVirtual, $kfaNamaVirtual) {
        try {

            $token = $this->tokenService->getAccessToken();

            $payload = [
                "resourceType" => "Medication",
                "code" => [
                    "coding" => [
                        [
                            "system"  => "http://sys-ids.kemkes.go.id/kfa",
                            "code"    => $kfaKodeAktual,
                            "display" => $kfaNamaDagang,
                        ]
                    ]
                ],
                "identifier" => [
                    [
                        "system" => "http://sys-ids.kemkes.go.id/medication/" . config('services.satusehat.org_id'),
                        "use"    => "official",
                        "value"  => $kfaKodeAktual . '-' . uniqid(), // unique
                    ]
                ],
                "status" => "active",
                "ingredient" => [
                    [
                        "itemCodeableConcept" => [
                            "coding" => [
                                [
                                    "system"  => "http://sys-ids.kemkes.go.id/kfa",
                                    "code"    => $kfaKodeVirtual,
                                    "display" => $kfaNamaVirtual
                                ]
                            ]
                        ],
                        "isActive" => true,
                    ]
                ],
                "extension" => [
                    [
                        "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/MedicationType",
                        "valueCodeableConcept" => [
                            "coding" => [
                                [
                                    "system"  => "http://terminology.kemkes.go.id/CodeSystem/medication-type",
                                    "code"    => "NC",
                                    "display" => "Non-compound"
                                ]
                            ]
                        ]
                    ]
                ],
            ];

            Log::info("POST Medication Non Racik Payload", $payload);

            $response = Http::withToken($token)
                ->post($this->baseUrl, $payload);

            if ($response->failed()) {
                Log::error("Error POST Medication Non Racik", [
                    'body' => $response->body()
                ]);
                throw new Exception("Gagal POST Medication Non Racik: " . $response->body());
            }

            // return ID Medication
            return $response->json('id');

        } catch (\Exception $e) {
            Log::error("StoreObatNonRacik Error: " . $e->getMessage());
            throw $e;
        }
    }
}
