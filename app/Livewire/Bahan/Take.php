<?php

namespace App\Livewire\Bahan;

use Livewire\Component;
use App\Models\BahanBaku;
use App\Models\MutasiBahanbaku;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class Take extends Component
{
    public string $tipe = 'keluar';
    public int $activeTab = 0;

    public array $items = [
        ['bahan_baku_id' => '', 'jumlah' => '', 'catatan' => ''],
    ];

    public $bahan = [];

    public function mount(): void
    {
        $this->bahan = BahanBaku::all();
    }

    public function render()
    {
        return view('livewire.bahan.take');
    }

    public function addTab(): void
    {
        $this->items[] = ['bahan_baku_id' => '', 'jumlah' => '', 'catatan' => ''];
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
            'items.*.jumlah'        => 'required|numeric|min:1',
            'items.*.catatan'       => 'nullable|string',
        ], [
            'items.*.bahan_baku_id.required' => 'Nama bahan baku wajib dipilih.',
            'items.*.bahan_baku_id.exists'   => 'Bahan baku tidak valid.',
            'items.*.jumlah.required'        => 'Jumlah wajib diisi.',
            'items.*.jumlah.min'             => 'Jumlah minimal 1.',
        ]);

        if (! Gate::allows('akses', 'Persediaan Bahan Baku Keluar')) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Anda tidak memiliki akses.']);
            return;
        }

        foreach ($this->items as $item) {
            $this->hitungStokBahanBaku($item);
        }

        $this->items     = [['bahan_baku_id' => '', 'jumlah' => '', 'catatan' => '']];
        $this->activeTab = 0;

        $this->dispatch('toast', ['type' => 'success', 'message' => 'Data Bahan Baku berhasil Diperbarui.']);
        $this->dispatch('pg:eventRefresh-DishTable');
        $this->dispatch('closetakeModalBahanbaku');

        return redirect()->route('bahanbaku.data');
    }

    protected function hitungStokBahanBaku(array $item): void
    {
        DB::transaction(function () use ($item) {

            $bahan = BahanBaku::lockForUpdate()->findOrFail($item['bahan_baku_id']);

            $jumlahKeluar = (int) $item['jumlah'];
            $catatan      = $item['catatan'] ?? '';
            $pengali      = (int) $bahan->pengali;

            $stokBesar = (int) $bahan->stok_besar;
            $stokKecil = (int) $bahan->stok_kecil;

            $totalStokKecil = ($stokBesar * $pengali) + $stokKecil;

            if ($totalStokKecil < $jumlahKeluar) {
                throw new \Exception("Stok bahan baku '{$bahan->nama}' tidak mencukupi.");
            }

            while ($stokKecil < $jumlahKeluar) {
                if ($stokBesar <= 0) {
                    throw new \Exception("Stok besar bahan baku '{$bahan->nama}' habis.");
                }

                $stokBesar--;
                $this->simpanMutasi($bahan->id, 'keluar', 1, $bahan->satuan_besar, $catatan . ' (Konversi stok besar ke kecil)');

                $stokKecil += $pengali;
                $this->simpanMutasi($bahan->id, 'masuk', $pengali, $bahan->satuan_kecil, $catatan . ' (Hasil konversi dari stok besar)');
            }

            $stokKecil -= $jumlahKeluar;
            $this->simpanMutasi($bahan->id, 'keluar', $jumlahKeluar, $bahan->satuan_kecil, $catatan . ' (Bahan Baku Outstock)');

            $bahan->update([
                'stok_besar' => $stokBesar,
                'stok_kecil' => $stokKecil,
            ]);
        });
    }

    protected function simpanMutasi(int $bahanId, string $jenis, int $jumlah, string $satuan, string $keterangan): void
    {
        MutasiBahanbaku::create([
            'bahan_baku_id' => $bahanId,
            'tipe'          => $jenis,
            'jumlah'        => $jumlah,
            'satuan'        => $satuan,
            'diajukan_oleh' => Auth::user()->biodata?->nama_lengkap,
            'catatan'       => $keterangan,
        ]);
    }
}
