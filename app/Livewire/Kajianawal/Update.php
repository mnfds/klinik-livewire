<?php

namespace App\Livewire\Kajianawal;

use App\Models\Pasien;
use App\Models\PasienTerdaftar;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Update extends Component
{
    public ?int $pasien_terdaftar_id = null;
    public ?PasienTerdaftar $pasienTerdaftar = null;
    public $kajian = null;

    // KAJIAN AWAL
    public $nama_pengkaji;

    // NAKES
    public $perawat;
    public $dokter;

    // PARAMETER SECTION
    public array $selected_forms = [];

    // --- TANDA VITAL ---
    public $suhu_tubuh, $nadi, $sistole, $diastole, $frekuensi_pernapasan;

    // --- PEMERIKSAAN FISIK ---
    public $tinggi_badan, $berat_badan, $imt;

    // --- DATA KESEHATAN ---
    public $keluhan_utama, $status_perokok;
    public array $riwayat_penyakit = [];
    public array $riwayat_alergi_obat = [];
    public array $obat_sedang_dikonsumsi = [];
    public array $riwayat_alergi_lainnya = [];

    // --- DATA ESTETIKA ---
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

    public function mount($pasien_terdaftar_id = null)
    {
        $this->perawat = User::whereHas('role', function ($query) {
            $query->where('nama_role', 'perawat');
        })->with('biodata')->get();

        $this->dokter = User::whereHas('role', function ($query) {
            $query->where('nama_role', 'dokter');
        })->with('dokter')->get();

        $this->pasien_terdaftar_id = $pasien_terdaftar_id;

        $this->pasienTerdaftar = PasienTerdaftar::with([
            'pasien',
            'poliklinik',
            'kajianAwal.tandaVital',
            'kajianAwal.pemeriksaanFisik',
            'kajianAwal.dataKesehatan',
            'kajianAwal.dataEstetika',
        ])->findOrFail($this->pasien_terdaftar_id);

        $this->kajian = $this->pasienTerdaftar->kajianAwal;

        // Pre-fill data kajian awal
        if ($this->kajian) {
            $this->nama_pengkaji = $this->kajian->nama_pengkaji;

            // Pre-fill tanda vital
            if ($this->kajian->tandaVital) {
                $this->selected_forms[] = 'tanda-vital';
                $this->suhu_tubuh           = $this->kajian->tandaVital->suhu_tubuh;
                $this->nadi                 = $this->kajian->tandaVital->nadi;
                $this->sistole              = $this->kajian->tandaVital->sistole;
                $this->diastole             = $this->kajian->tandaVital->diastole;
                $this->frekuensi_pernapasan = $this->kajian->tandaVital->frekuensi_pernapasan;
            }

            // Pre-fill pemeriksaan fisik
            if ($this->kajian->pemeriksaanFisik) {
                $this->selected_forms[] = 'pemeriksaan-fisik';
                $this->tinggi_badan = $this->kajian->pemeriksaanFisik->tinggi_badan;
                $this->berat_badan  = $this->kajian->pemeriksaanFisik->berat_badan;
                $this->imt          = $this->kajian->pemeriksaanFisik->imt;
            }

            // Pre-fill data kesehatan
            if ($this->kajian->dataKesehatan) {
                $this->selected_forms[]      = 'data-kesehatan';
                $this->keluhan_utama         = $this->kajian->dataKesehatan->keluhan_utama;
                $this->status_perokok        = $this->kajian->dataKesehatan->status_perokok;
                $this->riwayat_penyakit      = json_decode($this->kajian->dataKesehatan->riwayat_penyakit, true) ?? [];
                $this->riwayat_alergi_obat   = json_decode($this->kajian->dataKesehatan->riwayat_alergi_obat, true) ?? [];
                $this->riwayat_alergi_lainnya = json_decode($this->kajian->dataKesehatan->riwayat_alergi_lainnya, true) ?? [];
                $this->obat_sedang_dikonsumsi = json_decode($this->kajian->dataKesehatan->obat_sedang_dikonsumsi, true) ?? [];
            }

            // Pre-fill data estetika
            if ($this->kajian->dataEstetika) {
                $this->selected_forms[]    = 'data-estetika';
                $this->problem_dihadapi    = json_decode($this->kajian->dataEstetika->problem_dihadapi, true) ?? [];
                $this->lama_problem        = $this->kajian->dataEstetika->lama_problem;
                $this->tindakan_sebelumnya = json_decode($this->kajian->dataEstetika->tindakan_sebelumnya, true) ?? [];
                $this->penyakit_dialami    = $this->kajian->dataEstetika->penyakit_dialami;
                $this->alergi_kosmetik     = $this->kajian->dataEstetika->alergi_kosmetik;
                $this->sedang_hamil        = $this->kajian->dataEstetika->sedang_hamil;
                $this->usia_kehamilan      = $this->kajian->dataEstetika->usia_kehamilan;
                $this->metode_kb           = json_decode($this->kajian->dataEstetika->metode_kb, true) ?? [];
                $this->pengobatan_saat_ini = $this->kajian->dataEstetika->pengobatan_saat_ini;
                $this->produk_kosmetik     = $this->kajian->dataEstetika->produk_kosmetik;
            }
        }
    }

    public function update()
    {
        $rules = [
            'nama_pengkaji'       => 'required|string|max:255',
            'pasien_terdaftar_id' => 'required|exists:pasien_terdaftars,id',
        ];

        if (in_array('data-kesehatan', $this->selected_forms)) {
            $rules['keluhan_utama'] = 'required|string';
        }
        if (in_array('pemeriksaan-fisik', $this->selected_forms)) {
            $rules['tinggi_badan'] = 'required';
            $rules['berat_badan']  = 'required';
        }

        $this->validate($rules);

        if (! Gate::allows('akses', 'Kajian')) {
            $this->dispatch('toast', [
                'type'    => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        DB::beginTransaction();
        try {
            // Update kajian awal
            $this->kajian->update([
                'nama_pengkaji' => $this->nama_pengkaji,
            ]);

            // Update / create tanda vital
            if (in_array('tanda-vital', $this->selected_forms)) {
                $this->kajian->tandaVital()->updateOrCreate(
                    ['kajian_awal_id' => $this->kajian->id],
                    [
                        'suhu_tubuh'           => $this->suhu_tubuh,
                        'nadi'                 => $this->nadi,
                        'sistole'              => $this->sistole,
                        'diastole'             => $this->diastole,
                        'frekuensi_pernapasan' => $this->frekuensi_pernapasan,
                    ]
                );
            }

            // Update / create pemeriksaan fisik
            if (in_array('pemeriksaan-fisik', $this->selected_forms)) {
                $tinggi_meter = $this->tinggi_badan / 100;
                $imt = ($tinggi_meter > 0)
                    ? round($this->berat_badan / ($tinggi_meter * $tinggi_meter), 2)
                    : 0;

                $this->kajian->pemeriksaanFisik()->updateOrCreate(
                    ['kajian_awal_id' => $this->kajian->id],
                    [
                        'tinggi_badan' => $this->tinggi_badan,
                        'berat_badan'  => $this->berat_badan,
                        'imt'          => $imt,
                    ]
                );
            }

            // Update / create data kesehatan
            if (in_array('data-kesehatan', $this->selected_forms)) {
                $this->kajian->dataKesehatan()->updateOrCreate(
                    ['kajian_awal_id' => $this->kajian->id],
                    [
                        'keluhan_utama'          => $this->keluhan_utama,
                        'status_perokok'         => $this->status_perokok,
                        'riwayat_penyakit'       => json_encode($this->riwayat_penyakit),
                        'riwayat_alergi_obat'    => json_encode($this->riwayat_alergi_obat),
                        'riwayat_alergi_lainnya' => json_encode($this->riwayat_alergi_lainnya),
                        'obat_sedang_dikonsumsi' => json_encode($this->obat_sedang_dikonsumsi),
                    ]
                );
            }

            // Update / create data estetika
            if (in_array('data-estetika', $this->selected_forms)) {
                $this->kajian->dataEstetika()->updateOrCreate(
                    ['kajian_awal_id' => $this->kajian->id],
                    [
                        'problem_dihadapi'    => json_encode($this->problem_dihadapi),
                        'lama_problem'        => $this->lama_problem,
                        'tindakan_sebelumnya' => json_encode($this->tindakan_sebelumnya),
                        'penyakit_dialami'    => $this->penyakit_dialami,
                        'alergi_kosmetik'     => $this->alergi_kosmetik,
                        'sedang_hamil'        => $this->sedang_hamil,
                        'usia_kehamilan'      => $this->usia_kehamilan,
                        'metode_kb'           => json_encode($this->metode_kb),
                        'pengobatan_saat_ini' => $this->pengobatan_saat_ini,
                        'produk_kosmetik'     => $this->produk_kosmetik,
                    ]
                );
            }

            DB::commit();

            $this->dispatch('toast', [
                'type'    => 'success',
                'message' => 'Data Kajian Awal Berhasil Diperbarui',
            ]);

            return redirect()->route('pendaftaran.data');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toast', [
                'type'    => 'error',
                'message' => 'Gagal Memperbarui Data: ' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        if (! Gate::allows('akses', 'Kajian')) {
            session()->flash('toast', [
                'type'    => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }

        return view('livewire.kajianawal.update', [
            'pasienTerdaftar' => $this->pasienTerdaftar,
        ]);
    }
}
