<?php

namespace App\Livewire\Rekammedis;

use Livewire\Component;
use App\Models\Bundling;
use App\Models\DataEstetikaRM;
use App\Models\Pelayanan;
use App\Models\KajianAwal;
use App\Models\RekamMedis;
use App\Models\DataKesehatanRM;
use App\Models\DiagnosaRM;
use App\Models\IcdRM;
use App\Models\ObatNonRacikanRM;
use App\Models\ObatRacikanRM;
use App\Models\PasienTerdaftar;
use App\Models\PemeriksaanFisikRM;
use App\Models\PemeriksaanKulitRM;
use App\Models\RencanaLayananRM;
use App\Models\RencananaBundlingRM;
use App\Models\TandaVitalRM;
use Illuminate\Support\Facades\DB;
use Livewire\Volt\Compilers\Mount;
use Illuminate\Support\Facades\Auth;
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
    
    // berisikan data yang akan dimunculkan pada select layanan/tindakan
    public $layanan;
    public $bundling;

    // FORM DATA YANG PILIH //
    public array $selected_forms_subjective = [];
    public array $selected_forms_objective = [];
    public array $selected_forms_assessment = [];
    public array $selected_forms_plan = [];
    

    // OBJECTIVE

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

    //ASSESSMENT
    public $diagnosa;
    public $icd10 = [];

    //PLAN
    public $rencana_layanan = [
        'pelayanan_id' => [],
        'jumlah_pelayanan' => [],
    ];

    public $rencana_bundling = [
        'bundling_id' => [],
        'jumlah_bundling' => [],
    ];

    public $layanandanbundling = [
        'layanan' => [],
        'bundling' => [],
    ];

    public $obat_non_racikan = [
        'nama_obat_non_racikan' => [],
        'jumlah_obat_non_racikan'=> [],
        'satuan_obat_non_racikan'=> [],
        'dosis_obat_non_racikan'=> [],
        'hari_obat_non_racikan'=> [],
        'aturan_pakai_obat_non_racikan'=> [],
    ];

    // public $obat_racikan = [
    //     'nama_racikan' => [],
    //     'jumlah_racikan' => [],
    //     'satuan_racikan' => [],
    //     'aturan_pakai_racikan' => [],
    //     'metode_racikan' => [],
    // ];

    // public $bahan_racikan = [
    //     'nama_obat_racikan' => [],
    //     'jumlah_obat_racikan' => [],
    //     'satuan_obat_racikan' => [],
    // ];

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


    public function mount($pasien_terdaftar_id = null)
    {
        $this->nama_dokter = Auth::user()->dokter->nama_dokter ?? '-';
        $this->pasien_terdaftar_id = $pasien_terdaftar_id;
        $this->layanan = Pelayanan::all();
        $this->bundling = Bundling::all();

        $this->layanandanbundling['layanan'] = $this->layanan;
        $this->layanandanbundling['bundling'] = $this->bundling;

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
        DB::beginTransaction();

            try {
                $rekammedis = RekamMedis::create([
                    'nama_dokter' => $this->nama_dokter,
                    'pasien_terdaftar_id' => $this->pasien_terdaftar_id,
                    'keluhan_utama' => $this->keluhan_utama,
                    'tingkat_kesadaran' => $this->tingkat_kesadaran,
                ]);

                PasienTerdaftar::findOrFail($this->pasien_terdaftar_id)
                    ->update(['status_terdaftar' => 'peresepan']);

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
                if (in_array('diagnosa', $this->selected_forms_assessment)) {
                    DiagnosaRM::create([
                        'rekam_medis_id' => $rekammedis->id,
                        'diagnosa' => $this->diagnosa,
                    ]);
                }

                // SIMPAN DATA ICD 10 REKAM MEDIS
                if (in_array('icd_10', $this->selected_forms_assessment)) {
                    foreach ($this->icd10 as $item) {
                        if (!empty($item['code'])) {
                            IcdRM::create([
                                'rekam_medis_id' => $rekammedis->id,
                                'code'           => $item['code'],
                                'name_id'        => $item['name_id'],
                                'name_en'        => $item['name_en'],
                            ]);
                        }
                    }
                }

            // ----- ASSESSMENT ----- //
                
            // ----- PLAN ----- //

                // SIMPAN DATA RENCANA LAYANAN REKAM MEDIS
                if (in_array('rencana-layanan', $this->selected_forms_plan)) {

                    foreach ($this->rencana_layanan['pelayanan_id'] as $index => $pelayananId) {
                        RencanaLayananRM::create([
                            'rekam_medis_id'   => $rekammedis->id,
                            'pelayanan_id'     => $pelayananId,
                            'jumlah_pelayanan' => $this->rencana_layanan['jumlah_pelayanan'][$index],
                        ]);
                    }
                }

                // SIMPAN DATA PAKET BUNDLING REKAM MEDIS
                if (in_array('rencana-bundling', $this->selected_forms_plan)) {
                    foreach ($this->rencana_bundling['bundling_id'] as $index => $bundlingId) {
                        RencananaBundlingRM::create([
                            'rekam_medis_id'   => $rekammedis->id,
                            'bundling_id'      => $bundlingId,
                            'jumlah_bundling'  => $this->rencana_bundling['jumlah_bundling'][$index],
                        ]);
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

            // ----- PLAN ----- //

                DB::commit();
                
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Rekam Medis Berhasil Ditambahkan.'
                ]);

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

        // dd([
        //     $this->selected_forms_subjective,
        //     $this->selected_forms_objective,
        //     $this->selected_forms_assessment,
        //     $this->selected_forms_plan,
        //     // $this->pemeriksaan_fisik,
        //     // $this->tanda_vital,
        //     // $this->data_kesehatan,
        //     // $this->tingkat_kesadaran,
        //     // $this->diagnosa,
        //     // $this->icd10,
        //     // $this->data_estetika,
        //     // $this->pemeriksaan_estetika,
        //     // $this->rencana_layanan,
        //     // $this->obat_non_racikan,
        //     // $this->obat_racikan,
        //     // $this->bahan_racikan,
        // ]);
    }

    public function render()
    {
        return view('livewire.rekammedis.create');
    }
}
