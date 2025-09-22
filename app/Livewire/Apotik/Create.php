<?php

namespace App\Livewire\Apotik;

use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\ProdukDanObat;
use App\Models\TransaksiApotik;
use Illuminate\Support\Facades\DB;

class Create extends Component
{
    public $produk;
    public $obat_estetika = [];

    public function mount()
    {
        $this->produk = ProdukDanObat::all();
        $uuid = (string) Str::uuid();
        $this->obat_estetika[$uuid] = $this->emptyRowWithUuid($uuid);
    }

    private function emptyRowWithUuid($uuid)
    {
        return [
            'produk_id' => null,
            'jumlah_produk' => 1,
            'potongan' => 0,
            'diskon' => 0,
            'harga_asli' => 0,
            'subtotal' => 0,
            'uuid' => $uuid,
        ];
    }

    public function addRow()
    {
        $uuid = (string) Str::uuid();
        $this->obat_estetika[$uuid] = $this->emptyRowWithUuid($uuid);
    }

    public function removeRow($uuid)
    {
        unset($this->obat_estetika[$uuid]);
    }

    public function updatedObatEstetika($value, $key)
    {
        [$uuid, $field] = explode('.', $key);

        if (in_array($field, ['produk_id', 'jumlah_produk', 'potongan', 'diskon'])) {
            $row = $this->obat_estetika[$uuid];

            if ($row['produk_id']) {
                $produk   = $this->produk->find($row['produk_id']);
                $harga    = (int) ($produk->harga_dasar ?? 0);
                $jumlah   = (int) ($row['jumlah_produk'] ?? 1);
                $potongan = (int) ($row['potongan'] ?? 0);
                $diskon   = (float) ($row['diskon'] ?? 0);

                $hargaAsli = $harga * $jumlah;
                $subtotal  = $hargaAsli - $potongan;
                $subtotal -= $subtotal * ($diskon / 100);

                $this->obat_estetika[$uuid]['harga_asli'] = (int) max($hargaAsli, 0);
                $this->obat_estetika[$uuid]['subtotal']   = (int) max($subtotal, 0);
            } else {
                $this->obat_estetika[$uuid]['harga_asli'] = 0;
                $this->obat_estetika[$uuid]['subtotal']   = 0;
            }
        }
    }

    public function create()
    {
        dd($this->obat_estetika);
        // nanti disini bisa disimpan ke DB
    }

    public function render()
    {
        return view('livewire.apotik.create');
    }
}