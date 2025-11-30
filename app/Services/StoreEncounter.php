<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class StoreEncounter
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
    public function handle($pasien_satusehat, $dokter_satusehat, $organisasi_satusehat, $location_satusehat, string $tanggal_kunjungan, $waktu_tiba): string
    {
        try {
            $token = $this->tokenService->getAccessToken();

            $payload = $this->buildEncounterPayload(
                $pasien_satusehat,
                $dokter_satusehat,
                $organisasi_satusehat,
                $location_satusehat,
                $tanggal_kunjungan,
                $waktu_tiba,
            );

            // dd($payload);
            Log::info('SATUSEHAT Encounter Payload', $payload);

            $response = Http::withToken($token)
                ->post($this->baseUrl, $payload);

            if ($response->failed()) {
                Log::error('Gagal POST Encounter', ['body' => $response->body()]);
                throw new Exception("Error Encounter API: " . $response->body());
            }

            $result = $response->json();

            Log::info('Encounter berhasil dibuat', [
                'encounter_id' => $result['id'] ?? null
            ]);

            return $result['id']; // return Encounter ID

        } catch (\Exception $e) {

            Log::error("Encounter Error: ".$e->getMessage());
            throw $e;
        }
    }

    /**
     * BUILD PAYLOAD ENCOUNTER
     */
    private function buildEncounterPayload($pasien_satusehat, $dokter_satusehat, $organisasi_satusehat, $location_satusehat, $tanggal_kunjungan, $waktu_tiba)
    {
        return [
            "resourceType" => "Encounter",

            "status" => "arrived",

            "class" => [
                "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
                "code"   => "AMB",
                "display"=> "ambulatory",
            ],

            // ---- Data Pasien ----
            "subject" => [
                "reference" => "Patient/{$pasien_satusehat->no_ihs}",
                "display"   => $pasien_satusehat->nama,
            ],

            // ---- Dokter / Practitioner ----
            "participant" => [
                [
                    "type" => [
                        [
                            "coding" => [
                                [
                                    "system"  => "http://terminology.hl7.org/CodeSystem/v3-ParticipationType",
                                    "code"    => "ATND",
                                    "display" => "attender",
                                ]
                            ]
                        ]
                    ],
                    "individual" => [
                        "reference" => "Practitioner/{$dokter_satusehat->ihs}",
                        "display"   => $dokter_satusehat->nama_dokter,
                    ],
                ],
            ],

            // ---- Period ----
            "period" => [
                "start" => $waktu_tiba
            ],

            // ---- Lokasi ----
            "location" => [
                [
                    "location" => [
                        "reference" => "Location/" . $location_satusehat->id_satusehat,
                        "display"   => $location_satusehat->name,
                    ]
                ]
            ],

            // ---- History ----
            "statusHistory" => [
                [
                    "status" => "arrived",
                    "period" => [
                        "start" => $waktu_tiba
                    ]
                ]
            ],

            // ---- Organization ----
            "serviceProvider" => [
                // "reference" => "Organization/" . $organisasi_satusehat,
                "reference" => "Organization/" . config('services.satusehat.org_id'),
            ],

            // ---- Identifier ----
            "identifier" => [
                [
                    "system" => "http://sys-ids.kemkes.go.id/encounter/" . config('services.satusehat.org_id'),
                    "value"  => "P" . now()->format("YmdHis")
                ]
            ],
        ];
    }
}
