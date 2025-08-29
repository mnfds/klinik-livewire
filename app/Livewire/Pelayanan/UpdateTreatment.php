<?php

namespace App\Livewire\Pelayanan;

use App\Models\Treatment;
use Livewire\Component;

class UpdateTreatment extends Component
{
    public $treatmentId;
    public $nama_treatment, $harga_treatment, $harga_bersih, $deskripsi;
    public $diskon = 0;

    public $harga_treatment_show, $harga_bersih_show;
 
    #[\Livewire\Attributes\On('editTreatment')]
    public function editTreatment($rowId): void
    {
        $this->treatmentId = $rowId;

        $treatment = Treatment::findOrFail($rowId);

        $this->nama_treatment  = $treatment->nama_treatment;
        $this->harga_treatment = $treatment->harga_treatment;
        $this->deskripsi       = $treatment->deskripsi;
        $this->diskon          = $treatment->diskon ?? 0;
        $this->harga_bersih    = $treatment->harga_bersih ?? $treatment->harga_treatment;

        $this->harga_treatment_show = (int) preg_replace('/\D/', '', $this->harga_treatment);
        $this->harga_bersih_show = (int) preg_replace('/\D/', '', $this->harga_bersih);
        $this->dispatch('setHargaTreatment', $this->harga_treatment);
        $this->dispatch('openModal');
    }

    public function updated($property)
    {
        if (in_array($property, ['harga_treatment', 'diskon'])) {
            $harga  = (int) $this->harga_treatment;
            $diskon = (int) $this->diskon;

            $this->harga_bersih = $harga - ($harga * $diskon / 100);
        }
    }

    public function update()
    {
        $this->validate([
            'nama_treatment'   => 'required',
            'harga_treatment'  => 'required|numeric|min:0',
            'diskon'           => 'nullable|numeric|min:0|max:100',
            'deskripsi'        => 'nullable|string',
        ]);

        Treatment::where('id', $this->treatmentId)->update([
            'nama_treatment'  => $this->nama_treatment,
            'harga_treatment' => $this->harga_treatment,
            'diskon'          => $this->diskon,
            'harga_bersih'    => $this->harga_bersih,
            'deskripsi'       => $this->deskripsi,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data pelayanan estetika berhasil diperbarui.'
        ]);

        $this->dispatch('closeModalEstetika');
        $this->reset();

        return redirect()->route('pelayanan.data');
    }

    public function render()
    {
        return view('livewire.pelayanan.update-treatment');
    }
}
