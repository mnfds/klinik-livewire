<?php

namespace App\Livewire\Produkdanobat\Mutasi;

use App\Models\MutasiProdukDanObat;
use App\Models\ProdukDanObat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Outstock extends Component
{
    public string $tipe = 'keluar';
    public int $activeTab = 0;

    public array $items = [
        ['produk_id' => '', 'jumlah' => '', 'catatan' => ''],
    ];

    public $produkobat = [];

    public function mount(): void
    {
        $this->produkobat = ProdukDanObat::all();
    }

    public function render()
    {
        return view('livewire.produkdanobat.mutasi.outstock');
    }

    public function addTab(): void
    {
        $this->items[] = ['produk_id' => '', 'jumlah' => '', 'catatan' => ''];
        $this->activeTab = count($this->items) - 1;
    }

    public function removeTab(int $index): void
    {
        if (count($this->items) <= 1) return;

        array_splice($this->items, $index, 1);

        if ($this->activeTab >= count($this->items)) {
            $this->activeTab = count($this->items) - 1;
        }
    }

    public function store()
    {
        $this->validate([
            'items'             => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produk_dan_obats,id',
            'items.*.jumlah'    => 'required|numeric|min:1',
            'items.*.catatan'   => 'nullable|string',
        ], [
            'items.*.produk_id.required' => 'Nama produk/obat wajib dipilih.',
            'items.*.produk_id.exists'   => 'Produk/obat tidak valid.',
            'items.*.jumlah.required'    => 'Jumlah wajib diisi.',
            'items.*.jumlah.min'         => 'Jumlah minimal 1.',
        ]);

        if (! Gate::allows('akses', 'Persediaan Produk & Obat Keluar')) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Anda tidak memiliki akses.']);
            return;
        }

        $pengaju = Auth::user()->biodata?->nama_lengkap;

        foreach ($this->items as $item) {
            MutasiProdukDanObat::create([
                'produk_id'     => $item['produk_id'],
                'tipe'          => $this->tipe,
                'jumlah'        => $item['jumlah'],
                'catatan'       => $item['catatan'] ?? null,
                'diajukan_oleh' => $pengaju,
            ]);

            ProdukDanObat::findOrFail($item['produk_id'])->decrement('stok', $item['jumlah']);
        }

        $this->items     = [['produk_id' => '', 'jumlah' => '', 'catatan' => '']];
        $this->activeTab = 0;

        $this->dispatch('toast', ['type' => 'success', 'message' => 'Data Produk/Obat berhasil Diperbarui.']);
        $this->dispatch('pg:eventRefresh-DishTable');
        $this->dispatch('closeoutstockModalProdukDanObat');

        return redirect()->route('produk-obat.data');
    }
}
