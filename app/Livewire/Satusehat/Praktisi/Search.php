<?php

namespace App\Livewire\Satusehat\Praktisi;

use App\Models\Practitioner;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use App\Services\GetPractitionerByNik;

class Search extends Component
{
    public $selectedUser; // id user dipilih
    public $users = [];   // untuk select
    public $praktisi = [];

    public $name, $no_ihs, $nik;
    public $nama_lengkap;
    public function mount()
    {
        // Load users beserta relasi dokter dan biodata
        $this->users = User::with(['biodata', 'dokter'])->get();
    }

    public function searchpractioner(GetPractitionerByNik $practitionerService)
    {
        // contoh ambil relasinya
        $user = User::with(['biodata', 'dokter'])
            ->find($this->selectedUser);
        
        $this->nik = $user->dokter->nik 
            ?? $user->biodata->nik 
            ?? '-';
        $this->nama_lengkap = $user->dokter->nama_dokter ?? $user->biodata->nama_lengkap ?? '-';

        try {
            $practitioner = $practitionerService->handle($this->nik);

            $this->praktisi = $practitioner;

            $this->no_ihs = $practitioner['no_ihs'];
            $this->name = $practitioner['nama'];

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Data Praktisi Berhasil Ditemukan Pada Satu Sehat.',
            ]);
            // dd($practitioner);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Data Praktisi Tidak Ditemukan Pada Satu Sehat.',
            ]);
        }

    }

    public function saveIHS($selectedUser)
    {
        // Dapatkan user + relasi
        $user = User::with(['biodata', 'dokter'])->find($selectedUser);

        if (!$user) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'User tidak ditemukan.'
            ]);
            return;
        }

        try {
            // Simpan no IHS ke tabel dokter atau biodata
            if ($user->dokter) {
                $user->dokter->update([
                    'ihs' => $this->no_ihs,  
                ]);
            } else if ($user->biodata) {
                $user->biodata->update([
                    'ihs' => $this->no_ihs,  
                ]);
            }

            if (
                !empty($this->praktisi['no_ihs']) &&
                !empty($this->praktisi['nama'])
            ) {
                Practitioner::create([
                    'name'         => $this->nama_lengkap,
                    'gender'       => $this->praktisi['gender'] ?? null,
                    'birthdate'    => $this->praktisi['birthdate'] ?? null,
                    'id_satusehat' => $this->praktisi['id_satusehat'],
                    'nik'          => $this->nik ?? "-",
                    'ihs'          => $this->praktisi['no_ihs'],
                    'city'         => $this->praktisi['city'] ?? null,
                    'address_line' => $this->praktisi['address_line'] ?? null,
                ]);
                
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'No IHS Berserta Data lainnya Berhasil Disimpan!'
                ]);
            } else {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Data Praktisi Tidak Lengkap. Tidak Bisa Disimpan.'
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Gagal menyimpan IHS', [
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Gagal menyimpan ke database.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.satusehat.praktisi.search');
    }
}
