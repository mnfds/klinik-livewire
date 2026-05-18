<?php

namespace App\Livewire\Rekammedis;

use Livewire\Component;
use App\Models\Icd;
use App\Models\IcdRM;
use App\Models\KfaObat;
use App\Models\Bundling;
use App\Models\Pelayanan;
use App\Models\Treatment;
use App\Models\DiagnosaRM;
use App\Models\KajianAwal;
use App\Models\RekamMedis;
use Illuminate\Support\Str;
use App\Models\TandaVitalRM;
use App\Models\ObatRacikanRM;
use App\Models\ProdukDanObat;
use App\Models\DataEstetikaRM;
use Illuminate\Support\Carbon;
use App\Models\DataKesehatanRM;
use App\Models\PasienTerdaftar;
use App\Models\RencanaProdukRM;
use App\Models\ObatNonRacikanRM;
use App\Models\RencanaLayananRM;
use App\Services\StoreCondition;
use App\Services\StoreObatRacik;
use App\Services\StoreVitalSign;
use App\Services\StoreAlergiObat;
use App\Models\PemeriksaanFisikRM;
use App\Models\PemeriksaanKulitRM;
use App\Models\RencanaTreatmentRM;
use Illuminate\Support\Facades\DB;
use Livewire\Volt\Compilers\Mount;
use App\Models\PelayananBundlingRM;
use App\Models\ProdukBundlingUsage;
use App\Models\RencananaBundlingRM;
use App\Models\TreatmentBundlingRM;
use App\Services\StoreKeluhanUtama;
use App\Services\StoreObatNonRacik;
use Illuminate\Support\Facades\Log;
use App\Models\ProdukObatBundlingRM;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Services\StoreObatDikonsumsi;
use App\Models\PelayananBundlingUsage;
use App\Models\TreatmentBundlingUsage;
use App\Services\StoreRiwayatPenyakit;
use App\Services\StoreKonselingService;
use App\Services\StorePemeriksaanFisik;
use App\Services\StoreTingkatKesadaran;
use App\Services\PutInProgressEncounter;
use App\Services\StoreIntruksiObatRacik;
use App\Services\StoreKonselingProcedure;
use App\Services\StoreIntruksiObatNonRacik;
use Illuminate\Validation\ValidationException;
use App\View\Components\rekammedis\rencanalayanan;

class Keep extends Component
{    
    // DATA YANG AKAN DI STORE PADA RekamMedis::model //
    public $rekammedis;
    public $nama_dokter;
    public $keluhan_utama;
    public $tingkat_kesadaran;

    public ?int $pasien_terdaftar_id = null;
    public ?PasienTerdaftar $pasienTerdaftar = null;
    public $kajian;
    public ?int $rekam_medis_id = null;
    public $rekamMedisLama; // simpan instance RM lama

    public $encounter_id;
    
    // berisikan data bundling beserta isinya yang pernah dibeli oleh pasien
    public $bundlingPasien = [
        'treatments' => [],
        'pelayanans' => [],
        'produks' => [],
    ];

    // berisikan data yang akan menampung penggunaan layanan tersisa
    public $layananTerpilih = []; 

    public ?int $pasien_id = null;
    
    // berisikan data yang akan dimunculkan pada select layanan/tindakan
    public $layanan;
    public $bundling;
    public $treatment;
    public $skincare;

    // FORM DATA YANG PILIH //
    public array $selected_forms_subjective = [];
    public array $selected_forms_objective = [];
    public array $selected_forms_assessment = [];
    public array $selected_forms_plan = [];

    //SUBJECTIVE
        public $data_kesehatan = [
            'status_perokok' => null,
            'riwayat_penyakit' => null,
            'riwayat_alergi_obat' => null,
            'obat_sedang_dikonsumsi' => null,
            'riwayat_alergi_lainnya' => null,
        ];

        public $data_estetika = [
            'problem_dihadapi' => [],
            'lama_problem' => null,
            'tindakan_sebelumnya' => [],
            'penyakit_dialami' => null,
            'alergi_kosmetik' => null,
            'sedang_hamil' => null,
            'usia_kehamilan' => null,
            'metode_kb' => null,
            'pengobatan_saat_ini' => null,
            'produk_kosmetik' => null,
        ];
    //SUBJECTIVE
    
    //OBJECTIVE
        public $pemeriksaan_fisik = [
            'tinggi_badan' => null,
            'berat_badan' => null,
            'imt' => null,
        ];
        public $pemeriksaan_estetika = [
            'warna_kulit' => null,
            'ketebalan_kulit' => null,
            'kadar_minyak' => null,
            'kerapuhan_kulit' => null,
            'kekencangan_kulit' => null,
            'melasma' => null,
            'acne' => [],
            'lesions' => [],
        ];
        public $tanda_vital = [
            'suhu_tubuh' => null,
            'nadi' => null,
            'sistole' => null,
            'diastole' => null,
            'frekuensi_pernapasan' => null,
        ];
    //OBJECTIVE

    //ASSESSMENT
        public $diagnosa;
        public $icd10 = [];
    //ASSESSMENT

    //PLAN
        public $rencana_layanan = [
            'pelayanan_id' => [],
            'jumlah_pelayanan' => [],
        ];
        public $rencanaLayananLabels = [];

        public $rencana_estetika = [
            'treatments_id' => [],
            'jumlah_treatment' => [],
            'potongan' => [],
            'diskon' => [],
            'subtotal' => [],
        ];
        public $rencanaEstetikaLabels = [];

        public $obat_estetika = [
            'produk_id' => [],
            'jumlah_produk' => [],
            'potongan' => [],
            'diskon' => [],
            'subtotal' => [],
        ];
        public $obatEstetikaLabels = [];

        public $rencana_bundling = [
            'bundling_id' => [],
            'jumlah_bundling' => [],
            'potongan' => [],
            'diskon' => [],
            'subtotal' => [],
            'details' => [
                'treatments' => [],
                'pelayanans' => [],
                'produks' => [],
            ]
        ];
        public $rencanaBundlingLabels = [];

        public $layanandanbundling = [
            'layanan' => [],
            'treatment' => [],
            'bundling' => [],
            'skincare' => [],
        ];

        public $obat_non_racikan = [
            'nama_obat_non_racikan' => [],
            'jumlah_obat_non_racikan'=> [],
            'satuan_obat_non_racikan'=> [],
            'dosis_obat_non_racikan'=> [],
            'hari_obat_non_racikan'=> [],
            'aturan_pakai_obat_non_racikan'=> [],
        ];
        public $obatNonRacikLabels = [];

        public $racikanItems = [
            [
                'nama_racikan' => '',
                'jumlah_racikan' => '',
                'satuan_racikan' => '',
                'dosis_obat_racikan' => '',
                'hari_obat_racikan' => '',
                'aturan_pakai_racikan' => '',
                'metode_racikan' => '',
                'bahan' => [
                    [
                        'nama_obat_racikan' => '',
                        'jumlah_obat_racikan' => '',
                        'satuan_obat_racikan' => '',
                    ]
                ],
            ],
        ];

    //PLAN

    public function tambahLayananBundling($id, $tipe, $nama, $sisa, $bundlingName, $group_bundling_lama)
    {
        // pastikan bundling sudah ada di array
        if (!isset($this->layananTerpilih[$bundlingName])) {
            $this->layananTerpilih[$bundlingName] = [];
        }

        // cari apakah item ini sudah dipilih sebelumnya
        $existingIndex = collect($this->layananTerpilih[$bundlingName])
            ->search(fn($item) => $item['id'] == $id && $item['tipe'] == $tipe);

        if ($existingIndex !== false) {
            // sudah ada → tambahkan jumlah dipakai (tapi tidak boleh melebihi sisa)
            $current = $this->layananTerpilih[$bundlingName][$existingIndex]['dipakai'];
            $new = min($current + 1, $sisa);

            $this->layananTerpilih[$bundlingName][$existingIndex]['dipakai'] = $new;
        } else {
            // belum ada → tambahkan baru dengan dipakai = 1
            $this->layananTerpilih[$bundlingName][] = [
                'id' => $id,
                'tipe' => $tipe,
                'nama' => $nama,
                'sisa' => $sisa,
                'dipakai' => 1,
                'group_bundling_lama' => $group_bundling_lama,
            ];
        }
    }

    public function hapusLayanan($bundlingName, $index)
    {
        if (isset($this->layananTerpilih[$bundlingName][$index])) {
            unset($this->layananTerpilih[$bundlingName][$index]);
        }

        // Re-index agar array tetap rapi (opsional tapi sering membantu)
        if (isset($this->layananTerpilih[$bundlingName])) {
            $this->layananTerpilih[$bundlingName] = array_values($this->layananTerpilih[$bundlingName]);
        }

        // Hapus grup jika kosong
        if (empty($this->layananTerpilih[$bundlingName])) {
            unset($this->layananTerpilih[$bundlingName]);
        }
    }

    public function tambahLayanan($bundlingName, $index)
    {
        $item = &$this->layananTerpilih[$bundlingName][$index];
        if ($item['dipakai'] < $item['sisa']) {
            $item['dipakai']++;
        }
    }

    public function kurangiLayanan($bundlingName, $index)
    {
        $item = &$this->layananTerpilih[$bundlingName][$index];
        if ($item['dipakai'] > 1) {
            $item['dipakai']--;
        }
    }

    /**
     * Sanitasi setiap kali properti layananTerpilih berubah (Livewire hook).
     * key contoh: layananTerpilih.Paket%20Whitening.0.dipakai
     */
    public function updatedLayananTerpilih($value, $key)
    {
        // Pastikan kita sedang update field dipakai
        if (Str::endsWith($key, '.dipakai')) {
            // parse key menjadi parts
            $parts = explode('.', $key); // ["layananTerpilih", "<bundlingName>", "<index>", "dipakai"]
            if (count($parts) >= 4) {
                $bundlingName = $parts[1];
                $index = (int) $parts[2];

                if (!isset($this->layananTerpilih[$bundlingName][$index])) return;

                // normalize angka
                $dipakai = (int) $this->layananTerpilih[$bundlingName][$index]['dipakai'];
                $sisa = (int) $this->layananTerpilih[$bundlingName][$index]['sisa'];

                if ($dipakai < 1) $dipakai = 1;
                if ($dipakai > $sisa) $dipakai = $sisa;

                $this->layananTerpilih[$bundlingName][$index]['dipakai'] = $dipakai;
            }
        }
    }
    
    public function mount($rekam_medis_id = null)
    {
        $this->rekam_medis_id = $rekam_medis_id;

        $rm = RekamMedis::with([
            'pasienTerdaftar.pasien',
            'pasienTerdaftar.dokter',
            'pasienTerdaftar.poliklinik',
            'dataKesehatanRM',
            'dataEstetikaRM',
            'tandaVitalRM',
            'pemeriksaanFisikRM',
            'pemeriksaanKulitRM',
            'diagnosaRM',
            'icdRM',
            'rencanaLayananRM.pelayanan',
            'rencanaTreatmentRM.treatment',
            'rencanaBundlingRM.bundling',
            'obatNonRacikanRM',
            'obatRacikanRM.bahanRacikan',
            'rencanaProdukRM.produk',
        ])->findOrFail($rekam_medis_id);

        $this->rekamMedisLama = $rm;
        $this->pasien_terdaftar_id = $rm->pasien_terdaftar_id;
        $this->pasienTerdaftar = $rm->pasienTerdaftar;
        $this->pasien_id = $rm->pasienTerdaftar->pasien_id;
        $this->nama_dokter = $rm->nama_dokter;
        $this->keluhan_utama = $rm->keluhan_utama;
        $this->tingkat_kesadaran = $rm->tingkat_kesadaran;

        // Load layanan/bundling untuk dropdown (sama seperti component lama)
        $this->layanan = Pelayanan::all();
        $this->bundling = Bundling::with([
            'treatmentBundlings.treatment',
            'pelayananBundlings.pelayanan',
            'produkObatBundlings.produk',
        ])->get();
        $this->treatment = Treatment::all();
        $this->skincare = ProdukDanObat::all();
        $this->layanandanbundling = [
            'layanan'   => $this->layanan,
            'bundling'  => $this->bundling,
            'treatment' => $this->treatment,
            'skincare'  => $this->skincare,
        ];

        // Bundling pasien
        if ($this->pasien_id) {
            $this->bundlingPasien['treatments'] = TreatmentBundlingRM::with('bundling', 'treatment')
                ->where('pasien_id', $this->pasien_id)->get();
            $this->bundlingPasien['pelayanans'] = PelayananBundlingRM::with('bundling', 'pelayanan')
                ->where('pasien_id', $this->pasien_id)->get();
            $this->bundlingPasien['produks'] = ProdukObatBundlingRM::with('bundling', 'produk')
                ->where('pasien_id', $this->pasien_id)->get();
        }

        // Ambil usage is_final=false dari rekam medis LAIN milik pasien ini
        $treatmentKeepLain = \App\Models\TreatmentBundlingUsage::where('pasien_id', $this->pasien_id)
            ->where('rekam_medis_id', '!=', $rm->id)
            ->where('is_pembelian_baru', false)
            ->where('is_final', false)
            ->get()
            ->groupBy(fn($u) => $u->treatments_id . '_' . $u->group_bundling);

        $pelayananKeepLain = \App\Models\PelayananBundlingUsage::where('pasien_id', $this->pasien_id)
            ->where('rekam_medis_id', '!=', $rm->id)
            ->where('is_pembelian_baru', false)
            ->where('is_final', false)
            ->get()
            ->groupBy(fn($u) => $u->pelayanan_id . '_' . $u->group_bundling);

        $produkKeepLain = \App\Models\ProdukBundlingUsage::where('pasien_id', $this->pasien_id)
            ->where('rekam_medis_id', '!=', $rm->id)
            ->where('is_pembelian_baru', false)
            ->where('is_final', false)
            ->get()
            ->groupBy(fn($u) => $u->produk_obat_id . '_' . $u->group_bundling);

        $treatmentUsages = \App\Models\TreatmentBundlingUsage::where('rekam_medis_id', $rm->id)
            ->where('is_pembelian_baru', false) // hanya yang dipakai, bukan pembelian baru
            ->with('bundling', 'treatment')
            ->get();

        $pelayananUsages = \App\Models\PelayananBundlingUsage::where('rekam_medis_id', $rm->id)
            ->where('is_pembelian_baru', false)
            ->with('bundling', 'pelayanan')
            ->get();

        $produkUsages = \App\Models\ProdukBundlingUsage::where('rekam_medis_id', $rm->id)
            ->where('is_pembelian_baru', false)
            ->with('bundling', 'produk')
            ->get();

        // Rebuild layananTerpilih dari usage
        foreach ($treatmentUsages as $usage) {
            $bundlingName = $usage->bundling?->nama ?? 'Unknown';
            $record = \App\Models\TreatmentBundlingRM::where('pasien_id', $this->pasien_id)
                ->where('treatments_id', $usage->treatments_id)
                ->where('group_bundling', $usage->group_bundling)
                ->first();

            if (!$record) continue;

            $sisa = $usage->is_final
                ? $record->jumlah_awal - ($record->jumlah_terpakai - $usage->jumlah_dipakai)
                : $record->jumlah_awal - $record->jumlah_terpakai;

            $key = $usage->treatments_id . '_' . $usage->group_bundling;
            $dipegangLain = $treatmentKeepLain->get($key)?->sum('jumlah_dipakai') ?? 0;
            $sisa = max(0, $sisa - $dipegangLain);

            if (!isset($this->layananTerpilih[$bundlingName])) {
                $this->layananTerpilih[$bundlingName] = [];
            }
            $this->layananTerpilih[$bundlingName][] = [
                'id'                  => $record->id,
                'tipe'                => 'treatment',
                'nama'                => $usage->treatment?->nama_treatment ?? '',
                'sisa'                => $sisa,
                'dipakai'             => $usage->jumlah_dipakai,
                'group_bundling_lama' => $usage->group_bundling,
            ];
        }
        foreach ($pelayananUsages as $usage) {
            $bundlingName = $usage->bundling?->nama ?? 'Unknown';
            $record = \App\Models\PelayananBundlingRM::where('pasien_id', $this->pasien_id)
                ->where('pelayanan_id', $usage->pelayanan_id)
                ->where('group_bundling', $usage->group_bundling)
                ->first();

            if (!$record) continue;

            $sisa = $usage->is_final
                ? $record->jumlah_awal - ($record->jumlah_terpakai - $usage->jumlah_dipakai)
                : $record->jumlah_awal - $record->jumlah_terpakai;

            $key = $usage->pelayanan_id . '_' . $usage->group_bundling;
            $dipegangLain = $pelayananKeepLain->get($key)?->sum('jumlah_dipakai') ?? 0;
            $sisa = max(0, $sisa - $dipegangLain);

            if (!isset($this->layananTerpilih[$bundlingName])) {
                $this->layananTerpilih[$bundlingName] = [];
            }
            $this->layananTerpilih[$bundlingName][] = [
                'id'                  => $record->id,
                'tipe'                => 'pelayanan',
                'nama'                => $usage->pelayanan?->nama ?? '',
                'sisa'                => $sisa,
                'dipakai'             => $usage->jumlah_dipakai,
                'group_bundling_lama' => $usage->group_bundling,
            ];
        }
        foreach ($produkUsages as $usage) {
            $bundlingName = $usage->bundling?->nama ?? 'Unknown';
            $record = \App\Models\ProdukObatBundlingRM::where('pasien_id', $this->pasien_id)
                ->where('produk_obat_id', $usage->produk_obat_id)
                ->where('group_bundling', $usage->group_bundling)
                ->first();

            if (!$record) continue;

            $sisa = $usage->is_final
                ? $record->jumlah_awal - ($record->jumlah_terpakai - $usage->jumlah_dipakai)
                : $record->jumlah_awal - $record->jumlah_terpakai;

            $key = $usage->produk_obat_id . '_' . $usage->group_bundling;
            $dipegangLain = $produkKeepLain->get($key)?->sum('jumlah_dipakai') ?? 0;
            $sisa = max(0, $sisa - $dipegangLain);

            if (!isset($this->layananTerpilih[$bundlingName])) {
                $this->layananTerpilih[$bundlingName] = [];
            }
            $this->layananTerpilih[$bundlingName][] = [
                'id'                  => $record->id,
                'tipe'                => 'produk',
                'nama'                => $usage->produk?->nama_dagang ?? '',
                'sisa'                => $sisa,
                'dipakai'             => $usage->jumlah_dipakai,
                'group_bundling_lama' => $usage->group_bundling,
            ];
        }
        // ── LOAD DATA KE FORM ──

        // Subjective: Data Kesehatan
        if ($rm->dataKesehatanRM) {
            $this->selected_forms_subjective[] = 'data-kesehatan';
            $dk = $rm->dataKesehatanRM;
            $this->data_kesehatan = [
                'status_perokok'         => $dk->status_perokok,
                'riwayat_penyakit'       => json_decode($dk->riwayat_penyakit ?? '[]', true),
                'riwayat_alergi_obat'    => json_decode($dk->riwayat_alergi_obat ?? '[]', true),
                'riwayat_alergi_lainnya' => json_decode($dk->riwayat_alergi_lainnya ?? '[]', true),
                'obat_sedang_dikonsumsi' => json_decode($dk->obat_sedang_dikonsumsi ?? '[]', true),
            ];
        }

        // Subjective: Data Estetika
        if ($rm->dataEstetikaRM) {
            $this->selected_forms_subjective[] = 'data-estetika';
            $de = $rm->dataEstetikaRM;
            $this->data_estetika = [
                'problem_dihadapi'    => json_decode($de->problem_dihadapi ?? '[]', true),
                'lama_problem'        => $de->lama_problem,
                'tindakan_sebelumnya' => json_decode($de->tindakan_sebelumnya ?? '[]', true),
                'penyakit_dialami'    => $de->penyakit_dialami,
                'alergi_kosmetik'     => $de->alergi_kosmetik,
                'sedang_hamil'        => $de->sedang_hamil,
                'usia_kehamilan'      => $de->usia_kehamilan,
                'metode_kb'           => json_decode($de->metode_kb ?? '[]', true),
                'pengobatan_saat_ini' => $de->pengobatan_saat_ini,
                'produk_kosmetik'     => $de->produk_kosmetik,
            ];
        }

        // Objective: Tanda Vital
        if ($rm->tandaVitalRM) {
            $this->selected_forms_objective[] = 'tanda-vital';
            $tv = $rm->tandaVitalRM;
            $this->tanda_vital = [
                'suhu_tubuh'           => $tv->suhu_tubuh,
                'nadi'                 => $tv->nadi,
                'sistole'              => $tv->sistole,
                'diastole'             => $tv->diastole,
                'frekuensi_pernapasan' => $tv->frekuensi_pernapasan,
            ];
        }

        // Objective: Pemeriksaan Fisik
        if ($rm->pemeriksaanFisikRM) {
            $this->selected_forms_objective[] = 'pemeriksaan-fisik';
            $pf = $rm->pemeriksaanFisikRM;
            $this->pemeriksaan_fisik = [
                'tinggi_badan' => $pf->tinggi_badan,
                'berat_badan'  => $pf->berat_badan,
                'imt'          => $pf->imt,
            ];
        }

        // Objective: Pemeriksaan Estetika/Kulit
        if ($rm->pemeriksaanKulitRM) {
            $this->selected_forms_objective[] = 'pemeriksaan-estetika';
            $pk = $rm->pemeriksaanKulitRM;
            $this->pemeriksaan_estetika = [
                'warna_kulit'       => $pk->warna_kulit,
                'ketebalan_kulit'   => $pk->ketebalan_kulit,
                'kadar_minyak'      => $pk->kadar_minyak,
                'kerapuhan_kulit'   => $pk->kerapuhan_kulit,
                'kekencangan_kulit' => $pk->kekencangan_kulit,
                'melasma'           => $pk->melasma,
                'acne'              => json_decode($pk->acne ?? '[]', true),
                'lesions'           => json_decode($pk->lesions ?? '[]', true),
            ];
        }

        // Assessment: ICD10
        if ($rm->icdRM->isNotEmpty()) {
            $this->icd10 = $rm->icdRM->map(fn($i) => [
                'code'    => $i->code,
                'name_id' => $i->name_id,
                'name_en' => $i->name_en,
            ])->toArray();
        }

        // Assessment: Diagnosa
        if ($rm->diagnosaRM) {
            $this->diagnosa = $rm->diagnosaRM->diagnosa;
        }

        // Plan: Rencana Layanan
        if ($rm->rencanaLayananRM->isNotEmpty()) {
            $this->selected_forms_plan[] = 'rencana-layanan';
            $this->rencana_layanan = [
                'pelayanan_id'     => $rm->rencanaLayananRM->pluck('pelayanan_id')->toArray(),
                'jumlah_pelayanan' => $rm->rencanaLayananRM->pluck('jumlah_pelayanan')->toArray(),
            ];
            $this->rencanaLayananLabels = $rm->rencanaLayananRM
            ->map(fn($r) => $r->pelayanan?->nama_pelayanan ?? '')
            ->toArray();
        }

        // Plan: Rencana Treatment Estetika
        if ($rm->rencanaTreatmentRM->isNotEmpty()) {
            $this->selected_forms_plan[] = 'rencana-estetika';
            $this->rencana_estetika = [
                'treatments_id'    => $rm->rencanaTreatmentRM->pluck('treatments_id')->toArray(),
                'jumlah_treatment' => $rm->rencanaTreatmentRM->pluck('jumlah_treatment')->toArray(),
                'potongan'         => $rm->rencanaTreatmentRM->pluck('potongan')->toArray(),
                'diskon'           => $rm->rencanaTreatmentRM->pluck('diskon')->toArray(),
                'subtotal'         => $rm->rencanaTreatmentRM->pluck('subtotal')->toArray(),
            ];
            $this->rencanaEstetikaLabels = $rm->rencanaTreatmentRM
            ->map(fn($r) => $r->treatment?->nama_treatment ?? '')
            ->toArray();
            // dd($this->rencanaEstetikaLabels);
        }

        // Plan: Rencana Bundling
        if ($rm->rencanaBundlingRM->isNotEmpty()) {
            $this->selected_forms_plan[] = 'rencana-bundling';

            $detailTreatments = [];
            $detailPelayanans = [];
            $detailProduks    = [];

            foreach ($rm->rencanaBundlingRM as $i => $rbRM) {
                // Key = treatments_id (cocok dengan $tb['treatment']['id'] di blade)
                $detailTreatments[$i] = TreatmentBundlingRM::where('pasien_id', $this->pasien_id)
                    ->where('group_bundling', $rbRM->group_bundling)
                    ->get()
                    ->mapWithKeys(fn($t) => [
                        (string) $t->treatments_id => [
                            'treatments_id'    => $t->treatments_id,
                            'jumlah_per_bundle' => $rbRM->jumlah_bundling > 0
                                ? $t->jumlah_awal / $rbRM->jumlah_bundling
                                : 0,
                            'jumlah_awal'      => $t->jumlah_awal,
                            'jumlah_terpakai'  => $t->jumlah_terpakai,
                        ]
                    ])->toArray();

                // Key = pelayanan_id (cocok dengan $pb['pelayanan']['id'] di blade)
                $detailPelayanans[$i] = PelayananBundlingRM::where('pasien_id', $this->pasien_id)
                    ->where('group_bundling', $rbRM->group_bundling)
                    ->get()
                    ->mapWithKeys(fn($p) => [
                        (string) $p->pelayanan_id => [
                            'pelayanan_id'     => $p->pelayanan_id,
                            'jumlah_per_bundle' => $rbRM->jumlah_bundling > 0
                                ? $p->jumlah_awal / $rbRM->jumlah_bundling
                                : 0,
                            'jumlah_awal'      => $p->jumlah_awal,
                            'jumlah_terpakai'  => $p->jumlah_terpakai,
                        ]
                    ])->toArray();

                // Key = produk_obat_id (cocok dengan $prb['produk']['id'] di blade)
                $detailProduks[$i] = ProdukObatBundlingRM::where('pasien_id', $this->pasien_id)
                    ->where('group_bundling', $rbRM->group_bundling)
                    ->get()
                    ->mapWithKeys(fn($p) => [
                        (string) $p->produk_obat_id => [
                            'produk_obat_id'   => $p->produk_obat_id,
                            'jumlah_per_bundle' => $rbRM->jumlah_bundling > 0
                                ? $p->jumlah_awal / $rbRM->jumlah_bundling
                                : 0,
                            'jumlah_awal'      => $p->jumlah_awal,
                            'jumlah_terpakai'  => $p->jumlah_terpakai,
                        ]
                    ])->toArray();
            }

            $this->rencana_bundling = [
                'bundling_id'    => $rm->rencanaBundlingRM->pluck('bundling_id')->toArray(),
                'jumlah_bundling'=> $rm->rencanaBundlingRM->pluck('jumlah_bundling')->toArray(),
                'potongan'       => $rm->rencanaBundlingRM->pluck('potongan')->toArray(),
                'diskon'         => $rm->rencanaBundlingRM->pluck('diskon')->toArray(),
                'subtotal'       => $rm->rencanaBundlingRM->pluck('subtotal')->toArray(),
                'details'        => [
                    'treatments' => $detailTreatments,
                    'pelayanans' => $detailPelayanans,
                    'produks'    => $detailProduks,
                ],
            ];

            $this->rencanaBundlingLabels = $rm->rencanaBundlingRM
                ->map(fn($r) => $r->bundling?->nama ?? '')
                ->toArray();
        }

        // Plan: Produk Estetika
        if ($rm->rencanaProdukRM->isNotEmpty()) {
            $this->selected_forms_plan[] = 'obat-estetika';
            $this->obat_estetika = [
                'produk_id'     => $rm->rencanaProdukRM->pluck('produk_id')->toArray(),
                'jumlah_produk' => $rm->rencanaProdukRM->pluck('jumlah_produk')->toArray(),
                'potongan'      => $rm->rencanaProdukRM->pluck('potongan')->toArray(),
                'diskon'        => $rm->rencanaProdukRM->pluck('diskon')->toArray(),
                'subtotal'      => $rm->rencanaProdukRM->pluck('subtotal')->toArray(),
            ];
            $this->obatEstetikaLabels = $rm->rencanaProdukRM
            ->map(fn($r) => $r->produk?->nama_dagang ?? '')
            ->toArray();
        }

        // Plan: Obat Non Racikan
        if ($rm->obatNonRacikanRM->isNotEmpty()) {
            $this->selected_forms_plan[] = 'obat-non-racikan';
            $this->obat_non_racikan = [
                'nama_obat_non_racikan'         => $rm->obatNonRacikanRM->pluck('nama_obat_non_racikan')->toArray(),
                'jumlah_obat_non_racikan'       => $rm->obatNonRacikanRM->pluck('jumlah_obat_non_racikan')->toArray(),
                'satuan_obat_non_racikan'       => $rm->obatNonRacikanRM->pluck('satuan_obat_non_racikan')->toArray(),
                'dosis_obat_non_racikan'        => $rm->obatNonRacikanRM->pluck('dosis_obat_non_racikan')->toArray(),
                'hari_obat_non_racikan'         => $rm->obatNonRacikanRM->pluck('hari_obat_non_racikan')->toArray(),
                'aturan_pakai_obat_non_racikan' => $rm->obatNonRacikanRM->pluck('aturan_pakai_obat_non_racikan')->toArray(),
            ];
            $this->obatNonRacikLabels = $rm->obatNonRacikanRM
            ->map(fn($r) => $r->nama_obat_non_racikan ?? '')
            ->toArray();
        }

        // Plan: Obat Racikan
        if ($rm->obatRacikanRM->isNotEmpty()) {
            $this->selected_forms_plan[] = 'obat-racikan';
            $this->racikanItems = $rm->obatRacikanRM->map(fn($r) => [
                'nama_racikan'         => $r->nama_racikan,
                'jumlah_racikan'       => $r->jumlah_racikan,
                'satuan_racikan'       => $r->satuan_racikan,
                'dosis_obat_racikan'   => $r->dosis_obat_racikan,
                'hari_obat_racikan'    => $r->hari_obat_racikan,
                'aturan_pakai_racikan' => $r->aturan_pakai_racikan,
                'metode_racikan'       => $r->metode_racikan,
                'bahan'                => $r->bahanRacikan->map(fn($b) => [
                    'nama_obat_racikan'   => $b->nama_obat_racikan,
                    'jumlah_obat_racikan' => $b->jumlah_obat_racikan,
                    'satuan_obat_racikan' => $b->satuan_obat_racikan,
                ])->toArray(),
            ])->toArray();
        }
    }

    public function create()
    {
        $this->updateData(keepStatus: false);
    }

    public function createAndKeep()
    {
        $this->updateData(keepStatus: true);
    }

    public function updateData(bool $keepStatus = false)
    {
        $rules = [
            'nama_dokter'         => 'required|string|max:255',
            'pasien_terdaftar_id' => 'required|exists:pasien_terdaftars,id',
            'keluhan_utama'       => 'required|string',
            'tingkat_kesadaran'   => 'required|string',
        ];
        if (collect($this->icd10)->flatten(1)->count() === 0) {
            $rules['icd10'] = 'required';
        }
        if (in_array('pemeriksaan-fisik', $this->selected_forms_objective)) {
            $rules['pemeriksaan_fisik.tinggi_badan'] = 'required';
            $rules['pemeriksaan_fisik.berat_badan']  = 'required';
        }
        $this->validate($rules);

        DB::beginTransaction();
        try {
            $rm = RekamMedis::findOrFail($this->rekam_medis_id);

            // ── UPDATE REKAM MEDIS UTAMA ──
            $rm->update([
                'nama_dokter'      => $this->nama_dokter,
                'keluhan_utama'    => $this->keluhan_utama,
                'tingkat_kesadaran'=> $this->tingkat_kesadaran,
            ]);

            $pt = PasienTerdaftar::with(['pasien', 'dokter'])->find($this->pasien_terdaftar_id);
            $waktu_diperiksa = $pt->waktu_diperiksa ?? Carbon::now('Asia/Makassar')->setTimezone('UTC')->toIso8601String();

            // ── STATUS ──
            if ($keepStatus) {
                $status = 'keep';
            } else {
                $status = 'pembayaran';
                if (in_array('obat-non-racikan', $this->selected_forms_plan)) {
                    $status = 'peresepan';
                }
                if (in_array('obat-racikan', $this->selected_forms_plan)) {
                    $status = 'peresepan';
                }
                if (in_array('rencana-bundling', $this->selected_forms_plan)) {
                    $adaObat = \App\Models\ProdukBundlingUsage::where('rekam_medis_id', $this->rekamMedisLama->id)
                                ->with('produk')
                                ->get();

                    $golonganObat = [
                        'Obat Bebas', 'Obat Bebas Terbatas', 'Obat Keras',
                        'Obat Narkotika', 'Obat Psikotropika',
                        'Obat fitofarmaka', 'OHT (Obat Herbal Terstandar)',
                        'Jamu', 'Lain - Lain',
                    ];

                    $adaObatDenganGolongan = $adaObat->contains(function ($item) use ($golonganObat) {
                        return in_array($item->produk?->golongan, $golonganObat);
                    });

                    if ($adaObatDenganGolongan) {
                        $status = 'peresepan';
                    }
                }
                if (!empty($this->layananTerpilih)) {
                    $golonganObat = [
                        'Obat Bebas', 'Obat Bebas Terbatas', 'Obat Keras',
                        'Obat Narkotika', 'Obat Psikotropika', 'Obat fitofarmaka',
                        'OHT (Obat Herbal Terstandar)', 'Jamu', 'Lain - Lain',
                    ];
                    $pasien_Id = $this->pasien_id ?? null;

                    $adaObatSisa = collect($this->layananTerpilih)
                        ->flatten(1)
                        ->filter(fn($item) => 
                            $item['tipe'] === 'produk' && 
                            (int)($item['dipakai'] ?? 0) > 0
                        )
                        ->contains(function ($item) use ($golonganObat, $pasien_Id) {
                            $record = \App\Models\ProdukObatBundlingRM::where('id', $item['id'])
                                ->where('pasien_id', $pasien_Id)
                                ->with('produk')
                                ->first();

                            return $record && in_array($record->produk?->golongan, $golonganObat);
                        });

                    if ($adaObatSisa) {
                        $status = 'peresepan';
                    }
                }
            }

            PasienTerdaftar::findOrFail($this->pasien_terdaftar_id)->update([
                'status_terdaftar' => $status,
                'waktu_diperiksa'  => $waktu_diperiksa,
            ]);

            // ── SUBJECTIVE: updateOrCreate pakai rekam_medis_id sebagai key ──
            if (in_array('data-kesehatan', $this->selected_forms_subjective)) {
                DataKesehatanRM::updateOrCreate(
                    ['rekam_medis_id' => $rm->id],
                    [
                        'status_perokok'         => $this->data_kesehatan['status_perokok'],
                        'riwayat_penyakit'       => json_encode($this->data_kesehatan['riwayat_penyakit']),
                        'riwayat_alergi_obat'    => json_encode($this->data_kesehatan['riwayat_alergi_obat']),
                        'riwayat_alergi_lainnya' => json_encode($this->data_kesehatan['riwayat_alergi_lainnya']),
                        'obat_sedang_dikonsumsi' => json_encode($this->data_kesehatan['obat_sedang_dikonsumsi']),
                    ]
                );
            }

            if (in_array('data-estetika', $this->selected_forms_subjective)) {
                // dd([$rm->id,$this->data_estetika['sedang_hamil']]);
                DataEstetikaRM::where('rekam_medis_id', $rm->id)->updateOrInsert(
                    ['rekam_medis_id' => $rm->id],
                    [
                        'problem_dihadapi'    => json_encode($this->data_estetika['problem_dihadapi']),
                        'lama_problem'        => $this->data_estetika['lama_problem'],
                        'tindakan_sebelumnya' => json_encode($this->data_estetika['tindakan_sebelumnya']),
                        'penyakit_dialami'    => $this->data_estetika['penyakit_dialami'],
                        'alergi_kosmetik'     => $this->data_estetika['alergi_kosmetik'],
                        'sedang_hamil'        => $this->data_estetika['sedang_hamil'],
                        'usia_kehamilan' => $this->data_estetika['sedang_hamil'] === 'tidak' ? null : $this->data_estetika['usia_kehamilan'],
                        'metode_kb'           => json_encode($this->data_estetika['metode_kb']),
                        'pengobatan_saat_ini' => $this->data_estetika['pengobatan_saat_ini'],
                        'produk_kosmetik'     => $this->data_estetika['produk_kosmetik'],
                    ]
                );
            }

            // ── OBJECTIVE ──
            if (in_array('tanda-vital', $this->selected_forms_objective)) {
                TandaVitalRM::updateOrCreate(
                    ['rekam_medis_id' => $rm->id],
                    [
                        'suhu_tubuh'           => $this->tanda_vital['suhu_tubuh'],
                        'nadi'                 => $this->tanda_vital['nadi'],
                        'sistole'              => $this->tanda_vital['sistole'],
                        'diastole'             => $this->tanda_vital['diastole'],
                        'frekuensi_pernapasan' => $this->tanda_vital['frekuensi_pernapasan'],
                    ]
                );
            }

            if (in_array('pemeriksaan-fisik', $this->selected_forms_objective)) {
                PemeriksaanFisikRM::updateOrCreate(
                    ['rekam_medis_id' => $rm->id],
                    [
                        'tinggi_badan' => $this->pemeriksaan_fisik['tinggi_badan'],
                        'berat_badan'  => $this->pemeriksaan_fisik['berat_badan'],
                        'imt'          => $this->pemeriksaan_fisik['imt'],
                    ]
                );
            }

            if (in_array('pemeriksaan-estetika', $this->selected_forms_objective)) {
                PemeriksaanKulitRM::updateOrCreate(
                    ['rekam_medis_id' => $rm->id],
                    [
                        'warna_kulit'       => $this->pemeriksaan_estetika['warna_kulit'],
                        'ketebalan_kulit'   => $this->pemeriksaan_estetika['ketebalan_kulit'],
                        'kadar_minyak'      => $this->pemeriksaan_estetika['kadar_minyak'],
                        'kerapuhan_kulit'   => $this->pemeriksaan_estetika['kerapuhan_kulit'],
                        'kekencangan_kulit' => $this->pemeriksaan_estetika['kekencangan_kulit'],
                        'melasma'           => $this->pemeriksaan_estetika['melasma'],
                        'acne'              => json_encode($this->pemeriksaan_estetika['acne']),
                        'lesions'           => json_encode($this->pemeriksaan_estetika['lesions']),
                    ]
                );
            }

            // ── ASSESSMENT ──
            DiagnosaRM::updateOrCreate(
                ['rekam_medis_id' => $rm->id],
                ['diagnosa' => $this->diagnosa]
            );

            // ICD10: delete lama → insert ulang (lebih aman karena bisa banyak row)
            IcdRM::where('rekam_medis_id', $rm->id)->delete();
            foreach ($this->icd10 as $item) {
                if (empty($item['code'])) continue;
                IcdRM::create([
                    'rekam_medis_id' => $rm->id,
                    'code'           => $item['code'],
                    'name_id'        => $item['name_id'],
                    'name_en'        => $item['name_en'],
                ]);
            }

            // ── PLAN: delete lama → insert ulang (karena jumlah row bisa berubah) ──
            RencanaLayananRM::where('rekam_medis_id', $rm->id)->delete();
            if (in_array('rencana-layanan', $this->selected_forms_plan)) {
                foreach ($this->rencana_layanan['pelayanan_id'] as $index => $pelayananId) {
                    if (empty($pelayananId)) continue;
                    RencanaLayananRM::create([
                        'rekam_medis_id'   => $rm->id,
                        'pelayanan_id'     => $pelayananId,
                        'jumlah_pelayanan' => $this->rencana_layanan['jumlah_pelayanan'][$index],
                    ]);
                }
            }

            RencanaTreatmentRM::where('rekam_medis_id', $rm->id)->delete();
            if (in_array('rencana-estetika', $this->selected_forms_plan)) {
                foreach ($this->rencana_estetika['treatments_id'] as $index => $treatmentId) {
                    if (empty($treatmentId)) continue;
                    RencanaTreatmentRM::create([
                        'rekam_medis_id'   => $rm->id,
                        'treatments_id'    => $treatmentId,
                        'jumlah_treatment' => $this->rencana_estetika['jumlah_treatment'][$index] ?? 1,
                        'potongan'         => $this->rencana_estetika['potongan'][$index] ?? 0,
                        'diskon'           => $this->rencana_estetika['diskon'][$index] ?? 0,
                        'subtotal'         => $this->rencana_estetika['subtotal'][$index] ?? 0,
                    ]);
                }
            }

            RencanaProdukRM::where('rekam_medis_id', $rm->id)->delete();
            if (in_array('obat-estetika', $this->selected_forms_plan)) {
                foreach ($this->obat_estetika['produk_id'] as $index => $produkId) {
                    if (empty($produkId)) continue;
                    RencanaProdukRM::create([
                        'rekam_medis_id' => $rm->id,
                        'produk_id'      => $produkId,
                        'jumlah_produk'  => $this->obat_estetika['jumlah_produk'][$index] ?? 1,
                        'potongan'       => $this->obat_estetika['potongan'][$index] ?? 0,
                        'diskon'         => $this->obat_estetika['diskon'][$index] ?? 0,
                        'subtotal'       => $this->obat_estetika['subtotal'][$index] ?? 0,
                    ]);
                }
            }

            ObatNonRacikanRM::where('rekam_medis_id', $rm->id)->delete();
            if (in_array('obat-non-racikan', $this->selected_forms_plan)) {
                foreach ($this->obat_non_racikan['nama_obat_non_racikan'] as $index => $namaObat) {
                    if (empty($namaObat)) continue;
                    ObatNonRacikanRM::create([
                        'rekam_medis_id'                => $rm->id,
                        'nama_obat_non_racikan'         => $namaObat,
                        'jumlah_obat_non_racikan'       => $this->obat_non_racikan['jumlah_obat_non_racikan'][$index] ?? 1,
                        'satuan_obat_non_racikan'       => $this->obat_non_racikan['satuan_obat_non_racikan'][$index] ?? null,
                        'dosis_obat_non_racikan'        => $this->obat_non_racikan['dosis_obat_non_racikan'][$index] ?? null,
                        'hari_obat_non_racikan'         => $this->obat_non_racikan['hari_obat_non_racikan'][$index] ?? null,
                        'aturan_pakai_obat_non_racikan' => $this->obat_non_racikan['aturan_pakai_obat_non_racikan'][$index] ?? null,
                    ]);
                }
            }

            // Obat racikan: hapus bahan dulu lewat relasi, lalu racikan utama
            ObatRacikanRM::where('rekam_medis_id', $rm->id)->each(function ($r) {
                $r->bahanRacikan()->delete();
                $r->delete();
            });
            if (in_array('obat-racikan', $this->selected_forms_plan)) {
                foreach ($this->racikanItems as $racikan) {
                    $obatRacikan = ObatRacikanRM::create([
                        'rekam_medis_id'       => $rm->id,
                        'nama_racikan'         => $racikan['nama_racikan'] ?? null,
                        'jumlah_racikan'       => $racikan['jumlah_racikan'] ?? 1,
                        'satuan_racikan'       => $racikan['satuan_racikan'] ?? null,
                        'dosis_obat_racikan'   => $racikan['dosis_obat_racikan'] ?? null,
                        'hari_obat_racikan'    => $racikan['hari_obat_racikan'] ?? null,
                        'aturan_pakai_racikan' => $racikan['aturan_pakai_racikan'] ?? null,
                        'metode_racikan'       => $racikan['metode_racikan'] ?? null,
                    ]);
                    foreach ($racikan['bahan'] ?? [] as $bahan) {
                        $obatRacikan->bahanRacikan()->create([
                            'nama_obat_racikan'   => $bahan['nama_obat_racikan'] ?? null,
                            'jumlah_obat_racikan' => $bahan['jumlah_obat_racikan'] ?? 1,
                            'satuan_obat_racikan' => $bahan['satuan_obat_racikan'] ?? null,
                        ]);
                    }
                }
            }

            // RENCANA BUNDLING — hanya tambah bundling BARU
            if (in_array('rencana-bundling', $this->selected_forms_plan)) {

                // Bundling yang masih ada di form
                $currentBundlingIds = collect($this->rencana_bundling['bundling_id'])
                    ->filter(fn($id) => !empty($id))
                    ->values()
                    ->toArray();

                // Hapus bundling yang dihapus user dari form
                $existingBundlings = RencananaBundlingRM::where('rekam_medis_id', $rm->id)->get();
                foreach ($existingBundlings as $existing) {
                    if (!in_array((string) $existing->bundling_id, array_map('strval', $currentBundlingIds))) {
                        $groupBundling = $existing->group_bundling;
                        \App\Models\TreatmentBundlingUsage::where('group_bundling', $groupBundling)->delete();
                        \App\Models\PelayananBundlingUsage::where('group_bundling', $groupBundling)->delete();
                        \App\Models\ProdukBundlingUsage::where('group_bundling', $groupBundling)->delete();
                        TreatmentBundlingRM::where('group_bundling', $groupBundling)->delete();
                        PelayananBundlingRM::where('group_bundling', $groupBundling)->delete();
                        ProdukObatBundlingRM::where('group_bundling', $groupBundling)->delete();
                        $existing->delete();
                    }
                }

                // Refresh existing setelah delete
                $existingBundlingMap = RencananaBundlingRM::where('rekam_medis_id', $rm->id)
                    ->get()
                    ->keyBy('bundling_id'); // key by bundling_id untuk lookup cepat

                foreach ($this->rencana_bundling['bundling_id'] as $index => $bundlingId) {
                    if (empty($bundlingId)) continue;

                    if ($existingBundlingMap->has($bundlingId)) {
                        // ── BUNDLING SUDAH ADA → UPDATE jumlah_terpakai detail ──
                        $existingRM = $existingBundlingMap->get($bundlingId);
                        $groupBundling = $existingRM->group_bundling;

                        // Update header (jumlah, diskon, potongan, subtotal)
                        $existingRM->update([
                            'jumlah_bundling' => $this->rencana_bundling['jumlah_bundling'][$index] ?? 1,
                            'potongan'        => $this->rencana_bundling['potongan'][$index] ?? 0,
                            'diskon'          => $this->rencana_bundling['diskon'][$index] ?? 0,
                            'subtotal'        => $this->rencana_bundling['subtotal'][$index] ?? 0,
                        ]);

                        // Update jumlah_terpakai treatments
                        if (!empty($this->rencana_bundling['details']['treatments'][$index])) {
                            foreach ($this->rencana_bundling['details']['treatments'][$index] as $t) {
                                TreatmentBundlingRM::where('pasien_id', $this->pasien_id)
                                    ->where('group_bundling', $groupBundling)
                                    ->where('treatments_id', $t['treatments_id'])
                                    ->update([
                                        'jumlah_awal'     => $t['jumlah_awal'],
                                        'jumlah_terpakai' => $t['jumlah_terpakai'],
                                    ]);
                            }
                        }

                        // Update jumlah_terpakai pelayanans
                        if (!empty($this->rencana_bundling['details']['pelayanans'][$index])) {
                            foreach ($this->rencana_bundling['details']['pelayanans'][$index] as $t) {
                                PelayananBundlingRM::where('pasien_id', $this->pasien_id)
                                    ->where('group_bundling', $groupBundling)
                                    ->where('pelayanan_id', $t['pelayanan_id'])
                                    ->update([
                                        'jumlah_awal'     => $t['jumlah_awal'],
                                        'jumlah_terpakai' => $t['jumlah_terpakai'],
                                    ]);
                            }
                        }

                        // Update jumlah_terpakai produks
                        if (!empty($this->rencana_bundling['details']['produks'][$index])) {
                            foreach ($this->rencana_bundling['details']['produks'][$index] as $t) {
                                ProdukObatBundlingRM::where('pasien_id', $this->pasien_id)
                                    ->where('group_bundling', $groupBundling)
                                    ->where('produk_obat_id', $t['produk_obat_id'])
                                    ->update([
                                        'jumlah_awal'     => $t['jumlah_awal'],
                                        'jumlah_terpakai' => $t['jumlah_terpakai'],
                                    ]);
                            }
                        }

                    } else {
                        // ── BUNDLING BARU → INSERT ──
                        $group_bundling = 'GB-' . \Illuminate\Support\Str::random(4);

                        RencananaBundlingRM::create([
                            'rekam_medis_id'  => $rm->id,
                            'bundling_id'     => $bundlingId,
                            'jumlah_bundling' => $this->rencana_bundling['jumlah_bundling'][$index] ?? 1,
                            'potongan'        => $this->rencana_bundling['potongan'][$index] ?? 0,
                            'diskon'          => $this->rencana_bundling['diskon'][$index] ?? 0,
                            'subtotal'        => $this->rencana_bundling['subtotal'][$index] ?? 0,
                            'group_bundling'  => $group_bundling,
                        ]);

                        //insert detail treatments/pelayanans/produks
                        if (!empty($this->rencana_bundling['details']['treatments'][$index])) {
                            foreach ($this->rencana_bundling['details']['treatments'][$index] as $t) {
                                TreatmentBundlingRM::create([
                                    'pasien_id'       => $this->pasien_id,
                                    'bundling_id'     => $bundlingId,
                                    'treatments_id'   => $t['treatments_id'],
                                    'jumlah_awal'     => $t['jumlah_awal'],
                                    'jumlah_terpakai' => $t['jumlah_terpakai'],
                                    'group_bundling'  => $group_bundling,
                                ]);

                                if (!empty($t['jumlah_terpakai']) && $t['jumlah_terpakai'] > 0) {
                                    \App\Models\TreatmentBundlingUsage::create([
                                        'pasien_id'         => $this->pasien_id,
                                        'rekam_medis_id'    => $rm->id,
                                        'bundling_id'       => $bundlingId,
                                        'group_bundling'    => $group_bundling,
                                        'treatments_id'     => $t['treatments_id'],
                                        'jumlah_dipakai'    => $t['jumlah_terpakai'],
                                        'kategori'          => 'penggunaan_sisa',
                                        'is_pembelian_baru' => true,
                                        'is_final'          => false,
                                    ]);
                                }
                            }
                        }

                        // Detail pelayanans
                        if (!empty($this->rencana_bundling['details']['pelayanans'][$index])) {
                            foreach ($this->rencana_bundling['details']['pelayanans'][$index] as $t) {
                                PelayananBundlingRM::create([
                                    'pasien_id'       => $this->pasien_id,
                                    'bundling_id'     => $bundlingId,
                                    'pelayanan_id'    => $t['pelayanan_id'],
                                    'jumlah_awal'     => $t['jumlah_awal'],
                                    'jumlah_terpakai' => $t['jumlah_terpakai'],
                                    'group_bundling'  => $group_bundling,
                                ]);

                                if (!empty($t['jumlah_terpakai']) && $t['jumlah_terpakai'] > 0) {
                                    \App\Models\PelayananBundlingUsage::create([
                                        'pasien_id'         => $this->pasien_id,
                                        'rekam_medis_id'    => $rm->id,
                                        'bundling_id'       => $bundlingId,
                                        'group_bundling'    => $group_bundling,
                                        'pelayanan_id'      => $t['pelayanan_id'],
                                        'jumlah_dipakai'    => $t['jumlah_terpakai'],
                                        'kategori'          => 'penggunaan_sisa',
                                        'is_pembelian_baru' => true,
                                        'is_final'          => false,
                                    ]);
                                }
                            }
                        }

                        // Detail produks
                        if (!empty($this->rencana_bundling['details']['produks'][$index])) {
                            foreach ($this->rencana_bundling['details']['produks'][$index] as $t) {
                                ProdukObatBundlingRM::create([
                                    'pasien_id'       => $this->pasien_id,
                                    'bundling_id'     => $bundlingId,
                                    'group_bundling'  => $group_bundling,
                                    'produk_obat_id'  => $t['produk_obat_id'],
                                    'jumlah_awal'     => $t['jumlah_awal'],
                                    'jumlah_terpakai' => $t['jumlah_terpakai'],
                                ]);

                                if (!empty($t['jumlah_terpakai']) && $t['jumlah_terpakai'] > 0) {
                                    \App\Models\ProdukBundlingUsage::create([
                                        'pasien_id'         => $this->pasien_id,
                                        'rekam_medis_id'    => $rm->id,
                                        'bundling_id'       => $bundlingId,
                                        'group_bundling'    => $group_bundling,
                                        'produk_obat_id'    => $t['produk_obat_id'],
                                        'jumlah_dipakai'    => $t['jumlah_terpakai'],
                                        'kategori'          => 'penggunaan_sisa',
                                        'is_pembelian_baru' => true,
                                        'is_final'          => false,
                                    ]);
                                }
                            }
                        }
                    }
                }

                // Finalisasi semua usage is_final = false → true
                \App\Models\TreatmentBundlingUsage::where('rekam_medis_id', $rm->id)
                    ->where('is_final', false)
                    ->update(['is_final' => true]);

                \App\Models\PelayananBundlingUsage::where('rekam_medis_id', $rm->id)
                    ->where('is_final', false)
                    ->update(['is_final' => true]);

                \App\Models\ProdukBundlingUsage::where('rekam_medis_id', $rm->id)
                    ->where('is_final', false)
                    ->update(['is_final' => true]);
            }
            // LAYANAN TERPILIH (sisa bundling) — rollback dulu, lalu insert ulang
            if (!empty($this->layananTerpilih)) {

                // Rollback jumlah_terpakai HANYA dari usage yang sudah final
                \App\Models\TreatmentBundlingUsage::where('rekam_medis_id', $rm->id)
                    ->where('is_pembelian_baru', false)
                    ->where('is_final', true)
                    ->each(function ($usage) {
                        \App\Models\TreatmentBundlingRM::where('treatments_id', $usage->treatments_id)
                            ->where('group_bundling', $usage->group_bundling)
                            ->decrement('jumlah_terpakai', $usage->jumlah_dipakai);
                    });

                \App\Models\PelayananBundlingUsage::where('rekam_medis_id', $rm->id)
                    ->where('is_pembelian_baru', false)
                    ->where('is_final', true)
                    ->each(function ($usage) {
                        \App\Models\PelayananBundlingRM::where('pelayanan_id', $usage->pelayanan_id)
                            ->where('group_bundling', $usage->group_bundling)
                            ->decrement('jumlah_terpakai', $usage->jumlah_dipakai);
                    });

                \App\Models\ProdukBundlingUsage::where('rekam_medis_id', $rm->id)
                    ->where('is_pembelian_baru', false)
                    ->where('is_final', true)
                    ->each(function ($usage) {
                        \App\Models\ProdukObatBundlingRM::where('produk_obat_id', $usage->produk_obat_id)
                            ->where('group_bundling', $usage->group_bundling)
                            ->decrement('jumlah_terpakai', $usage->jumlah_dipakai);
                    });

                // Hapus semua usage lama
                \App\Models\TreatmentBundlingUsage::where('rekam_medis_id', $rm->id)
                    ->where('is_pembelian_baru', false)->delete();
                \App\Models\PelayananBundlingUsage::where('rekam_medis_id', $rm->id)
                    ->where('is_pembelian_baru', false)->delete();
                \App\Models\ProdukBundlingUsage::where('rekam_medis_id', $rm->id)
                    ->where('is_pembelian_baru', false)->delete();

                // Insert ulang
                foreach ($this->layananTerpilih as $namaBundling => $items) {
                    foreach ($items as $item) {
                        $jumlahDipakai = (int) ($item['dipakai'] ?? 0);
                        $group_bundling_lama = $item['group_bundling_lama'];
                        if ($jumlahDipakai <= 0) continue;

                        switch ($item['tipe']) {
                            case 'treatment':
                                $record = \App\Models\TreatmentBundlingRM::where('id', $item['id'])
                                    ->where('pasien_id', $this->pasien_id)->first();
                                break;
                            case 'produk':
                                $record = \App\Models\ProdukObatBundlingRM::where('id', $item['id'])
                                    ->where('pasien_id', $this->pasien_id)->first();
                                break;
                            case 'pelayanan':
                                $record = \App\Models\PelayananBundlingRM::where('id', $item['id'])
                                    ->where('pasien_id', $this->pasien_id)->first();
                                break;
                            default:
                                $record = null;
                        }

                        if (!$record) continue;

                        // Hanya update jumlah_terpakai jika final
                        if (!$keepStatus) {
                            $record->update([
                                'jumlah_terpakai' => min(
                                    $record->jumlah_terpakai + $jumlahDipakai,
                                    $record->jumlah_awal
                                )
                            ]);
                        }

                        $basePayload = [
                            'pasien_id'         => $this->pasien_id,
                            'rekam_medis_id'    => $rm->id,
                            'bundling_id'       => $record->bundling_id,
                            'group_bundling'    => $group_bundling_lama,
                            'jumlah_dipakai'    => $jumlahDipakai,
                            'is_pembelian_baru' => false,
                            'is_final'          => !$keepStatus,
                        ];

                        try {
                            match ($item['tipe']) {
                                'treatment' => \App\Models\TreatmentBundlingUsage::create(
                                    $basePayload + ['treatments_id' => $record->treatments_id]
                                ),
                                'produk' => \App\Models\ProdukBundlingUsage::create(
                                    $basePayload + ['produk_obat_id' => $record->produk_obat_id]
                                ),
                                'pelayanan' => \App\Models\PelayananBundlingUsage::create(
                                    $basePayload + ['pelayanan_id' => $record->pelayanan_id]
                                ),
                            };
                        } catch (\Exception $e) {
                            Log::error("Gagal create BundlingUsage", [
                                'tipe'  => $item['tipe'],
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                }
            }
            
            DB::commit();

            $this->dispatch('toast', [
                'type'    => 'success',
                'message' => $keepStatus 
                    ? 'Data diperbarui, status tetap Konsultasi.' 
                    : 'Rekam Medis berhasil diperbarui.',
            ]);

            $rm->load([
                'rencanaLayananRM', 'rencanaTreatmentRM', 'rencanaBundlingRM',
                'treatmentBundlingUsages', 'pelayananBundlingUsages',
            ]);

            if (!$keepStatus && (
                $rm->rencanaLayananRM->isNotEmpty() ||
                $rm->rencanaTreatmentRM->isNotEmpty() ||
                $rm->rencanaBundlingRM->isNotEmpty() ||
                $rm->treatmentBundlingUsages->isNotEmpty() ||
                $rm->pelayananBundlingUsages->isNotEmpty()
            )) {
                return redirect()->route('rekam-medis-pasien.pengurangan', [
                    'pasien_terdaftar_id' => $this->pasien_terdaftar_id,
                ]);
            }

            return redirect()->route('pendaftaran.data');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toast', [
                'type'    => 'error',
                'message' => 'Gagal memperbarui data: ' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        if (! Gate::allows('akses', 'Rekam Medis')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.rekammedis.keep');
    }
}
