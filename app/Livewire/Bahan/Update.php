<?php

namespace App\Livewire\Bahan;

use Livewire\Component;
use App\Models\BahanBaku;
use App\Models\MutasiBahanbaku;
use Illuminate\Support\Facades\Gate;

class Update extends Component
{
    public $bahan_id;
    public $nama, $kode, $lokasi, $expired_at, $reminder, $keterangan, $satuan_besar, $satuan_kecil;
    public $satuan_besar_old, $satuan_kecil_old;
    // public $stok_besar = 0;
    public $pengali = 0;
    // public $stok_kecil = 0;


    #[\Livewire\Attributes\On('getupdatebahanbaku')]
    public function getupdatebahanbaku($rowId): void
    {
        $this->bahan_id = $rowId;
        
        $bahan = BahanBaku::findOrFail($rowId);

        $this->nama         = $bahan->nama;
        $this->kode         = $bahan->kode;
        // $this->stok_besar   = $bahan->stok_besar;
        $this->satuan_besar = $bahan->satuan_besar;
        $this->satuan_besar_old = $bahan->satuan_besar;
        $this->pengali      = $bahan->pengali;
        // $this->stok_kecil   = $bahan->stok_kecil;
        $this->satuan_kecil = $bahan->satuan_kecil;
        $this->satuan_kecil_old = $bahan->satuan_kecil;
        $this->lokasi       = $bahan->lokasi;
        $this->expired_at   = $bahan->expired_at;
        $this->reminder     = $bahan->reminder;
        $this->keterangan   = $bahan->keterangan;

        $this->dispatch('openModal');
    }
    
    public function update()
    {
        $this->validate([
            'nama'          => 'required',
            // 'stok_besar'    => 'numeric|required',
            'satuan_besar'  => 'required',
            'pengali'       => 'numeric|required',
            // 'stok_kecil'    => 'numeric|required',
            'satuan_kecil'  => 'required',
            // 'kode'          => 'nullable',
            'lokasi'        => 'nullable',
            'expired_at'    => 'nullable',
            'reminder'      => 'nullable',
            'keterangan'    => 'nullable',
        ]);
        if (! Gate::allows('akses', 'Persediaan Bahan Baku Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        BahanBaku::where('id', $this->bahan_id)->update([
            'nama'            => $this->nama,
            // 'stok_besar'      => (int) $this->stok_besar,
            'satuan_besar'    => $this->satuan_besar,
            'pengali'         => (int) $this->pengali,
            // 'stok_kecil'      => (int) $this->stok_kecil,
            'satuan_kecil'    => $this->satuan_kecil,
            // 'kode'         => $this->kode,
            'lokasi'          => $this->lokasi,
            'expired_at'      => $this->expired_at,
            'reminder'        => (int) $this->reminder,
            'keterangan'      => $this->keterangan,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);

        // ❌ Tutup modal via JS
        $this->dispatch('closemodaleditbahanbaku');

        // 🔄 Reset form
        $this->reset();

        return redirect()->route('bahanbaku.data'); // untuk PowerGrid refresh
    }

    public function render()
    {
        return view('livewire.bahan.update');
    }
}
