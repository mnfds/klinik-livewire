<?php

namespace App\Livewire\Satusehat\Organisasi;

use App\Models\Organization;
use App\Models\PoliKlinik;
use Livewire\Component;
use App\Services\GetOrganization;
use Illuminate\Support\Facades\Log;

class Search extends Component
{
    public string $partof = '';
    public array $dataOrganisasi = [];
    public bool $loading = false;

    public function search(GetOrganization $service)
    {
        $this->validate([
            'partof' => 'required|string|min:1'
        ]);

        try {
            $this->loading = true;

            // Panggil service
            $this->dataOrganisasi = $service->byPartOf($this->partof);
            
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Organization Ditemukan Pada Satu Sehat.'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Organization ID Tidak Ditemukan Pada Satu Sehat'
            ]);
            Log::error('Gagal Mengambil Data Organization', [
                'message' => $e->getMessage(),
            ]);
            // $this->dispatch('alert', message: "Gagal mengambil data: " . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function saved($orgId)
    {
        try {
            // Cari data org berdasarkan ID
            $org = collect($this->dataOrganisasi)->firstWhere('id', $orgId);
    
            if (!$org) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Data organisasi tidak ditemukan.'
                ]);
                return;
            }
            $organization = Organization::updateOrCreate(
                [
                    'id_satusehat'  => $org['id'],
                    'departemen'    => $org['name'],
                    'kota'          => $org['city'],
                    'alamat'        => $org['line'],
                    'kode_pos'      => $org['postalCode'],
                    'no_telp'       => $org['telecom']['phone'] ?? null,
                    'email'         => $org['telecom']['email'] ?? null,
                    'web'           => $org['telecom']['url'] ?? null,
                    'status'        => $org['active'],
                ]
            );

            $poli = PoliKlinik::where('nama_poli',$org['name'])->first();

            if($poli){
                $poli->update([
                    'organization_id' => $organization->id,
                ]);
            }
    
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Berhasil Disimpan Ke Database Lokal!'
            ]);
        } catch (\Throwable $b) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Gagal Disimpan Ke Database Lokal'
            ]);
            Log::error('Gagal Disimpan Ke Database Lokal', [
                'message' => $b->getMessage(),
            ]);
        }
    }
    public function render()
    {
        return view('livewire.satusehat.organisasi.search');
    }
}