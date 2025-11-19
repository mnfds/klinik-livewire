<?php

namespace App\Livewire\Satusehat\Organisasi;

use Livewire\Component;
use Livewire\Volt\Compilers\Mount;
use App\Services\StoreOrganization;
use Illuminate\Support\Facades\Log;

class Store extends Component
{
    public $departemen,$kota,$alamat,$kode_pos,$no_telp,$email,$web,$status;
    public $id_satusehat;
    public $id_organization;
    
    
    public function mount()
    {
        $this->id_organization = config('services.satusehat.org_id');  
    }

    public function render()
    {
        return view('livewire.satusehat.organisasi.store');
    }

    public function store(StoreOrganization $orgService)
    {
        try {
            // ======== PAYLOAD ==========
            $payload = [
                "resourceType" => "Organization",
                "active" => true,
                "identifier" => [
                    [
                        "use" => "official",
                        "system" => "http://sys-ids.kemkes.go.id/organization/" . $this->id_organization,
                        "value" => "Klinik Dokter L"
                    ]
                ],
                "type" => [
                    [
                        "coding" => [
                            [
                                "system" => "http://terminology.hl7.org/CodeSystem/organization-type",
                                "code" => "dept",
                                "display" => "Hospital Department",
                            ]
                        ]
                    ]
                ],
                "name" => $this->departemen,
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
                    [
                        "use" => "work",
                        "type" => "both",
                        "line" => [$this->alamat],
                        "city" => $this->kota,
                        "postalCode" => $this->kode_pos,
                        "country" => "ID",
                        "extension" => [
                            [
                                "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/administrativeCode",
                                "extension" => [
                                    ["url" => "province", "valueCode" => "63"],
                                    ["url" => "city", "valueCode" => "6371"],
                                    ["url" => "district", "valueCode" => "637102"],
                                    ["url" => "village", "valueCode" => "6371021001"],
                                ]
                            ]
                        ]
                    ]
                ],
                "partOf" => [
                    "reference" => "Organization/" . $this->id_organization,
                    "display" => "Klinik Dokter L"
                ]
            ];
    
            // ======== CALL SERVICE ==========
            $response = $orgService->createOrganization($payload);
    
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
                'message' => 'Organisasi berhasil dikirim ke SATUSEHAT'
            ]);
        } catch (\Throwable $th) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Gagal mengirim organisasi'
            ]);
            Log::error('Gagal Mengambil Data Organization', [
                'message' => $th->getMessage(),
            ]);
        }

        return view('livewire.satusehat.organisasi.store');
    }
}
