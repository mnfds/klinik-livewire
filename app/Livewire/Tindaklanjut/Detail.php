<?php

namespace App\Livewire\Tindaklanjut;

use App\Models\Pasien;
use Livewire\Component;

class Detail extends Component
{
    public ?int $pasien_id = null;
    public ?Pasien $pasien = null;

    public array $bundlingAktif = [];
    public array $bundlingSelesai = [];

    public function mount($pasien_id = null)
    {
        $this->pasien_id = $pasien_id;
        
        if (!$this->pasien_id) return;

        $this->pasien = Pasien::with([
            'pelayananBundlings.pelayanan',
            'pelayananBundlings.bundling',
            'produkObatBundlings.produk',
            'produkObatBundlings.bundling',
            'treatmentBundlings.treatment',
            'treatmentBundlings.bundling',
        ])->find($this->pasien_id);

        if (!$this->pasien) return;

        // 🔹 Satukan semua item bundling dari berbagai tabel
        $semuaItem = collect()
            ->merge($this->pasien->pelayananBundlings)
            ->merge($this->pasien->produkObatBundlings)
            ->merge($this->pasien->treatmentBundlings)
            ->map(function ($item) {
                return [
                    'group_bundling'   => $item->group_bundling,
                    'bundling'         => $item->bundling->nama ?? '-',
                    'tipe'             => $item instanceof \App\Models\PelayananBundlingRM ? 'Pelayanan' :
                                        ($item instanceof \App\Models\ProdukObatBundlingRM ? 'Produk' : 'Treatment'),
                    'nama_item'        => $item->pelayanan->nama_pelayanan
                                        ?? $item->produk->nama_dagang
                                        ?? $item->treatment->nama_treatment
                                        ?? '-',
                    'jumlah_awal'      => $item->jumlah_awal,
                    'jumlah_terpakai'  => $item->jumlah_terpakai,
                    'sisa'             => max(0, $item->jumlah_awal - $item->jumlah_terpakai),
                ];
            })
            ->groupBy('group_bundling');

        // 🔸 Pisahkan antara aktif dan selesai berdasarkan status per group
        $this->bundlingAktif = $semuaItem
            ->filter(fn($items) => $items->contains(fn($i) => $i['jumlah_terpakai'] < $i['jumlah_awal']))
            ->toArray();

        $this->bundlingSelesai = $semuaItem
            ->filter(fn($items) => $items->every(fn($i) => $i['jumlah_terpakai'] >= $i['jumlah_awal']))
            ->toArray();
    }

    public function render()
    {
        return view('livewire.tindaklanjut.detail');
    }
}
