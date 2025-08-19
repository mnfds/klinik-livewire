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
    public ?int $pasien_terdaftar_id = null;
    public ?PasienTerdaftar $pasienTerdaftar = null;
    public $kajian;
    public $rekammedis;

    public $nama_dokter;

    // berisikan data yang akan dimunculkan pada select layanan/tindakan
    public $layanan;
    public $bundling;

    // FORM DATA YANG PILIH //
    public array $selected_forms_subjective = [];
    public array $selected_forms_objective = [];
    public array $selected_forms_assessment = [];
    public array $selected_forms_plan = [];

    // OBJECTIVE
    public $tingkat_kesadaran;

    public $pemeriksaan_fisik = [
        'tinggi_badan' => null,
        'berat_badan' => null,
        'imt' => null,
    ];
    public $pemeriksaan_estetika = [
        'warna_kulit' => 'kuning',
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
        'keluhan_utama' => null,
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
        'aturan_pakai_obat_non_racikan'=> [],
    ];
    public $obat_racikan = [
        'nama_racikan' => [],
        'jumlah_racikan' => [],
        'satuan_racikan' => [],
        'aturan_pakai_racikan' => [],
        'metode_racikan' => [],
    ];

    public $bahan_racikan = [
        'nama_obat_racikan' => [],
        'jumlah_obat_racikan' => [],
        'satuan_obat_racikan' => [],
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
                ]);

                PasienTerdaftar::findOrFail($this->pasien_terdaftar_id)
                    ->update(['status_terdaftar' => 'diperiksa']);

                // ----- SUBJECTIVE ----- //

                // SIMPAN DATA KESEHATAN REKAM MEDIS
                if (in_array('data-kesehatan', $this->selected_forms_subjective)) {
                    DataKesehatanRM::create([
                        'rekam_medis_id' => $rekammedis->id,
                        'keluhan_utama' => $this->keluhan_utama,
                        'status_perokok' => $this->status_perokok,
                        'riwayat_penyakit' => json_encode($this->riwayat_penyakit),
                        'riwayat_alergi_obat' => json_encode($this->riwayat_alergi_obat),
                        'riwayat_alergi_lainnya' => json_encode($this->riwayat_alergi_lainnya),
                        'obat_sedang_dikonsumsi' => json_encode($this->obat_sedang_dikonsumsi),
                    ]);
                }
                // SIMPAN DATA ESTETIKA REKAM MEDIS
                if (in_array('data-estetika', $this->selected_forms_subjective)) {
                    DataEstetikaRM::create([
                        'rekam_medis_id' => $rekammedis->id,
                        'problem_dihadapi' => json_encode($this->problem_dihadapi),
                        'lama_problem' => $this->lama_problem,
                        'tindakan_sebelumnya' => json_encode($this->tindakan_sebelumnya),
                        'penyakit_dialami' => $this->penyakit_dialami,
                        'alergi_kosmetik' => $this->alergi_kosmetik,
                        'sedang_hamil' => $this->sedang_hamil,
                        'usia_kehamilan' => $this->usia_kehamilan,
                        'metode_kb' => json_encode($this->metode_kb),
                        'pengobatan_saat_ini' => $this->pengobatan_saat_ini,
                        'produk_kosmetik' => $this->produk_kosmetik,
                    ]);
                }

                // ----- SUBJECTIVE ----- //


                // ----- OBJECTIVE ----- //

                // SIMPAN DATA TANDA VITAL REKAM MEDIS
                if (in_array('tanda-vital', $this->selected_forms_objective)) {
                    TandaVitalRM::create([
                        'rekam_medis_id' => $rekammedis->id,
                        'suhu_tubuh' => $this->suhu_tubuh,
                        'nadi' => $this->nadi,
                        'sistole' => $this->sistole,
                        'diastole' => $this->diastole,
                        'frekuensi_pernapasan' => $this->frekuensi_pernapasan,
                    ]);
                }

                // SIMPAN DATA PEMERIKSAAN FISIK REKAM MEDIS
                if (in_array('pemeriksaan-fisik', $this->selected_forms_objective)) {
                    PemeriksaanFisikRM::create([
                        'rekam_medis_id' => $rekammedis->id,
                        'tinggi_badan' => $this->tinggi_badan,
                        'berat_badan' => $this->berat_badan,
                        'imt' => $this->imt,
                    ]);
                }

                // SIMPAN DATA PEMERIKSAAN KULIT REKAM MEDIS
                if (in_array('pemeriksaan-estetika', $this->selected_forms_objective)) {
                    PemeriksaanKulitRM::create([
                        'rekam_medis_id' => $rekammedis->id,
                        'warna_kulit' => $this->warna_kulit,
                        'ketebalan_kulit' => $this->ketebalan_kulit,
                        'kadar_minyak' => $this->kadar_minyak,
                        'kerapuhan_kulit' => $this->kerapuhan_kulit,
                        'kekencangan_kulit' => $this->kekencangan_kulit,
                        'melasma' => $this->melasma,
                        'acne' => json_encode($this->acne),
                        'lesions' => json_encode($this->lesions),
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
                    IcdRM::create([
                        'rekam_medis_id' => $rekammedis->id,
                        'icd10' => json_encode($this->icd10),
                    ]);
                }

                // ----- ASSESSMENT ----- //
                
                // ----- PLAN ----- //

                // SIMPAN DATA RENCANA LAYANAN REKAM MEDIS
                if (in_array('rencana-layanan', $this->selected_forms_plan)) {
                    foreach ($this->pelayanan_id as $index => $pelayananId) {
                        RencanaLayananRM::create([
                            'rekam_medis_id'   => $rekammedis->id,
                            'pelayanan_id'     => $pelayananId,
                            'jumlah_pelayanan' => $this->jumlah_pelayanan[$index],
                        ]);
                    }
                }

                // SIMPAN DATA PAKET BUNDLING REKAM MEDIS
                if (in_array('rencana-bundling', $this->selected_forms_plan)) {
                    foreach ($this->bundling_id as $index => $bundlingId) {
                        RencananaBundlingRM::create([
                            'rekam_medis_id'   => $rekammedis->id,
                            'bundling_id'      => $bundlingId,
                            'jumlah_bundling'  => $this->jumlah_bundling[$index] ?? 1,
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
                            'aturan_pakai_obat_non_racikan' => $this->obat_non_racikan['aturan_pakai_obat_non_racikan'][$index] ?? null,
                        ]);
                    }
                }
                
                // SIMPAN DATA OBAT RACIKAN
                if (in_array('obat-racikan', $this->selected_forms_plan)) {
                    foreach ($this->obat_racikan['nama_racikan'] as $index => $namaRacikan) {
                        // 1. Buat racikan
                        $obatRacikan = ObatRacikanRM::create([
                            'rekam_medis_id' => $rekammedis->id,
                            'nama_racikan' => $namaRacikan,
                            'jumlah_racikan' => $this->obat_racikan['jumlah_racikan'][$index] ?? 1,
                            'satuan_racikan' => $this->obat_racikan['satuan_racikan'][$index] ?? null,
                            'aturan_pakai_racikan' => $this->obat_racikan['aturan_pakai_racikan'][$index] ?? null,
                            'metode_racikan' => $this->obat_racikan['metode_racikan'][$index] ?? null,
                        ]);

                        // 2. Buat bahan untuk racikan ini
                        if (!empty($this->bahan_racikan['nama_obat_racikan'][$index])) {
                            foreach ($this->bahan_racikan['nama_obat_racikan'][$index] as $bahanIndex => $namaBahan) {
                                $obatRacikan->bahanRacikan()->create([
                                    'nama_obat_racikan' => $namaBahan,
                                    'jumlah_obat_racikan' => $this->bahan_racikan['jumlah_obat_racikan'][$index][$bahanIndex] ?? 1,
                                    'satuan_obat_racikan' => $this->bahan_racikan['satuan_obat_racikan'][$index][$bahanIndex] ?? null,
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
