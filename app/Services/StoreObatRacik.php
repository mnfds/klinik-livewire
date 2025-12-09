<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class StoreObatRacik
{
    protected string $baseUrl;
    protected getAccessToken $tokenService;

    public function __construct(getAccessToken $tokenService)
    {
        $this->baseUrl = config('services.satusehat.base_url') . '/Medication';
        $this->tokenService = $tokenService;
    }

    /**
     * POST Medication Obat Racikan
     */
    public function handle(string $namaRacikan, array $ingredients)
    {
        try {
            $token = $this->tokenService->getAccessToken();

            $payload = [
                "resourceType" => "Medication",
                "status" => "active",
                "code" => [
                    "text" => $namaRacikan
                ],
                "identifier" => [
                    [
                        "system" => "http://sys-ids.kemkes.go.id/medication/" . config('services.satusehat.org_id'),
                        "value"  => 'RACIK-' . uniqid()
                    ]
                ],
                "extension" => [
                    [
                        "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/MedicationType",
                        "valueCodeableConcept" => [
                            "coding" => [
                                [
                                    "system"  => "http://terminology.kemkes.go.id/CodeSystem/medication-type",
                                    "code"    => "C",
                                    "display" => "Compound"
                                ]
                            ]
                        ]
                    ]
                ],
                "ingredient" => []
            ];

            // Loop isi bahan racikan
            foreach ($ingredients as $ing) {
                $payload["ingredient"][] = [
                    "itemCodeableConcept" => [
                        "coding" => [
                            [
                                "system"  => "http://sys-ids.kemkes.go.id/kfa",
                                "code"    => $ing['kfaKodeAktual'],
                                "display" => $ing['namaObatDagang']
                            ]
                        ]
                    ],
                    "isActive" => true
                ];
            }

            Log::info("POST Medication RACIKAN", $payload);

            $res = Http::withToken($token)->post($this->baseUrl, $payload);

            if ($res->failed()) {
                Log::error("Error POST Medication RACIKAN", [
                    'body' => $res->body()
                ]);
                throw new Exception("Gagal POST Medication RACIKAN: " . $res->body());
            }

            return $res->json('id');

        } catch (\Exception $e) {
            Log::error("StoreObatRacik Error: " . $e->getMessage());
            throw $e;
        }
    }
}
