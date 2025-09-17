<?php

namespace App\Livewire\Rekammedis;

use App\Models\Pasien;
use Livewire\Component;
use App\Models\KajianAwal;
use App\Models\RekamMedis;
use App\Models\PasienTerdaftar;

class Detail extends Component
{
    public ?int $pasien_terdaftar_id = null;
    public ?PasienTerdaftar $pasienTerdaftar = null;
    public ?Pasien $pasien = null;
    public $kajian = null;
    public $rekammedis = null;

    public function mount($pasien_terdaftar_id = null)
    {
        $this->pasien_terdaftar_id = $pasien_terdaftar_id;

        // 🔥 Eager load semua relasi dalam satu query
        $this->pasienTerdaftar = PasienTerdaftar::with([
            'pasien',
            'kajianAwal.pemeriksaanFisik',
            'kajianAwal.tandaVital',
            'kajianAwal.dataKesehatan',
            'kajianAwal.dataEstetika',
            'rekamMedis.dataKesehatanRM',
            'rekamMedis.dataEstetikaRM',
            'rekamMedis.tandaVitalRM',
            'rekamMedis.pemeriksaanFisikRM',
            'rekamMedis.pemeriksaanKulitRM',
            'rekamMedis.diagnosaRM',
            'rekamMedis.icdRM',
            'rekamMedis.rencanaLayananRM',
            'rekamMedis.rencanaTreatmentRM',
            'rekamMedis.rencanaProdukRM',
            'rekamMedis.rencanaLayananRM',
            'rekamMedis.rencanaBundlingRM',
            'rekamMedis.obatNonRacikanRM',
            'rekamMedis.obatRacikanRM',
        ])->find($this->pasien_terdaftar_id);

        // Simpan shortcut ke variabel agar gampang dipakai di blade
        $this->pasien     = $this->pasienTerdaftar?->pasien;
        $this->kajian     = $this->pasienTerdaftar?->kajianAwal;
        $this->rekammedis = $this->pasienTerdaftar?->rekamMedis;
    }

    public function render()
    {
        return view('livewire.rekammedis.detail', [
            'pasienTerdaftar' => $this->pasienTerdaftar,
            'pasien'          => $this->pasien,
            'kajian'          => $this->kajian,
            'rekammedis'      => $this->rekammedis,
        ]);
    }
}