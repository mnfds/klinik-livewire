<?php

namespace App\Livewire\Pendapatanlainnya;

use App\Models\Pendapatanlainnya;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Update extends Component
{
    public $no_transaksi, $tanggal_transaksi, $keterangan, $unit_usaha, $status;
    public $total_tagihan;
    public $pendapatan_id;

    public function render()
    {
        return view('livewire.pendapatanlainnya.update');
    }

    #[\Livewire\Attributes\On('getupdatependapatan')]
    public function getupdatependapatan($rowId): void
    {
        $this->pendapatan_id = $rowId;

        $pendapatan = Pendapatanlainnya::findOrFail($this->pendapatan_id);

        $this->total_tagihan        = $pendapatan->total_tagihan;
        $this->keterangan           = $pendapatan->keterangan;
        $this->unit_usaha           = $pendapatan->unit_usaha;
        $this->status               = $pendapatan->status;
        $this->dispatch('setJumlahPendapatan', $this->total_tagihan);
        $this->dispatch('openmodaleditpendapatan');
    }

    public function updatePendapatan()
    {
        $this->validate([
            'total_tagihan'  => 'required',
            'status'        => 'required',
            'keterangan'    => 'required',
            'unit_usaha'    => 'required',
        ]);
        if (! Gate::allows('akses', 'Pendapatan Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        Pendapatanlainnya::where('id', $this->pendapatan_id)->update([
            'total_tagihan' => $this->total_tagihan,
            'status' => $this->status,
            'keterangan' => $this->keterangan,
            'unit_usaha' => $this->unit_usaha,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);
        $this->dispatch('modaleditpendapatan');
        $this->reset();
        return redirect()->route('aruskas.data');
    }

}
