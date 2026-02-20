<?php

namespace App\Livewire\Pelayanan;

use App\Models\BahanBaku;
use App\Models\LayananBahan;
use App\Models\Pelayanan;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Updatebahanlayanan extends Component
{
    public $layananBahanId;
    public $nama_layanan;
    public $allBahan;
    public $selectedBahan = [];

    public function mount()
    {
        // Inisialisasi kosong agar tidak null saat pertama kali render
        $this->allBahan = collect();

    }  
    
    #[\Livewire\Attributes\On('getupdatebahanlayanan')]
    public function getupdatebahanlayanan($rowId): void
    {
        $this->layananBahanId = $rowId;

        $pelayanan = Pelayanan::findOrFail($rowId);
        $this->nama_layanan = $pelayanan->nama_pelayanan;

        // Ambil semua bahan baku urut berdasarkan id
        $this->allBahan = BahanBaku::all();

        // Ambil bahan baku yang sudah dipakai pelayanan
        $this->selectedBahan = LayananBahan::where('pelayanan_id', $this->layananBahanId)
            ->pluck('bahan_baku_id')
            ->toArray();

        // Buka modal
        $this->dispatch('modaleditbahanlayanan');
    }

    public function updatelayanan()
    {
        if (! Gate::allows('akses', 'Pelayanan Estetika Tambah Bahan')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        // Simpan relasi bahan baku dengan pelayanan
        LayananBahan::where('pelayanan_id', $this->layananBahanId)->delete();

        foreach ($this->selectedBahan as $bahanId) {
            LayananBahan::create([
                'pelayanan_id' => $this->layananBahanId,
                'bahan_baku_id' => $bahanId,
            ]);
        }

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data Bahan Baku Terkait Berhasil Diperbarui.',
        ]);

        $this->dispatch('modaleditbahanlayanan');
        $this->reset();

        return redirect()->route('pelayanan.data');
    }

    public function render()
    {
        return view('livewire.pelayanan.updatebahanlayanan');
    }
}
