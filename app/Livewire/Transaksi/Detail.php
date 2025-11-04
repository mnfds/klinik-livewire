<?php

namespace App\Livewire\Transaksi;

use Livewire\Component;
use App\Models\PasienTerdaftar;
use App\Models\RencanaProdukRM;
use App\Models\ObatRacikanFinal;
use App\Models\RencanaLayananRM;
use App\Models\RencanaTreatmentRM;
use App\Models\ObatNonRacikanFinal;
use App\Models\RencananaBundlingRM;

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
    }

    public function render()
    {
        return view('livewire.transaksi.detail');
    }

    public function create(){
        $nonRacikanIds = $this->selectedObat;
        $racikanIds = $this->selectedRacikan;

        // Update kolom konfirmasi menjadi 'terkonfirmasi'
        if (!empty($nonRacikanIds)) {
            ObatNonRacikanFinal::whereIn('id', $nonRacikanIds)
                ->update(['konfirmasi' => 'terkonfirmasi']);
        }

        if (!empty($racikanIds)) {
            ObatRacikanFinal::whereIn('id', $racikanIds)
                ->update(['konfirmasi' => 'terkonfirmasi']);
        }

        PasienTerdaftar::findOrFail($this->pasien_terdaftar_id)->update(['status_terdaftar' => 'lunas']);
        
        $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Transaksi Selesai.'
        ]);

        $this->reset();

        return redirect()->route('transaksi.kasir');
    }
}
