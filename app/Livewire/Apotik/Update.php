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
    public $obat_estetika = [];

    public $barang_list = [];
    public $barang_transaksi = [];

    public bool $showProduk = false;
    public bool $showBarang = false;

    // === TAMBAHAN: form pembayaran ===
    public bool $showPaymentForm = false;
    public $metode_pembayaran = null;
    public $diskon = 0;
    public $potongan = 0;
    public $note = null;

    public function mount($id)
    {
        $this->produk = ProdukDanObat::all();
        $this->barang_list = Barang::all();
        $this->transaksi = TransaksiApotik::with('riwayat.produk')->findOrFail($id);

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
        if(!empty($this->obat_estetika)) {
            $this->showProduk = true;
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
        if(!empty($this->barang_transaksi)) {
            $this->showBarang = true;
        }

        // === TAMBAHAN: prefill data pembayaran dari transaksi ===
        $this->metode_pembayaran = $this->transaksi->metode_pembayaran;
        $this->diskon            = $this->transaksi->diskon ?? 0;
        $this->potongan          = $this->transaksi->potongan ?? 0;
        $this->note              = $this->transaksi->note;
    }

    // ... addRow(), removeRow(), addRowBarang(), removeRowBarang() tetap sama ...

    protected function rulesPayment()
    {
        return [
            'metode_pembayaran' => 'required|in:Tunai,Qris,Shopeepay,Mandiri,BCA,BRI,BNI',
            'diskon'            => 'nullable|numeric|min:0|max:100',
            'potongan'          => 'nullable|numeric|min:0',
            'note'              => 'nullable|string|max:255',
        ];
    }

    public function getTotalKotorProperty()
    {
        $totalProduk = $this->showProduk
            ? collect($this->obat_estetika)->sum(fn($item) => (float) ($item['subtotal'] ?? 0))
            : 0;
        $totalBarang = $this->showBarang
            ? collect($this->barang_transaksi)->sum(fn($item) => (float) ($item['subtotal'] ?? 0))
            : 0;
        return $totalProduk + $totalBarang;
    }

    public function getTotalBersihProperty()
    {
        $total = $this->totalKotor;
        $diskonRp = $total * ((float) ($this->diskon ?: 0) / 100);
        $bersih = $total - $diskonRp - (float) ($this->potongan ?: 0);
        return max(0, round($bersih));
    }

    public function openPayment()
    {
        if ($this->totalKotor <= 0) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Belum ada item transaksi.',
            ]);
            return;
        }
        $this->showPaymentForm = true;
    }

    public function closePayment()
    {
        $this->showPaymentForm = false;
    }

    public function update()
    {
        $this->validate([
            'obat_estetika.*.produk_id' => 'required|exists:produk_dan_obats,id',
            'obat_estetika.*.jumlah_produk' => 'required|integer|min:1',
            'barang_transaksi.*.barang_id' => 'nullable|exists:barangs,id',
            'barang_transaksi.*.jumlah'    => 'nullable|integer|min:1',
        ]);
        $this->validate($this->rulesPayment());

        if (! Gate::allows('akses', 'Transaksi Apotik Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        $totalHarga = 0;

        if (!$this->showProduk) {
            $this->transaksi->riwayat()->delete();
        }
        if($this->showProduk){
            $this->transaksi->riwayat()->delete();
            foreach ($this->obat_estetika as $item) {
                $harga_asli    = (float) ($item['harga_asli'] ?? 0);
                $diskon        = (float) ($item['diskon']     ?? 0);
                $potongan      = (float) ($item['potongan']   ?? 0);
                $subtotalFinal = $this->hitungSubtotal($harga_asli, $diskon, $potongan);

                $this->transaksi->riwayat()->create([
                    'produk_id' => $item['produk_id'],
                    'jumlah_produk' => $item['jumlah_produk'],
                    'harga_asli' => $harga_asli,
                    'potongan' => $potongan,
                    'diskon' => $diskon,
                    'subtotal' => round($subtotalFinal),
                ]);

                $totalHarga += $subtotalFinal;
            }
        }

        if (!$this->showBarang) {
            $this->transaksi->riwayatBarang()->delete();
        }
        if($this->showBarang){
            $this->transaksi->riwayatBarang()->delete();
            foreach ($this->barang_transaksi as $item) {
                if (empty($item['barang_id'])) continue;

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
        }

        // === TAMBAHAN: hitung ulang total_tagihan_bersih pakai diskon/potongan transaksi ===
        $diskonRp = $totalHarga * ((float) ($this->diskon ?: 0) / 100);
        $totalBersih = max(0, round($totalHarga - $diskonRp - (float) ($this->potongan ?: 0)));

        $this->transaksi->update([
            'total_harga'          => round($totalHarga),
            'metode_pembayaran'    => $this->metode_pembayaran,
            'diskon'               => (int) ($this->diskon ?: 0),
            'potongan'             => (int) ($this->potongan ?: 0),
            'total_tagihan_bersih' => $totalBersih,
            'note'                 => $this->note,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Transaksi berhasil Diedit!',
        ]);
        return redirect()->route('apotik.kasir');
    }

    private function hitungSubtotal(float $harga_asli, float $diskon, float $potongan): float
    {
        $afterDiskon = $harga_asli - ($harga_asli * ($diskon / 100));
        return $afterDiskon - $potongan;
    }

    public function formProdukOpen()
    {
        $this->showProduk = true;
    }

    public function formBarangOpen()
    {
        $this->showBarang = true;
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