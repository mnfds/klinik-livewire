<?php

namespace App\Livewire\Transaksi;

use Carbon\Carbon;
use Livewire\Component;
use Mike42\Escpos\Printer;
use App\Models\PasienTerdaftar;
use App\Models\TransaksiKlinik;
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
            $data_transaksi = TransaksiKlinik::with([
                'rekammedis.pasienTerdaftar.pasien',
                'rekammedis.pasienTerdaftar.poliklinik',
                'rekammedis.rencanaLayananRM.pelayanan',
                'rekammedis.rencanaTreatmentRM.treatment',
                'rekammedis.rencanaProdukRM.produk',
                'rekammedis.rencanaBundlingRM.bundling',
                'rekammedis.rencanaBundlingRM.bundling.treatmentBundlingRM',
                'rekammedis.rencanaBundlingRM.bundling.treatmentBundlingRM.treatment',
                'rekammedis.rencanaBundlingRM.bundling.pelayananBundlingRM',
                'rekammedis.rencanaBundlingRM.bundling.pelayananBundlingRM.pelayanan',
                'rekammedis.rencanaBundlingRM.bundling.produkObatBundlingRM',
                'rekammedis.rencanaBundlingRM.bundling.produkObatBundlingRM.produk',
            ])->findOrFail($rowId);

            $rm     = $data_transaksi->rekammedis;
            $pasien = $rm?->pasienTerdaftar?->pasien;
            $poli   = $rm?->pasienTerdaftar?->poliklinik;

            $layanans   = $rm?->rencanaLayananRM ?? collect();
            $treatments = $rm?->rencanaTreatmentRM ?? collect();
            $produks    = $rm?->rencanaProdukRM ?? collect();
            $bundlings    = $rm?->rencanaBundlingRM ?? collect();
            // dd($produks);

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
            $printBundling = function ($item) use ($printer, $printLR, $line) {

                $bundling = $item->bundling;

                $nama     = $bundling->nama ?? 'Bundling';
                $harga    = $bundling->harga ?? 0;
                $qty      = $item->jumlah_bundling ?? 1;
                $diskon   = $item->diskon ?? 0;
                $potongan = $item->potongan ?? 0;
                $subtotal = $item->subtotal ?? 0;

                // Header bundling
                $printLR($nama, "{$qty}x " . number_format($harga));

                // Isi bundling (indent)
                foreach ($bundling->treatmentBundlingRM ?? [] as $t) {
                    $printer->text("  - " . ($t->treatment->nama_treatment ?? '') . " " . $t->jumlah_awal . "x" . "\n");
                }

                foreach ($bundling->pelayananBundlingRM ?? [] as $l) {
                    $printer->text("  - " . ($l->pelayanan->nama_pelayanan ?? '') . " " . $l->jumlah_awal . "x" . "\n");
                }

                foreach ($bundling->produkObatBundlingRM ?? [] as $p) {
                    $printer->text("  - " . ($p->produk->nama_dagang ?? '') . " " . $p->jumlah_awal . "x" . "\n");
                }

                if ($diskon > 0) {
                    $printLR("Disc", "{$diskon}%");
                }

                if ($potongan > 0) {
                    $printLR("Pot", number_format($potongan));
                }

                $printLR('', "= " . number_format($subtotal));
                // $line();

                return $subtotal;
            };

            /* ================= HEADER ================= */
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setTextSize(2, 1);
            $printer->text("KLINIK DOKTER L\n");
            $printer->setTextSize(1, 1);
            $printer->text("Jl. Gatot Subroto No.88\n");
            $printer->text("Banjarmasin\n");
            $line();

            /* ================= INFO ================= */
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("No Invoice : {$data_transaksi->no_transaksi}\n");
            $printer->text(
                "Tanggal    : " .
                Carbon::parse($data_transaksi->tanggal_transaksi)
                    ->timezone('Asia/Makassar')
                    ->format('d/m/Y H:i') . " WITA\n"
            );
            $printer->text("Pasien     : " . ($pasien->nama ?? '-') . "\n");
            $printer->text("Poliklinik : " . ($poli->nama_poli ?? '-') . "\n");
            $line();

            /* ================= ITEMS ================= */
            $printer->text("ITEM PEMBELIAN\n");
            $line();

            $grandTotal = 0;

            /* ===== TREATMENT ===== */
            if ($treatments->isNotEmpty()) {
                $printer->text("TREATMENT\n");
                $line();

                foreach ($treatments as $item) {
                    $subtotal = $item->subtotal ?? 0;
                    $grandTotal += $subtotal;

                    $printItem(
                        $item->treatment->nama_treatment ?? 'Treatment',
                        $item->treatment->harga_treatment ?? 0,
                        $item->jumlah_treatment ?? 1,
                        $item->diskon ?? 0,
                        $item->potongan ?? 0,
                        $subtotal
                    );
                }
            }

            /* ===== LAYANAN ===== */
            if ($layanans->isNotEmpty()) {
                $line();
                $printer->text("LAYANAN\n");
                $line();

                foreach ($layanans as $item) {
                    $subtotal = $item->subtotal ?? 0;
                    $grandTotal += $subtotal;

                    $printItem(
                        $item->pelayanan->nama_pelayanan ?? 'Layanan',
                        $item->pelayanan->harga ?? 0,
                        $item->jumlah ?? 1,
                        $item->diskon ?? 0,
                        $item->potongan ?? 0,
                        $subtotal
                    );
                }
            }

            /* ===== PRODUK ===== */
            if ($produks->isNotEmpty()) {
                $line();
                $printer->text("PRODUK\n");
                $line();

                foreach ($produks as $item) {
                    $subtotal = $item->subtotal ?? 0;
                    $grandTotal += $subtotal;

                    $printItem(
                        $item->produk->nama_dagang ?? 'Produk',
                        $item->produk->harga_dasar ?? 0,
                        $item->jumlah_produk ?? 1,
                        $item->diskon ?? 0,
                        $item->potongan ?? 0,
                        $subtotal
                    );
                }
            }

            /* ===== BUNDLING ===== */
            if ($bundlings->isNotEmpty()) {
                $line();
                $printer->text("BUNDLING\n");
                $line();

                foreach ($bundlings as $item) {
                    $grandTotal += $printBundling($item);
                }
            }
            $line();
            /* ================= TOTAL ================= */
            $printer->setEmphasis(true);
            $printLR("TOTAL", "Rp " . number_format($grandTotal));
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
        return view('livewire.transaksi.invoice');
    }

}
