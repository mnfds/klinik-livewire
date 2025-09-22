<?php

namespace App\Livewire\Apotik;

use Livewire\Component;
use App\Models\ProdukDanObat;
use App\Models\TransaksiApotik;

class Update extends Component
{
    public $transaksi;
    public $produk;
    public $obat_estetika = []; // array dinamis untuk row

    public function mount($id)
    {
        $this->produk = ProdukDanObat::all();
        $this->transaksi = TransaksiApotik::with('riwayat.produk')->findOrFail($id);

        // isi $obat_estetika berdasarkan riwayat transaksi
        foreach ($this->transaksi->riwayat as $item) {
            $this->obat_estetika[] = [
                'uuid' => uniqid(),
                'produk_id' => $item->produk_id,
                'jumlah_produk' => $item->jumlah_produk,
                'harga_asli' => $item->harga_asli,
                'potongan' => $item->potongan,
                'diskon' => $item->diskon,
                'subtotal' => $item->subtotal
            ];
        }
    }

    public function addRow()
    {
        $this->obat_estetika[] = [
            'uuid' => uniqid(),
            'produk_id' => null,
            'jumlah_produk' => 1,
            'harga_asli' => 0,
            'potongan' => 0,
            'diskon' => 0,
            'subtotal' => 0
        ];
    }

    public function removeRow($index)
    {
        unset($this->obat_estetika[$index]);
        $this->obat_estetika = array_values($this->obat_estetika);
    }

    public function updatedObatEstetika($value, $key)
    {
        // misal key = "0.jumlah_produk", kita bisa hitung subtotal
        list($index, $field) = explode('.', $key);
        $row = &$this->obat_estetika[$index];

        if ($field == 'jumlah_produk' || $field == 'harga_asli' || $field == 'diskon') {
            $row['subtotal'] = ($row['harga_asli'] * $row['jumlah_produk']) - $row['diskon'];
        }
    }

    public function update()
    {
        $this->validate([
            'obat_estetika.*.produk_id' => 'required|exists:produk_dan_obats,id',
            'obat_estetika.*.jumlah_produk' => 'required|integer|min:1',
        ]);

        $totalHarga = 0;

        // Hapus riwayat lama dulu
        $this->transaksi->riwayat()->delete();

        foreach ($this->obat_estetika as $item) {
            $harga_asli = $item['harga_asli']; // sudah dihitung di frontend: harga_dasar * jumlah
            $potongan = $item['potongan'] ?? 0; // nominal
            $diskon = $item['diskon'] ?? 0;     // persen

            $afterPotongan = $harga_asli - $potongan;
            $subtotalFinal = $afterPotongan - ($afterPotongan * ($diskon / 100));

            // Simpan riwayat baru
            $this->transaksi->riwayat()->create([
                'produk_id' => $item['produk_id'],
                'jumlah_produk' => $item['jumlah_produk'],
                'harga_asli' => $harga_asli,
                'potongan' => $potongan,
                'diskon' => $diskon,
                'subtotal' => round($subtotalFinal), // dibulatkan ke integer
            ]);

            $totalHarga += $subtotalFinal;
        }

        // Update total harga transaksi
        $this->transaksi->update([
            'total_harga' => round($totalHarga),
        ]);

        session()->flash('success', 'Transaksi berhasil diperbarui!');
        return redirect()->route('apotik.kasir');
    }

    public function render()
    {
        return view('livewire.apotik.update');
    }
}
