<?php

namespace App\Livewire\Pendapatanlainnya;

use App\Models\Pendapatanlainnya;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Pelunasan extends Component
{
    public $pendapatan_id;
    public $root_id;

    public $no_transaksi;
    public $keterangan;
    public $unit_usaha;
    public $metode_pembayaran;

    public $total_tagihan = 0;
    public $total_dibayarkan_group = 0;
    public $sisa_tagihan = 0;

    public $nominal_pelunasan;

    public function render()
    {
        return view('livewire.pendapatanlainnya.pelunasan');
    }

    #[\Livewire\Attributes\On('getpelunasan')]
    public function getpelunasan($rowId): void
    {
        $pendapatan = Pendapatanlainnya::findOrFail($rowId);

        $this->pendapatan_id = $pendapatan->id;
        $this->root_id       = $pendapatan->root_id;

        $this->no_transaksi        = $pendapatan->no_transaksi;
        $this->unit_usaha          = $pendapatan->unit_usaha;
        $this->metode_pembayaran   = $pendapatan->metode_pembayaran;

        $this->total_tagihan           = $pendapatan->total_tagihan;
        $this->total_dibayarkan_group  = $pendapatan->total_dibayarkan_group;
        $this->sisa_tagihan            = $pendapatan->sisa_tagihan;

        $this->nominal_pelunasan = null;
        $this->keterangan = null; // reset, user isi baru tiap kali buka modal

        $this->dispatch('setInfoPelunasan', [
            'total_tagihan' => $this->total_tagihan,
            'total_dibayarkan_group' => $this->total_dibayarkan_group,
            'sisa_tagihan' => $this->sisa_tagihan,
        ]);
        $this->dispatch('openmodalpelunasanpendapatan');
    }

    public function storePelunasan()
    {
        $this->validate([
            'nominal_pelunasan' => 'required|numeric|min:1|max:' . $this->sisa_tagihan,
            'keterangan'        => 'required|string|max:255',
            'metode_pembayaran' => 'required',
        ], [
            'nominal_pelunasan.max' => 'Nominal tidak boleh melebihi sisa tagihan (Rp ' . number_format($this->sisa_tagihan, 0, ',', '.') . ').',
        ]);

        if (! Gate::allows('akses', 'Pendapatan Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        $rootRow = Pendapatanlainnya::findOrFail($this->root_id);
        $sisaTerbaru = $rootRow->sisa_tagihan;

        if ($this->nominal_pelunasan > $sisaTerbaru) {
            $this->addError('nominal_pelunasan', 'Sisa tagihan sudah berubah, silakan cek ulang.');
            return;
        }

        $totalDibayarkanBaru = $rootRow->total_dibayarkan_group + $this->nominal_pelunasan;
        $status = $totalDibayarkanBaru >= $rootRow->total_tagihan ? 'lunas' : 'belum lunas';

        Pendapatanlainnya::create([
            'parent_id'          => $this->root_id,
            'no_transaksi'       => $this->no_transaksi,
            'tanggal_transaksi'  => now(),
            'keterangan'         => $this->keterangan,
            'total_tagihan'      => $rootRow->total_tagihan,
            'total_dibayarkan'   => $this->nominal_pelunasan,
            'unit_usaha'         => $rootRow->unit_usaha,
            'metode_pembayaran'  => $this->metode_pembayaran,
            'status'             => $status,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $status === 'lunas' ? 'Pembayaran berhasil, tagihan LUNAS.' : 'Pembayaran sebagian berhasil disimpan.',
        ]);

        $this->dispatch('closemodalpelunasanpendapatan');
        $this->dispatch('pg:eventRefresh')->to(\App\Livewire\Pendapatanlainnya\PendapatanTable::class);

        $this->reset(['nominal_pelunasan', 'keterangan']);
        return redirect()->route('aruskas.data');
    }
}