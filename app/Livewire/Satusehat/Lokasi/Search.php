<?php

namespace App\Livewire\Satusehat\Lokasi;

use Livewire\Component;
use App\Services\GetLocation;
use Illuminate\Support\Facades\Log;

class Search extends Component
{
    public string $org_id = '';
    public array $dataLocation = [];
    public bool $loading = false;

    public function searchloc(GetLocation $service)
    {
        $this->validate([
            'org_id' => 'required|string|min:1'
        ]);

        try {
            $this->loading = true;

            $this->dataLocation = $service->byOrganization($this->org_id);

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Location ditemukan dari SATUSEHAT.'
            ]);

        } catch (\Exception $e) {

            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Location tidak ditemukan di SATUSEHAT.'
            ]);

            Log::error('Gagal mengambil lokasi', [
                'message' => $e->getMessage(),
            ]);

        } finally {
            $this->loading = false;
        }
        // dd($this->dataLocation);
    }

    public function render()
    {
        return view('livewire.satusehat.lokasi.search');
    }
}
