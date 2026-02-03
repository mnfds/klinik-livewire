<?php

namespace App\Livewire\Bahanbakubesar;

use Livewire\Component;
use App\Models\BahanBakuBesar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Store extends Component
{
    public $nama, $kode, $satuan, $stok, $lokasi, $expired_at, $reminder, $keterangan;
    public $bahanbaku_id, $jumlah, $diajukan_oleh, $catatan;
    public $tipe = 'masuk';
    public $bahanbakubesar;

    public function render()
    {
        return view('livewire.bahanbakubesar.store');
    }

    public function store()
    {
        $this->validate([
            'nama'          => 'required',
            'stok'          => 'numeric|required',
            'satuan'        => 'required',
            'kode'          => 'nullable',
            'expired_at'    => 'nullable',
            'reminder'      => 'nullable',
            'lokasi'        => 'nullable',
            'keterangan'    => 'nullable',
        ]);
        if (! Gate::allows('akses', 'Persediaan Bahan Baku Tambah')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        $bahanbakubesar = BahanBakuBesar::create([
            'nama'      => $this->nama,
            'stok'      => $this->stok,
            'satuan'    => $this->satuan,
            'kode'      => $this->kode,
            'expired_at'=> $this->expired_at,
            'reminder'  => $this->reminder,
            'lokasi'    => $this->lokasi,
            'keterangan'=> $this->keterangan,
        ]);

        $bahanbakubesar->mutasibahanbakubesar()->create([
            'bahan_baku_besar_id'=> $bahanbakubesar->id,
            'tipe'               => $this->tipe,
            'jumlah'             => $bahanbakubesar->stok,
            'diajukan_oleh'      => Auth::user()->biodata?->nama_lengkap,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data Bahan Baku berhasil Dibuat.'
        ]);

        $this->dispatch('closestoreModalBahanBesar');

        $this->reset();

        return redirect()->route('bahanbakubesar.data');
    }
}
