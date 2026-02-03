<?php

namespace App\Livewire\Bahan;

use Livewire\Component;
use App\Models\BahanBaku;
use App\Models\BahanBakuBesar;
use App\Models\MutasiBahanBakuBesar;
use App\Models\TransferBahanBaku;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Store extends Component
{
    public $nama, $kode, $satuan, $lokasi, $expired_at, $reminder, $keterangan;
    public $bahanbaku_id, $diajukan_oleh, $catatan;
    public $bahanbakubesar_id;

    public $jumlah = 0;
    public $pengali = 0;
    public $stok = 0;

    public $tipe = 'masuk';
    public $bahanbaku;
    public $bahanbakubesar;

    public function render()
    {
        return view('livewire.bahan.store');
    }

    public function mount()
    {
        $this->bahanbakubesar = BahanBakuBesar::all();
    }

    public function updatedJumlah()
    {
        $this->hitungStok();
    }

    public function updatedPengali()
    {
        $this->hitungStok();
    }

    private function hitungStok()
    {
        $this->stok = (int) $this->jumlah * (int) $this->pengali;
    }

    public function store()
    {
        $nama_bahan = BahanBakuBesar::findOrFail($this->bahanbakubesar_id);
        $this->nama = $nama_bahan->nama;

        $this->validate([
            'bahanbakubesar_id' => 'required',
            'jumlah'            => 'required',
            'pengali'           => 'required',
            'stok'              => 'numeric|required',
            'satuan'            => 'required',
            'kode'              => 'nullable',
            'expired_at'        => 'nullable',
            'reminder'          => 'nullable',
            'lokasi'            => 'nullable',
            'keterangan'        => 'nullable',
        ]);
        if (! Gate::allows('akses', 'Persediaan Bahan Baku Tambah')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        $bahanbaku = BahanBaku::create([
            'nama'      => $this->nama,
            'stok'      => $this->stok,
            'satuan'    => $this->satuan,
            'kode'      => $this->kode,
            'expired_at'=> $this->expired_at,
            'reminder'  => $this->reminder,
            'lokasi'    => $this->lokasi,
            'keterangan'=> $this->keterangan,
        ]);

        $transferBahanBaku = TransferBahanBaku::create([
            'bahan_baku_besar_id' => $this->bahanbakubesar_id,
            'bahan_baku_id'       => $bahanbaku->id,
            'pengali'             => $this->pengali
        ]);

        $mutasiBahanBakuBesar = MutasiBahanBakuBesar::create([
            'bahan_baku_besar_id'   => $transferBahanBaku->bahan_baku_besar_id,
            'tipe' => 'keluar',
            'jumlah'   => $this->jumlah,
            'catatan'   => $this->catatan,
            'diajukan_oleh' => Auth::user()->biodata?->nama_lengkap,
        ]);

        $bahanbaku->mutasibahan()->create([
            'bahan_bakus_id'     => $bahanbaku->id,
            'tipe'               => $this->tipe,
            'jumlah'             => $bahanbaku->stok,
            'diajukan_oleh'      => Auth::user()->biodata?->nama_lengkap,
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
