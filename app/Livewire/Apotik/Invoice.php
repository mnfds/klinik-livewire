<?php

namespace App\Livewire\Apotik;

use Livewire\Component;
use Carbon\Carbon;
use Mike42\Escpos\Printer;
use App\Models\PasienTerdaftar;
use App\Models\TransaksiApotik;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class Invoice extends Component
{
    public $transaksiId;

    #[\Livewire\Attributes\On('print')]
    public function invoice($rowId): void
    {
        try {
            /* ================= DATA ================= */
            $data_transaksi = TransaksiApotik::with([
                'pasien',
                'riwayat.produk',
                'riwayatBarang.barang',
            ])
            ->findOrFail($rowId);

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

    public function render()
    {
        return view('livewire.apotik.invoice');
    }
}
