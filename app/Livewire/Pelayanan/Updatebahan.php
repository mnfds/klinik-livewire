<?php

namespace App\Livewire\Pelayanan;

use App\Models\BahanBaku;
use App\Models\Treatment;
use App\Models\TreatmentBahan;
use Livewire\Component;

class Updatebahan extends Component
{
    public $treatmentBahanId;
    public $nama_treatment;
    public $allBahan;
    public $selectedBahan = [];

    public function mount()
    {
        // Inisialisasi kosong agar tidak null saat pertama kali render
        $this->allBahan = collect();
    }

    #[\Livewire\Attributes\On('getupdatebahan')]
    public function getupdatebahan($rowId): void
    {
        $this->treatmentBahanId = $rowId;

        $treatment = Treatment::findOrFail($rowId);
        $this->nama_treatment = $treatment->nama_treatment;

        // Ambil semua bahan baku urut berdasarkan id
        $this->allBahan = BahanBaku::all();

        // Ambil bahan baku yang sudah dipakai treatment
        $this->selectedBahan = TreatmentBahan::where('treatments_id', $this->treatmentBahanId)
            ->pluck('bahan_baku_id')
            ->toArray();

        // Buka modal
        $this->dispatch('modaleditbahan');
    }

    public function update()
    {
        // Simpan relasi bahan baku dengan treatment
        TreatmentBahan::where('treatments_id', $this->treatmentBahanId)->delete();

        foreach ($this->selectedBahan as $bahanId) {
            TreatmentBahan::create([
                'treatments_id' => $this->treatmentBahanId,
                'bahan_baku_id' => $bahanId,
            ]);
        }

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data Bahan Baku Terkait Berhasil Diperbarui.',
        ]);

        $this->dispatch('closeModalbahan');
        $this->reset();

        return redirect()->route('pelayanan.data');
    }

    public function render()
    {
        return view('livewire.pelayanan.updatebahan');
    }
}
