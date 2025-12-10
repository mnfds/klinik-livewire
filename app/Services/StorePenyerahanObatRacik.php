<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class StorePenyerahanObatRacik
{
    protected string $baseUrl;
    protected getAccessToken $tokenService;

    public function __construct(getAccessToken $tokenService)
    {
        $this->baseUrl = config('services.satusehat.base_url') . '/MedicationDispense';
        $this->tokenService = $tokenService;
    }

    /**
     * POST Medication Dispense untuk Obat Racikan
     */
    public function handle(
        $encounterId, $medId, $medRequestId,
        $pasienNama, $pasienIhs,
        // $dokterNama, $dokterIhs,
        $waktuDisiapkan, $waktuDiserahkan,
        $medName
    ) {
        try {

            $token = $this->tokenService->getAccessToken();

            $payload = [
                "resourceType" => "MedicationDispense",
                "identifier" => [
                    [
                        "system"  => "http://sys-ids.kemkes.go.id/prescription/" . config('services.satusehat.org_id'),
                        "use"    => "official",
                        "value" => 'MD-' . $encounterId . '-' . uniqid(),
                    ],
                    [
                        "system"  => "http://sys-ids.kemkes.go.id/prescription-item/" . config('services.satusehat.org_id'),
                        "use"    => "official",
                        "value" => 'MD-ITEM-' . $encounterId . '-' . uniqid(),
                    ],
                ],
                "status" => "completed",
                "category" => [
                    "coding" => [
                        [
                            "system"  => "http://terminology.hl7.org/fhir/CodeSystem/medicationdispense-category",
                            "code"    => "community",
                            "display" => "Community",
                        ]
                    ]
                ],
                "medicationReference" => [
                    "reference" => "Medication/" . $medId,
                    "display" => $medName,
                ],
                "subject" => [
                    "reference" => "Patient/" . $pasienIhs,
                    "display" => $pasienNama,
                ],
                "context" => [
                    "reference" => "Encounter/" . $encounterId,
                ],
                // Apoteker
                // "performer" => [
                //     [
                //         "actor" => [
                //             "reference" => "Practitioner/" . $dokterIhs,
                //             "display" => $dokterNama,
                //         ]
                //     ],
                // ],
                "authorizingPrescription" => [
                    [
                        "reference" => "MedicationRequest/" . $medRequestId,
                    ]
                ],
                "whenPrepared"=> $waktuDisiapkan,
                "whenHandedOver"=> $waktuDiserahkan,
            ];

            Log::info("POST MedicationDispense RACIK Payload", $payload);

            $response = Http::withToken($token)->post($this->baseUrl, $payload);

            if ($response->failed()) {
                Log::error("Error MedicationDispense RACIK", [
                    'body' => $response->body()
                ]);
                throw new Exception("Gagal POST MedicationDispense RACIK: " . $response->body());
            }

            return $response->json('id');

        } catch (\Exception $e) {
            Log::error("StorePenyerahanObatRacik Error: " . $e->getMessage());
            throw $e;
        }
    }

}
