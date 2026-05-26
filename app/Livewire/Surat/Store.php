<?php

namespace App\Livewire\Surat;

use App\Models\PasienTerdaftar;
use App\Models\PoliKlinik;
use Carbon\Carbon;
use Livewire\Component;

class Store extends Component
{

    public $no_surat, $mulai_berlaku, $selesai_berlaku, $tipe_ttd, $sakit; // DATA SURAT
    public $pasien_id, $poli_id, $dokter_id, $tanggal_kunjungan, $waktu_tiba, $jenis_kunjungan, $status_terdaftar; // DATA PASIEN TERDAFTAR

    public function mount(){
        $this->no_surat = 101;
    }

    public function render()
    {
        return view('livewire.surat.store');
    }

    public function store()
    {
        $this->pasienTerdaftarCreate();
        dd(
            $this->pasien_id,
            $this->mulai_berlaku,
            $this->selesai_berlaku,
            $this->tipe_ttd,
            $this->no_surat,
            $this->tipe_surat,
        );
    }

    public function pasienTerdaftarCreate()
    {
        $this->poli_id = PoliKlinik::where('kode','UMM')->value('id');
        $this->tanggal_kunjungan = now()->setTimezone('Asia/Makassar');
        $this->waktu_tiba = Carbon::now('Asia/Makassar')->setTimezone('UTC')->toIso8601String();
        $this->jenis_kunjungan = $this->tipe_surat;
        $this->status_terdaftar = "pembayaran";
        PasienTerdaftar::create([
            'pasien_id'         => $this->pasien_id,
            'poli_id'           => $this->poli_id->id,
            'dokter_id'         => $this->dokter_id,
            'tanggal_kunjungan' => $this->tanggal_kunjungan,
            'waktu_tiba'        => $this->waktu_tiba,
            'jenis_kunjungan'   => $this->jenis_kunjungan,
            'status_terdaftar'  => $this->status_terdaftar,
            'encounter_id'      => null, // null kalau tidak dikirim ke satusehat
        ]);
    }
}
