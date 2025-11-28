<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class PutInProgressEncounter
{
    protected string $baseUrl;
    protected getAccessToken $tokenService;

    public function __construct(getAccessToken $tokenService)
    {
        $this->baseUrl = config('services.satusehat.base_url') . '/Encounter';
        $this->tokenService = $tokenService;
    }

    /**
     * POST Encounter ke Satu Sehat
     */
    public function handle($encounterId, $waktuTiba, $pasienNama, $pasienIhs, $dokterNama, $dokterIhs, $location)
    {
        try {
            $token = $this->tokenService->getAccessToken();

            // Waktu sekarang UTC
            $nowUtc = now('Asia/Makassar')->setTimezone('UTC')->format('Y-m-d\TH:i:s+00:00');

            // Waktu tiba → convert to UTC
            $tibaUtc = \Carbon\Carbon::parse($waktuTiba, 'Asia/Makassar')
                ->setTimezone('UTC')
                ->format('Y-m-d\TH:i:s+00:00');

            $payload = [
                "resourceType" => "Encounter",
                "id" => $encounterId,

                "identifier" => [
                    [
                        "system" => "http://sys-ids.kemkes.go.id/encounter/" . config('services.satusehat.org_id'),
                        "value"  => "P" . now()->format("YmdHis")
                    ]
                ],
                "status" => "in-progress",
                        
                // Kelas encounter, dalam hal ini "AMB" (ambulatory) yang berarti rawat jalan
                "class" => [
                    "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
                    "code" => "AMB",
                    "display" => "ambulatory"
                ],

                "subject" => [
                    "reference" => "Patient/{$pasienIhs}",
                    "display"   => $pasienNama,
                ],

                "participant" => [
                    [
                        "type" => [
                            [
                                "coding" => [
                                    [
                                        "system" => "http://terminology.hl7.org/CodeSystem/v3-ParticipationType",
                                        "code" => "ATND",
                                        "display" => "attender"
                                    ]
                                ]
                            ]
                        ],
                        "individual" => [
                            "reference" => "Practitioner/{$dokterIhs}",
                            "display"   => $dokterNama
                        ]
                    ],
                ],

                "period" => [
                    "start" => $tibaUtc
                ],

                "location" => [
                    [
                        "location" => [
                            "reference" => "Location/" . $location, // ID lokasi dalam sistem
                            "display" => "Poliklinik Umum" // Nama lokasi pelayanan
                        ],
                        // Ekstensi tambahan untuk menentukan kelas layanan
                        "extension" => [
                            [
                                "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/ServiceClass",
                                "extension" => [
                                    [
                                        "url" => "value",
                                        "valueCodeableConcept" => [
                                            "coding" => [
                                                [
                                                    "system" => "http://terminology.kemkes.go.id/CodeSystem/locationServiceClass-Outpatient",
                                                    "code" => "reguler", // Kode kelas layanan
                                                    "display" => "Kelas Reguler" // Deskripsi kelas layanan
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],

                "statusHistory" => [
                    [
                        "status" => "arrived",
                        "period" => [
                            "start" => $tibaUtc,
                            "end"   => $nowUtc
                        ]
                    ],
                    [
                        "status" => "in-progress",
                        "period" => [
                            "start" => $nowUtc
                        ]
                    ]
                ],

                "serviceProvider" => [
                    "reference" => "Organization/" . config('services.satusehat.org_id'),
                ]
            ];

            Log::info("PUT Encounter Payload", $payload);

            $response = Http::withToken($token)
                ->put($this->baseUrl . '/' . $encounterId, $payload);

            if ($response->failed()) {
                Log::error("Error PUT Encounter", ['body' => $response->body()]);
                throw new Exception("Gagal PUT Encounter: " . $response->body());
            }

            return true;

        } catch (\Exception $e) {
            Log::error("PutInProgressEncounter Error: " . $e->getMessage());
            throw $e;
        }
    }
}