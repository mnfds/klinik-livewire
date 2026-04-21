<?php

namespace App\Livewire\Barang;

use App\Models\Barang;
use Livewire\Component;
use App\Models\MutasiBarang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Restok extends Component
{
    public string $tipe = 'masuk';
    public int $activeTab = 0;

    public array $items = [
        ['barang_id' => '', 'jumlah' => '', 'catatan' => ''],
    ];

    public $barang = [];

    public function mount(): void
    {
        $this->barang = Barang::all();
    }

    public function render()
    {
        return view('livewire.barang.restok');
    }

    public function addTab(): void
    {
        $this->items[] = ['barang_id' => '', 'jumlah' => '', 'catatan' => ''];
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
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah'    => 'required|numeric|min:1',
            'items.*.catatan'   => 'nullable|string',
        ], [
            'items.*.barang_id.required' => 'Nama barang wajib dipilih.',
            'items.*.barang_id.exists'   => 'Barang tidak valid.',
            'items.*.jumlah.required'    => 'Jumlah wajib diisi.',
            'items.*.jumlah.min'         => 'Jumlah minimal 1.',
        ]);

        if (! Gate::allows('akses', 'Persediaan Barang Masuk')) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Anda tidak memiliki akses.']);
            return;
        }

        $pengaju = Auth::user()->biodata?->nama_lengkap;

        foreach ($this->items as $item) {
            MutasiBarang::create([
                'barang_id'     => $item['barang_id'],
                'tipe'          => $this->tipe,
                'jumlah'        => $item['jumlah'],
                'catatan'       => $item['catatan'] ?? null,
                'diajukan_oleh' => $pengaju,
            ]);

            // ✅ increment, bukan decrement
            Barang::findOrFail($item['barang_id'])->increment('stok', $item['jumlah']);
        }

        $this->items     = [['barang_id' => '', 'jumlah' => '', 'catatan' => '']];
        $this->activeTab = 0;

        $this->dispatch('toast', ['type' => 'success', 'message' => 'Data Barang berhasil Diperbarui.']);
        $this->dispatch('pg:eventRefresh-DishTable');
        $this->dispatch('closerestockModalBarang');

        return redirect()->route('barang.data');
    }
}
