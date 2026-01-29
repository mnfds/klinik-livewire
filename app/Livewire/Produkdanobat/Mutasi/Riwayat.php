<?php

namespace App\Livewire\Produkdanobat\Mutasi;

use App\Models\MutasiProdukDanObat;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Gate;

#[Layout('layouts.app')]
class Riwayat extends Component
{
    public function render()
    {
        if (! Gate::allows('akses', 'Riwayat Produk & Obat')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.produkdanobat.mutasi.riwayat');
    }
}
