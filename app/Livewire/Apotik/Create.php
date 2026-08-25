<?php

namespace App\Livewire\Apotik;

use App\Models\Barang;
use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\ProdukDanObat;
use App\Models\TransaksiApotik;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use Mike42\Escpos\Printer;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class Create extends Component
{
    public $pasien_id;

    // VAR SIMPAN OBAT/SKINCARE (PARACETAMOL, SUNSCREEN, DLL)
    public $produk;
    public $obat_estetika = [];

    // VAR SIMPAN BARANG TERJUAL (THUMBLER, TAS, DLL)
    public $barang;
    public $barang_terjual = [];

    public bool $showProduk = true; // form terbuka
    public bool $showBarang = false; // form tertutup

    public bool $showPaymentForm = false;
    public $metode_pembayaran = null;
    public $diskon = 0;
    public $potongan = 0;
    public $note = null;

    public function mount()
    {
        $this->produk = ProdukDanObat::all();
        $uuid = (string) Str::uuid();
        $this->obat_estetika[$uuid] = $this->emptyRowWithUuid($uuid);

        $this->barang = Barang::all();
        $uid = (string) Str::uuid();
        $this->barang_terjual[$uid] = $this->emptyRowWithUidBarang($uid);
    }



    // FUNCTION DINAMIS PRODUK OBAT/SKINCARE (PARACETAMOL, SUNSCREEN, DLL)
        private function emptyRowWithUuid($uuid)
        {
            return [
                'produk_id' => null,
                'jumlah_produk' => 1,
                'harga_satuan' => 0,
                'potongan' => 0,
                'diskon' => 0,
                // 'harga_asli' => 0,
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

            if (!isset($this->obat_estetika[$uuid])) return;

            $row = $this->obat_estetika[$uuid];

            // hanya respon field penting
            if (!in_array($field, ['produk_id', 'jumlah_produk', 'potongan', 'diskon'])) {
                return;
            }

            if (!$row['produk_id']) {
                $this->obat_estetika[$uuid]['harga_satuan'] = 0;
                $this->obat_estetika[$uuid]['subtotal'] = 0;
                return;
            }

            $produk = $this->produk->find($row['produk_id']);
            if (!$produk) return;

            // ✅ Ambil dari DB
            $hargaSatuan = (int) ($produk->harga_dasar ?? 0);
            $defaultDiskon = (float) ($produk->diskon ?? 0);
            $defaultPotongan = (int) ($produk->potongan ?? 0);

            // 🔥 HANYA set default saat produk dipilih
            if ($field === 'produk_id') {
                $this->obat_estetika[$uuid]['diskon'] = $defaultDiskon;
                $this->obat_estetika[$uuid]['potongan'] = $defaultPotongan;
            }

            $jumlah   = (int) ($this->obat_estetika[$uuid]['jumlah_produk'] ?? 1);
            $potongan = (int) ($this->obat_estetika[$uuid]['potongan'] ?? 0);
            $diskon   = (float) ($this->obat_estetika[$uuid]['diskon'] ?? 0);

            $total = $hargaSatuan * $jumlah;
            $total -= ($total * $diskon / 100);
            $total -= $potongan;

            $this->obat_estetika[$uuid]['harga_satuan'] = $hargaSatuan;
            $this->obat_estetika[$uuid]['subtotal'] = max(0, (int) $total);
            // dd($this->obat_estetika);
        }
    // FUNCTION DINAMIS PRODUK OBAT/SKINCARE (PARACETAMOL, SUNSCREEN, DLL)
        
    // FUNCTION DINAMIS BARANG TERJUAL (THUMBLER, TAS, DLL)
        private function emptyRowWithUidBarang($uid)
        {
            return [
                'barang_id' => null,
                'jumlah_barang' => 1,
                'harga_satuan' => 0,
                'potongan' => 0,
                'diskon' => 0,
                // 'harga_asli' => 0,
                'subtotal' => 0,
                'uid' => $uid,
            ];
        }

        public function addRowBarang()
        {
            $uid = (string) Str::uuid();
            $this->barang_terjual[$uid] = $this->emptyRowWithUidBarang($uid);
        }

        public function removeRowBarang($uid)
        {
            unset($this->barang_terjual[$uid]);
        }

        public function updatedBarangTerjual($value, $key)
        {
            [$uid, $field] = explode('.', $key);

            if (!isset($this->barang_terjual[$uid])) return;

            $row = $this->barang_terjual[$uid];

            // hanya respon field penting
            if (!in_array($field, ['barang_id', 'jumlah_barang', 'potongan', 'diskon'])) {
                return;
            }

            if (!$row['barang_id']) {
                $this->barang_terjual[$uid]['harga_satuan'] = 0;
                $this->barang_terjual[$uid]['subtotal'] = 0;
                return;
            }

            $barang = $this->barang->find($row['barang_id']);
            if (!$barang) return;

            // ✅ Ambil dari DB
            $hargaSatuan = (int) ($barang->harga_dasar ?? 0);
            $defaultDiskon = (float) ($barang->diskon ?? 0);
            $defaultPotongan = (int) ($barang->potongan ?? 0);

            // 🔥 HANYA set default saat barang dipilih
            if ($field === 'barang_id') {
                $this->barang_terjual[$uid]['diskon'] = $defaultDiskon;
                $this->barang_terjual[$uid]['potongan'] = $defaultPotongan;
            }

            $jumlah   = (int) ($this->barang_terjual[$uid]['jumlah_barang'] ?? 1);
            $potongan = (int) ($this->barang_terjual[$uid]['potongan'] ?? 0);
            $diskon   = (float) ($this->barang_terjual[$uid]['diskon'] ?? 0);

            $total = $hargaSatuan * $jumlah;
            $total -= ($total * $diskon / 100);
            $total -= $potongan;

            $this->barang_terjual[$uid]['harga_satuan'] = $hargaSatuan;
            $this->barang_terjual[$uid]['subtotal'] = max(0, (int) $total);
            // dd($this->barang_terjual);
        }
    // FUNCTION DINAMIS BARANG TERJUAL (THUMBLER, TAS, DLL)

    public function getTotalKotorProperty()
    {
        $totalProduk = collect($this->obat_estetika)->sum(fn($item) => (int) ($item['subtotal'] ?? 0));
        $totalBarang = collect($this->barang_terjual)->sum(fn($item) => (int) ($item['subtotal'] ?? 0));
        return $totalProduk + $totalBarang;
    }

    public function getTotalBersihProperty()
    {
        $total = $this->totalKotor;
        $diskonRp = $total * ((float) ($this->diskon ?: 0) / 100);
        $bersih = $total - $diskonRp - (int) ($this->potongan ?: 0);
        return max(0, (int) $bersih);
    }

    public function openPayment()
    {
        if ($this->totalKotor <= 0) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Belum ada item transaksi yang ditambahkan.',
            ]);
            return;
        }

        $this->diskon = 0;
        $this->potongan = 0;
        $this->metode_pembayaran = null;
        $this->note = null;
        $this->showPaymentForm = true;
    }

    public function closePayment()
    {
        $this->showPaymentForm = false;
        $this->reset(['metode_pembayaran', 'diskon', 'potongan', 'note']);
    }

    protected function rulesPayment()
    {
        return [
            'metode_pembayaran' => 'required|in:Tunai,Qris,Shopeepay,Mandiri,BCA,BRI,BNI',
            'diskon'            => 'nullable|numeric|min:0|max:100',
            'potongan'          => 'nullable|numeric|min:0',
            'note'              => 'nullable|string|max:255',
        ];
    }

    public function create()
    {
        if (! Gate::allows('akses', 'Transaksi Apotik Tambah')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        
        $this->validate($this->rulesPayment());
        
        DB::transaction(function () {
            // dd([
            //     "obat" => $this->obat_estetika,
            //     "barang" => $this->barang_terjual,
            // ]);
            // Hitung total harga
            $totalproduk = collect($this->obat_estetika)->sum(fn($item) => (int) $item['subtotal']);
            $totalbarang = collect($this->barang_terjual)->sum(fn($item) => (int) $item['subtotal']);
            $total = $totalproduk + $totalbarang;

            $diskonRp = (int) ($total * ((float) ($this->diskon ?: 0) / 100));
            $potongan = (int) ($this->potongan ?: 0);
            $totalBersih = max(0, $total - $diskonRp - $potongan);

            // Generate no_transaksi unik
            $noTransaksi = 'TRX-' . now()->format('YmdHis');

            // Simpan transaksi utama
            $transaksi = TransaksiApotik::create([
                'no_transaksi'          => $noTransaksi,
                'kasir_nama'            => Auth::user()->biodata?->nama_lengkap
                                            ?? Auth::user()->name
                                            ?? 'Kasir Apotik',
                'tanggal'               => now(),
                'total_harga'           => $total,
                'metode_pembayaran'     => $this->metode_pembayaran,
                'diskon'                => (int) ($this->diskon ?: 0),
                'potongan'              => $potongan,
                'total_tagihan_bersih'  => $totalBersih,
                'note'                  => $this->note,
                'pasien_id'             => $this->pasien_id,
            ]);

            // Simpan detail
            if($this->showProduk){
                foreach ($this->obat_estetika as $row) {
                    $transaksi->riwayat()->create([
                        'produk_id'     => $row['produk_id'],
                        'jumlah_produk' => $row['jumlah_produk'] ?? 0,
                        'potongan'      => $row['potongan'] ?: 0,
                        'diskon'        => $row['diskon'] ?: 0,
                        'subtotal'      => $row['subtotal'] ?? 0,
                    ]);
                }
            }

            if($this->showBarang){
                foreach ($this->barang_terjual as $row) {
                    $transaksi->riwayatBarang()->create([
                        'barang_id'     => $row['barang_id'],
                        'jumlah_barang' => $row['jumlah_barang'] ?? 0,
                        'potongan'      => $row['potongan'] ?: 0,
                        'diskon'        => $row['diskon'] ?: 0,
                        'subtotal'      => $row['subtotal'] ?? 0,
                    ]);
                }
            }

            // Kurangi stok + catat mutasi
            if($this->showBarang){
                $this->kurangiStokApotik($transaksi, $this->obat_estetika);
            }
            if($this->showProduk){
                $this->kurangiStokBarang($transaksi, $this->barang_terjual);
            }
            //cetak invoice
            $this->invoice($transaksi->id);
            // Reset form
            $this->reset('obat_estetika');
            $this->closePayment();
        });

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Transaksi berhasil disimpan!',
        ]);

        return redirect()->route('apotik.kasir');
    }

    protected function invoice(int $transaksiId)
    {
        try {
            /* ================= DATA ================= */
            $data_transaksi = TransaksiApotik::with([
                'pasien',
                'riwayat.produk',
                'riwayatBarang.barang',
            ])
            ->findOrFail($transaksiId);

            $pasien        = $data_transaksi->pasien;
            $riwayatObat   = $data_transaksi->riwayat ?? collect();
            $riwayatBarang = $data_transaksi->riwayatBarang ?? collect();

            /* ================= PRINTER ================= */
            $LINE_WIDTH = 32;

            $connector = new WindowsPrintConnector("b21");
            $profile   = CapabilityProfile::load("simple");
            $printer   = new Printer($connector, $profile);

            /* ================= HELPERS ================= */
            $line = function () use ($printer, $LINE_WIDTH) {
                $printer->text(str_repeat('-', $LINE_WIDTH) . "\n");
            };

            $printLR = function ($left, $right = '') use ($printer, $LINE_WIDTH) {
                $left  = substr($left, 0, $LINE_WIDTH);
                $right = substr((string) $right, 0, $LINE_WIDTH);

                $space = $LINE_WIDTH - strlen($left) - strlen($right);
                $printer->text($left . str_repeat(' ', max(0, $space)) . $right . "\n");
            };

            $printItem = function ($nama, $harga, $qty, $diskon, $potongan, $subtotal)
                use ($printLR, $line) {

                $printLR($nama, " {$qty}x " . number_format($harga));

                if ($diskon > 0) {
                    $printLR("Disc", "{$diskon}%");
                }

                if ($potongan > 0) {
                    $printLR("Pot", number_format($potongan));
                }

                $printLR('', "= " . number_format($subtotal));
                // $line();
            };

            /* ================= HEADER ================= */
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setTextSize(2, 1);
            $printer->text("APOTIK DOKTER L\n");
            $printer->setTextSize(1, 1);
            $printer->text("Jl. Gatot Subroto No.88\n");
            $printer->text("Banjarmasin\n");
            $line();

            /* ================= INFO ================= */
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("No Invoice : {$data_transaksi->no_transaksi}\n");
            $printer->text(
                "Tanggal    : " .
                Carbon::parse($data_transaksi->tanggal)
                    ->timezone('Asia/Makassar')
                    ->format('d/m/Y H:i') . " WITA\n"
            );
            $printer->text("Kasir      : " . ($data_transaksi->kasir_nama ?? '-') . "\n");
            $printer->text("Pasien     : " . ($pasien->nama ?? '-') . "\n");
            $line();

            /* ================= ITEMS ================= */
            $printer->text("ITEM PEMBELIAN\n");
            $line();

            /* ===== OBAT / PRODUK ===== */
            if ($riwayatObat->isNotEmpty()) {
                foreach ($riwayatObat as $item) {
                    $printItem(
                        $item->produk->nama_dagang ?? 'Produk',
                        $item->produk->harga_dasar ?? 0,
                        $item->jumlah_produk ?? 1,
                        $item->diskon ?? 0,
                        $item->potongan ?? 0,
                        $item->subtotal ?? 0
                    );
                }
            }

            /* ===== BARANG ===== */
            if ($riwayatBarang->isNotEmpty()) {
                foreach ($riwayatBarang as $item) {
                    $printItem(
                        $item->barang->nama ?? 'Barang',
                        $item->barang->harga_dasar ?? 0,
                        $item->jumlah_barang ?? 1,
                        $item->diskon ?? 0,
                        $item->potongan ?? 0,
                        $item->subtotal ?? 0
                    );
                }
            }

            $line();

            /* ================= TOTAL ================= */
            $printer->setEmphasis(true);
            $printLR("SUBTOTAL", "Rp " . number_format($data_transaksi->total_harga));
            $printLR("Disc", number_format($data_transaksi->diskon) . " %");
            $printLR("Pot", "Rp " . number_format($data_transaksi->potongan));
            $printLR("TOTAL", "Rp " . number_format($data_transaksi->total_tagihan_bersih));
            $printLR(" ", " " . $data_transaksi->metode_pembayaran);
            $printer->setEmphasis(false);

            /* ================= FOOTER ================= */
            $line();
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Terima kasih\n");
            $printer->text("Semoga Lekas Sembuh\n\n");

            $printer->cut();
            $printer->close();

        } catch (\Throwable $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function kurangiStokApotik($transaksi, array $items)
    {
        foreach ($items as $row) {

            if (!isset($row['produk_id']) || ($row['jumlah_produk'] ?? 0) <= 0) {
                continue;
            }

            $produk = ProdukDanObat::lockForUpdate()->find($row['produk_id']);
            if (! $produk) continue;

            $jumlah = (int) $row['jumlah_produk'];
            if ($jumlah <= 0) continue;

            $stokBaru = max($produk->stok - $jumlah, 0);

            // Update stok
            $produk->update([
                'stok' => $stokBaru,
            ]);

            // Catat mutasi
            $produk->mutasiproduk()->create([
                'tipe' => 'keluar',
                'jumlah' => $jumlah,
                'diajukan_oleh' => Auth::user()->biodata?->nama_lengkap ?? Auth::user()->name,
                'catatan' => 'Transaksi Apotik - No: ' . $transaksi->no_transaksi,
            ]);
        }
    }

    protected function kurangiStokBarang($transaksi, array $items)
    {
        foreach ($items as $row) {

            if (!isset($row['barang_id']) || ($row['jumlah_barang'] ?? 0) <= 0) {
                continue;
            }

            $barang = Barang::lockForUpdate()->find($row['barang_id']);
            if (! $barang) continue;

            $jumlah = (int) $row['jumlah_barang'];
            if ($jumlah <= 0) continue;

            $stokBaru = max($barang->stok - $jumlah, 0);

            // Update stok
            $barang->update([
                'stok' => $stokBaru,
            ]);

            // Catat mutasi
            $barang->mutasi()->create([
                'tipe' => 'keluar',
                'jumlah' => $jumlah,
                'diajukan_oleh' => Auth::user()->biodata?->nama_lengkap ?? Auth::user()->name,
                'catatan' => 'Transaksi Apotik - No: ' . $transaksi->no_transaksi,
            ]);
        }
    }

    public function render()
    {
        if (! Gate::allows('akses', 'Transaksi Apotik Tambah')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.apotik.create');
    }

    public function formProdukOpen()
    {
        $this->showProduk = true;
    }

    public function formBarangOpen()
    {
        $this->showBarang = true;
    }
}