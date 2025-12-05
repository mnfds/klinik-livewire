<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class StoreKonselingProcedure
{
    protected string $baseUrl;
    protected getAccessToken $tokenService;

    public function __construct(getAccessToken $tokenService)
    {
        $this->baseUrl = config('services.satusehat.base_url') . '/Procedure';
        $this->tokenService = $tokenService;
    }

    /**
     * POST Condition ke Satu Sehat
     */
    public function handle(
        $encounterId, $pasienNama, $pasienIhs,
        $dokterNama, $dokterIhs, $WaktuDiperiksa,
        $serviceId, $reasonIcd
        )
    {
        try {
            $token = $this->tokenService->getAccessToken();
            $payload = [
                "resourceType" => "Procedure",

                // relasi ke ServiceRequest konseling yang barusan dibuat
                "basedOn" => [
                    [
                        "reference" => "ServiceRequest/" . $serviceId
                    ]
                ],

                "status" => "completed",

                "category" => [
                    "coding" => [
                        [
                            "system"  => "http://snomed.info/sct",
                            "code"    => "409063005",
                            "display" => "Counseling"
                        ]
                    ]
                ],

                "code" => [
                    "coding" => [
                        [
                            "system"  => "http://hl7.org/fhir/sid/icd-9-cm",
                            "code"    => "94.4",
                            "display" => "Other psychotherapy and counselling"
                        ],
                        [
                            "system"  => "http://terminology.kemkes.go.id/CodeSystem/kptl",
                            "code"    => "12017.PC013",
                            "display" => "Konseling Individu"
                        ]
                    ]
                ],

                "subject" => [
                    "reference" => "Patient/" . $pasienIhs,
                    "display"   => $pasienNama,
                ],

                "encounter" => [
                    "reference" => "Encounter/" . $encounterId
                ],

                // gunakan tanggal diperiksa
                "performedPeriod" => [
                    "start" => $WaktuDiperiksa,
                    "end"   => $WaktuDiperiksa,
                ],

                "performer" => [
                    [
                        "actor" => [
                            "reference" => "Practitioner/" . $dokterIhs,
                            "display"   => $dokterNama
                        ]
                    ]
                ],

                "reasonCode" => [
                    [
                        "coding" => [
                            [
                                "system"  => "http://hl7.org/fhir/sid/icd-10",
                                "code"    => $reasonIcd['code'],
                                "display" => $reasonIcd['display']
                            ]
                        ]
                    ]
                ],

                "note" => [
                    [
                        "text" => "Konseling keresahan pasien karena diagnosis " . $reasonIcd['name_id'] 
                    ]
                ],
            ];

            Log::info("POST Procedure Konseling Payload", $payload);

            $response = Http::withToken($token)
                ->post($this->baseUrl, $payload);

            if ($response->failed()) {
                Log::error("Error POST Procedure Konseling", ['body' => $response->body()]);
                throw new Exception("Gagal POST Procedure Konseling: " . $response->body());
            }
            
            return $response->json('id');

            // return true;

        } catch (\Exception $e) {
            Log::error("StoreKonselingProcedure Error: " . $e->getMessage());
            throw $e;
        }
    }
}