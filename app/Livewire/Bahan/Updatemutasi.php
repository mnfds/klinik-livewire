<?php

namespace App\Livewire\Bahan;

use App\Models\Biodata;
use Livewire\Component;
use App\Models\BahanBaku;
use App\Models\MutasiBahanbaku;
use Illuminate\Support\Facades\Gate;

class Updatemutasi extends Component
{
    public $riwayat_id;
    public $bahan_baku_id, $tipe, $jumlah, $diajukan_oleh, $catatan;
    public $riwayat;

    public $listBahan = [];
    public $listOrang = [];


    public function mount()
    {
        $this->listBahan = BahanBaku::pluck('nama', 'id')->toArray();
        $this->listOrang = Biodata::pluck('nama_lengkap', 'id')->toArray();
    }

    #[\Livewire\Attributes\On('getupdatemutasibahan')]
    public function getupdatemutasibahan($rowId): void
    {
        $this->riwayat_id = $rowId;

        $riwayat = MutasiBahanbaku::with('bahanbaku')->findOrFail($rowId);
        $this->bahan_baku_id   = $riwayat->bahanbaku->id;
        $this->tipe   = $riwayat->tipe;
        $this->jumlah   = $riwayat->jumlah;
        $this->diajukan_oleh   = $riwayat->diajukan_oleh;
        $this->catatan   = $riwayat->catatan;

        $this->dispatch('openModal');
    }

    public function update()
    {
        $this->validate([
            'bahan_baku_id' => 'required',
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

        $data = MutasiBahanbaku::where('id', $this->riwayat_id)->update([
            'bahan_baku_id' => $this->bahan_baku_id,
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
        $this->dispatch('closemodaleditmutasibahan');

        // 🔄 Reset form
        $this->reset();

        return redirect()->route('bahanbaku.riwayat'); // untuk PowerGrid refresh
    }

    public function render()
    {
        return view('livewire.bahan.updatemutasi');
    }
}
