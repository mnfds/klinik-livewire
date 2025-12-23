<?php

namespace App\Livewire\Satusehat\Lokasi;

use Livewire\Component;
use App\Models\Locations;
use App\Models\PoliKlinik;
use App\Services\GetLocation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

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

    public function saveLocation($org_id)
    {
        if (! Gate::allows('akses', 'Tambah Lokasi Satu Sehat')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        try {
            // Cari data org berdasarkan ID
            $org = collect($this->dataLocation)->firstWhere('id', $org_id);
            // dd($org);
            if (!$org) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Data Location Tidak Ditemukan.'
                ]);
                return;
            }
            $locations = Locations::updateOrCreate(
                [
                    'id_satusehat'  => $org['id'],
                    'name'          => $org['name'],
                    'description'   => $org['description'],

                    'alamat'        => $org['address'],
                    'kota'          => $org['city'],
                    'kode_pos'      => $org['postalCode'],
                    'province_code' => $org['province_code'],
                    'city_code'     => $org['city_code'],
                    'district_code' => $org['district_code'],
                    'village_code'  => $org['village_code'],
                    'rt'            => $org['rt'],
                    'rw'            => $org['rw'],
                    
                    'no_telp'       => $org['phone'] ?? null,
                    'email'         => $org['email'] ?? null,
                    'web'           => $org['website'] ?? null,

                    'longitude'     => $org['longitude'],
                    'latitude'      => $org['latitude'],
                    'altitude'      => $org['altitude'],
                ]
            );
    
            $poli = PoliKlinik::where('nama_poli',$org['name'])->first();

            if($poli){
                $poli->update([
                    'location_id' => $locations->id,
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
        return view('livewire.satusehat.lokasi.search');
    }
}
