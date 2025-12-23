<?php

namespace App\Livewire\Resep;

use Livewire\Component;
use App\Models\ObatFinal;
use App\Models\PasienTerdaftar;
use App\Models\ObatRacikanFinal;
use App\Models\BahanRacikanFinal;
use Illuminate\Support\Facades\DB;
use App\Models\ObatNonRacikanFinal;
use Illuminate\Support\Facades\Gate;

class Detail extends Component
{
    public ?int $pasien_terdaftar_id = null;
    public ?PasienTerdaftar $pasienTerdaftar = null;

    // hanya untuk binding data ke form
    public array $obatNonRacikanItems = [];
    public array $obatRacikanItems = [];
    public array $produkRencanaItems = [];
    public array $produkBundlingItems = [];

    //input obat final
    public $rekammedis_id;
    public $tuslah;
    public $embalase;
    public $totalracik;
    public $totalnonracik;

    public $obatNonracikFinal = '[]';
    public $obatRacikanFinal = '[]';

    public $racikanInput = [
        [
            'obat_final_id', //ambil dari variable create obat_final
            'nama_racikan', // ambil dari form
            'jumlah_racikan', //ambil dari form
            'satuan_racikan', //ambil dari form
            'total_racikan', //ambil dari form
            'dosis', // ambil dari form
            'hari', // ambil dari form
            'aturan_pakai', // ambil dari form
            'metode_racikan', // tidak digunakan terlebih dahulu
        ]
    ];

    public $bahanRacikanInput = [
        [
            'produk_id', 
            'obat_racikan_final_id', // ambil dari id pada varibel create racikan
            'jumlah_obat',
            'satuan_obat',
            'harga_obat',
            'total_obat',
        ]
    ];

    public $nonRacikanInput = [
        [
            'obat_final_id', //ambil dari variabel  create obat_final
            'produk_id', // ambil dari form
            'jumlah_obat', // ambil dari form
            'satuan_obat', // ambil dari form
            'harga_obat', // ambil dari form
            'total_obat',  // ambil dari form
            'dosis', // ambil dari form
            'hari', // ambil dari form
            'aturan_pakai', // ambil dari form
            ]
    ];

    public function mount($pasien_terdaftar_id = null)
    {
        $this->pasien_terdaftar_id = $pasien_terdaftar_id;

        $this->pasienTerdaftar = PasienTerdaftar::with([
            'rekamMedis.obatNonRacikanRM',
            'rekamMedis.obatRacikanRM.bahanRacikan',
            'rekamMedis.rencanaProdukRM',
            'rekamMedis.rencanaBundlingRM.bundling.produkObatBundlings.produk',
        ])->findOrFail($this->pasien_terdaftar_id);

        $this->rekammedis_id = $this->pasienTerdaftar->rekamMedis->id;

        // mapping data obat non racikan
        $this->obatNonRacikanItems = $this->pasienTerdaftar->rekamMedis->obatNonRacikanRM->map(fn($o) => [
                'id'   => $o->id,
                'nama_obat_non_racikan'   => $o->nama_obat_non_racikan,
                'jumlah_obat_non_racikan' => $o->jumlah_obat_non_racikan,
                'satuan_obat_non_racikan' => $o->satuan_obat_non_racikan,
                'dosis_obat_non_racikan'  => $o->dosis_obat_non_racikan,
                'hari_obat_non_racikan'   => $o->hari_obat_non_racikan,
                'aturan_pakai_obat_non_racikan' => $o->aturan_pakai_obat_non_racikan,
            ])->toArray();

        // mapping data obat racikan
        $this->obatRacikanItems = $this->pasienTerdaftar->rekamMedis->obatRacikanRM->map(fn($r) => [
                'id' => $r->id,
                'nama_racikan' => $r->nama_racikan,
                'jumlah_racikan' => $r->jumlah_racikan,
                'satuan_racikan' => $r->satuan_racikan,
                'dosis_obat_racikan' => $r->dosis_obat_racikan,
                'hari_obat_racikan' => $r->hari_obat_racikan,
                'aturan_pakai_racikan' => $r->aturan_pakai_racikan,
                'metode_racikan' => $r->metode_racikan,
                'bahan' => $r->bahanRacikan->map(fn($b) => [
                    'id' => $b->id,
                    'nama_obat_racikan'   => $b->nama_obat_racikan,
                    'jumlah_obat_racikan' => $b->jumlah_obat_racikan,
                    'satuan_obat_racikan' => $b->satuan_obat_racikan,
                ])->toArray(),
            ])->toArray();

        // mapping data produk individual
        $this->produkRencanaItems = $this->pasienTerdaftar->rekamMedis->rencanaProdukRM->map(fn($p) => [
            'id' => $p->id,
            'nama_produk' => $p->produk->nama_dagang,
            'jumlah' => $p->jumlah_produk,
            'satuan' => $p->produk->sediaan,
        ])->toArray();
        // mapping data produk bundling
        $this->produkBundlingItems = $this->pasienTerdaftar
            ->rekamMedis
            ->rencanaBundlingRM
            ->flatMap(function ($rencanaBundling) {
                return $rencanaBundling->bundling->produkObatBundlings->map(function ($p) use ($rencanaBundling) {
                    return [
                        'bundling_id' => $rencanaBundling->bundling->id,
                        'nama_bundling' => $rencanaBundling->bundling->nama,
                        'produk_id' => $p->produk->id ?? null,
                        'nama_produk' => $p->produk->nama_dagang ?? '-',
                        'jumlah' => $p->jumlah ?? '-',
                        'satuan' => $p->produk->sediaan ?? '-',
                    ];
                });
            })->toArray();

    }

    public function render()
    {
        return view('livewire.resep.detail');
    }

    public function hitungTotalObat()
    {
        $nonracik = json_decode($this->obatNonracikFinal, true);
        $racikan = json_decode($this->obatRacikanFinal, true);

        $totalNonRacik = 0;
        $totalRacik = 0;

        // Hitung total non racik
        if (is_array($nonracik) && count($nonracik) > 0) {
            foreach ($nonracik as $item) {
                $totalNonRacik += (int) ($item['total'] ?? 0);
            }
        }

        // Hitung total racik
        if (is_array($racikan) && count($racikan) > 0) {
            foreach ($racikan as $index => $racik) {
                $totalBahan = 0;

                if (!empty($racik['bahan'])) {
                    foreach ($racik['bahan'] as $bahan) {
                        $totalBahan += (int) ($bahan['total'] ?? 0);
                    }
                }

                // simpan ke array racikan
                $racikan[$index]['total_racikan'] = $totalBahan;

                $totalRacik += $totalBahan;
            }
        }

        // simpan hasil ke properti
        $this->totalnonracik = $totalNonRacik;
        $this->totalracik = $totalRacik;

        // opsional: update kembali versi encoded JSON racikan (kalau perlu dikirim ke DB)
        $this->obatRacikanFinal = json_encode($racikan);
    }

    public function create()
    {
        if (! Gate::allows('akses', 'Kalkulasi Obat')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        $this->hitungTotalObat();

        $nonracik = json_decode($this->obatNonracikFinal, true);
        $racikan = json_decode($this->obatRacikanFinal, true);

        DB::beginTransaction();

        try {

            PasienTerdaftar::findOrFail($this->pasien_terdaftar_id)
                ->update(['status_terdaftar' => 'pembayaran']);

            // 1. Buat data obat_final
            $obatFinal = ObatFinal::create([
                'rekam_medis_id' => $this->rekammedis_id,
                'totalracik' => $this->totalracik,
                'totalnonracik' => $this->totalnonracik,
                'embalase' => $this->embalase,
                'tuslah' => $this->tuslah,
            ]);

            // 2. Simpan data obat non racikan
            if (is_array($nonracik) && count($nonracik) > 0) {
                foreach ($nonracik as $item) {
                    ObatNonRacikanFinal::create([
                        'obat_final_id' => $obatFinal->id,
                        'produk_id' => $item['id'],
                        'jumlah_obat' => $item['jumlah'],
                        'satuan_obat' => $item['satuan'],
                        'harga_obat' => $item['harga_satuan'],
                        'total_obat' => $item['total'],
                        'dosis' => $item['dosis'],
                        'hari' => $item['hari'],
                        'aturan_pakai' => $item['aturan_pakai'],
                        'konfirmasi' => 'proses',
                    ]);
                }
            }

            // 3. Simpan data racikan dan bahan-bahannya
            if (is_array($racikan) && count($racikan) > 0) {
                foreach ($racikan as $racik) {
                    $racikanFinal = ObatRacikanFinal::create([
                        'obat_final_id' => $obatFinal->id,
                        'nama_racikan' => $racik['nama_racikan'],
                        'jumlah_racikan' => $racik['jumlah_racikan'],
                        'satuan_racikan' => $racik['satuan_racikan'],
                        'total_racikan' => $racik['total_racikan'],
                        'dosis' => $racik['dosis'],
                        'hari' => $racik['hari'],
                        'aturan_pakai' => $racik['aturan_pakai'],
                        'konfirmasi' => 'proses',
                        'metode_racikan' => $racik['metode_racikan'] ?? null,
                    ]);

                    if (!empty($racik['bahan']) && is_array($racik['bahan'])) {
                        foreach ($racik['bahan'] as $bahan) {
                            BahanRacikanFinal::create([
                                'obat_racikan_final_id' => $racikanFinal->id,
                                'produk_id' => $bahan['id'],
                                'jumlah_obat' => $bahan['jumlah'],
                                'satuan_obat' => $bahan['satuan'],
                                'harga_obat' => $bahan['harga_satuan'],
                                'total_obat' => $bahan['total'],
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            $this->dispatch('toast',[
                'type' => 'success',
                'message' => 'Data Obat Berhasil Ditambahkan',
            ]);

            $this->dispatch('closeStoreModal');

            $this->reset();

            return redirect()->route('resep.data');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Gagal Menyimpan Data: ' . $e->getMessage()
            ]);
        }
    }


}