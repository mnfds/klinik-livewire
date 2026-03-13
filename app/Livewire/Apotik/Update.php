<?php

namespace App\Livewire\Apotik;

use App\Models\Barang;
use Livewire\Component;
use App\Models\ProdukDanObat;
use App\Models\TransaksiApotik;
use Illuminate\Support\Facades\Gate;

class Update extends Component
{
    public $transaksi;
    public $produk;
    public $obat_estetika = []; // array dinamis untuk row

    public $barang_list = []; // data master barang
    public $barang_transaksi = []; // array dinamis row barang

    public function mount($id)
    {
        $this->produk = ProdukDanObat::all();
        $this->barang_list = Barang::all();
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
        foreach ($this->transaksi->riwayatBarang as $item) {
            $this->barang_transaksi[] = [
                'uuid'         => uniqid(),
                'barang_id'    => $item->barang_id,
                'jumlah'       => $item->jumlah_barang,
                'harga_asli'   => $item->harga_asli,
                'potongan'     => $item->potongan,
                'diskon'       => $item->diskon,
                'subtotal'     => $item->subtotal,
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

    public function removeRow($uuid)
    {
        $this->obat_estetika = array_filter(
            $this->obat_estetika,
            fn($row) => $row['uuid'] !== $uuid
        );
        $this->obat_estetika = array_values($this->obat_estetika);
    }

    public function addRowBarang()
    {
        $this->barang_transaksi[] = [
            'uuid'       => uniqid(),
            'barang_id'  => null,
            'jumlah'     => 1,
            'harga_asli' => 0,
            'potongan'   => 0,
            'diskon'     => 0,
            'subtotal'   => 0,
        ];
    }

    public function removeRowBarang($uuid)
    {
        $this->barang_transaksi = array_filter(
            $this->barang_transaksi,
            fn($row) => $row['uuid'] !== $uuid
        );
        $this->barang_transaksi = array_values($this->barang_transaksi);
    }

    // public function updatedObatEstetika($value, $key)
    // {
    //     // misal key = "0.jumlah_produk", kita bisa hitung subtotal
    //     list($index, $field) = explode('.', $key);
    //     $row = &$this->obat_estetika[$index];

    //     if ($field == 'jumlah_produk' || $field == 'harga_asli' || $field == 'diskon') {
    //         $row['subtotal'] = ($row['harga_asli'] * $row['jumlah_produk']) - $row['diskon'];
    //     }
    // }

    public function update()
    {
        $this->validate([
            'obat_estetika.*.produk_id' => 'required|exists:produk_dan_obats,id',
            'obat_estetika.*.jumlah_produk' => 'required|integer|min:1',
            'barang_transaksi.*.barang_id' => 'nullable|exists:barangs,id', // nullable jika section kosong
            'barang_transaksi.*.jumlah'    => 'nullable|integer|min:1',
        ]);
        if (! Gate::allows('akses', 'Transaksi Apotik Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        $totalHarga = 0;

        // Hapus riwayat lama dulu
        $this->transaksi->riwayat()->delete();

        foreach ($this->obat_estetika as $item) {
            $harga_asli    = (float) ($item['harga_asli'] ?? 0);
            $diskon        = (float) ($item['diskon']     ?? 0);
            $potongan      = (float) ($item['potongan']   ?? 0);
            $subtotalFinal = $this->hitungSubtotal($harga_asli, $diskon, $potongan);

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

        // Hapus riwayat barang lama
        $this->transaksi->riwayatBarang()->delete();

        foreach ($this->barang_transaksi as $item) {
            if (empty($item['barang_id'])) continue; // skip row kosong

            $harga_asli    = (float) ($item['harga_asli'] ?? 0);
            $diskon        = (float) ($item['diskon']     ?? 0);
            $potongan      = (float) ($item['potongan']   ?? 0);
            $subtotalFinal = $this->hitungSubtotal($harga_asli, $diskon, $potongan);

            $this->transaksi->riwayatBarang()->create([
                'barang_id'     => $item['barang_id'],
                'jumlah_barang' => $item['jumlah'],
                'harga_asli'    => $harga_asli,
                'potongan'      => $potongan,
                'diskon'        => $diskon,
                'subtotal'      => round($subtotalFinal),
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

    private function hitungSubtotal(float $harga_asli, float $diskon, float $potongan): float
    {
        $afterDiskon = $harga_asli - ($harga_asli * ($diskon / 100));
        return $afterDiskon - $potongan;
    }

    public function render()
    {
        if (! Gate::allows('akses', 'Transaksi Apotik Edit')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.apotik.update');
    }
}
