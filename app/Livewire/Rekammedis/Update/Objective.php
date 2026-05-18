<?php

namespace App\Livewire\Rekammedis\Update;

use App\Models\PasienTerdaftar;
use App\Models\PemeriksaanFisikRM;
use App\Models\PemeriksaanKulitRM;
use App\Models\RekamMedis;
use App\Models\TandaVitalRM;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Objective extends Component
{
    public $rekammedis;
    public $tingkat_kesadaran;
    public ?int $pasien_terdaftar_id = null;
    public ?PasienTerdaftar $pasienTerdaftar = null;
    public ?int $rekam_medis_id = null;
    public $rekamMedisLama; // simpan instance RM lama
    public array $selected_forms_objective = [];
    public ?int $pasien_id = null;

    //OBJECTIVE
        public $pemeriksaan_fisik = [
            'tinggi_badan' => null,
            'berat_badan' => null,
            'imt' => null,
        ];
        public $pemeriksaan_estetika = [
            'warna_kulit' => null,
            'ketebalan_kulit' => null,
            'kadar_minyak' => null,
            'kerapuhan_kulit' => null,
            'kekencangan_kulit' => null,
            'melasma' => null,
            'acne' => [],
            'lesions' => [],
        ];
        public $tanda_vital = [
            'suhu_tubuh' => null,
            'nadi' => null,
            'sistole' => null,
            'diastole' => null,
            'frekuensi_pernapasan' => null,
        ];
    //OBJECTIVE

    public function render()
    {
        return view('livewire.rekammedis.update.objective');
    }

    public function mount($rekam_medis_id = null)
    {
        $this->rekam_medis_id = $rekam_medis_id;

        $rm = RekamMedis::with([
            'pasienTerdaftar.pasien',
            'pasienTerdaftar.dokter',
            'pasienTerdaftar.poliklinik',
            'tandaVitalRM',
            'pemeriksaanFisikRM',
            'pemeriksaanKulitRM',
        ])->findOrFail($rekam_medis_id);

        $this->rekamMedisLama = $rm;
        $this->pasien_terdaftar_id = $rm->pasien_terdaftar_id;
        $this->pasienTerdaftar = $rm->pasienTerdaftar;
        $this->pasien_id = $rm->pasienTerdaftar->pasien_id;
        $this->tingkat_kesadaran = $rm->tingkat_kesadaran;

        // Objective: Tanda Vital
        if ($rm->tandaVitalRM) {
            $this->selected_forms_objective[] = 'tanda-vital';
            $tv = $rm->tandaVitalRM;
            $this->tanda_vital = [
                'suhu_tubuh'           => $tv->suhu_tubuh,
                'nadi'                 => $tv->nadi,
                'sistole'              => $tv->sistole,
                'diastole'             => $tv->diastole,
                'frekuensi_pernapasan' => $tv->frekuensi_pernapasan,
            ];
        }

        // Objective: Pemeriksaan Fisik
        if ($rm->pemeriksaanFisikRM) {
            $this->selected_forms_objective[] = 'pemeriksaan-fisik';
            $pf = $rm->pemeriksaanFisikRM;
            $this->pemeriksaan_fisik = [
                'tinggi_badan' => $pf->tinggi_badan,
                'berat_badan'  => $pf->berat_badan,
                'imt'          => $pf->imt,
            ];
        }

        // Objective: Pemeriksaan Estetika/Kulit
        if ($rm->pemeriksaanKulitRM) {
            $this->selected_forms_objective[] = 'pemeriksaan-estetika';
            $pk = $rm->pemeriksaanKulitRM;
            $this->pemeriksaan_estetika = [
                'warna_kulit'       => $pk->warna_kulit,
                'ketebalan_kulit'   => $pk->ketebalan_kulit,
                'kadar_minyak'      => $pk->kadar_minyak,
                'kerapuhan_kulit'   => $pk->kerapuhan_kulit,
                'kekencangan_kulit' => $pk->kekencangan_kulit,
                'melasma'           => $pk->melasma,
                'acne'              => json_decode($pk->acne ?? '[]', true),
                'lesions'           => json_decode($pk->lesions ?? '[]', true),
            ];
        }
    }

    public function updateObjective()
    {
        // ── VALIDASI ─────────────────────────────────────────────
        $rules = [
            'pasien_terdaftar_id' => 'required|exists:pasien_terdaftars,id',
            'tingkat_kesadaran'   => 'required|string',
        ];

        $this->validate($rules);

        // ── HITUNG IMT OTOMATIS ──────────────────────────────────
        if (in_array('pemeriksaan-fisik', $this->selected_forms_objective)) {
            $tb = $this->pemeriksaan_fisik['tinggi_badan'] / 100; // cm → m
            $bb = $this->pemeriksaan_fisik['berat_badan'];
            $this->pemeriksaan_fisik['imt'] = $tb > 0
                ? round($bb / ($tb * $tb), 2)
                : null;
        }

        // ── AMBIL DATA SEBELUM TRANSACTION ───────────────────────
        $rm = RekamMedis::findOrFail($this->rekam_medis_id);

        DB::beginTransaction();
        try {
            // ── UPDATE REKAM MEDIS UTAMA ─────────────────────────
            $rm->update([
                'tingkat_kesadaran' => $this->tingkat_kesadaran,
            ]);

            // ── TANDA VITAL ──────────────────────────────────────
            if (in_array('tanda-vital', $this->selected_forms_objective)) {
                TandaVitalRM::updateOrCreate(
                    ['rekam_medis_id' => $rm->id],
                    [
                        'suhu_tubuh'           => $this->tanda_vital['suhu_tubuh'],
                        'nadi'                 => $this->tanda_vital['nadi'],
                        'sistole'              => $this->tanda_vital['sistole'],
                        'diastole'             => $this->tanda_vital['diastole'],
                        'frekuensi_pernapasan' => $this->tanda_vital['frekuensi_pernapasan'],
                    ]
                );
            } else {
                // User uncheck → hapus data lama
                TandaVitalRM::where('rekam_medis_id', $rm->id)->delete();
            }

            // ── PEMERIKSAAN FISIK ────────────────────────────────
            if (in_array('pemeriksaan-fisik', $this->selected_forms_objective)) {
                PemeriksaanFisikRM::updateOrCreate(
                    ['rekam_medis_id' => $rm->id],
                    [
                        'tinggi_badan' => $this->pemeriksaan_fisik['tinggi_badan'],
                        'berat_badan'  => $this->pemeriksaan_fisik['berat_badan'],
                        'imt'          => $this->pemeriksaan_fisik['imt'],
                    ]
                );
            } else {
                PemeriksaanFisikRM::where('rekam_medis_id', $rm->id)->delete();
            }

            // ── PEMERIKSAAN ESTETIKA ─────────────────────────────
            if (in_array('pemeriksaan-estetika', $this->selected_forms_objective)) {
                PemeriksaanKulitRM::updateOrCreate(
                    ['rekam_medis_id' => $rm->id],
                    [
                        'warna_kulit'       => $this->pemeriksaan_estetika['warna_kulit'],
                        'ketebalan_kulit'   => $this->pemeriksaan_estetika['ketebalan_kulit'],
                        'kadar_minyak'      => $this->pemeriksaan_estetika['kadar_minyak'],
                        'kerapuhan_kulit'   => $this->pemeriksaan_estetika['kerapuhan_kulit'],
                        'kekencangan_kulit' => $this->pemeriksaan_estetika['kekencangan_kulit'],
                        'melasma'           => $this->pemeriksaan_estetika['melasma'],
                        'acne'              => json_encode($this->pemeriksaan_estetika['acne'] ?? []),
                        'lesions'           => json_encode($this->pemeriksaan_estetika['lesions'] ?? []),
                    ]
                );
            } else {
                PemeriksaanKulitRM::where('rekam_medis_id', $rm->id)->delete();
            }

            DB::commit();

            $this->dispatch('toast', [
                'type'    => 'success',
                'message' => 'Objective rekam medis berhasil diperbarui.',
            ]);

            return redirect()->route('pendaftaran.data');

        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('toast', [
                'type'    => 'error',
                'message' => 'Gagal memperbarui data: ' . $e->getMessage(),
            ]);
        }
    }
}
