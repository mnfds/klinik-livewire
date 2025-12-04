<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class StoreObatDikonsumsi
{
    protected string $baseUrl;
    protected getAccessToken $tokenService;

    public function __construct(getAccessToken $tokenService)
    {
        $this->baseUrl = config('services.satusehat.base_url') . '/MedicationStatement';
        $this->tokenService = $tokenService;
    }

    /**
     * POST Obat Dikonsumsi ke Satu Sehat
     */
    public function handle(
        $encounterId, $pasienNama, $pasienIhs,
        array $obatKonsumsiList
    ) {
        try {
            $token = $this->tokenService->getAccessToken();

            // Loop semua ICD
            foreach ($obatKonsumsiList as $obatkonsumsi) {

                $payload = [
                    "resourceType" => "MedicationStatement",
                    "status" => "active",
                    "category" => [
                        "coding" => [
                            [
                                "system" => "http://terminology.hl7.org/CodeSystem/medication-statement-category",
                                "code" => "community",
                                "display" => "Community"
                            ]
                        ]
                    ],
                    "medicationCodeableConcept" => [
                        "coding" => [
                            [
                                "system" => "http://sys-ids.kemkes.go.id/kfa",
                                "code" => $obatkonsumsi['kode_kfa_aktual'],
                                "display" => $obatkonsumsi['nama_obat_aktual']
                            ]
                        ],
                    ],
                    "subject" => [
                        "reference" => "Patient/" . $pasienIhs,
                        "display" => $pasienNama
                    ],
                    "informationSource" => [
                        "reference" => "Patient/" . $pasienIhs,
                        "display" => $pasienNama
                    ],
                    "context" => [
                        "reference" => "Encounter/" . $encounterId
                    ],
                ];

                Log::info("POST MEDICATION STATEMENT Obat Dikonsumsi Payload", $payload);

                $response = Http::withToken($token)
                    ->post($this->baseUrl, $payload);

                if ($response->failed()) {
                    Log::error("Error POST MEDICATION STATEMENT Obat Dikonsumsi", ['body' => $response->body()]);
                    throw new Exception("Gagal POST MEDICATION STATEMENT Obat Dikonsumsi: " . $response->body());
                }
            }

            return true;

        } catch (\Exception $e) {
            Log::error("StoreObatDikonsumsi Error: " . $e->getMessage());
            throw $e;
        }
    }

}