<?php

namespace App\Livewire\Apotik;

use Livewire\Component;
use App\Models\TransaksiApotik;
use Illuminate\Support\Facades\Gate;

class Detail extends Component
{
    public $transaksi;

    public function mount($id)
    {
        $this->transaksi = TransaksiApotik::with(['riwayat.produk','riwayatBarang.barang'])->findOrFail($id);
    }

    public function render()
    {
        if (! Gate::allows('akses', 'Transaksi Apotik Detail')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.apotik.detail');
    }
}
