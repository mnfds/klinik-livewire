<?php

namespace App\Livewire\Pendapatanlainnya;

use App\Models\Pendapatanlainnya;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Create extends Component
{
    public $no_transaksi, $tanggal_transaksi, $keterangan, $unit_usaha, $metode_pembayaran, $status;
    public $total_tagihan;
    public $total_dibayarkan;

    public function render()
    {
        return view('livewire.pendapatanlainnya.create');
    }

    public function storePendapatan(){
        $this->validate([
            'total_tagihan'         => 'required|numeric|min:1',
            'total_dibayarkan'      => 'required|numeric|min:0|lte:total_tagihan',
            'keterangan'            => 'required',
            'unit_usaha'            => 'required',
            'metode_pembayaran'     => 'required',
        ]);

        if (! Gate::allows('akses', 'Pendapatan Tambah')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        $this->no_transaksi = 'TRX-' . now()->format('YmdHis');
        $this->tanggal_transaksi = now();

        // status otomatis: lunas kalau dibayarkan sudah menutupi tagihan
        $status = $this->total_dibayarkan >= $this->total_tagihan ? 'lunas' : 'belum lunas';

        Pendapatanlainnya::create([
            'parent_id'              => null, // ini row akar / transaksi baru
            'no_transaksi'           => $this->no_transaksi,
            'tanggal_transaksi'      => $this->tanggal_transaksi,
            'keterangan'             => $this->keterangan,
            'total_tagihan'          => $this->total_tagihan,
            'total_dibayarkan'       => $this->total_dibayarkan,
            'unit_usaha'             => $this->unit_usaha,
            'metode_pembayaran'      => $this->metode_pembayaran,
            'status'                 => $status,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Pendapatan Lainnya Berhasil Ditambahkan.'
        ]);
        $this->dispatch('storePendapatan');
        $this->reset();
        return redirect()->route('aruskas.data');
    }
}
