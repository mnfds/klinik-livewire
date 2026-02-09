<?php

namespace App\Livewire\Bahan;

use Livewire\Component;
use App\Models\BahanBaku;
use App\Models\MutasiBahanbaku;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Restok extends Component
{
    public $bahan_baku_id, $jenis_keluar, $jumlah, $satuan, $diajukan_oleh, $catatan;
    public $tipe = 'masuk';

    public $bahan = [];

    public function mount()
    {
        $this->bahan = BahanBaku::all();
    }

    public function render()
    {
        return view('livewire.bahan.restok');
    }

    public function store()
    {
        $this->validate([
            'bahan_baku_id'   => 'required',
            'jumlah'   => 'required|numeric',
            'catatan'   => 'nullable',
        ]);
        if (! Gate::allows('akses', 'Persediaan Bahan Baku Masuk')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        $bahan = BahanBaku::lockForUpdate()->findOrFail($this->bahan_baku_id);
        if ($this->jenis_keluar === "besar"){
            $this->satuan = $bahan->satuan_besar;

            MutasiBahanbaku::create([
                'bahan_baku_id'   => $this->bahan_baku_id,
                'tipe' => $this->tipe,
                'jumlah'   => $this->jumlah,
                'satuan'   => $this->satuan,
                'diajukan_oleh' => Auth::user()->biodata?->nama_lengkap,
                'catatan'   => $this->catatan,
            ]);
            $stokBesarSekarang = $bahan->stok_besar + (int) $this->jumlah;
            $bahan->update([
                'stok_besar' => $stokBesarSekarang,
            ]);
        }
        if ($this->jenis_keluar === "kecil"){
            $this->satuan = $bahan->satuan_kecil;

            MutasiBahanbaku::create([
                'bahan_baku_id'   => $this->bahan_baku_id,
                'tipe' => $this->tipe,
                'jumlah'   => $this->jumlah,
                'satuan'   => $this->satuan,
                'diajukan_oleh' => Auth::user()->biodata?->nama_lengkap,
                'catatan'   => $this->catatan,
            ]);

            $stokKecilSekarang = $bahan->stok_kecil + (int) $this->jumlah;
            $bahan->update([
                'stok_kecil' => $stokKecilSekarang,
            ]);
        }
        if ($this->jenis_keluar === "besarkecil"){
            $this->satuan = $bahan->satuan_kecil;

            $stokBesarSekarang = (int) $bahan->stok_besar - (int) $this->jumlah;
            $hitungStokKecilMasuk = (int) $this->jumlah * (int) $bahan->pengali;
            $stokKecilSekarang = (int) $bahan->stok_kecil + (int) $hitungStokKecilMasuk;

            MutasiBahanbaku::create([
                'bahan_baku_id'   => $this->bahan_baku_id,
                'tipe' => 'keluar',
                'jumlah'   => $this->jumlah,
                'satuan'   => $bahan->satuan_besar,
                'diajukan_oleh' => Auth::user()->biodata?->nama_lengkap,
                'catatan'   => $this->catatan,
            ]);
            MutasiBahanbaku::create([
                'bahan_baku_id'   => $this->bahan_baku_id,
                'tipe' => 'masuk',
                'jumlah'   => $hitungStokKecilMasuk,
                'satuan'   => $bahan->satuan_kecil,
                'diajukan_oleh' => Auth::user()->biodata?->nama_lengkap,
                'catatan'   => $this->catatan,
            ]);
            
            $bahan->update([
                'stok_kecil' => (int) $stokKecilSekarang,
                'stok_besar' => (int) $stokBesarSekarang,
            ]);
        }

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data Bahan Baku berhasil Diperbarui.'
        ]);

        $this->reset();

        $this->dispatch('pg:eventRefresh-DishTable');

        $this->dispatch('closerestockModalBahanbaku');

        return redirect()->route('bahanbaku.data');
    }
}
