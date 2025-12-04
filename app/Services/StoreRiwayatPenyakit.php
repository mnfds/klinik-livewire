<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class StoreRiwayatPenyakit
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
    public function handle(
        $encounterId, $pasienNama, $pasienIhs,
        $dokterNama, $dokterIhs, $WaktuDiperiksa,
        array $icdList
    ) {
        try {
            $token = $this->tokenService->getAccessToken();

            // Loop semua ICD
            foreach ($icdList as $icd) {

                $payload = [
                    "resourceType" => "Condition",
                    "clinicalStatus" => [
                        "coding" => [
                            [
                                "system" => "http://terminology.hl7.org/CodeSystem/condition-clinical",
                                "code" => "inactive",
                                "display" => "Inactive"
                            ]
                        ]
                    ],
                    "category" => [
                        [
                            "coding" => [
                                [
                                    "system" => "http://terminology.kemkes.go.id",
                                    "code" => "previous-condition",
                                    "display" => "Previous Condition"
                                ]
                            ]
                        ]
                    ],
                    "code" => [
                        "coding" => [
                            [
                                "system" => "http://hl7.org/fhir/sid/icd-10",
                                "code" => $icd['code'],
                                "display" => $icd['name_en']
                            ]
                        ],
                    ],
                    "subject" => [
                        "reference" => "Patient/" . $pasienIhs,
                        "display" => $pasienNama
                    ],
                    "encounter" => [
                        "reference" => "Encounter/" . $encounterId
                    ],
                    "recordedDate" => $WaktuDiperiksa,
                    "recorder" => [
                        "reference" => "Practitioner/" . $dokterIhs,
                        "display" => $dokterNama
                    ],
                    "note" => [
                        [
                            "text" => "Memiliki riwayat " . $icd['name_id']
                        ]
                    ],
                ];

                Log::info("POST Condition Riwayat Penyakit Payload", $payload);

                $response = Http::withToken($token)
                    ->post($this->baseUrl, $payload);

                if ($response->failed()) {
                    Log::error("Error POST Condition Riwayat Penyakit", ['body' => $response->body()]);
                    throw new Exception("Gagal POST Condition Riwayat Penyakit: " . $response->body());
                }
            }

            return true;

        } catch (\Exception $e) {
            Log::error("StoreRiwayatPenyakit Error: " . $e->getMessage());
            throw $e;
        }
    }

}