<?php

namespace App\Livewire\Barang;

use App\Models\Barang;
use Livewire\Component;
use App\Models\MutasiBarang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Store extends Component
{
    public $nama, $kode, $satuan, $stok, $harga_dasar, $harga_bersih, $lokasi, $keterangan;
    public $barang_id, $jumlah, $diajukan_oleh, $catatan;
    public $tipe = 'masuk';
    public $barang;
    public $diskon = 0;
    public $potongan = 0;

    public function render()
    {
        return view('livewire.barang.store');
    }

    public function store()
    {
        $this->validate([
            'nama'          => 'required',
            'stok'          => 'numeric|required',
            'satuan'        => 'required',
            'harga_dasar'   => 'required',
            'lokasi'        => 'nullable',
            'keterangan'    => 'nullable',
        ]);
        if (! Gate::allows('akses', 'Persediaan Barang Tambah')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        $barang = Barang::create([
            'nama'   => $this->nama,
            'stok'   => $this->stok,
            'satuan'   => $this->satuan,
            'kode'   => $this->kode,
            'harga_dasar'   => $this->harga_dasar,
            'diskon'   => $this->diskon,
            'potongan'   => $this->potongan,
            'harga_bersih'   => $this->harga_bersih,
            'lokasi'   => $this->lokasi,
            'keterangan'   => $this->keterangan,
        ]);

        $barang->mutasi()->create([
            'barang_id'     => $barang->id,
            'tipe'     => $this->tipe,
            'jumlah'     => $barang->stok,
            'diajukan_oleh' => Auth::user()->biodata?->nama_lengkap,
            'catatan'   => "Item Baru Saja Ditambahkan",
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data Barang berhasil Diperbarui.'
        ]);

        $this->dispatch('closestoreModalBarang');

        $this->reset();

        return redirect()->route('barang.data');
    }
}
