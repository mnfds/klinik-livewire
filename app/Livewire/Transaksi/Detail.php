<?php

namespace App\Livewire\Transaksi;

use Livewire\Component;
use App\Models\Bundling;
use App\Models\ProdukDanObat;
use App\Models\PasienTerdaftar;
use App\Models\RencanaProdukRM;
use App\Models\ObatRacikanFinal;
use App\Models\RencanaLayananRM;
use App\Models\RencanaTreatmentRM;
use App\Models\ObatNonRacikanFinal;
use App\Models\RekamMedis;
use App\Models\RencananaBundlingRM;
use App\Models\RiwayatTransaksiKlinik;
use App\Models\TransaksiKlinik;
use Illuminate\Support\Facades\DB;

class Detail extends Component
{
    public ?int $pasien_terdaftar_id = null;
    public ?PasienTerdaftar $pasienTerdaftar = null;

    public $pasien;
    public $rekammedis_id;
    public $obatapoteker;
    public $obatracik;
    public $obatnonracik;
    public $pelayanan;
    public $treatment;
    public $produk;
    public $bundling;
    public $bundlingUsages;

    // Obat yang di centang
    public $selectedObat = []; // untuk non racikan
    public $selectedRacikan = [];

    public function mount($id)
    {
        $this->pasien_terdaftar_id = $id;

        // Ambil semua relasi penting dalam satu query
        $this->pasienTerdaftar = PasienTerdaftar::with([
            'pasien',
            'rekamMedis.rencanaLayananRM.pelayanan',
            'rekamMedis.obatFinal',
            'rekamMedis.obatNonRacikanRM',
            'rekamMedis.obatRacikanRM.bahanRacikan',
            'rekamMedis.rencanaTreatmentRM.treatment',
            'rekamMedis.rencanaProdukRM.produk',
            'rekamMedis.rencanaBundlingRM.bundling.treatmentBundlings',
            'rekamMedis.rencanaBundlingRM.bundling.pelayananBundlings',
            'rekamMedis.rencanaBundlingRM.bundling.produkObatBundlings',
            'rekamMedis.produkBundlingUsages.produk',
            'rekamMedis.produkBundlingUsages.bundling',
        ])->findOrFail($this->pasien_terdaftar_id);

        // Simpan data pasien
        $this->pasien = $this->pasienTerdaftar->pasien;

        // Ambil rekam medis (jika ada)
        $rekamMedis = $this->pasienTerdaftar->rekamMedis;
        $this->rekammedis_id = $rekamMedis->id ?? null;

        // Jika ada rekam medis, ambil semua rencana dari relasi yang sudah di-eager load
        if ($rekamMedis) {
            $this->obatapoteker     = $rekamMedis->obatFinal ?? collect();
            $this->obatnonracik     = $rekamMedis->obatNonRacikanRM ?? collect();
            $this->obatracik        = $rekamMedis->obatRacikanRM ?? collect();
            $this->pelayanan        = $rekamMedis->rencanaLayananRM ?? collect();
            $this->treatment        = $rekamMedis->rencanaTreatmentRM ?? collect();
            $this->produk           = $rekamMedis->rencanaProdukRM ?? collect();
            $this->bundling         = $rekamMedis->rencanaBundlingRM ?? collect();
        } else {
            $this->pelayanan        = $this->treatment = $this->produk = $this->bundling = collect();
        }

        // Auto-check semua obat non-racik
        $this->selectedObat = $this->obatapoteker
            ->flatMap(fn($final) => $final->obatNonRacikanFinals->pluck('id'))
            ->toArray();

        // Auto-check semua obat racik
        $this->selectedRacikan = $this->obatapoteker
            ->flatMap(fn($final) => $final->obatRacikanFinals->pluck('id'))
            ->toArray();
        
        $this->bundlingUsages = collect();

        if ($rekamMedis) {
            $this->bundlingUsages = collect()
                ->merge($rekamMedis->produkBundlingUsages ?? [])
                ->merge($rekamMedis->treatmentBundlingUsages ?? [])
                ->merge($rekamMedis->pelayananBundlingUsages ?? []);
        }
    }

    public function render()
    {
        return view('livewire.transaksi.detail');
    }

    // public function create(){
    //     $nonRacikanIds = $this->selectedObat;
    //     $racikanIds = $this->selectedRacikan;

    //     // Update kolom konfirmasi menjadi 'terkonfirmasi'
    //     if (!empty($nonRacikanIds)) {
    //         ObatNonRacikanFinal::whereIn('id', $nonRacikanIds)
    //             ->update(['konfirmasi' => 'terkonfirmasi']);
    //     }

    //     if (!empty($racikanIds)) {
    //         ObatRacikanFinal::whereIn('id', $racikanIds)
    //             ->update(['konfirmasi' => 'terkonfirmasi']);
    //     }

    //     PasienTerdaftar::findOrFail($this->pasien_terdaftar_id)->update(['status_terdaftar' => 'lunas']);

    //     $this->kurangiStokProduk();

    //     $this->dispatch('toast', [
    //             'type' => 'success',
    //             'message' => 'Transaksi Selesai.'
    //     ]);

    //     $this->reset();

    //     return redirect()->route('transaksi.kasir');
    // }

    public function create()
    {
        DB::beginTransaction();

        try {
            $nonRacikanIds = $this->selectedObat;
            $racikanIds = $this->selectedRacikan;

            // Update konfirmasi obat
            if (!empty($nonRacikanIds)) {
                ObatNonRacikanFinal::whereIn('id', $nonRacikanIds)
                    ->update(['konfirmasi' => 'terkonfirmasi']);
            }

            if (!empty($racikanIds)) {
                ObatRacikanFinal::whereIn('id', $racikanIds)
                    ->update(['konfirmasi' => 'terkonfirmasi']);
            }

            // ✅ 1. Buat Transaksi Klinik
            $rekamMedis = RekamMedis::findOrFail($this->rekammedis_id);

            $transaksi = TransaksiKlinik::create([
                'rekam_medis_id' => $rekamMedis->id,
                'no_transaksi' => 'TRX-' . now()->format('YmdHis'),
                'tanggal_transaksi' => now(),
                'total_tagihan' => 0, // akan di-update setelah item disimpan
                'status' => 'belum_bayar',
            ]);

            $totalTagihan = 0;

            // ✅ 2. Simpan semua item ke riwayat transaksi

            // --- Pelayanan ---
            foreach ($this->pelayanan as $item) {
                $harga = (int) ($item->pelayanan->harga_bersih ?? 0);
                RiwayatTransaksiKlinik::create([
                    'transaksi_klinik_id' => $transaksi->id,
                    'jenis_item' => 'pelayanan',
                    'nama_item' => $item->pelayanan->nama_pelayanan ?? '-',
                    'qty' => $item->jumlah_pelayanan,
                    'harga' => $harga,
                    'subtotal' => $harga,
                ]);
                $totalTagihan += $harga;
            }

            // --- Treatment ---
            foreach ($this->treatment as $item) {
                $subtotaltreatment = (int) ($item->subtotal ?? 0);
                RiwayatTransaksiKlinik::create([
                    'transaksi_klinik_id' => $transaksi->id,
                    'jenis_item' => 'treatment',
                    'nama_item' => $item->treatment->nama_treatment ?? '-',
                    'qty' => $item->jumlah_treatment,
                    'harga' => $item->treatment->harga_bersih,
                    'subtotal' => $item->subtotal,
                ]);
                $totalTagihan += $subtotaltreatment;
            }

            // --- Produk ---
            foreach ($this->produk as $item) {
                $subtotalproduk = (int) ($item->subtotal ?? 0);
                RiwayatTransaksiKlinik::create([
                    'transaksi_klinik_id' => $transaksi->id,
                    'jenis_item' => 'produk',
                    'nama_item' => $item->produk->nama_dagang ?? '-',
                    'qty' => $item->jumlah_produk,
                    'harga' => $item->produk->harga_bersih,
                    'subtotal' => $subtotalproduk,
                ]);
                $totalTagihan += $subtotalproduk;
            }

            // --- Bundling ---
            foreach ($this->bundling as $item) {
                $subtotalbundling = (int) ($item->subtotal ?? 0);
                RiwayatTransaksiKlinik::create([
                    'transaksi_klinik_id' => $transaksi->id,
                    'jenis_item' => 'bundling',
                    'nama_item' => $item->bundling->nama ?? '-',
                    'qty' => $item->jumlah_bundling,
                    'harga' => $item->bundling->harga_bersih,
                    'subtotal' => $subtotalbundling,
                ]);
                $totalTagihan += $subtotalbundling;
            }

            // --- Obat Non Racik ---
            foreach ($this->selectedObat as $item) {
                $subtotalnonracik = (int) ($item->total_obat ?? 0);
                RiwayatTransaksiKlinik::create([
                    'transaksi_klinik_id' => $transaksi->id,
                    'jenis_item' => 'obat_non_racik',
                    'nama_item' => $item->produk->nama_dagang ?? '-',
                    'qty' => $item->jumlah_obat,
                    'harga' => $item->harga_obat,
                    'subtotal' => $subtotalnonracik,
                ]);
                $totalTagihan += $subtotalnonracik;
            }

            // --- Obat Racik ---
            foreach ($this->selectedObat as $item) {
                $subtotalracik = (int) ($item->total_racikan ?? 0);
                RiwayatTransaksiKlinik::create([
                    'transaksi_klinik_id' => $transaksi->id,
                    'jenis_item' => 'obat_racik',
                    'nama_item' => $item->bahanRacikanFinals->produk->nama_dagang ?? '-',
                    'qty' => $item->jumlah_racikan,
                    'harga' => $subtotalracik,
                    'subtotal' => $subtotalracik,
                ]);
                $totalTagihan += $subtotalracik;
            }

            // ✅ 3. Update total tagihan transaksi
            $transaksi->update([
                'total_tagihan' => $totalTagihan,
                'status' => 'lunas',
            ]);

            // ✅ 4. Update status pasien + kurangi stok
            PasienTerdaftar::findOrFail($this->pasien_terdaftar_id)->update(['status_terdaftar' => 'lunas']);
            $this->kurangiStokProduk();

            DB::commit();

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Transaksi berhasil disimpan'
            ]);

            $this->reset();
            return redirect()->route('transaksi.kasir');

        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan: ' . $th->getMessage()
            ]);
        }
    }

    protected function kurangiStokProduk()
    {
        // --- Pengurangan Stok Produk Dari Pembelian Produk ---
        foreach ($this->produk as $item) {
            if ($item->produk) {
                $produk = $item->produk; // relasi ke ProdukDanObat
                $jumlah = $item->jumlah_produk ?? 0;
                
                $stokBaru = max($produk->stok - $jumlah, 0);
                $produk->update(['stok' => $stokBaru]);
            }
        }

        // --- Pengurangan Stok Produk Dari Obat Non Racik ---
        foreach ($this->selectedObat as $obatId) {
            $nonracik = ObatNonRacikanFinal::with('produk')->find($obatId);
            if (! $nonracik) continue;

            $produk = $nonracik->produk;
            $jumlah = (int) ($nonracik->jumlah_obat ?? 0);

            if (! $produk || $jumlah <= 0) continue;

            $stokBaru = max($produk->stok - $jumlah, 0);
            $produk->update(['stok' => $stokBaru]);

        }

        // --- Pengurangan Stok Produk Dari Obat Non Racik ---
        foreach ($this->selectedRacikan as $racikId) {
            // Ambil racikan beserta bahan-bahan yang digunakan
            $racik = ObatRacikanFinal::with('bahanRacikanFinals.produk')->find($racikId);
            if (! $racik) continue;

            foreach ($racik->bahanRacikanFinals as $bahan) {
                $produk = ProdukDanObat::lockForUpdate()->find($bahan->produk_id);
                if (! $produk) continue;

                $jumlah = (int) ($bahan->jumlah_obat ?? 0);
                if ($jumlah <= 0) continue;

                $stokBaru = max($produk->stok - $jumlah, 0);
                $produk->update(['stok' => $stokBaru]);
            }
        }

        // --- Pengurangan Stok Produk Dari Produk Bundling ---
        foreach ($this->bundling as $rencanaBundling) {
            $rencana = RencananaBundlingRM::with('bundling.produkObatBundlings.produk')->find($rencanaBundling->id);
            if (! $rencana || ! $rencana->bundling) continue;

            foreach ($rencana->bundling->produkObatBundlings as $item) {
                $produk = ProdukDanObat::lockForUpdate()->find($item->produk_id);
                if (! $produk) continue;

                $jumlah = (int) ($item->jumlah ?? 0); // ← ini sudah benar
                if ($jumlah <= 0) continue;

                $stokBaru = max($produk->stok - $jumlah, 0);

                $produk->update(['stok' => $stokBaru]);
            }
        }

    }

}
