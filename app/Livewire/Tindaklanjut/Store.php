<?php

namespace App\Livewire\Tindaklanjut;

use Livewire\Component;
use App\Models\Bundling;
use App\Models\Pasien;
use App\Models\PelayananBundlingRM;
use App\Models\ProdukObatBundlingRM;
use App\Models\TreatmentBundlingRM;

class Store extends Component
{
    public ?int $pasien_id = null;
    public ?int $bundling_id = null;
    public array $bundlings = []; // simpan data
    public $bundlingDetails = []; // show detail bundling berdasarkan bundling yang dipilih,
    public int $jumlah_bundling = 1;

    public function mount($pasien_id = null)
    {
        $this->pasien_id = $pasien_id;
        $this->bundlings = Bundling::select('id', 'nama')->get()->toArray();
    }

    public function updatedBundlingId($value)
    {
        if (!$value) {
            $this->bundlingDetails = [];
            return;
        }

        $bundling = Bundling::with([
            'pelayananBundlings.pelayanan:id,nama_pelayanan',
            'produkObatBundlings.produk:id,nama_dagang',
            'treatmentBundlings.treatment:id,nama_treatment',
        ])->find($value);

        if ($bundling) {
        $this->bundlingDetails = [
            'nama' => $bundling->nama,
            'harga' => $bundling->harga,
            'pelayanans' => $bundling->pelayananBundlings->map(fn($p) => [
                'id' => $p->pelayanan->id ?? null,
                'nama' => $p->pelayanan->nama_pelayanan ?? '-',
                'jumlah' => $p->jumlah ?? 1,
                'terpakai' => 0,
            ])->toArray(),
            'produk_obats' => $bundling->produkObatBundlings->map(fn($p) => [
                'id' => $p->produk->id ?? null,
                'nama' => $p->produk->nama_dagang ?? '-',
                'jumlah' => $p->jumlah ?? 1,
                'terpakai' => 0,
            ])->toArray(),
            'treatments' => $bundling->treatmentBundlings->map(fn($t) => [
                'id' => $t->treatment->id ?? null,
                'nama' => $t->treatment->nama_treatment ?? '-',
                'jumlah' => $t->jumlah ?? 1,
                'terpakai' => 0,
            ])->toArray(),
        ];
        } else {
            $this->bundlingDetails = [];
        }
    }

    public function store()
    {
        $this->validate([
            'pasien_id'        => 'required|exists:pasiens,id',
            'bundling_id'      => 'required|exists:bundlings,id',
            'jumlah_bundling'  => 'required|integer|min:1',
        ]);

        $pasien = Pasien::findOrFail($this->pasien_id);
        $bundling = Bundling::with([
            'pelayananBundlings.pelayanan',
            'produkObatBundlings.produk',
            'treatmentBundlings.treatment',
        ])->findOrFail($this->bundling_id);

        // --- Pelayanan Bundling ---
        if (!empty($this->bundlingDetails['pelayanans'])) {
            foreach ($this->bundlingDetails['pelayanans'] as $p) {
                PelayananBundlingRM::create([
                    'pasien_id'        => $pasien->id,
                    'bundling_id'      => $bundling->id,
                    'pelayanan_id'     => $p['id'],
                    'jumlah_awal'      => ($p['jumlah'] ?? 1) * $this->jumlah_bundling,
                    'jumlah_terpakai'  => $p['terpakai'] ?? 0,
                ]);
            }
        }

        // --- Produk Bundling ---
        if (!empty($this->bundlingDetails['produk_obats'])) {
            foreach ($this->bundlingDetails['produk_obats'] as $p) {
                ProdukObatBundlingRM::create([
                    'pasien_id'        => $pasien->id,
                    'bundling_id'      => $bundling->id,
                    'produk_obat_id'   => $p['id'],
                    'jumlah_awal'      => ($p['jumlah'] ?? 1) * $this->jumlah_bundling,
                    'jumlah_terpakai'  => $p['terpakai'] ?? 0,
                ]);
            }
        }

        // --- Treatment Bundling ---
        if (!empty($this->bundlingDetails['treatments'])) {
            foreach ($this->bundlingDetails['treatments'] as $t) {
                TreatmentBundlingRM::create([
                    'pasien_id'        => $pasien->id,
                    'bundling_id'      => $bundling->id,
                    'treatments_id'    => $t['id'],
                    'jumlah_awal'      => ($t['jumlah'] ?? 1) * $this->jumlah_bundling,
                    'jumlah_terpakai'  => $t['terpakai'] ?? 0,
                ]);
            }
        }

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Bundling berhasil ditambahkan (' . $this->jumlah_bundling . 'x)',
        ]);

        $this->dispatch('closestoreModalTindakLanjut');
        $this->reset();

        return redirect()->route('tindaklanjut.data');
    }
    
    public function render()
    {
        return view('livewire.tindaklanjut.store');
    }
}
