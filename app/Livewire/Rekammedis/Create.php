<?php

namespace App\Livewire\Rekammedis;

use App\Models\IcdRM;
use Livewire\Component;
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
use App\Services\StoreVitalSign;
use App\Models\PemeriksaanFisikRM;
use App\Models\PemeriksaanKulitRM;
use App\Models\RencanaTreatmentRM;
use Illuminate\Support\Facades\DB;
use Livewire\Volt\Compilers\Mount;
use App\Models\PelayananBundlingRM;
use App\Models\ProdukBundlingUsage;
use App\Models\RencananaBundlingRM;
use App\Models\TreatmentBundlingRM;
use Illuminate\Support\Facades\Log;
use App\Models\ProdukObatBundlingRM;
use Illuminate\Support\Facades\Auth;
use App\Models\PelayananBundlingUsage;
use App\Models\TreatmentBundlingUsage;
use App\Services\StorePemeriksaanFisik;
use App\Services\PutInProgressEncounter;
use App\Services\StoreKeluhanUtama;
use App\View\Components\rekammedis\rencanalayanan;

class Create extends Component
{    
    // DATA YANG AKAN DI STORE PADA RekamMedis::model //
    public $rekammedis;
    public $nama_dokter;
    public $keluhan_utama;
    public $tingkat_kesadaran;

    public ?int $pasien_terdaftar_id = null;
    public ?PasienTerdaftar $pasienTerdaftar = null;
    public $kajian;

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

        public $rencana_estetika = [
            'treatments_id' => [],
            'jumlah_treatment' => [],
            'potongan' => [],
            'diskon' => [],
            'subtotal' => [],
        ];

        public $obat_estetika = [
            'produk_id' => [],
            'jumlah_produk' => [],
            'potongan' => [],
            'diskon' => [],
            'subtotal' => [],
        ];

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

    public function tambahLayananBundling($id, $tipe, $nama, $sisa, $bundlingName)
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

    public function mount($pasien_terdaftar_id = null)
    {
        $this->nama_dokter = Auth::user()->dokter->nama_dokter ?? '-';
        $this->pasien_terdaftar_id = $pasien_terdaftar_id;
        if ($pasien_terdaftar_id) {
            $this->pasienTerdaftar = PasienTerdaftar::with('pasien')->find($pasien_terdaftar_id);
            $this->pasien_id = $this->pasienTerdaftar?->pasien_id;
        }

        $this->layanan = Pelayanan::all();
        $this->bundling = Bundling::with([
            'treatmentBundlings.treatment',
            'pelayananBundlings.pelayanan',
            'produkObatBundlings.produk',
        ])->get();
        $this->treatment = Treatment::all();
        $this->skincare = ProdukDanObat::all();

        $this->layanandanbundling['layanan'] = $this->layanan;
        $this->layanandanbundling['bundling'] = $this->bundling;
        $this->layanandanbundling['treatment'] = $this->treatment;
        $this->layanandanbundling['skincare'] = $this->skincare;

        if ($this->pasien_id) {
            // Ambil treatment bundling pasien
            $this->bundlingPasien['treatments'] = TreatmentBundlingRM::with('bundling', 'treatment')
                ->where('pasien_id', $this->pasien_id)
                ->get();

            // Ambil pelayanan bundling pasien
            $this->bundlingPasien['pelayanans'] = PelayananBundlingRM::with('bundling', 'pelayanan')
                ->where('pasien_id', $this->pasien_id)
                ->get();

            // Ambil produk/obat bundling pasien
            $this->bundlingPasien['produks'] = ProdukObatBundlingRM::with('bundling', 'produk',)
                ->where('pasien_id', $this->pasien_id)
                ->get();
        }

        if ($this->pasien_terdaftar_id) {
            $this->pasienTerdaftar = PasienTerdaftar::findOrFail($this->pasien_terdaftar_id);
            $this->kajian = KajianAwal::with('pemeriksaanFisik')
                ->where('pasien_terdaftar_id', $this->pasien_terdaftar_id)
                ->first();

            if ($this->kajian && $this->kajian->pemeriksaanFisik) {
                $this->pemeriksaan_fisik = [
                    'tinggi_badan' => $this->kajian->pemeriksaanFisik->tinggi_badan,
                    'berat_badan' => $this->kajian->pemeriksaanFisik->berat_badan,
                    'imt' => $this->kajian->pemeriksaanFisik->imt,
                ];
            }
            if ($this->kajian && $this->kajian->tandaVital) {
                $this->tanda_vital = [
                    'suhu_tubuh' => $this->kajian->tandaVital->suhu_tubuh,
                    'nadi' => $this->kajian->tandaVital->nadi,
                    'sistole' => $this->kajian->tandaVital->sistole,
                    'diastole' => $this->kajian->tandaVital->diastole,
                    'frekuensi_pernapasan' => $this->kajian->tandaVital->frekuensi_pernapasan,
                ];
            }
            if ($this->kajian && $this->kajian->dataKesehatan) {
                $this->data_kesehatan = [
                    'keluhan_utama' => $this->kajian->dataKesehatan->keluhan_utama,
                    'status_perokok' => $this->kajian->dataKesehatan->status_perokok,
                    'riwayat_penyakit' => json_decode($this->kajian->dataKesehatan->riwayat_penyakit ?? '[]', true),
                    'riwayat_alergi_obat' => json_decode($this->kajian->dataKesehatan->riwayat_alergi_obat ?? '[]', true),
                    'obat_sedang_dikonsumsi' => json_decode($this->kajian->dataKesehatan->obat_sedang_dikonsumsi ?? '[]', true),
                    'riwayat_alergi_lainnya' => json_decode($this->kajian->dataKesehatan->riwayat_alergi_lainnya ?? '[]', true),
                ];
            }
            if ($this->kajian && $this->kajian->dataEstetika) {
                $this->data_estetika = [
                    'problem_dihadapi' => json_decode($this->kajian->dataEstetika->problem_dihadapi ?? '[]', true),
                    'lama_problem' => $this->kajian->dataEstetika->lama_problem,
                    'tindakan_sebelumnya' => json_decode($this->kajian->dataEstetika->tindakan_sebelumnya ?? '[]', true),
                    'penyakit_dialami' => $this->kajian->dataEstetika->penyakit_dialami,
                    'alergi_kosmetik' => $this->kajian->dataEstetika->alergi_kosmetik,
                    'sedang_hamil' => $this->kajian->dataEstetika->sedang_hamil,
                    'usia_kehamilan' => $this->kajian->dataEstetika->usia_kehamilan,
                    'metode_kb' => json_decode($this->kajian->dataEstetika->metode_kb ?? '[]', true),
                    'pengobatan_saat_ini' => $this->kajian->dataEstetika->pengobatan_saat_ini,
                    'produk_kosmetik' => $this->kajian->dataEstetika->produk_kosmetik,
                ];
            }
        }
    }

    public function create()
    {
        $this->validate([
            'nama_dokter' => 'required|string|max:255',
            'pasien_terdaftar_id' => 'required|exists:pasien_terdaftars,id',
        ]);
        // dd([
        //     $this->selected_forms_subjective,
        //     $this->selected_forms_objective,
        //     $this->selected_forms_assessment,
        //     $this->selected_forms_plan,
        //     $this->pemeriksaan_fisik,
        //     $this->tanda_vital,
            // $this->data_kesehatan,
            // $this->tingkat_kesadaran,
            // $this->diagnosa,
            // $this->icd10,
            // $this->data_estetika,
            // $this->pemeriksaan_estetika,
            // $this->rencana_layanan,
            // $this->rencana_estetika,
            // $this->rencana_bundling,
            // $this->obat_estetika,
            // $this->obat_non_racikan,
            // $this->obat_racikan,
            // $this->bahan_racikan,
            // $this->layananTerpilih,
        // ]);

        DB::beginTransaction();

            try {
                $rekammedis = RekamMedis::create([
                    'nama_dokter' => $this->nama_dokter,
                    'pasien_terdaftar_id' => $this->pasien_terdaftar_id,
                    'keluhan_utama' => $this->keluhan_utama,
                    'tingkat_kesadaran' => $this->tingkat_kesadaran,
                ]);
                
                $pt = PasienTerdaftar::with(['pasien', 'dokter'])->find($this->pasien_terdaftar_id);
                // ambil waktu diperiksa
                $waktu_diperiksa = $pt->waktu_diperiksa ?? Carbon::now('Asia/Makassar')->setTimezone('UTC')->toIso8601String();
                
                //put encounter
                $kirimsatusehat = $pt->encounter_id;
                if($kirimsatusehat){               
                    
                    // Encounter ID yang sudah dibuat saat POST Encounter
                    $encounterId = $pt->encounter_id;
                    // Panggil PUT Encounter
                    $putEncounter = app(PutInProgressEncounter::class);
                    $putEncounter->handle(
                        encounterId: $encounterId,
                        waktuTiba: $pt->waktu_tiba,
                        WaktuDiperiksa: $waktu_diperiksa,
                        pasienNama: $pt->pasien->nama,
                        pasienIhs: $pt->pasien->no_ihs,
                        dokterNama: $pt->dokter->nama_dokter,
                        dokterIhs: $pt->dokter->ihs,
                        location: $pt->poliklinik->location,
                    );
                    
                    $PostKeluhanUtama = app(StoreKeluhanUtama::class);
                    $PostKeluhanUtama->handle(
                        encounterId: $encounterId,
                        WaktuDiperiksa: $waktu_diperiksa,
                        pasienNama: $pt->pasien->nama,
                        pasienIhs: $pt->pasien->no_ihs,
                        dokterNama: $pt->dokter->nama_dokter,
                        dokterIhs: $pt->dokter->ihs,
                        keluhanUtama: $rekammedis->keluhan_utama,
                    );
                }

                $status = 'pembayaran';
                if (in_array('obat-non-racikan', $this->selected_forms_plan)) {
                    $status = 'peresepan';
                }

                if (in_array('obat-racikan', $this->selected_forms_plan)) {
                    $status = 'peresepan';
                }
                
                PasienTerdaftar::findOrFail($this->pasien_terdaftar_id)
                    ->update([
                        'status_terdaftar' => $status,
                        'waktu_diperiksa' => $waktu_diperiksa
                    ]);

                // PasienTerdaftar::findOrFail($this->pasien_terdaftar_id)
                //     ->update(['status_terdaftar' => 'peresepan']);

            // ----- SUBJECTIVE ----- //

                // SIMPAN DATA KESEHATAN REKAM MEDIS
                if (in_array('data-kesehatan', $this->selected_forms_subjective)) {
                    DataKesehatanRM::create([
                        'rekam_medis_id' => $rekammedis->id,
                        'status_perokok' => $this->data_kesehatan['status_perokok'],
                        'riwayat_penyakit' => json_encode($this->data_kesehatan['riwayat_penyakit']),
                        'riwayat_alergi_obat' => json_encode($this->data_kesehatan['riwayat_alergi_obat']),
                        'riwayat_alergi_lainnya' => json_encode($this->data_kesehatan['riwayat_alergi_lainnya']),
                        'obat_sedang_dikonsumsi' => json_encode($this->data_kesehatan['obat_sedang_dikonsumsi']),
                    ]);
                }
                // SIMPAN DATA ESTETIKA REKAM MEDIS
                if (in_array('data-estetika', $this->selected_forms_subjective)) {
                    DataEstetikaRM::create([
                        'rekam_medis_id' => $rekammedis->id,
                        'problem_dihadapi' => json_encode($this->data_estetika['problem_dihadapi']),
                        'lama_problem' => $this->data_estetika['lama_problem'],
                        'tindakan_sebelumnya' => json_encode($this->data_estetika['tindakan_sebelumnya']),
                        'penyakit_dialami' => $this->data_estetika['penyakit_dialami'],
                        'alergi_kosmetik' => $this->data_estetika['alergi_kosmetik'],
                        'sedang_hamil' => $this->data_estetika['sedang_hamil'],
                        'usia_kehamilan' => $this->data_estetika['usia_kehamilan'],
                        'metode_kb' => json_encode($this->data_estetika['metode_kb']),
                        'pengobatan_saat_ini' => $this->data_estetika['pengobatan_saat_ini'],
                        'produk_kosmetik' => $this->data_estetika['produk_kosmetik'],
                    ]);
                }

            // ----- SUBJECTIVE ----- //


            // ----- OBJECTIVE ----- //

                // SIMPAN DATA TANDA VITAL REKAM MEDIS
                if (in_array('tanda-vital', $this->selected_forms_objective)) {
                    if($kirimsatusehat){
                        
                        // Encounter ID yang sudah dibuat saat POST Encounter
                        $encounterId = $pt->encounter_id;
                        
                        $PostVitalSign = app(StoreVitalSign::class);
                        $observation = $PostVitalSign->handle(
                            encounterId: $encounterId,
                            pasienNama: $pt->pasien->nama,
                            pasienIhs: $pt->pasien->no_ihs,
                            dokterNama: $pt->dokter->nama_dokter,
                            dokterIhs: $pt->dokter->ihs,
                            waktuTiba: $pt->waktu_tiba,
                            sistole: $this->tanda_vital['sistole'],
                            diastole: $this->tanda_vital['diastole'],
                            suhu_tubuh: $this->tanda_vital['suhu_tubuh'],
                            nadi: $this->tanda_vital['nadi'],
                            pernapasan: $this->tanda_vital['frekuensi_pernapasan'],
                        );
                    }
                    TandaVitalRM::create([
                        'rekam_medis_id' => $rekammedis->id,
                        'suhu_tubuh' => $this->tanda_vital['suhu_tubuh'],
                        'nadi' => $this->tanda_vital['nadi'],
                        'sistole' => $this->tanda_vital['sistole'],
                        'diastole' => $this->tanda_vital['diastole'],
                        'frekuensi_pernapasan' => $this->tanda_vital['frekuensi_pernapasan'],
                    ]);
                }

                // SIMPAN DATA PEMERIKSAAN FISIK REKAM MEDIS
                if (in_array('pemeriksaan-fisik', $this->selected_forms_objective)) {
                    if($kirimsatusehat){
                        
                        // Encounter ID yang sudah dibuat saat POST Encounter
                        $encounterId = $pt->encounter_id;
                        
                        $PostPemeriksaanFisik = app(StorePemeriksaanFisik::class);
                        $observationFisik = $PostPemeriksaanFisik->handle(
                            encounterId: $encounterId,
                            pasienNama: $pt->pasien->nama,
                            pasienIhs: $pt->pasien->no_ihs,
                            dokterNama: $pt->dokter->nama_dokter,
                            dokterIhs: $pt->dokter->ihs,
                            waktuTiba: $pt->waktu_tiba,
                            tinggiBadan: $this->pemeriksaan_fisik['tinggi_badan'],
                            beratBadan: $this->pemeriksaan_fisik['berat_badan'],
                        );
                    }
                    PemeriksaanFisikRM::create([
                        'rekam_medis_id' => $rekammedis->id,
                        'tinggi_badan' => $this->pemeriksaan_fisik['tinggi_badan'],
                        'berat_badan' => $this->pemeriksaan_fisik['berat_badan'],
                        'imt' => $this->pemeriksaan_fisik['imt'],
                    ]);
                }

                // SIMPAN DATA PEMERIKSAAN KULIT REKAM MEDIS
                if (in_array('pemeriksaan-estetika', $this->selected_forms_objective)) {
                    PemeriksaanKulitRM::create([
                        'rekam_medis_id' => $rekammedis->id,
                        'warna_kulit' => $this->pemeriksaan_estetika['warna_kulit'],
                        'ketebalan_kulit' => $this->pemeriksaan_estetika['ketebalan_kulit'],
                        'kadar_minyak' => $this->pemeriksaan_estetika['kadar_minyak'],
                        'kerapuhan_kulit' => $this->pemeriksaan_estetika['kerapuhan_kulit'],
                        'kekencangan_kulit' => $this->pemeriksaan_estetika['kekencangan_kulit'],
                        'melasma' => $this->pemeriksaan_estetika['melasma'],
                        'acne' => json_encode($this->pemeriksaan_estetika['acne']),
                        'lesions' => json_encode($this->pemeriksaan_estetika['lesions']),
                    ]);
                }
                
            // ----- OBJECTIVE ----- //

            // ----- ASSESSMENT ----- //

                // SIMPAN DATA DIAGNOSA REKAM MEDIS
                // if (in_array('diagnosa', $this->selected_forms_assessment)) {
                    DiagnosaRM::create([
                        'rekam_medis_id' => $rekammedis->id,
                        'diagnosa' => $this->diagnosa,
                    ]);
                // }

                // SIMPAN DATA ICD 10 REKAM MEDIS
                    $condition_id = null;
                    $encounterId = $pt->encounter_id;
                // if (in_array('icd_10', $this->selected_forms_assessment)) {
                    foreach ($this->icd10 as $item) {
                        // Kirim Data ICD ke Satu Sehat
                        if($kirimsatusehat){
                            
                            // Panggil PostCondition
                            $PostCondition = app(StoreCondition::class);
                            $condition_id = $PostCondition->handle(
                                encounterId: $encounterId,
                                pasienNama: $pt->pasien->nama,
                                pasienIhs: $pt->pasien->no_ihs,
                                icdCode: $item['code'],
                                icdName: $item['name_en'],
                            );
                        }
                        // dd($condition_id);
                        if (!empty($item['code'])) {
                            IcdRM::create([
                                'rekam_medis_id' => $rekammedis->id,
                                'condition_id'   => $condition_id,
                                'code'           => $item['code'],
                                'name_id'        => $item['name_id'],
                                'name_en'        => $item['name_en'],
                            ]);
                        }
                    }
                // }

            // ----- ASSESSMENT ----- //
                
            // ----- PLAN ----- //

                // SIMPAN DATA RENCANA LAYANAN REKAM MEDIS
                if (in_array('rencana-estetika', $this->selected_forms_plan)) {
                    foreach ($this->rencana_estetika['treatments_id'] as $index => $treatmentId) {

                        RencanaTreatmentRM::create([
                            'rekam_medis_id' => $rekammedis->id,
                            'treatments_id' => $treatmentId,
                            'jumlah_treatment' => $this->rencana_estetika['jumlah_treatment'][$index] ?? 1,
                            'potongan' => $this->rencana_estetika['potongan'][$index] ?? 0,
                            'diskon' => $this->rencana_estetika['diskon'][$index] ?? 0,
                            'subtotal' => $this->rencana_estetika['subtotal'][$index] ?? 0,
                        ]);
                    }
                }

                if (in_array('rencana-layanan', $this->selected_forms_plan)) {

                    foreach ($this->rencana_layanan['pelayanan_id'] as $index => $pelayananId) {
                        RencanaLayananRM::create([
                            'rekam_medis_id'   => $rekammedis->id,
                            'pelayanan_id'     => $pelayananId,
                            'jumlah_pelayanan' => $this->rencana_layanan['jumlah_pelayanan'][$index],
                        ]);
                    }
                }

                if (in_array('obat-estetika', $this->selected_forms_plan)) {

                    foreach ($this->obat_estetika['produk_id'] as $index => $produkId) {
                        RencanaProdukRM::create([
                            'rekam_medis_id'   => $rekammedis->id,
                            'produk_id'     => $produkId,
                            'jumlah_produk' => $this->obat_estetika['jumlah_produk'][$index] ?? 1,
                            'potongan' => $this->obat_estetika['potongan'][$index] ?? 0,
                            'diskon' => $this->obat_estetika['diskon'][$index] ?? 0,
                            'subtotal' => $this->obat_estetika['subtotal'][$index] ?? 0,
                        ]);
                    }
                }

                // SIMPAN DATA PAKET BUNDLING REKAM MEDIS
                // if (in_array('rencana-bundling', $this->selected_forms_plan)) {
                //     foreach ($this->rencana_bundling['bundling_id'] as $index => $bundlingId) {
                //         RencananaBundlingRM::create([
                //             'rekam_medis_id'   => $rekammedis->id,
                //             'bundling_id'      => $bundlingId,
                //             'jumlah_bundling'  => $this->rencana_bundling['jumlah_bundling'][$index] ?? 1,
                //             'potongan' => $this->rencana_bundling['potongan'][$index] ?? 0,
                //             'diskon' => $this->rencana_bundling['diskon'][$index] ?? 0,
                //             'subtotal' => $this->rencana_bundling['subtotal'][$index] ?? 0,
                //         ]);
                        
                //         // Ambil pasien_id
                //         $pasienId = $this->pasien_id;

                //         // Simpan detail treatment
                //         if (!empty($this->rencana_bundling['details']['treatments'][$index])) {
                //             foreach ($this->rencana_bundling['details']['treatments'][$index] as $t) {
                //                 TreatmentBundlingRM::create([
                //                     'pasien_id'      => $pasienId,
                //                     'bundling_id'    => $bundlingId,
                //                     'treatments_id'   => $t['treatments_id'],
                //                     'jumlah_awal'    => $t['jumlah_awal'],
                //                     'jumlah_terpakai'=> $t['jumlah_terpakai'],
                //                 ]);
                //             }
                //         }
                //         // Simpan detail Pelayanan
                //         if (!empty($this->rencana_bundling['details']['pelayanans'][$index])) {
                //             foreach ($this->rencana_bundling['details']['pelayanans'][$index] as $t) {
                //                 PelayananBundlingRM::create([
                //                     'pasien_id'      => $pasienId,
                //                     'bundling_id'    => $bundlingId,
                //                     'pelayanan_id'   => $t['pelayanan_id'],
                //                     'jumlah_awal'    => $t['jumlah_awal'],
                //                     'jumlah_terpakai'=> $t['jumlah_terpakai'],
                //                 ]);
                //             }
                //         }
                //         // Simpan detail Produk & Obat
                //         if (!empty($this->rencana_bundling['details']['produks'][$index])) {
                //             foreach ($this->rencana_bundling['details']['produks'][$index] as $t) {
                //                 ProdukObatBundlingRM::create([
                //                     'pasien_id'      => $pasienId,
                //                     'bundling_id'    => $bundlingId,
                //                     'produk_obat_id'   => $t['produk_obat_id'],
                //                     'jumlah_awal'    => $t['jumlah_awal'],
                //                     'jumlah_terpakai'=> $t['jumlah_terpakai'],
                //                 ]);
                //             }
                //         }
                //     }
                // }
                // SIMPAN DATA PAKET BUNDLING REKAM MEDIS KETIKA PERTAMA DIBELI
                if (in_array('rencana-bundling', $this->selected_forms_plan)) {
                    foreach ($this->rencana_bundling['bundling_id'] as $index => $bundlingId) {
                        $rekamMedisId = $rekammedis->id;

                        $bundlingRecord = RencananaBundlingRM::create([
                            'rekam_medis_id'   => $rekamMedisId,
                            'bundling_id'      => $bundlingId,
                            'jumlah_bundling'  => $this->rencana_bundling['jumlah_bundling'][$index] ?? 1,
                            'potongan' => $this->rencana_bundling['potongan'][$index] ?? 0,
                            'diskon' => $this->rencana_bundling['diskon'][$index] ?? 0,
                            'subtotal' => $this->rencana_bundling['subtotal'][$index] ?? 0,
                        ]);
                        
                        // Ambil pasien_id
                        $pasienId = $this->pasien_id;
                        $group_bundling = 'GB-' . Str::random(4);
                        /**
                         * ==========================
                         * SIMPAN DETAIL TREATMENT
                         * ==========================
                         */
                        if (!empty($this->rencana_bundling['details']['treatments'][$index])) {
                            foreach ($this->rencana_bundling['details']['treatments'][$index] as $t) {
                                $treatmentRM = TreatmentBundlingRM::create([
                                    'pasien_id'       => $pasienId,
                                    'bundling_id'     => $bundlingId,
                                    'treatments_id'   => $t['treatments_id'],
                                    'jumlah_awal'     => $t['jumlah_awal'],
                                    'jumlah_terpakai' => $t['jumlah_terpakai'],
                                    'group_bundling'   => $group_bundling,
                                ]);

                                // Jika ada jumlah_terpakai > 0 → simpan usage awal
                                if (!empty($t['jumlah_terpakai']) && $t['jumlah_terpakai'] > 0) {
                                    \App\Models\TreatmentBundlingUsage::create([
                                        'pasien_id'       => $pasienId,
                                        'rekam_medis_id'  => $rekamMedisId,
                                        'bundling_id'     => $bundlingId,
                                        'treatments_id'   => $t['treatments_id'],
                                        'jumlah_dipakai'  => $t['jumlah_terpakai'],
                                        'kategori'        => 'penggunaan_sisa',
                                        'is_pembelian_baru' => true,
                                    ]);
                                }
                            }
                        }

                        /**
                         * ==========================
                         * SIMPAN DETAIL PELAYANAN
                         * ==========================
                         */
                        if (!empty($this->rencana_bundling['details']['pelayanans'][$index])) {
                            foreach ($this->rencana_bundling['details']['pelayanans'][$index] as $t) {
                                $pelayananRM = PelayananBundlingRM::create([
                                    'pasien_id'       => $pasienId,
                                    'bundling_id'     => $bundlingId,
                                    'pelayanan_id'    => $t['pelayanan_id'],
                                    'jumlah_awal'     => $t['jumlah_awal'],
                                    'jumlah_terpakai' => $t['jumlah_terpakai'],
                                    'group_bundling'   => $group_bundling,
                                ]);

                                if (!empty($t['jumlah_terpakai']) && $t['jumlah_terpakai'] > 0) {
                                    \App\Models\PelayananBundlingUsage::create([
                                        'pasien_id'       => $pasienId,
                                        'rekam_medis_id'  => $rekamMedisId,
                                        'bundling_id'     => $bundlingId,
                                        'pelayanan_id'    => $t['pelayanan_id'],
                                        'jumlah_dipakai'  => $t['jumlah_terpakai'],
                                        'kategori'        => 'penggunaan_sisa',
                                        'is_pembelian_baru' => true,
                                    ]);
                                }
                            }
                        }

                        /**
                         * ==========================
                         * SIMPAN DETAIL PRODUK / OBAT
                         * ==========================
                         */
                        if (!empty($this->rencana_bundling['details']['produks'][$index])) {
                            foreach ($this->rencana_bundling['details']['produks'][$index] as $t) {
                                $produkRM = ProdukObatBundlingRM::create([
                                    'pasien_id'       => $pasienId,
                                    'bundling_id'     => $bundlingId,
                                    'produk_obat_id'  => $t['produk_obat_id'],
                                    'jumlah_awal'     => $t['jumlah_awal'],
                                    'jumlah_terpakai' => $t['jumlah_terpakai'],
                                    'group_bundling'   => $group_bundling,
                                ]);

                                if (!empty($t['jumlah_terpakai']) && $t['jumlah_terpakai'] > 0) {
                                    \App\Models\ProdukBundlingUsage::create([
                                        'pasien_id'       => $pasienId,
                                        'rekam_medis_id'  => $rekamMedisId,
                                        'bundling_id'     => $bundlingId,
                                        'produk_obat_id'  => $t['produk_obat_id'],
                                        'jumlah_dipakai'  => $t['jumlah_terpakai'],
                                        'kategori'        => 'penggunaan_sisa',
                                        'is_pembelian_baru' => true,
                                    ]);
                                }
                            }
                        }
                    }
                }
          
                // SIMPAN DATA OBAT NON RACIK
                if (in_array('obat-non-racikan', $this->selected_forms_plan)) {
                    foreach ($this->obat_non_racikan['nama_obat_non_racikan'] as $index => $namaObat) {
                        ObatNonRacikanRM::create([
                            'rekam_medis_id' => $rekammedis->id,
                            'nama_obat_non_racikan' => $namaObat,
                            'jumlah_obat_non_racikan' => $this->obat_non_racikan['jumlah_obat_non_racikan'][$index] ?? 1,
                            'satuan_obat_non_racikan' => $this->obat_non_racikan['satuan_obat_non_racikan'][$index] ?? null,
                            'dosis_obat_non_racikan' => $this->obat_non_racikan['dosis_obat_non_racikan'][$index] ?? null,
                            'hari_obat_non_racikan' => $this->obat_non_racikan['hari_obat_non_racikan'][$index] ?? null,
                            'aturan_pakai_obat_non_racikan' => $this->obat_non_racikan['aturan_pakai_obat_non_racikan'][$index] ?? null,
                        ]);
                    }
                }
                
                // SIMPAN DATA OBAT RACIKAN
                if (in_array('obat-racikan', $this->selected_forms_plan)) {
                    foreach ($this->racikanItems as $racikan) {
                        // 1. Buat racikan utama
                        $obatRacikan = ObatRacikanRM::create([
                            'rekam_medis_id'      => $rekammedis->id,
                            'nama_racikan'        => $racikan['nama_racikan'] ?? null,
                            'jumlah_racikan'      => $racikan['jumlah_racikan'] ?? 1,
                            'satuan_racikan'      => $racikan['satuan_racikan'] ?? null,
                            'dosis_obat_racikan'  => $racikan['dosis_obat_racikan'] ?? null,
                            'hari_obat_racikan'   => $racikan['hari_obat_racikan'] ?? null,
                            'aturan_pakai_racikan'=> $racikan['aturan_pakai_racikan'] ?? null,
                            'metode_racikan'      => $racikan['metode_racikan'] ?? null,
                        ]);

                        // 2. Simpan bahan racikan (jika ada)
                        if (!empty($racikan['bahan']) && is_array($racikan['bahan'])) {
                            foreach ($racikan['bahan'] as $bahan) {
                                $obatRacikan->bahanRacikan()->create([
                                    'obat_racikan_id' => $obatRacikan->id,
                                    'nama_obat_racikan' => $bahan['nama_obat_racikan'] ?? null,
                                    'jumlah_obat_racikan' => $bahan['jumlah_obat_racikan'] ?? 1,
                                    'satuan_obat_racikan' => $bahan['satuan_obat_racikan'] ?? null,
                                ]);
                            }
                        }
                    }
                }

                // SIMPAN DATA SISA ITEM BUNDLING YANG BELUM DIAMBIL
                if (!empty($this->layananTerpilih)) {
                    $pasienId = $this->pasien_id ?? null;

                    foreach ($this->layananTerpilih as $namaBundling => $items) {
                        foreach ($items as $item) {
                            $jumlahDipakai = (int) ($item['dipakai'] ?? 0);
                            if ($jumlahDipakai <= 0) continue;

                            switch ($item['tipe']) {
                                case 'treatment':
                                    $record = \App\Models\TreatmentBundlingRM::where('id', $item['id'])
                                        ->where('pasien_id', $pasienId)
                                        ->first();
                                    break;

                                case 'produk':
                                    $record = \App\Models\ProdukObatBundlingRM::where('id', $item['id'])
                                        ->where('pasien_id', $pasienId)
                                        ->first();
                                    break;

                                case 'pelayanan':
                                    $record = \App\Models\PelayananBundlingRM::where('id', $item['id'])
                                        ->where('pasien_id', $pasienId)
                                        ->first();
                                    break;

                                default:
                                    $record = null;
                            }

                            if (!$record) {
                                Log::warning("Record bundling tidak ditemukan", [
                                    'tipe' => $item['tipe'],
                                    'id' => $item['id'],
                                    'pasien' => $pasienId,
                                ]);
                                continue;
                            }

                            $baruTerpakai = min(
                                $record->jumlah_terpakai + $jumlahDipakai,
                                $record->jumlah_awal
                            );

                            $record->update(['jumlah_terpakai' => $baruTerpakai]);

                            // 🔍 Tambahkan logger di sini sebelum create
                            Log::info("Create BundlingUsage", [
                                'tipe' => $item['tipe'],
                                'pasien_id' => $pasienId,
                                'rekam_medis_id' => $rekammedis->id,
                                'bundling_id' => $record->bundling_id ?? null,
                                'jumlah_dipakai' => $jumlahDipakai,
                            ]);

                            try {
                                switch ($item['tipe']) {
                                    case 'treatment':
                                        \App\Models\TreatmentBundlingUsage::create([
                                            'pasien_id' => $pasienId,
                                            'rekam_medis_id' => $rekammedis->id,
                                            'bundling_id' => $record->bundling_id,
                                            'treatments_id' => $record->treatments_id,
                                            'jumlah_dipakai' => $jumlahDipakai,
                                            'is_pembelian_baru' => false,
                                        ]);
                                        break;

                                    case 'produk':
                                        \App\Models\ProdukBundlingUsage::create([
                                            'pasien_id' => $pasienId,
                                            'rekam_medis_id' => $rekammedis->id,
                                            'bundling_id' => $record->bundling_id,
                                            'produk_obat_id' => $record->produk_obat_id,
                                            'jumlah_dipakai' => $jumlahDipakai,
                                            'is_pembelian_baru' => false,
                                        ]);
                                        break;

                                    case 'pelayanan':
                                        \App\Models\PelayananBundlingUsage::create([
                                            'pasien_id' => $pasienId,
                                            'rekam_medis_id' => $rekammedis->id,
                                            'bundling_id' => $record->bundling_id,
                                            'pelayanan_id' => $record->pelayanan_id,
                                            'jumlah_dipakai' => $jumlahDipakai,
                                            'is_pembelian_baru' => false,
                                        ]);
                                        break;
                                }
                            } catch (\Exception $e) {
                                Log::error("Gagal create BundlingUsage", [
                                    'tipe' => $item['tipe'],
                                    'error' => $e->getMessage(),
                                ]);
                            }
                        }
                    }
                }

            // ----- PLAN ----- //

                DB::commit();
                if($kirimsatusehat){
                    $this->dispatch('toast', [
                        'type' => 'success',
                        'message' => 'Rekam Medis Berhasil Ditambahkan Dan Kirim Satu Sehat'
                    ]);
                }else{
                    $this->dispatch('toast', [
                        'type' => 'success',
                        'message' => 'Rekam Medis Berhasil Ditambahkan.'
                    ]);
                }

                $this->dispatch('closeStoreModal');

                $this->reset();

                return redirect()->route('pendaftaran.data');
            } catch (\Exception $e) {
                DB::rollBack();
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Gagal Menyimpan Data: ' . $e->getMessage()
                ]);
            }

    }

    public function render()
    {
        return view('livewire.rekammedis.create');
    }
}
