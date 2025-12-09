<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class StoreIntruksiObatRacik
{
    protected string $baseUrl;
    protected getAccessToken $tokenService;

    public function __construct(getAccessToken $tokenService)
    {
        $this->baseUrl = config('services.satusehat.base_url') . '/MedicationRequest';
        $this->tokenService = $tokenService;
    }

    /**
     * POST MedicationRequest untuk Obat Racikan
     */
    public function handle(
        string $medicationId,
        string $namaRacikan,
        string $encounterId,
        string $pasienNama,
        string $pasienIhs,
        string $dokterNama,
        string $dokterIhs,
        string $waktuDiperiksa,
        ?string $aturanPakai = null,
        ?string $dosis = null,
        ?int $jumlahHari = null,
        ?int $jumlahRacikan = null
    ) {
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
                        "value" => 'MR-ITEM-' . $encounterId . '-' . uniqid(),
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
                    "display" => $namaRacikan,
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

            Log::info("POST MedicationRequest RACIK Payload", $payload);

            $response = Http::withToken($token)->post($this->baseUrl, $payload);

            if ($response->failed()) {
                Log::error("Error MedicationRequest RACIK", [
                    'body' => $response->body()
                ]);
                throw new Exception("Gagal POST MedicationRequest RACIK: " . $response->body());
            }

            return $response->json('id');

        } catch (\Exception $e) {
            Log::error("StoreIntruksiObatRacik Error: " . $e->getMessage());
            throw $e;
        }
    }

}
