<?php

namespace App\Livewire\Rekammedis\Update;

use App\Models\PasienTerdaftar;
use App\Models\DataEstetikaRM;
use App\Models\DataKesehatanRM;
use App\Models\RekamMedis;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Subjective extends Component
{
    public $rekammedis;
    public $keluhan_utama;
    public ?int $pasien_terdaftar_id = null;
    public ?PasienTerdaftar $pasienTerdaftar = null;
    public ?int $rekam_medis_id = null;
    public $rekamMedisLama; // simpan instance RM lama
    public array $selected_forms_subjective = [];
    public ?int $pasien_id = null;

    //SUBJECTIVE
        public $data_kesehatan = [
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
    //SUBJECTIVE

    public function render()
    {
        return view('livewire.rekammedis.update.subjective');
    }

    public function mount($rekam_medis_id = null)
    {
        $this->rekam_medis_id = $rekam_medis_id;

        $rm = RekamMedis::with([
            'pasienTerdaftar.pasien',
            'pasienTerdaftar.dokter',
            'pasienTerdaftar.poliklinik',
            'dataKesehatanRM',
            'dataEstetikaRM',
        ])->findOrFail($rekam_medis_id);

        $this->rekamMedisLama = $rm;
        $this->pasien_terdaftar_id = $rm->pasien_terdaftar_id;
        $this->pasienTerdaftar = $rm->pasienTerdaftar;
        $this->pasien_id = $rm->pasienTerdaftar->pasien_id;
        $this->keluhan_utama = $rm->keluhan_utama;

        // Subjective: Data Kesehatan
        if ($rm->dataKesehatanRM) {
            $this->selected_forms_subjective[] = 'data-kesehatan';
            $dk = $rm->dataKesehatanRM;
            $this->data_kesehatan = [
                'status_perokok'         => $dk->status_perokok,
                'riwayat_penyakit'       => json_decode($dk->riwayat_penyakit ?? '[]', true),
                'riwayat_alergi_obat'    => json_decode($dk->riwayat_alergi_obat ?? '[]', true),
                'riwayat_alergi_lainnya' => json_decode($dk->riwayat_alergi_lainnya ?? '[]', true),
                'obat_sedang_dikonsumsi' => json_decode($dk->obat_sedang_dikonsumsi ?? '[]', true),
            ];
        }

        // Subjective: Data Estetika
        if ($rm->dataEstetikaRM) {
            $this->selected_forms_subjective[] = 'data-estetika';
            $de = $rm->dataEstetikaRM;
            $this->data_estetika = [
                'problem_dihadapi'    => json_decode($de->problem_dihadapi ?? '[]', true),
                'lama_problem'        => $de->lama_problem,
                'tindakan_sebelumnya' => json_decode($de->tindakan_sebelumnya ?? '[]', true),
                'penyakit_dialami'    => $de->penyakit_dialami,
                'alergi_kosmetik'     => $de->alergi_kosmetik,
                'sedang_hamil'        => $de->sedang_hamil,
                'usia_kehamilan'      => $de->usia_kehamilan,
                'metode_kb'           => json_decode($de->metode_kb ?? '[]', true),
                'pengobatan_saat_ini' => $de->pengobatan_saat_ini,
                'produk_kosmetik'     => $de->produk_kosmetik,
            ];
        }
    }

    public function updateSubjective()
    {
        // ── VALIDASI ─────────────────────────────────────────────
        $rules = [
            'pasien_terdaftar_id' => 'required|exists:pasien_terdaftars,id',
            'keluhan_utama'   => 'required|string',
        ];

        $this->validate($rules);

        // ── AMBIL DATA SEBELUM TRANSACTION ───────────────────────
        $rm = RekamMedis::findOrFail($this->rekam_medis_id);

        DB::beginTransaction();
        try {
            // ── UPDATE REKAM MEDIS UTAMA ─────────────────────────
            $rm->update([
                'keluhan_utama' => $this->keluhan_utama,
            ]);

            // ── DATA KESEHATAN ──────────────────────────────────────
            if (in_array('data-kesehatan', $this->selected_forms_subjective)) {
                DataKesehatanRM::updateOrCreate(
                    ['rekam_medis_id' => $rm->id],
                    [
                        'status_perokok' => $this->data_kesehatan['status_perokok'],
                        'riwayat_penyakit' => json_encode($this->data_kesehatan['riwayat_penyakit']),
                        'riwayat_alergi_obat' => json_encode($this->data_kesehatan['riwayat_alergi_obat']),
                        'riwayat_alergi_lainnya' => json_encode($this->data_kesehatan['riwayat_alergi_lainnya']),
                        'obat_sedang_dikonsumsi' => json_encode($this->data_kesehatan['obat_sedang_dikonsumsi']),
                    ]
                );
            } else {
                // User uncheck → hapus data lama
                DataKesehatanRM::where('rekam_medis_id', $rm->id)->delete();
            }

            // ── DATA ESTETIKA ────────────────────────────────
            if (in_array('data-estetika', $this->selected_forms_subjective)) {
                DataEstetikaRM::updateOrCreate(
                    ['rekam_medis_id' => $rm->id],
                    [
                        'problem_dihadapi' => json_encode($this->data_estetika['problem_dihadapi']),
                        'lama_problem' => $this->data_estetika['lama_problem'],
                        'tindakan_sebelumnya' => json_encode($this->data_estetika['tindakan_sebelumnya']),
                        'penyakit_dialami' => $this->data_estetika['penyakit_dialami'],
                        'alergi_kosmetik' => $this->data_estetika['alergi_kosmetik'],
                        'sedang_hamil' => $this->data_estetika['sedang_hamil'],
                        'usia_kehamilan' => $this->data_estetika['usia_kehamilan'],
                        'metode_kb' => json_encode($this->data_estetika['metode_kb']),
                        'pengobatan_saat_ini' => $this->data_estetika['pengobatan_saat_ini'],
                        'produk_kosmetik' => $this->data_estetika['produk_kosmetik'],
                    ]
                );
            } else {
                DataEstetikaRM::where('rekam_medis_id', $rm->id)->delete();
            }

            DB::commit();

            $this->dispatch('toast', [
                'type'    => 'success',
                'message' => 'Subjective rekam medis berhasil diperbarui.',
            ]);

            return redirect()->route('rekam-medis-pasien.detail', ['pasien_terdaftar_id' => $this->pasien_terdaftar_id]);

        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('toast', [
                'type'    => 'error',
                'message' => 'Gagal memperbarui data: ' . $e->getMessage(),
            ]);
        }
    }
}
