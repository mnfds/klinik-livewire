<?php

namespace App\Livewire\Pendapatanlainnya;

use App\Models\Pendapatanlainnya;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Create extends Component
{
    public $no_transaksi, $tanggal_transaksi, $keterangan, $unit_usaha, $status;
    public $total_tagihan = 0;

    public function render()
    {
        return view('livewire.pendapatanlainnya.create');
    }

    public function storePendapatan(){
        $this->no_transaksi = 'TRX-' . now()->format('YmdHis');
        $this->tanggal_transaksi = now();

        // dd([
        //     "no_transaksi" => $this->no_transaksi,
        //     "tanggal_transaksi" => $this->tanggal_transaksi,
        //     "keterangan" => $this->keterangan,
        //     "unit_usaha" => $this->unit_usaha,
        //     "status" => $this->status,
        //     "total_tagihan" => $this->total_tagihan,
        //     "diskon" => $this->diskon,
        //     "potongan" => $this->potongan,
        //     "total_tagihan_bersih" => $this->total_tagihan_bersih,
        // ]);

        $this->validate([
            'total_tagihan'  => 'required',
            'status'        => 'required',
            'keterangan'    => 'required',
            'unit_usaha'    => 'required',
        ]);
        if (! Gate::allows('akses', 'Pengajuan Pengeluaran Disetujui Tambah')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        Pendapatanlainnya::create([
            'no_transaksi'          => $this->no_transaksi,
            'tanggal_transaksi'     => $this->tanggal_transaksi,
            'keterangan'            => $this->keterangan,
            'total_tagihan'         => $this->total_tagihan,
            'unit_usaha'            => $this->unit_usaha,
            'status'                => $this->status,
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
