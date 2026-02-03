<?php

namespace App\Livewire\Bahanbakubesar;

use App\Models\BahanBakuBesar;
use App\Models\Biodata;
use App\Models\MutasiBahanBakuBesar;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Mutasiupdate extends Component
{
    public $riwayat_id;
    public $bahan_baku_besar_id, $tipe, $jumlah, $diajukan_oleh, $catatan;
    public $riwayat;

    public $listBahan = [];
    public $listOrang = [];

    public function mount()
    {
        $this->listBahan = BahanBakuBesar::pluck('nama', 'id')->toArray();
        $this->listOrang = Biodata::pluck('nama_lengkap', 'id')->toArray();
    }

    public function render()
    {
        return view('livewire.bahanbakubesar.mutasiupdate');
    }

    #[\Livewire\Attributes\On('getupdatemutasibahanbesar')]
    public function getupdatemutasibahanbesar($rowId): void
    {
        $this->riwayat_id = $rowId;

        $riwayat = MutasiBahanBakuBesar::with('bahanbakubesar')->findOrFail($rowId);
        $this->bahan_baku_besar_id   = $riwayat->bahanbakubesar->id;
        $this->tipe   = $riwayat->tipe;
        $this->jumlah   = $riwayat->jumlah;
        $this->diajukan_oleh   = $riwayat->diajukan_oleh;
        $this->catatan   = $riwayat->catatan;

        $this->dispatch('openModal');
    }

    public function update()
    {
        $this->validate([
            'bahan_baku_besar_id' => 'required',
            'tipe' => 'required',
            'jumlah' => 'required|numeric',
            'diajukan_oleh' => 'required',
            'catatan' => 'nullable',
        ]);
        if (! Gate::allows('akses', 'Persediaan Riwayat Bahan Baku Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        $data = MutasiBahanBakuBesar::where('id', $this->riwayat_id)->update([
            'bahan_baku_besar_id' => $this->bahan_baku_besar_id,
            'tipe' => $this->tipe,
            'jumlah' => $this->jumlah,
            'diajukan_oleh' => $this->diajukan_oleh,
            'catatan' => $this->catatan,
        ]);


        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);

        // ❌ Tutup modal via JS
        $this->dispatch('closemodaleditmutasibahanbesar');

        // 🔄 Reset form
        $this->reset();

        return redirect()->route('bahanbakubesar.mutasi'); // untuk PowerGrid refresh
    }

}
