<?php

namespace App\Livewire\Barang;

use App\Models\Barang;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Update extends Component
{
    public $barang_id;
    public $nama, $kode, $satuan, $lokasi, $harga_dasar, $harga_bersih, $keterangan;
    public $diskon = 0;
    public $potongan = 0;

    public $potongan_show;
    public $harga_dasar_show;
    public $harga_bersih_show;

    #[\Livewire\Attributes\On('getupdatebarang')]
    public function getupdatebarang($rowId): void
    {
        $this->barang_id = $rowId;

        $barang = Barang::findOrFail($rowId);

        $this->nama   = $barang->nama;
        $this->kode   = $barang->kode;
        $this->satuan   = $barang->satuan;
        $this->harga_dasar   = $barang->harga_dasar;
        $this->potongan   = $barang->potongan ?? 0;
        $this->diskon   = $barang->diskon ?? 0;
        $this->harga_bersih   = $barang->harga_bersih ?? $barang->harga_dasar;
        $this->lokasi   = $barang->lokasi;
        $this->keterangan   = $barang->keterangan;

        $this->potongan_show = (int) preg_replace('/\D/', '', $this->potongan);
        $this->harga_dasar_show = (int) preg_replace('/\D/', '', $this->harga_dasar);
        $this->harga_bersih_show = (int) preg_replace('/\D/', '', $this->harga_bersih);


        $this->dispatch('openModal');
    }

    public function updated($property)
    {
        if (in_array($property, ['harga_dasar', 'diskon', 'potongan'])) {
            $harga  = (int) $this->harga_dasar;
            $diskon = (int) $this->diskon;
            $potongan = (int) $this->potongan;

            $diskonNominal = ($harga * $diskon) / 100;
            $hargaSetelahDiskon = max(0, $harga - $diskonNominal);

            $this->harga_bersih = max(0, $hargaSetelahDiskon - $potongan);
        }
    }

    public function update()
    {
        $this->validate([
            'nama' => 'required',
            'satuan' => 'required',
            'harga_dasar' => 'required',
            'kode' => 'nullable',
            'lokasi' => 'nullable',
            'keterangan' => 'nullable',
        ]);
        if (! Gate::allows('akses', 'Persediaan Barang Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        Barang::where('id', $this->barang_id)->update([
            'nama' => $this->nama,
            'kode' => $this->kode,
            'satuan' => $this->satuan,
            'harga_dasar' => $this->harga_dasar,
            'diskon' => $this->diskon,
            'potongan' => $this->potongan,
            'harga_bersih' => $this->harga_bersih,
            'lokasi' => $this->lokasi,
            'keterangan' => $this->keterangan,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);

        // ❌ Tutup modal via JS
        $this->dispatch('closemodaleditbarang');

        // 🔄 Reset form
        $this->reset();

        return redirect()->route('barang.data'); // untuk PowerGrid refresh
    }

    public function render()
    {
        return view('livewire.barang.update');
    }
}
