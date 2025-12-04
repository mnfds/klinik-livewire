<?php

namespace App\Livewire\Kajianawal;

use App\Models\User;
use Livewire\Component;
use App\Models\KajianAwal;
use App\Models\TandaVital;
use App\Models\DataEstetika;
use App\Models\DataKesehatan;
use Illuminate\Support\Carbon;
use App\Models\PasienTerdaftar;
use App\Models\PemeriksaanFisik;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\PutInProgressEncounter;
use App\Services\StorePemeriksaanFisik;
use App\Services\StoreVitalSign;

class Create extends Component
{
    public ?int $pasien_terdaftar_id = null;
    public ?PasienTerdaftar $pasienTerdaftar = null;
    
    // ENCOUNTER ID DARI SATUSEHAT
    public $encounter_id;

    // KAJIAN AWAL //
    public $nama_pengkaji, $id_pasien_terdaftar;

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
    // --- DATA ESTETIKA --- //
    public $problem_dihadapi = [];
    public $lama_problem;
    public $tindakan_sebelumnya = [];
    public $penyakit_dialami;
    public $alergi_kosmetik;

    public $sedang_hamil;
    public $usia_kehamilan;

    public $metode_kb = [];
    public $pengobatan_saat_ini;
    public $produk_kosmetik;
    
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

    public function create()
    {
        $this->validate([
            'nama_pengkaji' => 'required|string|max:255',
            'pasien_terdaftar_id' => 'required|exists:pasien_terdaftars,id',
        ]);

        DB::beginTransaction();

        try {
            $kajianawal = KajianAwal::create([
                'nama_pengkaji' => $this->nama_pengkaji,
                'pasien_terdaftar_id' => $this->pasien_terdaftar_id,
            ]);

            PasienTerdaftar::findOrFail($this->pasien_terdaftar_id)
            ->update(['status_terdaftar' => 'konsultasi']);
            
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
            }

            // Simpan data tanda vital
            if (in_array('tanda-vital', $this->selected_forms)) {
                // if($kirimsatusehat){
                    
                //     $PostVitalSign = app(StoreVitalSign::class);
                //     $observation = $PostVitalSign->handle(
                //         encounterId: $encounterId,
                //         pasienNama: $pt->pasien->nama,
                //         pasienIhs: $pt->pasien->no_ihs,
                //         dokterNama: $pt->dokter->nama_dokter,
                //         dokterIhs: $pt->dokter->ihs,
                //         waktuTiba: $pt->waktu_tiba,
                //         sistole: $this->sistole,
                //         diastole: $this->diastole,
                //         suhu_tubuh: $this->suhu_tubuh,
                //         nadi: $this->nadi,
                //         pernapasan: $this->frekuensi_pernapasan,
                //     );
                // }
                TandaVital::create([
                    'kajian_awal_id' => $kajianawal->id,
                    'suhu_tubuh' => $this->suhu_tubuh,
                    'nadi' => $this->nadi,
                    'sistole' => $this->sistole,
                    'diastole' => $this->diastole,
                    'frekuensi_pernapasan' => $this->frekuensi_pernapasan,
                ]);
            }

            // Simpan data pemeriksaan fisik
            if (in_array('pemeriksaan-fisik', $this->selected_forms)) {
                // if($kirimsatusehat){
                    
                //     $PostPemeriksaanFisik = app(StorePemeriksaanFisik::class);
                //     $observationFisik = $PostPemeriksaanFisik->handle(
                //         encounterId: $encounterId,
                //         pasienNama: $pt->pasien->nama,
                //         pasienIhs: $pt->pasien->no_ihs,
                //         dokterNama: $pt->dokter->nama_dokter,
                //         dokterIhs: $pt->dokter->ihs,
                //         waktuTiba: $pt->waktu_tiba,
                //         tinggiBadan: $this->tinggi_badan,
                //         beratBadan: $this->berat_badan,
                //     );
                // }
                PemeriksaanFisik::create([
                    'kajian_awal_id' => $kajianawal->id,
                    'tinggi_badan' => $this->tinggi_badan,
                    'berat_badan' => $this->berat_badan,
                    'imt' => $this->imt,
                ]);
            }

            // Simpan data kesehatan
            if (in_array('data-kesehatan', $this->selected_forms)) {
                DataKesehatan::create([
                    'kajian_awal_id' => $kajianawal->id,
                    'keluhan_utama' => $this->keluhan_utama,
                    'status_perokok' => $this->status_perokok,
                    'riwayat_penyakit' => json_encode($this->riwayat_penyakit),
                    'riwayat_alergi_obat' => json_encode($this->riwayat_alergi_obat),
                    'riwayat_alergi_lainnya' => json_encode($this->riwayat_alergi_lainnya),
                    'obat_sedang_dikonsumsi' => json_encode($this->obat_sedang_dikonsumsi),
                ]);
            }

            // Simpan data estetika
            if (in_array('data-estetika', $this->selected_forms)) {
                DataEstetika::create([
                    'kajian_awal_id' => $kajianawal->id,
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

            DB::commit();
            if($kirimsatusehat){
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Data Medis Berhasil tersimpan Dan Kirim Satu Sehat'
                ]);
            }else{
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Data Medis Berhasil Tersimpan'
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

        return view('livewire.kajianawal.create');
    }
}
