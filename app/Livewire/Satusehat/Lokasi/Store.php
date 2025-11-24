<?php

namespace App\Livewire\Satusehat\Lokasi;

use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Village;
use Livewire\Component;
use App\Services\StoreLocation;
use Illuminate\Support\Facades\Log;

class Store extends Component
{
    public $name,$description;
    public $no_telp,$email,$web;
    public $alamat,$kota,$kode_pos,$province_code,$city_code,$district_code,$village_code,$rt,$rw;
    public $longitude,$latitude,$altitude;

    public $id_organization; // id organization dari satu sehat
    public $provinsi;

    public function mount()
    {
        $this->provinsi = Province::all();
        $this->id_organization = config('services.satusehat.org_id');  
        // $this->name = "dummy location";
        // $this->description = "description dummy post";

        // $this->no_telp = "098765456789";
        // $this->email = "dumb@gmail.com";
        // $this->web = "demo.klinikdokterl.com";

        // $this->alamat = "jl. dummy no 0 ";
        // $this->kota = "Banjarmasin";
        // $this->kode_pos = "70236";
        // $this->province_code = "63";
        // $this->city_code = "6371";
        // $this->district_code = "6371020";
        // $this->village_code = "6371020005";
        // $this->rt = "29";
        // $this->rw = "2";

        // $this->longitude = -3.326923;
        // $this->latitude = 114.616957;
        // $this->altitude = 12.00;
    }

    public function render()
    {
        return view('livewire.satusehat.lokasi.store');
    }

    public function store(StoreLocation $orgService)
    {
        try {
            $this->kota = Regency::where('id', $this->city_code)->value('name');
            // ======== PAYLOAD ==========
            $payload = [
                "resourceType" => "Location",
                "identifier" => [
                    [
                        "use" => "official",
                        "system" => "http://sys-ids.kemkes.go.id/location/" . $this->id_organization,
                        "value" => "Klinik Dokter L"
                    ]
                ],
                "status" => "active",
                "name" => $this->name,
                "description" => $this->description,
                "telecom" => [
                    [
                        "system" => "phone",
                        "value" => $this->no_telp,
                        "use" => "work"
                    ],
                    [
                        "system" => "email",
                        "value" => $this->email,
                        "use" => "work"
                    ],
                    [
                        "system" => "url",
                        "value" => $this->web,
                        "use" => "work"
                    ],
                ],
                "address" => [
                    "use" => "work",
                    "line" => [$this->alamat],
                    "city" => $this->kota,
                    "postalCode" => $this->kode_pos,
                    "country" => "ID",
                    "extension" => [
                        [
                            "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/administrativeCode",
                            "extension" => [
                                ["url" => "province", "valueCode" => $this->province_code],
                                ["url" => "city", "valueCode" => $this->city_code],
                                ["url" => "district", "valueCode" => $this->district_code],
                                ["url" => "village", "valueCode" => $this->village_code],
                                ["url" => "rt", "valueCode" => $this->rt],
                                ["url" => "rw", "valueCode" => $this->rw],
                            ]
                        ]
                    ]
                ],
                "physicalType" => [
                    "coding" => [
                        [
                            "system" => "http://terminology.hl7.org/CodeSystem/location-physical-type",
                            "code" => "ro",
                            "display" => "room",
                        ]
                    ]
                ],
                "position" => [
                    "longitude" => floatval($this->longitude),
                    "latitude" => floatval($this->latitude),
                    "altitude" => floatval($this->altitude),
                ],
                "managingOrganization" => [
                    "reference" => "Organization/" . $this->id_organization,
                ]
            ];
    
            // ======== CALL SERVICE ==========
            $response = $orgService->createLocation($payload);
            // dd($response);
            // Jika API mengembalikan OperationOutcome (gagal)
            if (isset($response['resourceType']) && $response['resourceType'] === 'OperationOutcome') {
    
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => $response['issue'][0]['details']['text'] ?? 'Gagal membuat organisasi.'
                ]);
    
                return;
            }
    
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Location Berhasil Dikirim Ke SATUSEHAT'
            ]);
        } catch (\Throwable $th) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Gagal Kirim Data Location'
            ]);
            Log::error('Gagal Mengambil Data Location', [
                'message' => $th->getMessage(),
            ]);
        }

        return view('livewire.satusehat.lokasi.store');
    }
}
