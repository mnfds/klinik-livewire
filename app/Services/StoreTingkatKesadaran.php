<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class StoreTingkatKesadaran
{
    protected string $baseUrl;
    protected getAccessToken $tokenService;

    public function __construct(getAccessToken $tokenService)
    {
        $this->baseUrl = config('services.satusehat.base_url') . '/Observation';
        $this->tokenService = $tokenService;
    }

    /**
     * POST Observation TIngkat Kesadaran ke Satu Sehat
     */
    public function handle(
        $encounterId, $pasienNama, $pasienIhs,
        $dokterNama, $dokterIhs, $WaktuDiperiksa, 
        $tingkatKesadaran,
        )
    {
        try {
            $token = $this->tokenService->getAccessToken();
            $mapping = [
                "Sadar Baik/Alert" => [
                    "code" => "248234008",
                    "display" => "Mentally alert"
                ],
                "Berespon dengan kata-kata" => [
                    "code" => "300202002",
                    "display" => "Response to voice"
                ],
                "Hanya berespons jika dirangsang nyeri" => [
                    "code" => "450847001",
                    "display" => "Response to pain"
                ],
                "Pasien tidak sadar" => [
                    "code" => "422768004",
                    "display" => "Unresponsive"
                ],
                "Gelisah atau bingung" => [
                    "code" => "130987000",
                    "display" => "Acute confusion"
                ],
                "Acute Confusional States" => [
                    "code" => "2776000",
                    "display" => "Delirium"
                ],
            ];

            // Ambil sesuai value select, default ke Alert
            $dataKesadaran = $mapping[$tingkatKesadaran] ?? $mapping["Sadar Baik/Alert"];

            $payload = [
                "resourceType" => "Observation",
                "status" => "final",
                "category" => [
                    [
                        "coding" => [
                            [
                                "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                                "code" => "vital-signs",
                                "display" => "Vital Signs"
                            ]
                        ]
                    ]
                ],
                "code" => [
                    "coding" => [
                        [
                            "system" => "http://loinc.org",
                            "code" => "67775-7",
                            "display" => "Level of responsiveness"
                        ]
                    ]
                ],
                "subject" => [
                    "reference" => "Patient/" . $pasienIhs,
                    "display" => $pasienNama
                ],
                "encounter" => [
                    "reference" => "Encounter/" . $encounterId
                ],
                "effectiveDateTime" => $WaktuDiperiksa,
                "issued" => $WaktuDiperiksa,
                "performer" => [
                    [
                        "reference" => "Practitioner/" . $dokterIhs,
                        "display" => $dokterNama
                    ]
                ],
                "valueCodeableConcept" => [
                    "coding" => [
                        [
                            "system" => "http://snomed.info/sct",
                            "code" => $dataKesadaran['code'],
                            "display" => $dataKesadaran['display'],
                        ]
                    ]
                ]
            ];

            Log::info("POST Observation Tingkat Kesadaran Payload", $payload);

            $response = Http::withToken($token)
                ->post($this->baseUrl, $payload);

            if ($response->failed()) {
                Log::error("Error POST Observation Tingkat Kesadaran", ['body' => $response->body()]);
                throw new Exception("Gagal POST Observation Tingkat Kesadaran: " . $response->body());
            }
            
            return $response->json('id');

            // return true;

        } catch (\Exception $e) {
            Log::error("StoreTingkatKesadaran Error: " . $e->getMessage());
            throw $e;
        }
    }
}