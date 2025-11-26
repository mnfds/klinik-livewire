<?php

namespace App\Livewire\Satusehat\Praktisi;

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

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'No IHS berhasil disimpan!'
            ]);

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
