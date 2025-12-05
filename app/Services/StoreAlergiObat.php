<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class StoreAlergiObat
{
    protected string $baseUrl;
    protected getAccessToken $tokenService;

    public function __construct(getAccessToken $tokenService)
    {
        $this->baseUrl = config('services.satusehat.base_url') . '/AllergyIntolerance';
        $this->tokenService = $tokenService;
    }

    /**
     * POST Alergi ke Satu Sehat
     */
    public function handle(
        $encounterId, $pasienNama, $pasienIhs,
        $dokterNama, $dokterIhs,
        array $obatAlergiList
    ) {
        try {
            $token = $this->tokenService->getAccessToken();

            // Loop semua ICD
            foreach ($obatAlergiList as $alergiobat) {

                $payload = [
                    "resourceType" => "AllergyIntolerance",
                    "category" => ["medication"],
                    "clinicalStatus" => [
                        "coding" => [
                            [
                                "system" => "http://terminology.hl7.org/CodeSystem/allergyintolerance-clinical",
                                "code" => "active",
                                "display" => "Active"
                            ]
                        ],
                    ],
                    "code" => [
                        "coding" => [
                            [
                                "system" => "http://sys-ids.kemkes.go.id/kfa",
                                "code" => $alergiobat['kode_kfa_aktual'],
                                "display" => $alergiobat['nama_obat_aktual'],
                            ]
                        ],
                    ],
                    "patient" => [
                        "reference" => "Patient/" . $pasienIhs,
                        "display" => $pasienNama
                    ],
                    "encounter" => [
                        "reference" => "Encounter/" . $encounterId
                    ],
                    "recorder" => [                         // ← WAJIB ADA!
                        "reference" => "Practitioner/" . $dokterIhs,
                        "display"  => $dokterNama
                    ]
                ];

                Log::info("POST AllergyIntolerance Obat Payload", $payload);

                $response = Http::withToken($token)
                    ->post($this->baseUrl, $payload);

                if ($response->failed()) {
                    Log::error("Error POST AllergyIntolerance Obat", ['body' => $response->body()]);
                    throw new Exception("Gagal POST AllergyIntolerance Obat: " . $response->body());
                }
            }

            return true;

        } catch (\Exception $e) {
            Log::error("StoreAlergiObat Error: " . $e->getMessage());
            throw $e;
        }
    }

}