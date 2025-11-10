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
        
        if ($this->pasien_id) {
            $this->pasien = Pasien::with([
                'pelayananBundlings.pelayanan',
                'pelayananBundlings.bundling',
                'produkObatBundlings.produk',
                'produkObatBundlings.bundling',
                'treatmentBundlings.treatment',
                'treatmentBundlings.bundling',
            ])->find($this->pasien_id);

            if ($this->pasien) {

                // --- Ambil semua bundling aktif (masih ada sisa) ---
                $pelayananAktif = collect($this->pasien->pelayananBundlings)
                    ->filter(fn($p) => $p->jumlah_terpakai < $p->jumlah_awal)
                    ->map(fn($p) => [
                        'tipe' => 'Pelayanan',
                        'bundling' => $p->bundling->nama ?? '-',
                        'nama_item' => $p->pelayanan->nama_pelayanan ?? '-',
                        'jumlah_awal' => $p->jumlah_awal,
                        'jumlah_terpakai' => $p->jumlah_terpakai,
                        'sisa' => $p->jumlah_awal - $p->jumlah_terpakai,
                    ]);

                $produkAktif = collect($this->pasien->produkObatBundlings)
                    ->filter(fn($p) => $p->jumlah_terpakai < $p->jumlah_awal)
                    ->map(fn($p) => [
                        'tipe' => 'Produk',
                        'bundling' => $p->bundling->nama ?? '-',
                        'nama_item' => $p->produk->nama_dagang ?? '-',
                        'jumlah_awal' => $p->jumlah_awal,
                        'jumlah_terpakai' => $p->jumlah_terpakai,
                        'sisa' => $p->jumlah_awal - $p->jumlah_terpakai,
                    ]);

                $treatmentAktif = collect($this->pasien->treatmentBundlings)
                    ->filter(fn($t) => $t->jumlah_terpakai < $t->jumlah_awal)
                    ->map(fn($t) => [
                        'tipe' => 'Treatment',
                        'bundling' => $t->bundling->nama ?? '-',
                        'nama_item' => $t->treatment->nama_treatment ?? '-',
                        'jumlah_awal' => $t->jumlah_awal,
                        'jumlah_terpakai' => $t->jumlah_terpakai,
                        'sisa' => $t->jumlah_awal - $t->jumlah_terpakai,
                    ]);

                $this->bundlingAktif = $pelayananAktif
                    ->merge($produkAktif)
                    ->merge($treatmentAktif)
                    ->groupBy('bundling')
                ->toArray();

                // --- Ambil bundling yang sudah selesai ---
                $pelayananSelesai = collect($this->pasien->pelayananBundlings)
                    ->filter(fn($p) => $p->jumlah_terpakai >= $p->jumlah_awal)
                    ->map(fn($p) => [
                        'tipe' => 'Pelayanan',
                        'bundling' => $p->bundling->nama ?? '-',
                        'nama_item' => $p->pelayanan->nama_pelayanan ?? '-',
                        'jumlah_awal' => $p->jumlah_awal,
                        'jumlah_terpakai' => $p->jumlah_terpakai,
                        'sisa' => 0,
                    ]);

                $produkSelesai = collect($this->pasien->produkObatBundlings)
                    ->filter(fn($p) => $p->jumlah_terpakai >= $p->jumlah_awal)
                    ->map(fn($p) => [
                        'tipe' => 'Produk',
                        'bundling' => $p->bundling->nama ?? '-',
                        'nama_item' => $p->produk->nama_dagang ?? '-',
                        'jumlah_awal' => $p->jumlah_awal,
                        'jumlah_terpakai' => $p->jumlah_terpakai,
                        'sisa' => 0,
                    ]);

                $treatmentSelesai = collect($this->pasien->treatmentBundlings)
                    ->filter(fn($t) => $t->jumlah_terpakai >= $t->jumlah_awal)
                    ->map(fn($t) => [
                        'tipe' => 'Treatment',
                        'bundling' => $t->bundling->nama ?? '-',
                        'nama_item' => $t->treatment->nama_treatment ?? '-',
                        'jumlah_awal' => $t->jumlah_awal,
                        'jumlah_terpakai' => $t->jumlah_terpakai,
                        'sisa' => 0,
                    ]);

                $this->bundlingSelesai = $pelayananSelesai
                    ->merge($produkSelesai)
                    ->merge($treatmentSelesai)
                    ->groupBy('bundling')
                    ->toArray();

            }
        }
    }

    public function render()
    {
        return view('livewire.tindaklanjut.detail');
    }
}
