<?php

namespace App\Livewire\Pendaftaran;

use Livewire\Component;
use App\Models\NomorAntrian;
use App\Models\PasienTerdaftar;
use Illuminate\Support\Facades\Gate;

class Data extends Component
{    
    public $jumlahPasienTerdaftar;
    public $jumlahPasienDiperiksa;

    public function mount()
    {
        $this->updateJumlahPasienTerdaftar();
        $this->updateJumlahPasienDiperiksa();
    }

    public function updateJumlahPasienTerdaftar()
    {
        $this->jumlahPasienTerdaftar = PasienTerdaftar::whereDate('created_at', today())
            ->where('status_terdaftar', 'terdaftar')
            ->count();
    }
    public function updateJumlahPasienDiperiksa()
    {
        $this->jumlahPasienDiperiksa = PasienTerdaftar::whereDate('created_at', today())
            ->where('status_terdaftar', 'konsultasi')
            ->count();
    }
    public function render()
    {
        if (! Gate::allows('akses', 'Pendaftaran')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.pendaftaran.data');
    }
}
