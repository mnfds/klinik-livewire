<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class StorePemeriksaanFisik
{
    protected string $baseUrl;
    protected getAccessToken $tokenService;

    public function __construct(getAccessToken $tokenService)
    {
        $this->baseUrl = config('services.satusehat.base_url') . '/Observation';
        $this->tokenService = $tokenService;
    }

    /**
     * POST Condition ke Satu Sehat
     */
    public function handle(
        $encounterId, $pasienNama, $pasienIhs,
        $dokterNama, $dokterIhs, $WaktuDiperiksa, 
        $tinggiBadan, $beratBadan,
        )
    {
        try {
            $token = $this->tokenService->getAccessToken();
        //========= TINGGI BADAN ===========
            $payloadTinggi = [
                "resourceType" => "Observation",
                "status"=> "final",
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
                            "code" => "8302-2",
                            "display" => "Body height"
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
                "effectiveDateTime" => $WaktuDiperiksa,
                "issued"=> $WaktuDiperiksa,
                "performer"=> [
                    [
                        "reference" => "Practitioner/" . $dokterIhs,
                        "display" => $dokterNama
                    ]
                ],
                "valueQuantity" => [
                    "value" => (int)$tinggiBadan,
                    "unit" => "cm",
                    "system" => "http://unitsofmeasure.org",
                    "code" => "cm"
                ]
            ];

            Log::info("POST Observation Tinggi Badan Payload", $payloadTinggi);

            $responseTinggi = Http::withToken($token)
                ->post($this->baseUrl, $payloadTinggi);

            if ($responseTinggi->failed()) {
                Log::error("Error POST Observation Tinggi Badan", ['body' => $responseTinggi->body()]);
                throw new Exception("Gagal POST Observation Tinggi Badan: " . $responseTinggi->body());
            }
            
            $tinggiBadanId = $responseTinggi->json('id');
        //========= TINGGI BADAN ===========

        //========= BERAT BADAN ===========
            $payloadBerat = [
                "resourceType" => "Observation",
                "status"=> "final",
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
                            "code" => "29463-7",
                            "display" => "Body weight"
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
                "effectiveDateTime" => $WaktuDiperiksa,
                "issued"=> $WaktuDiperiksa,
                "performer"=> [
                    [
                        "reference" => "Practitioner/" . $dokterIhs,
                        "display" => $dokterNama
                    ]
                ],
                "valueQuantity" => [
                    "value" => (int)$beratBadan,
                    "unit" => "kg",
                    "system" => "http://unitsofmeasure.org",
                    "code" => "kg"
                ]
            ];

            Log::info("POST Observation Berat Badan Payload", $payloadBerat);

            $responseBerat = Http::withToken($token)
                ->post($this->baseUrl, $payloadBerat);

            if ($responseBerat->failed()) {
                Log::error("Error POST Observation Berat Badan", ['body' => $responseBerat->body()]);
                throw new Exception("Gagal POST Observation Berat Badan: " . $responseBerat->body());
            }
            
            $BeratBadanId = $responseBerat->json('id');
        //========= BERAT BADAN ===========

            return [
                'tinggiBadan_id' => $tinggiBadanId,
                'beratBadan_id' => $BeratBadanId,
            ];
            // return true;

        } catch (\Exception $e) {
            Log::error("StorePemeriksaanFisik Error: " . $e->getMessage());
            throw $e;
        }
    }
}