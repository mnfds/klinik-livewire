<?php

namespace App\Livewire\Transaksi;

use Livewire\Component;
use App\Models\Bundling;
use App\Models\RekamMedis;
use Mike42\Escpos\Printer;
use App\Models\ProdukDanObat;
use Illuminate\Support\Carbon;
use App\Models\PasienTerdaftar;
use App\Models\RencanaProdukRM;
use App\Models\TransaksiKlinik;
use App\Models\ObatRacikanFinal;
use App\Models\RencanaLayananRM;
use App\Models\RencanaTreatmentRM;
use Illuminate\Support\Facades\DB;
use App\Models\ObatNonRacikanFinal;
use App\Models\RencananaBundlingRM;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Mike42\Escpos\CapabilityProfile;
use App\Models\RiwayatTransaksiKlinik;
use App\Services\PutInFinishedEncounter;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

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

    public bool $showPaymentForm = false;

    public $diskon = 0;
    public $potongan = 0;


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
        if (! Gate::allows('akses', 'Transaksi Klinik Detail')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.transaksi.detail');
    }

    public function create()
    {
        if (! Gate::allows('akses', 'Transaksi Klinik Selesai')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        $this->potongan = (int) str_replace('.', '', $this->potongan);

        DB::beginTransaction();

        // dd([
        //     'potongan' => $this->potongan,
        //     'diskon' => $this->diskon,
        // ]);
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

            $pt = $this->pasienTerdaftar;

            // Jika tidak ada waktu_pulang, update dengan Carbon::now
            if (!$pt->waktu_pulang) {
                $pt->update([
                    'waktu_pulang' => Carbon::now('Asia/Makassar')->setTimezone('UTC')->toIso8601String()
                ]);
            }

            // Setelah itu ambil ulang nilai waktu_pulang yang sudah pasti ada
            $waktu_pulang = $pt->waktu_pulang;
            //put encounter
            $kirimsatusehat = $pt->encounter_id;
            if ($kirimsatusehat) {

                // Encounter ID yang sudah dibuat saat POST Encounter
                $encounterId = $pt->encounter_id;
                $diagnosis = $pt->rekamMedis->icdRM ?? [];
                // perulangan diagnosis jika ada lebih dari 1 kode ICD
                $diagnosisData = []; //init diagnosis untuk simpan data dengan struktur FHIR
                $rank = 1;
                foreach ($diagnosis as $icd) {
                    if (!$icd || !$icd->code) {
                        continue; // Lewati jika tidak ada
                    }
                    if (!$icd->condition_id) {
                        continue; // aman
                    }

                    $diagnosisData[] = [
                        "condition" => [
                            "reference" => "Condition/" . $icd->condition_id,
                            "display" => $icd->name_id
                        ],
                        "use" => [
                            "coding" => [
                                [
                                    "system" => "http://terminology.hl7.org/CodeSystem/diagnosis-role",
                                    "code" => "DD",
                                    "display" => "Discharge diagnosis"
                                ]
                            ]
                        ],
                        "rank" => $rank++   // auto increment
                    ];
                }

                // Panggil PUT Encounter
                $putEncounter = app(PutInFinishedEncounter::class);
                $putEncounter->handle(
                    encounterId: $encounterId,
                    waktuTiba: $pt->waktu_tiba,
                    WaktuDiperiksa: $pt->waktu_diperiksa,
                    WaktuPulang: $waktu_pulang,
                    pasienNama: $pt->pasien->nama,
                    pasienIhs: $pt->pasien->no_ihs,
                    dokterNama: $pt->dokter->nama_dokter,
                    dokterIhs: $pt->dokter->ihs,
                    location: $pt->poliklinik->location,
                    diagnosisData: $diagnosisData,
                );
            }

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

            foreach ($this->selectedObat as $id) {
                $item = ObatNonRacikanFinal::with('produk')->find($id);
                if (!$item) continue;

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

            foreach ($this->selectedRacikan as $id) {
                $item = ObatRacikanFinal::with('bahanRacikanFinals.produk')->find($id);
                if (!$item) continue;

                $subtotalracik = (int) ($item->total_racikan ?? 0);

                RiwayatTransaksiKlinik::create([
                    'transaksi_klinik_id' => $transaksi->id,
                    'jenis_item' => 'obat_racik',
                    'nama_item' => $item->nama_racikan ?? '-',
                    'qty' => $item->jumlah_racikan,
                    'harga' => $subtotalracik,
                    'subtotal' => $subtotalracik,
                ]);

                $totalTagihan += $subtotalracik;
            }

            $diskonNominal = 0;

            // Diskon persen
            if ($this->diskon > 0) {
                $diskonNominal = ($totalTagihan * $this->diskon) / 100;
            }

            // Total setelah diskon
            $totalSetelahDiskon = $totalTagihan - $diskonNominal;

            // Potongan nominal
            $totalAkhir = $totalSetelahDiskon - ($this->potongan ?? 0);

            // Proteksi agar tidak minus
            if ($totalAkhir < 0) {
                $totalAkhir = 0;
            }

            // ✅ 3. Update total tagihan transaksi
            $transaksi->update([
                'total_tagihan' => $totalTagihan,
                'total_tagihan_bersih' => $totalAkhir,
                'diskon' => $this->diskon,
                'potongan' => $this->potongan,
                'status' => 'lunas',
            ]);

            // ✅ 4. Update status pasien + kurangi stok
            PasienTerdaftar::findOrFail($this->pasien_terdaftar_id)->update(['status_terdaftar' => 'lunas']);
            $this->kurangiStokProduk();
            $this->kurangiStokBahanBaku();
            $this->invoice($transaksi->id);

            DB::commit();
            if ($kirimsatusehat) {
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Transaksi berhasil disimpan Dan Kirim Satu Sehat'
                ]);
            } else {
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Transaksi berhasil disimpan'
                ]);
            }

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

    public function openPayment()
    {
        $this->diskon = 0;
        $this->potongan = 0;
        $this->showPaymentForm = true;
    }

    public function getTotalKotorProperty()
    {
        $total = 0;

        foreach ([$this->pelayanan, $this->treatment, $this->produk, $this->bundling] as $collection) {
            foreach ($collection ?? [] as $item) {
                $total += (int) (
                    $item->subtotal
                    ?? $item->pelayanan->harga_pelayanan
                    ?? $item->treatment->harga_treatment
                    ?? $item->produk->harga_jual
                    ?? $item->bundling->harga_bundling
                    ?? 0
                );
            }
        }

        foreach ($this->obatapoteker ?? [] as $obat) {

            $total +=
                ($obat->obatNonRacikanFinals?->whereIn('id', $this->selectedObat ?? [])->sum('total_obat') ?? 0) +
                ($obat->obatRacikanFinals?->whereIn('id', $this->selectedRacikan ?? [])->sum('total_racikan') ?? 0);

            $adaRacikan = $obat->obatRacikanFinals
                ?->whereIn('id', $this->selectedRacikan ?? [])
                ->isNotEmpty();

            if ($adaRacikan) {
                $total += ($obat->tuslah ?? 0) + ($obat->embalase ?? 0);
            }
        }

        return $total;
    }

    public function getTotalBayarProperty()
    {
        $total = $this->totalKotor;

        // diskon persen
        if ($this->diskon > 0) {
            $total -= ($total * $this->diskon / 100);
        }

        // potongan rupiah
        if ($this->potongan > 0) {
            $total -= (int) $this->potongan;
        }

        return max(0, (int) $total);
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

        // --- Pengurangan Stok Produk Dari Obat Racikan ---
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

    protected function kurangiStokBahanBaku()
    {
        DB::transaction(function () {
            foreach ($this->treatment as $item) {
                $jumlahTreatment = $item->jumlah_treatment ?? 1;
                // ambil semua bahan baku dari treatment
                $treatmentBahans = $item->treatment
                    ->treatmentbahan()
                    ->with('bahanbaku')
                    ->get();
                foreach ($treatmentBahans as $tb) {
                    $bahan = $tb->bahanbaku;
                    if (! $bahan) {
                        continue;
                    }
                    // total bahan baku yang dipakai
                    $jumlahKeluar = ($tb->jumlah ?? 1) * $jumlahTreatment;
                    // hitung stok baru (aman)
                    $stokAwal = $bahan->stok;
                    $stokAkhir = max($stokAwal - $jumlahKeluar, 0);
                    // update stok bahan baku
                    $bahan->update([
                        'stok' => $stokAkhir,
                    ]);
                    // catat mutasi keluar
                    $bahan->mutasibahan()->create([
                        'tipe'          => 'keluar',
                        'jumlah'        => $jumlahKeluar,
                        'diajukan_oleh' => Auth::user()->biodata?->nama_lengkap ?? 'System',
                        'catatan'       => 'Pemakaian bahan baku dari treatment',
                    ]);
                }
            }

            foreach ($this->bundling as $rb) {
                $jumlahBundling = $rb->jumlah_bundling ?? 1;
                $treatmentBundlings = $rb->bundling
                    ->treatmentBundlings()
                    ->with('treatment.treatmentbahan.bahanbaku')
                    ->get();
                foreach ($treatmentBundlings as $tb) {
                    $jumlahTreatmentDalamBundling = $tb->jumlah ?? 1;
                    foreach ($tb->treatment->treatmentbahan as $treatmentBahan) {
                        $bahan = $treatmentBahan->bahanbaku;
                        if (! $bahan) continue;
                        $jumlahKeluar =
                            ($treatmentBahan->jumlah ?? 1)
                            * $jumlahTreatmentDalamBundling
                            * $jumlahBundling;
                        if ($bahan->stok < $jumlahKeluar) {
                            throw new \Exception("Stok {$bahan->nama} tidak mencukupi");
                        }
                        $bahan->decrement('stok', $jumlahKeluar);
                        $bahan->mutasibahan()->create([
                            'tipe'          => 'keluar',
                            'jumlah'        => $jumlahKeluar,
                            'diajukan_oleh' => Auth::user()->biodata?->nama_lengkap ?? 'System',
                            'catatan'       => 'Pemakaian bahan baku dari bundling',
                        ]);
                    }
                }
            }
        });
    }

    protected function invoice(int $transaksiId)
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
            ])->findOrFail($transaksiId);

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
            $grandTotalBersih = $grandTotal;
            if ($data_transaksi->diskon > 0) {
            $grandTotalBersih -= ($grandTotalBersih * $data_transaksi->diskon / 100);
            }

            // potongan rupiah
            if ($data_transaksi->potongan > 0) {
                $grandTotalBersih -= (int) $data_transaksi->potongan;
            }
            $printer->setEmphasis(true);
            $printLR("SUBTOTAL", "Rp " . number_format($grandTotal));
            $printLR("Disc", number_format($data_transaksi->diskon) . " %");
            $printLR("Pot", "Rp " . number_format($data_transaksi->potongan));
            $printLR("TOTAL", "Rp " . number_format($grandTotalBersih));
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
}
