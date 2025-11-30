<?php

namespace App\Livewire\Pendaftaran;

use App\Models\Dokter;
use App\Models\Pasien;
use Livewire\Component;
use App\Models\PoliKlinik;
use App\Models\NomorAntrian;
use Livewire\WithFileUploads;
use App\Models\PasienTerdaftar;
use App\Services\StoreEncounter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Create extends Component
{
    use WithFileUploads;

    public ?int $pasien_id = null;
    public ?Pasien $pasien = null;
    public ?int $antrian_id = null;
    public $antrian;
    public $poli;
    public $dokter;

    public $dokter_satusehat;
    public $pasien_satusehat;

    // Properti untuk input form
    public $no_register, $nik, $no_ihs, $nama, $alamat, $no_telp;
    public $jenis_kelamin, $agama, $profesi, $tanggal_lahir;
    public $status, $foto_pasien, $deskripsi;
    public $waktu_tiba;

    public $jenis_kunjungan, $tanggal_kunjungan, $status_terdaftar;
    public $poli_id, $dokter_id;
    public $foto_pasien_preview; //show

    public function mount($pasien_id = null, $antrian_id = null)
    {
        $this->pasien_id = $pasien_id;
        $this->antrian_id = $antrian_id;

        $this->poli = PoliKlinik::where('status', true)->get();
        $this->dokter = Dokter::all();
        if ($this->pasien_id) {
            
            $this->pasien = Pasien::find($this->pasien_id);

            if ($this->pasien) {
                $this->no_register     = $this->pasien->no_register;
                $this->nik             = $this->pasien->nik;
                $this->no_ihs          = $this->pasien->no_ihs;
                $this->nama            = $this->pasien->nama;
                $this->alamat          = $this->pasien->alamat;
                $this->no_telp         = $this->pasien->no_telp;
                $this->jenis_kelamin   = $this->pasien->jenis_kelamin;
                $this->agama           = $this->pasien->agama;
                $this->profesi         = $this->pasien->profesi;
                $this->tanggal_lahir   = $this->pasien->tanggal_lahir;
                $this->status          = $this->pasien->status;
                $this->foto_pasien_preview     = $this->pasien->foto_pasien;
                $this->deskripsi       = $this->pasien->deskripsi;
            }
        }
        if ($this->antrian_id) {
            $this->antrian = \App\Models\NomorAntrian::with('poli')->find($this->antrian_id);

            if ($this->antrian) {
                $this->poli_id = $this->antrian->poli_id;
                $this->tanggal_kunjungan = $this->antrian->created_at->toDateString(); // atau pakai now()->toDateString()
            }
        }
    }

    public function submit(StoreEncounter $encounterService)
    {
        $validatedData = $this->validate([
            'pasien_id'         => 'required',
            'poli_id'           => 'required',
            'dokter_id'         => 'required',
            'tanggal_kunjungan' => 'required|date',
            'jenis_kunjungan'   => 'required|in:sehat,sakit',
        ]);

        $this->waktu_tiba = Carbon::now('Asia/Makassar')->toIso8601String();
        $pasien_satusehat = Pasien::findOrFail($this->pasien_id);
        $dokter_satusehat = Dokter::findOrFail($this->dokter_id);
        $poli = PoliKlinik::findOrFail($this->poli_id);

        // Cek relasi organization & location tetap wajib
        if (!$poli->organization) {
            throw new \Exception("Poli belum memiliki organization_id.");
        }
        if (!$poli->location) {
            throw new \Exception("Poli belum memiliki location_id.");
        }

        $organisasi_satusehat = $poli->organization->id_satusehat;
        $location_satusehat = $poli->location;
        $tanggal_kunjungan = $this->tanggal_kunjungan;
        $waktu_tiba = $this->waktu_tiba;

        $encounterId = null; // default (tidak kirim ke API)

        // Kirim ke API HANYA jika punya IHS (pasien,dokter) ID SATUSEHAT (organization, location)
        $kirimDataKeSatusehat = 
            !empty($pasien_satusehat->no_ihs) &&
            !empty($dokter_satusehat->ihs) &&
            !empty($organisasi_satusehat) &&
            !empty($location_satusehat);

        if ($kirimDataKeSatusehat) {

            // Semua IHS tersedia → kirim Encounter ke API
            $encounterService = app(StoreEncounter::class);

            $encounterId = $encounterService->handle(
                $pasien_satusehat,
                $dokter_satusehat,
                $organisasi_satusehat,
                $location_satusehat,
                $tanggal_kunjungan,
                $waktu_tiba,
            );

        } else {

            // Jika tidak memenuhi, hanya beri info saja
            Log::info('Encounter tidak dikirim ke API karena IHS tidak lengkap.', [
                'pasien_ihs' => $pasien_satusehat->no_ihs,
                'dokter_ihs' => $dokter_satusehat->ihs,
                'org_ihs'    => $organisasi_satusehat,
                'loc_ihs'    => $location_satusehat,
            ]);
        }

        //  SIMPAN DATA LOKAL
        $success = PasienTerdaftar::create([
            'pasien_id'         => $this->pasien_id,
            'poli_id'           => $this->poli_id,
            'dokter_id'         => $this->dokter_id,
            'tanggal_kunjungan' => $this->tanggal_kunjungan,
            'waktu_tiba'        => $this->waktu_tiba,
            'jenis_kunjungan'   => $this->jenis_kunjungan,
            'status_terdaftar'  => 'terdaftar',
            'encounter_id'      => $encounterId, // null kalau tidak dikirim ke satusehat
        ]);

        if ($success && $this->antrian) {
            NomorAntrian::findOrFail($this->antrian->id)->update(['status' => 'nonaktif']);
        }

        if($kirimDataKeSatusehat){
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Pasien Terdaftar Dan Kirim Satu Sehat'
            ]);
        }else{
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Pasien Terdaftar'
            ]);
        }

        return redirect()->route('pendaftaran.data');
    }

    public function render()
    {
        return view('livewire.pendaftaran.create');
    }
}
