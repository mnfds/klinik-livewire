<?php

namespace App\Livewire\Apotik;

use App\Models\ProdukDanObat;
use Livewire\Component;
use Livewire\Volt\Compilers\Mount;

class Create extends Component
{
    public $produk; 
    public $obat_estetika = [];

    public function mount()
    {
        $this->produk = ProdukDanObat::all();

        $this->obat_estetika[] = [
            'produk_id' => null,
            'jumlah_produk' => 1,
            'potongan' => 0,
            'diskon' => 0,
            'harga_asli' => 0,
            'subtotal' => 0,
        ];
    }

    public function addRow()
    {
        $this->obat_estetika[] = [
            'produk_id' => null,
            'jumlah_produk' => 1,
            'potongan' => 0,
            'diskon' => 0,
            'harga_asli' => 0,
            'subtotal' => 0,
        ];
    }

    public function removeRow($index)
    {
        unset($this->obat_estetika[$index]);
        $this->obat_estetika = array_values($this->obat_estetika); // reindex array
    }

    public function updatedObatEstetika($value, $key)
    {
        [$index, $field] = explode('.', $key);

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