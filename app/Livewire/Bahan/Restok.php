<?php

namespace App\Livewire\Bahan;

use Livewire\Component;
use App\Models\BahanBaku;
use App\Models\MutasiBahanbaku;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class Restok extends Component
{
    public string $tipe = 'masuk';
    public int $activeTab = 0;

    public array $items = [
        ['bahan_baku_id' => '', 'jenis_keluar' => 'besar', 'jumlah' => '', 'catatan' => ''],
    ];

    public $bahan = [];

    public function mount(): void
    {
        $this->bahan = BahanBaku::all();
    }

    public function render()
    {
        return view('livewire.bahan.restok');
    }

    public function addTab(): void
    {
        $this->items[] = ['bahan_baku_id' => '', 'jenis_keluar' => 'besar', 'jumlah' => '', 'catatan' => ''];
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
            'items'                 => 'required|array|min:1',
            'items.*.bahan_baku_id' => 'required|exists:bahan_bakus,id',
            'items.*.jenis_keluar'  => 'required|in:besar,kecil,besarkecil',
            'items.*.jumlah'        => 'required|numeric|min:1',
            'items.*.catatan'       => 'nullable|string',
        ], [
            'items.*.bahan_baku_id.required' => 'Nama bahan baku wajib dipilih.',
            'items.*.bahan_baku_id.exists'   => 'Bahan baku tidak valid.',
            'items.*.jenis_keluar.required'  => 'Jenis restok wajib dipilih.',
            'items.*.jumlah.required'        => 'Jumlah wajib diisi.',
            'items.*.jumlah.min'             => 'Jumlah minimal 1.',
        ]);

        if (! Gate::allows('akses', 'Persediaan Bahan Baku Masuk')) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Anda tidak memiliki akses.']);
            return;
        }

        foreach ($this->items as $item) {
            $this->prosesRestok($item);
        }

        $this->items     = [['bahan_baku_id' => '', 'jenis_keluar' => 'besar', 'jumlah' => '', 'catatan' => '']];
        $this->activeTab = 0;

        $this->dispatch('toast', ['type' => 'success', 'message' => 'Data Bahan Baku berhasil Diperbarui.']);
        $this->dispatch('pg:eventRefresh-DishTable');
        $this->dispatch('closerestockModalBahanbaku');

        return redirect()->route('bahanbaku.data');
    }

    protected function prosesRestok(array $item): void
    {
        DB::transaction(function () use ($item) {

            $bahan   = BahanBaku::lockForUpdate()->findOrFail($item['bahan_baku_id']);
            $jumlah  = (int) $item['jumlah'];
            $catatan = $item['catatan'] ?? '';
            $pengaju = Auth::user()->biodata?->nama_lengkap;

            match ($item['jenis_keluar']) {

                // ── Tambah stok besar ──────────────────────────────────
                'besar' => (function () use ($bahan, $jumlah, $catatan, $pengaju) {
                    MutasiBahanbaku::create([
                        'bahan_baku_id' => $bahan->id,
                        'tipe'          => 'masuk',
                        'jumlah'        => $jumlah,
                        'satuan'        => $bahan->satuan_besar,
                        'diajukan_oleh' => $pengaju,
                        'catatan'       => $catatan,
                    ]);
                    $bahan->increment('stok_besar', $jumlah);
                })(),

                // ── Tambah stok kecil ──────────────────────────────────
                'kecil' => (function () use ($bahan, $jumlah, $catatan, $pengaju) {
                    MutasiBahanbaku::create([
                        'bahan_baku_id' => $bahan->id,
                        'tipe'          => 'masuk',
                        'jumlah'        => $jumlah,
                        'satuan'        => $bahan->satuan_kecil,
                        'diajukan_oleh' => $pengaju,
                        'catatan'       => $catatan,
                    ]);
                    $bahan->increment('stok_kecil', $jumlah);
                })(),

                // ── Konversi stok besar → kecil ────────────────────────
                'besarkecil' => (function () use ($bahan, $jumlah, $catatan, $pengaju) {
                    $stokKecilMasuk = $jumlah * (int) $bahan->pengali;

                    // Keluar dari stok besar
                    MutasiBahanbaku::create([
                        'bahan_baku_id' => $bahan->id,
                        'tipe'          => 'keluar',
                        'jumlah'        => $jumlah,
                        'satuan'        => $bahan->satuan_besar,
                        'diajukan_oleh' => $pengaju,
                        'catatan'       => $catatan,
                    ]);

                    // Masuk ke stok kecil
                    MutasiBahanbaku::create([
                        'bahan_baku_id' => $bahan->id,
                        'tipe'          => 'masuk',
                        'jumlah'        => $stokKecilMasuk,
                        'satuan'        => $bahan->satuan_kecil,
                        'diajukan_oleh' => $pengaju,
                        'catatan'       => $catatan,
                    ]);

                    $bahan->decrement('stok_besar', $jumlah);
                    $bahan->increment('stok_kecil', $stokKecilMasuk);
                })(),
            };
        });
    }
}
