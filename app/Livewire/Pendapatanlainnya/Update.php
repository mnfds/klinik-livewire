<?php

namespace App\Livewire\Pendapatanlainnya;

use App\Models\Pendapatanlainnya;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Update extends Component
{
    public $no_transaksi, $tanggal_transaksi, $keterangan, $unit_usaha, $metode_pembayaran, $status;
    public $total_tagihan;
    public $total_dibayarkan;
    public $pendapatan_id;
    public $isPartOfGroup = false; // true kalau row ini sudah punya cicilan/bagian dari cicilan

    public function render()
    {
        return view('livewire.pendapatanlainnya.update');
    }

    #[\Livewire\Attributes\On('getupdatependapatan')]
    public function getupdatependapatan($rowId): void
    {
        $this->pendapatan_id = $rowId;

        $pendapatan = Pendapatanlainnya::findOrFail($this->pendapatan_id);

        $rootId = $pendapatan->parent_id ?? $pendapatan->id;
        $jumlahAnggotaGrup = Pendapatanlainnya::grup($rootId)->count();

        $this->isPartOfGroup      = $jumlahAnggotaGrup > 1;

        $this->total_tagihan        = $pendapatan->total_tagihan;
        $this->total_dibayarkan     = $pendapatan->total_dibayarkan;
        $this->keterangan           = $pendapatan->keterangan;
        $this->unit_usaha           = $pendapatan->unit_usaha;
        $this->metode_pembayaran    = $pendapatan->metode_pembayaran;
        $this->status               = $pendapatan->status;

        $this->dispatch('setJumlahPendapatan', $this->total_tagihan);
        $this->dispatch('setJumlahDibayarkanPendapatan', $this->total_dibayarkan);
        $this->dispatch('openmodaleditpendapatan');
    }

    public function updatePendapatan()
    {
        $this->validate([
            'total_tagihan'         => 'required|numeric|min:1',
            'total_dibayarkan'      => 'required|numeric|min:0',
            'keterangan'            => 'required',
            'unit_usaha'            => 'required',
            'metode_pembayaran'     => 'required',
        ]);

        if (! Gate::allows('akses', 'Pendapatan Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        $pendapatan = Pendapatanlainnya::findOrFail($this->pendapatan_id);
        $rootId = $pendapatan->root_id;

        \Illuminate\Support\Facades\DB::transaction(function () use ($pendapatan, $rootId) {
            $updateData = [
                'total_dibayarkan'  => $this->total_dibayarkan,
                'keterangan'        => $this->keterangan,
                'unit_usaha'        => $this->unit_usaha,
                'metode_pembayaran' => $this->metode_pembayaran,
            ];

            // total_tagihan hanya boleh diubah kalau row belum bagian dari cicilan (grup masih 1 row)
            if (! $this->isPartOfGroup) {
                $updateData['total_tagihan'] = $this->total_tagihan;
            }

            $pendapatan->update($updateData);

            // resync status SELURUH grup, bukan cuma row ini —
            // supaya tidak ada row lain yang statusnya jadi basi (misal masih 'lunas' padahal sisa berubah)
            $pendapatan->fresh()->resyncGroupStatus();
        });

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);
        $this->dispatch('closemodaleditpendapatan');
        $this->dispatch('pg:eventRefresh')->to(\App\Livewire\Pendapatanlainnya\PendapatanTable::class);
        $this->reset();
        return redirect()->route('aruskas.data');
    }
}
