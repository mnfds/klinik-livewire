<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class StoreKonselingService
{
    protected string $baseUrl;
    protected getAccessToken $tokenService;

    public function __construct(getAccessToken $tokenService)
    {
        $this->baseUrl = config('services.satusehat.base_url') . '/ServiceRequest';
        $this->tokenService = $tokenService;
    }

    /**
     * POST ServiceRequest Konseling ke Satu Sehat
     */
    public function handle(
        $encounterId, $pasienIhs,
        $dokterNama, $dokterIhs, $WaktuDiperiksa,
        $reasonIcd
        )
    {
        try {
            $token = $this->tokenService->getAccessToken();
            $payload = [
                "resourceType" => "ServiceRequest",
                "status" => "active",
                "intent" => "original-order",
                "priority" => "routine",
                "category" => [
                    [
                        "coding" => [
                            [
                                "system" => "http://snomed.info/sct",
                                "code" => "409063005",
                                "display" => "Counseling"
                            ]
                        ]
                    ]
                ],
                "code" => [
                    "coding" => [
                        [
                            "system" => "http://hl7.org/fhir/sid/icd-9-cm",
                            "code" => "94.4",
                            "display" => "Other psychotherapy and counselling"
                        ],
                        [
                            "system" => "http://terminology.kemkes.go.id/CodeSystem/kptl",
                            "code" => "12017.PC013",
                            "display" => "Konseling Individu"
                        ],
                    ]
                ],
                "subject" => [
                    "reference" => "Patient/" . $pasienIhs, // Mengambil dari input no_ihs
                ],
                "encounter" => [
                    "reference" => "Encounter/" . $encounterId, // Sesuaikan dengan encounter ID yang benar
                ],
                "occurrenceDateTime" => $WaktuDiperiksa,
                "authoredOn"=> $WaktuDiperiksa,
                "requester"=> [
                        "reference" => "Practitioner/" . $dokterIhs,
                        "display" => $dokterNama,
                ],
                "performer"=> [
                    [
                        "reference" => "Practitioner/" . $dokterIhs,
                        "display" => $dokterNama,
                    ]
                ],
                "reasonCode" => [
                    [
                        "coding" => [
                            [
                                "system"=> "http://hl7.org/fhir/sid/icd-10",
                                "code"=> $reasonIcd['code'],
                                "display"=> $reasonIcd['display']
                            ]
                        ]
                    ]
                ],
                "note"=> [
                    [
                        "text" => "Pasien melakukan konseling terkait masalah penyakitnya",
                    ]
                ],
            ];

            Log::info("POST ServiceRequest Konseling Payload", $payload);

            $response = Http::withToken($token)
                ->post($this->baseUrl, $payload);

            if ($response->failed()) {
                Log::error("Error POST ServiceRequest Konseling", ['body' => $response->body()]);
                throw new Exception("Gagal POST ServiceRequest Konseling: " . $response->body());
            }
            
            return $response->json('id');

            // return true;

        } catch (\Exception $e) {
            Log::error("StoreKonselingService Error: " . $e->getMessage());
            throw $e;
        }
    }
}