<?php

namespace App\Livewire\Bahan;

use Livewire\Component;
use App\Models\BahanBaku;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Store extends Component
{
    // BAHAN VAR
    public $nama, $kode, $lokasi, $expired_at, $reminder, $keterangan, $satuan_besar, $satuan_kecil;
    public $tipe = 'masuk';
    public $stok_besar = 0;
    public $pengali = 0;
    public $stok_kecil = 0;
    
    // MUTASI VAR
    public $bahanbaku_id, $jumlah, $satuan, $diajukan_oleh;
    public $catatan = "Bahan Baru Saja Ditambahkan";

    public function render()
    {
        return view('livewire.bahan.store');
    }

    public function updatedStokBesar()
    {
        $this->hitungStokKecil();
    }

    public function updatedPengali()
    {
        $this->hitungStokKecil();
    }

    private function hitungStokKecil()
    {
        $this->stok_kecil = (int) $this->stok_besar * (int) $this->pengali;
    }

    public function store()
    {
        $this->validate([
            'nama'          => 'required',
            'stok_besar'    => 'numeric|required',
            'satuan_besar'  => 'required',
            'pengali'       => 'numeric|required',
            'stok_kecil'    => 'numeric|required',
            'satuan_kecil'  => 'required',
            // 'kode'          => 'nullable',
            'lokasi'        => 'nullable',
            'expired_at'    => 'nullable',
            'reminder'      => 'nullable',
            'keterangan'    => 'nullable',
        ]);

        if (! Gate::allows('akses', 'Persediaan Bahan Baku Tambah')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        $bahanbaku = BahanBaku::create([
            'nama'            => $this->nama,
            'stok_besar'      => (int) $this->stok_besar,
            'satuan_besar'    => $this->satuan_besar,
            'pengali'         => (int) $this->pengali,
            'stok_kecil'      => (int) $this->stok_kecil,
            'satuan_kecil'    => $this->satuan_kecil,
            // 'kode'         => $this->kode,
            'lokasi'          => $this->lokasi,
            'expired_at'      => $this->expired_at,
            'reminder'        => (int) $this->reminder,
            'keterangan'      => $this->keterangan,
        ]);

        // CREATE MUTASI INSTOCK UNTUK BAHAN BAKU BESAR DAN KECIL
        $bahanbaku->mutasibahan()->create([
            'bahan_bakus_id'     => $bahanbaku->id,
            'tipe'               => $this->tipe,
            'jumlah'             => $bahanbaku->stok_besar,
            'satuan'             => $bahanbaku->satuan_besar,
            'diajukan_oleh'      => Auth::user()->biodata?->nama_lengkap,
            'catatan'            => $this->catatan,
        ]);
        $bahanbaku->mutasibahan()->create([
            'bahan_bakus_id'     => $bahanbaku->id,
            'tipe'               => $this->tipe,
            'jumlah'             => $bahanbaku->stok_kecil,
            'satuan'             => $bahanbaku->satuan_kecil,
            'diajukan_oleh'      => Auth::user()->biodata?->nama_lengkap,
            'catatan'            => $this->catatan,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data Bahan Baku berhasil Dibuat.'
        ]);

        $this->dispatch('closestoreModalBahan');

        $this->reset();

        return redirect()->route('bahanbaku.data');
    }
}
