<?php

namespace App\Livewire\Satusehat\Organisasi;

use Livewire\Component;
use App\Services\GetOrganization;

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
        } catch (\Exception $e) {
            $this->dispatch('alert', message: "Gagal mengambil data: " . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function render()
    {
        return view('livewire.satusehat.organisasi.search');
    }
}