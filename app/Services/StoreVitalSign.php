<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class StoreVitalSign
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
        $sistole, $diastole, $suhu_tubuh,
        $nadi, $pernapasan
        )
    {
        try {
            $token = $this->tokenService->getAccessToken();
        //========= SISTOLE ===========
            $payloadSistolik = [
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
                            "code" => "8480-6",
                            "display" => "Systolic blood pressure"
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
                    "value" => (int)$sistole,
                    "unit" => "mm[Hg]",
                    "system" => "http://unitsofmeasure.org",
                    "code" => "mm[Hg]"
                ]
            ];

            Log::info("POST Observation Sistolik Payload", $payloadSistolik);

            $responseSistolik = Http::withToken($token)
                ->post($this->baseUrl, $payloadSistolik);

            if ($responseSistolik->failed()) {
                Log::error("Error POST Observation Sistolik", ['body' => $responseSistolik->body()]);
                throw new Exception("Gagal POST Observation Sistolik: " . $responseSistolik->body());
            }
            
            $sistolikId = $responseSistolik->json('id');
        //========= SISTOLE ===========

        //========= DIASTOLE ===========
            $payloadDiastolik = [
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
                            "code" => "8462-4",
                            "display" => "Diastolic blood pressure"
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
                    "value" => (int)$diastole,
                    "unit" => "mm[Hg]",
                    "system" => "http://unitsofmeasure.org",
                    "code" => "mm[Hg]"
                ]
            ];

            Log::info("POST Observation Diastolik Payload", $payloadDiastolik);

            $responseDiastolik = Http::withToken($token)
                ->post($this->baseUrl, $payloadDiastolik);

            if ($responseDiastolik->failed()) {
                Log::error("Error POST Observation Diastolik", ['body' => $responseDiastolik->body()]);
                throw new Exception("Gagal POST Observation Diastolik: " . $responseDiastolik->body());
            }
            
            $diastolikId = $responseDiastolik->json('id');
        //========= DIASTOLE ===========

        //========= SUHU TUBUH ===========
            $payloadSuhuTubuh = [
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
                            "code" => "8310-5",
                            "display" => "Body temperature"
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
                    "value" => (float)$suhu_tubuh,
                    "unit" => "Cel",
                    "system" => "http://unitsofmeasure.org",
                    "code" => "Cel"
                ]
            ];

            Log::info("POST Observation Suhu Tubuh Payload", $payloadSuhuTubuh);

            $responseSuhutubuh = Http::withToken($token)
                ->post($this->baseUrl, $payloadSuhuTubuh);

            if ($responseSuhutubuh->failed()) {
                Log::error("Error POST Observation Suhu Tubuh", ['body' => $responseSuhutubuh->body()]);
                throw new Exception("Gagal POST Observation Suhu Tubuh: " . $responseSuhutubuh->body());
            }
            
            $suhutubuhId = $responseSuhutubuh->json('id');
        //========= SUHU TUBUH ===========

        //========= NADI / DENYUT JANTUNG ===========
            $payloadNadi = [
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
                            "code" => "8867-4",
                            "display" => "Heart rate"
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
                    "value" => (int)$nadi,
                    "unit" => "beats/min",
                    "system" => "http://unitsofmeasure.org",
                    "code" => "beats/min"
                ]
            ];

            Log::info("POST Observation Nadi (Denyut Jantung) Payload", $payloadNadi);

            $responseNadi = Http::withToken($token)
                ->post($this->baseUrl, $payloadNadi);

            if ($responseNadi->failed()) {
                Log::error("Error POST Observation Nadi (Denyut Jantung)", ['body' => $responseNadi->body()]);
                throw new Exception("Gagal POST Observation Nadi (Denyut Jantung): " . $responseNadi->body());
            }
            
            $nadiId = $responseNadi->json('id');
        //========= NADI / DENYUT JANTUNG ===========

        //========= FREKUENSI PERNAPASAN ===========
            $payloadPernapasan = [
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
                            "code" => "9279-1",
                            "display" => "Respiratory rate"
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
                    "value" => (int)$pernapasan,
                    "unit" => "breaths/min",
                    "system" => "http://unitsofmeasure.org",
                    "code" => "breaths/min"
                ]
            ];

            Log::info("POST Observation Frekuensi Pernapasan Payload", $payloadPernapasan);

            $responsePernapasan = Http::withToken($token)
                ->post($this->baseUrl, $payloadPernapasan);

            if ($responsePernapasan->failed()) {
                Log::error("Error POST Observation Frekuensi Pernapasan", ['body' => $responsePernapasan->body()]);
                throw new Exception("Gagal POST Observation Frekuensi Pernapasan: " . $responsePernapasan->body());
            }
            
            $pernapasanId = $responsePernapasan->json('id');
        //========= FREKUENSI PERNAPASAN ===========

            return [
                'sistolik_id' => $sistolikId,
                'diastolik_id' => $diastolikId,
                'suhutubuh_id' => $suhutubuhId,
                'nadi_id' => $nadiId,
                'pernapasan_id' => $pernapasanId,
            ];
            // return true;

        } catch (\Exception $e) {
            Log::error("StoreVitalSign Error: " . $e->getMessage());
            throw $e;
        }
    }
}