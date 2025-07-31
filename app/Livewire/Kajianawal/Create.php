<?php

namespace App\Livewire\Kajianawal;

use App\Models\User;
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

    // NAKES //
    public $perawat;
    public $dokter;

    // PARAMETER SECTION //
    public array $selected_forms = [];

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
        $this->perawat = User::whereHas('role', function ($query) {
            $query->where('nama_role', 'perawat');
        })->with('biodata')->get();
        
        $this->dokter = User::whereHas('role', function ($query) {
            $query->where('nama_role', 'dokter');
        })->with('dokter')->get();

        $this->pasien_terdaftar_id = $pasien_terdaftar_id;

        if ($this->pasien_terdaftar_id) {
            $this->pasienTerdaftar = PasienTerdaftar::findOrFail($this->pasien_terdaftar_id);
        }

    }

    public function create(){
        if (in_array('tanda-vital', $this->selected_forms)) {
            Log::info('Data Tanda Vital:', [
                'suhu_tubuh' => $this->suhu_tubuh,
                'nadi' => $this->nadi,
                'sistole' => $this->sistole,
                'diastole' => $this->diastole,
                'frekuensi_pernapasan' => $this->frekuensi_pernapasan,
            ]);
        }

        if (in_array('pemeriksaan-fisik', $this->selected_forms)) {
            Log::info('Data Pemeriksaan Fisik:', [
                'tinggi_badan' => $this->tinggi_badan,
                'berat_badan' => $this->berat_badan,
                'imt' => $this->imt,
            ]);
        }

        if (in_array('data-kesehatan', $this->selected_forms)) {
            Log::info('Data Kesehatan:', [
                'keluhan_utama' => $this->keluhan_utama,
                'status_perokok' => $this->status_perokok,
                'riwayat_penyakit' => $this->riwayat_penyakit,
                'riwayat_alergi_obat' => $this->riwayat_alergi_obat,
                'riwayat_alergi_lainnya' => $this->riwayat_alergi_lainnya,
                'obat_sedang_dikonsumsi' => $this->obat_sedang_dikonsumsi,
            ]);
        }
    }

    public function render()
    {

        return view('livewire.kajianawal.create');
    }
}
