<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class StoreIntruksiObatNonRacik
{
    protected string $baseUrl;
    protected getAccessToken $tokenService;

    public function __construct(getAccessToken $tokenService)
    {
        $this->baseUrl = config('services.satusehat.base_url') . '/MedicationRequest';
        $this->tokenService = $tokenService;
    }

    /**
     * POST Medication Request Obat Non Racik (Intruksi Obat Non Racik)
     */
    public function handle($medicationId, $kfaNamaDagang, $encounterId, $pasienNama, $pasienIhs, $dokterNama, $dokterIhs, $waktuDiperiksa) {
        try {

            $token = $this->tokenService->getAccessToken();

            $payload = [
                "resourceType" => "MedicationRequest",
                "identifier" => [
                    [
                        "system"  => "http://sys-ids.kemkes.go.id/prescription/" . config('services.satusehat.org_id'),
                        "use"    => "official",
                        "value" => 'MR-' . $encounterId . '-' . uniqid(),
                    ],
                    [
                        "system"  => "http://sys-ids.kemkes.go.id/prescription-item/" . config('services.satusehat.org_id'),
                        "use"    => "official",
                        "value" => 'MR-ITEM' . $encounterId . '-' . uniqid(),
                    ],
                ],
                "status" => "completed",
                "intent" => "order",
                "category" => [
                    [
                        "coding" => [
                            [
                                "system"  => "http://terminology.hl7.org/CodeSystem/medicationrequest-category",
                                "code"    => "community",
                                "display" => "Community",
                            ]
                        ]
                    ]
                ],
                "medicationReference" => [
                    "reference" => "Medication/" . $medicationId,
                    "display" => $kfaNamaDagang,
                ],
                "subject" => [
                    "reference" => "Patient/" . $pasienIhs,
                    "display" => $pasienNama,
                ],
                "encounter" => [
                    "reference" => "Encounter/" . $encounterId,
                ],
                "authoredOn" => $waktuDiperiksa,
                "requester" => [
                    "reference" => "Practitioner/" . $dokterIhs,
                    "display" => $dokterNama,
                ],
            ];

            Log::info("POST Medication Request Obat Non Racik Payload", $payload);

            $response = Http::withToken($token)
                ->post($this->baseUrl, $payload);

            if ($response->failed()) {
                Log::error("Error POST Medication Request Obat Non Racik", [
                    'body' => $response->body()
                ]);
                throw new Exception("Gagal POST Medication Request Obat Non Racik: " . $response->body());
            }

            // return ID Medication
            return $response->json('id');

        } catch (\Exception $e) {
            Log::error("StoreIntruksiObatNonRacik Error: " . $e->getMessage());
            throw $e;
        }
    }
}
