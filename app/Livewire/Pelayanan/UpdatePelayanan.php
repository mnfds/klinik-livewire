<?php

namespace App\Livewire\Pelayanan;

use App\Models\Pelayanan;
use Livewire\Component;

class UpdatePelayanan extends Component
{
    public $pelayananId;
    public $nama_pelayanan, $harga_pelayanan, $harga_bersih, $deskripsi;
    public $diskon = 0;

    public $harga_pelayanan_show, $harga_bersih_show;

    #[\Livewire\Attributes\On('editPelayanan')]
    public function editPelayanan($rowId): void
    {
        $this->pelayananId = $rowId;

        $pelayanan = Pelayanan::findOrFail($rowId);

        $this->nama_pelayanan  = $pelayanan->nama_pelayanan;
        $this->harga_pelayanan = $pelayanan->harga_pelayanan;
        $this->deskripsi       = $pelayanan->deskripsi;
        $this->diskon          = $pelayanan->diskon ?? 0;
        $this->harga_bersih    = $pelayanan->harga_bersih ?? $pelayanan->harga_pelayanan;

        $this->harga_pelayanan_show = (int) preg_replace('/\D/', '', $this->harga_pelayanan);
        $this->harga_bersih_show = (int) preg_replace('/\D/', '', $this->harga_bersih);
        $this->dispatch('setHargaPelayanan', $this->harga_pelayanan);
        $this->dispatch('openModal');
    }

    public function updated($property)
    {
        if (in_array($property, ['harga_pelayanan', 'diskon'])) {
            $harga  = (int) $this->harga_pelayanan;
            $diskon = (int) $this->diskon;

            $this->harga_bersih = $harga - ($harga * $diskon / 100);
        }
    }

    public function update()
    {
        $this->validate([
            'nama_pelayanan'   => 'required',
            'harga_pelayanan'  => 'required|numeric|min:0',
            'diskon'           => 'nullable|numeric|min:0|max:100',
            'deskripsi'        => 'nullable|string',
        ]);

        Pelayanan::where('id', $this->pelayananId)->update([
            'nama_pelayanan'  => $this->nama_pelayanan,
            'harga_pelayanan' => $this->harga_pelayanan,
            'diskon'          => $this->diskon,
            'harga_bersih'    => $this->harga_bersih,
            'deskripsi'       => $this->deskripsi,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data pelayanan medis berhasil diperbarui.'
        ]);

        $this->dispatch('closeModal');
        $this->reset();

        return redirect()->route('pelayanan.data');
    }

    public function render()
    {
        return view('livewire.pelayanan.update-pelayanan');
    }
}

