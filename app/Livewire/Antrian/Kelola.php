<?php

namespace App\Livewire\Antrian;

use App\Models\NomorAntrian;
use App\Models\PasienTerdaftar;
use Livewire\Component;

class Kelola extends Component
{
    public $jumlahPasien;
    public $jumlahPasienTerdaftar;
    public $jumlahPasienDiperiksa;

    public function mount()
    {
        $this->updateJumlahPasien();
        $this->updateJumlahPasienTerdaftar();
        $this->updateJumlahPasienDiperiksa();
    }

    public function updateJumlahPasien()
    {
        $this->jumlahPasien = NomorAntrian::whereDate('created_at', today())
            ->whereIn('status', ['masuk', 'dipanggil'])->count();
    }
    public function updateJumlahPasienTerdaftar()
    {
        $this->jumlahPasienTerdaftar = PasienTerdaftar::whereDate('created_at', today())
            ->where('status_terdaftar', 'terdaftar')->count();
    }
    public function updateJumlahPasienDiperiksa()
    {
        $this->jumlahPasienDiperiksa = PasienTerdaftar::whereDate('created_at', today())
            ->where('status_terdaftar', 'terkaji')->count();
    }

    public function refreshTableMasuk()
    {
        $this->dispatch('pg:eventRefresh-AntrianMasuk');
    }
    public function refreshTableDipanggil()
    {
        $this->dispatch('pg:eventRefresh-AntrianDipanggil');
    }

    public function render()
    {
        return view('livewire.antrian.kelola');
    }
}
