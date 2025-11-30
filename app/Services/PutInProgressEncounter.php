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
    public function handle($encounterId, $waktuTiba, $WaktuDiperiksa, $pasienNama, $pasienIhs, $dokterNama, $dokterIhs, $location)
    {
        try {
            $token = $this->tokenService->getAccessToken();

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
                    "start" => $waktuTiba
                ],

                "location" => [
                    [
                        "location" => [
                            "reference" => "Location/" . $location->id_satusehat, // ID lokasi dalam sistem
                            "display" => $location->name // Nama lokasi pelayanan
                        ],
                    ]
                ],

                "statusHistory" => [
                    [
                        "status" => "arrived",
                        "period" => [
                            "start" => $waktuTiba,
                            "end"   => $WaktuDiperiksa
                        ]
                    ],
                    [
                        "status" => "in-progress",
                        "period" => [
                            "start" => $WaktuDiperiksa
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