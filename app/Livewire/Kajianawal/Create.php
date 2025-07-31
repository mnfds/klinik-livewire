<?php

namespace App\Livewire\Kajianawal;

use Livewire\Component;
use App\Models\PasienTerdaftar;
use Illuminate\Support\Facades\Log;

class Create extends Component
{
    public ?int $pasien_terdaftar_id = null;
    public ?PasienTerdaftar $pasienTerdaftar = null;
    public $pemeriksaan_fisik;
    public $tanda_vital;
    public $data_kesehatan;

    // PASIEN TERDAFTAR //
    public $pasien_id, $poli_id, $dokter_id, $jenis_kunjungan, $tanggal_kunjungan;
    public $status_terdaftar;

    // INFORMASI PASIEN //
    public $nomor_ihs, $nik, $no_register, $nama_pasien, $tanggal_lahir, $jenis_kelamin;

    // POLIKLINIK //
    public $nama_poli, $kode, $status;

    // DOKTER //
    public $nama_dokter, $ttd_digital;

    //***DINAMIS FORM VARIABEL***//
    
    // --- TANDA VITAL -- //
    public $suhu_tubuh, $nadi, $sistole, $diastole, $frekuensi_pernapasan;
    // --- PEMERIKSAAN FISIK -- //
    public $tinggi_badan, $berat_badan, $imt; //imt = berat_badan dibagi tinggi_badan pangkat 2
    // --- DATA KESEHATAN --- //
    public $keluhan_utama, $status_perokok;
    public array $riwayat_penyakit = [];
    public array $riwayat_alergi_obat = [];
    public array $obat_sedang_dikonsumsi = [];
    public array $riwayat_alergi_lainnya = [];
    
    //***DINASMIS FORM VARIABEL***/

    public function mount($pasien_terdaftar_id = null)
    {
        $this->pasien_terdaftar_id = $pasien_terdaftar_id;

        if ($this->pasien_terdaftar_id) {
            $this->pasienTerdaftar = PasienTerdaftar::findOrFail($this->pasien_terdaftar_id);
        }
    }

    public function create(){
        dd([
            $this->suhu_tubuh,
            $this->nadi,
            $this->sistole,
            $this->diastole,
            $this->frekuensi_pernapasan,
            $this->tinggi_badan,
            $this->berat_badan,
            $this->imt,
            $this->keluhan_utama,
            $this->status_perokok,
            $this->riwayat_penyakit,
            $this->riwayat_alergi_obat,
            $this->riwayat_alergi_lainnya,
            $this->obat_sedang_dikonsumsi,
        ]);
    }

    public function render()
    {

        return view('livewire.kajianawal.create');
    }
}
