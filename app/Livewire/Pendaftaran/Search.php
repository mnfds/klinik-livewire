<?php

namespace App\Livewire\Pendaftaran;

use App\Models\NomorAntrian;
use App\Models\Pasien;
use Livewire\Component;

class Search extends Component
{
    public $antrianId;
    public $antrianTerdaftar;

    public $search = '';
    public $hasilPencarian = [];
    public $pasienDipilih = null;

    public function mount($id = null)
    {
        $this->antrianId = $id;

        if ($id) {
            $this->antrianTerdaftar = NomorAntrian::find($id);
        }
    }

    public function updatedSearch()
    {
        if (strlen($this->search) < 1) {
            $this->hasilPencarian = [];
            return;
        }

        $this->hasilPencarian = Pasien::query()
            ->where('nama', 'like', '%' . $this->search . '%')
            ->orWhere('no_register', 'like', '%' . $this->search . '%')
            ->limit(10)
            ->get(['id', 'nama', 'no_register'])
            ->toArray();
    }

    public function pilihPasien($id)
    {
        $pasien = Pasien::find($id);
        $this->pasienDipilih = $pasien;
        $this->search = $pasien->nama;
        $this->hasilPencarian = [];
    }

    public function clearPasien()
    {
        $this->pasienDipilih = null;
        $this->search = '';
        $this->hasilPencarian = [];
    }

    public function lanjutkan()
    {
        if (!$this->pasienDipilih) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Pilih pasien terlebih dahulu.',
            ]);
            return;
        }

        $url = route('pendaftaran.create', [
            'pasien_id' => $this->pasienDipilih['id'],
            'antrian_id' => $this->antrianId,
        ]);

        $this->redirect($url);
    }

    public function render()
    {
        return view('livewire.pendaftaran.search');
    }
}
