<?php

namespace App\Livewire\Apotik;

use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\ProdukDanObat;
use Livewire\Volt\Compilers\Mount;

class Create extends Component
{
    public $produk; 
    public $obat_estetika = [];

    public function mount()
    {
        $this->produk = ProdukDanObat::all();

        $this->obat_estetika[] = $this->emptyRow();
    }

    private function emptyRow()
    {
        return [
            'produk_id' => null,
            'jumlah_produk' => 1,
            'potongan' => 0,
            'diskon' => 0,
            'harga_asli' => 0,
            'subtotal' => 0,
            'uuid' => (string) \Illuminate\Support\Str::uuid(), // unik untuk wire:key
        ];
    }

    private function findIndexByUuid($uuid)
    {
        foreach ($this->obat_estetika as $i => $row) {
            if ($row['uuid'] === $uuid) {
                return $i;
            }
        }
        return null;
    }

    public function addRow()
    {
        $uuid = (string) Str::uuid();
        $this->obat_estetika[$uuid] = [
            'produk_id' => null,
            'jumlah_produk' => 1,
            'potongan' => 0,
            'diskon' => 0,
            'harga_asli' => 0,
            'subtotal' => 0,
            'uuid' => $uuid,
        ];
    }

    public function removeRow($uuid)
    {
        unset($this->obat_estetika[$uuid]);
    }

    public function updatedObatEstetika($value, $key)
    {
        $parts = explode('.', $key, 2);

        if (count($parts) !== 2) {
            return;
        }

        [$index, $field] = $parts;

        if (in_array($field, ['produk_id', 'jumlah_produk', 'potongan', 'diskon'])) {
            $row = $this->obat_estetika[$index];

            if ($row['produk_id']) {
                $produk = $this->produk->find($row['produk_id']);
                if ($produk) {
                    $harga = $produk->harga_dasar ?? 0;
                    $jumlah = $row['jumlah_produk'] ?? 1;
                    $potongan = $row['potongan'] ?? 0;
                    $diskon = $row['diskon'] ?? 0;

                    $hargaAsli = $harga * $jumlah;
                    $subtotal = ($hargaAsli - $potongan);
                    $subtotal -= $subtotal * ($diskon / 100);

                    $this->obat_estetika[$index]['harga_asli'] = max($hargaAsli, 0);
                    $this->obat_estetika[$index]['subtotal'] = max($subtotal, 0);
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.apotik.create');
    }
}