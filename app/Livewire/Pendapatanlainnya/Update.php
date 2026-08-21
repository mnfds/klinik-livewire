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

        // status dihitung ulang otomatis berdasarkan total dibayarkan kumulatif se-grup
        $rootId = $pendapatan->parent_id ?? $pendapatan->id;
        $totalDibayarkanGrupLain = Pendapatanlainnya::grup($rootId)
            ->where('id', '!=', $pendapatan->id)
            ->sum('total_dibayarkan');

        $status = ($totalDibayarkanGrupLain + $this->total_dibayarkan) >= $this->total_tagihan
            ? 'lunas'
            : 'belum lunas';

        $updateData = [
            'total_dibayarkan'  => $this->total_dibayarkan,
            'status'            => $status,
            'keterangan'        => $this->keterangan,
            'unit_usaha'        => $this->unit_usaha,
            'metode_pembayaran' => $this->metode_pembayaran,
        ];

        // total_tagihan hanya boleh diubah kalau row belum bagian dari cicilan (grup masih 1 row)
        if (! $this->isPartOfGroup) {
            $updateData['total_tagihan'] = $this->total_tagihan;
        }

        $pendapatan->update($updateData);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);
        $this->dispatch('closemodaleditpendapatan');
        $this->reset();
        return redirect()->route('aruskas.data');
    }
}
