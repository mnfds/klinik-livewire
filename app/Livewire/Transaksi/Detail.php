<?php

namespace App\Livewire\Transaksi;

use Livewire\Component;
use App\Models\PasienTerdaftar;
use App\Models\RencanaLayananRM;
use App\Models\RencananaBundlingRM;
use App\Models\RencanaProdukRM;
use App\Models\RencanaTreatmentRM;

class Detail extends Component
{
    public ?int $pasien_terdaftar_id = null;
    public ?PasienTerdaftar $pasienTerdaftar = null;

    public $pasien;
    public $rekammedis_id;
    public $pelayanan;
    public $treatment;
    public $produk;
    public $bundling;

    public function mount($id)
    {
        $this->pasien_terdaftar_id = $id;

        // Ambil semua relasi penting dalam satu query
        $this->pasienTerdaftar = PasienTerdaftar::with([
            'pasien',
            'rekamMedis.rencanaLayananRM.pelayanan',
            'rekamMedis.rencanaTreatmentRM.treatment',
            'rekamMedis.rencanaProdukRM.produk',
            'rekamMedis.rencanaBundlingRM.bundling.treatmentBundlings',
            'rekamMedis.rencanaBundlingRM.bundling.pelayananBundlings',
            'rekamMedis.rencanaBundlingRM.bundling.produkObatBundlings',
        ])->findOrFail($this->pasien_terdaftar_id);

        // Simpan data pasien
        $this->pasien = $this->pasienTerdaftar->pasien;

        // Ambil rekam medis (jika ada)
        $rekamMedis = $this->pasienTerdaftar->rekamMedis;
        $this->rekammedis_id = $rekamMedis->id ?? null;

        // Jika ada rekam medis, ambil semua rencana dari relasi yang sudah di-eager load
        if ($rekamMedis) {
            $this->pelayanan = $rekamMedis->rencanaLayananRM ?? collect();
            $this->treatment = $rekamMedis->rencanaTreatmentRM ?? collect();
            $this->produk    = $rekamMedis->rencanaProdukRM ?? collect();
            $this->bundling  = $rekamMedis->rencanaBundlingRM ?? collect();
        } else {
            $this->pelayanan = $this->treatment = $this->produk = $this->bundling = collect();
        }
    }

    public function render()
    {
        return view('livewire.transaksi.detail');
    }
}
