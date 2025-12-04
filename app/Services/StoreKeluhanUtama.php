<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class StoreKeluhanUtama
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
        $keluhanUtama,
        )
    {
        try {
            $token = $this->tokenService->getAccessToken();
            $payload = [
                "resourceType" => "Condition",
                "category" => [
                    [
                        "coding" => [
                            [
                                "system" => "http://terminology.kemkes.go.id",
                                "code" => "chief-complaint",
                                "display" => "Chief Complaint"
                            ]
                        ]
                    ]
                ],
                "code" => [
                    "coding" => [
                        [
                            "system" => "http://snomed.info/sct",
                            "code" => "422587007",
                            "display" => "Chief complaint (finding)"
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
                "onsetDateTime" => $WaktuDiperiksa,
                "recordedDate"=> $WaktuDiperiksa,
                "recorder"=> [
                        "reference" => "Practitioner/" . $dokterIhs,
                        "display" => $dokterNama,
                ],
                "note"=> [
                    [
                        "text" => $keluhanUtama,
                    ]
                ],
            ];

            Log::info("POST Condition Keluhan Utama Payload", $payload);

            $response = Http::withToken($token)
                ->post($this->baseUrl, $payload);

            if ($response->failed()) {
                Log::error("Error POST Condition Keluhan Utama", ['body' => $response->body()]);
                throw new Exception("Gagal POST Condition Keluhan Utama: " . $response->body());
            }
            
            return $response->json('id');

            // return true;

        } catch (\Exception $e) {
            Log::error("StoreKeluhanUtama Error: " . $e->getMessage());
            throw $e;
        }
    }
}