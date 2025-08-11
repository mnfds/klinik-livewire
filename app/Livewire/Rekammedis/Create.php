<?php

namespace App\Livewire\Rekammedis;

use App\Models\PasienTerdaftar;
use App\Models\KajianAwal;
use Livewire\Component;
use Livewire\Volt\Compilers\Mount;

class Create extends Component
{
    public ?int $pasien_terdaftar_id = null;
    public ?PasienTerdaftar $pasienTerdaftar = null;
    public $kajian;

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

    public function mount($pasien_terdaftar_id = null)
    {
        $this->pasien_terdaftar_id = $pasien_terdaftar_id;

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
        dd([
            $this->pemeriksaan_fisik,
            $this->tanda_vital,
            $this->data_kesehatan,
            $this->tingkat_kesadaran,
            $this->diagnosa,
            $this->icd10,
            $this->data_estetika,
            $this->pemeriksaan_estetika,
        ]);
    }

    public function render()
    {
        return view('livewire.rekammedis.create');
    }
}
