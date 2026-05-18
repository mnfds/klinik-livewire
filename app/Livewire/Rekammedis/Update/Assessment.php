<?php

namespace App\Livewire\Rekammedis\Update;

use App\Models\PasienTerdaftar;
use App\Models\DiagnosaRM;
use App\Models\IcdRM;
use App\Models\RekamMedis;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Assessment extends Component
{
    public $rekammedis;
    public ?int $pasien_terdaftar_id = null;
    public ?PasienTerdaftar $pasienTerdaftar = null;
    public ?int $rekam_medis_id = null;
    public $rekamMedisLama; // simpan instance RM lama
    public ?int $pasien_id = null;

    //ASSESSMENT
        public $diagnosa;
        public $icd10 = [];
    //ASSESSMENT

    public function render()
    {
        return view('livewire.rekammedis.update.assessment');
    }

    public function mount($rekam_medis_id = null)
    {
        $this->rekam_medis_id = $rekam_medis_id;

        $rm = RekamMedis::with([
            'pasienTerdaftar.pasien',
            'pasienTerdaftar.dokter',
            'pasienTerdaftar.poliklinik',
            'diagnosaRM',
            'icdRM',
        ])->findOrFail($rekam_medis_id);

        $this->rekamMedisLama = $rm;
        $this->pasien_terdaftar_id = $rm->pasien_terdaftar_id;
        $this->pasienTerdaftar = $rm->pasienTerdaftar;
        $this->pasien_id = $rm->pasienTerdaftar->pasien_id;

        // Assessment: ICD10
        if ($rm->icdRM->isNotEmpty()) {
            $this->icd10 = $rm->icdRM->map(fn($i) => [
                'code'    => $i->code,
                'name_id' => $i->name_id,
                'name_en' => $i->name_en,
            ])->toArray();
        }

        // Assessment: Diagnosa
        if ($rm->diagnosaRM) {
            $this->diagnosa = $rm->diagnosaRM->diagnosa;
        }
    }

    public function updateAssessment()
    {
        // ── VALIDASI ─────────────────────────────────────────────
        $rules = [
            'pasien_terdaftar_id' => 'required|exists:pasien_terdaftars,id',
        ];

        $this->validate($rules);

        // ── AMBIL DATA SEBELUM TRANSACTION ───────────────────────
        $rm = RekamMedis::findOrFail($this->rekam_medis_id);

        DB::beginTransaction();
        try {
            // ── ICD 10 ──────────────────────────────────────
            IcdRM::where('rekam_medis_id', $rm->id)->delete();
            foreach ($this->icd10 as $item) {
                if (empty($item['code'])) continue;
                IcdRM::create([
                    'rekam_medis_id' => $rm->id,
                    'code'           => $item['code'],
                    'name_id'        => $item['name_id'],
                    'name_en'        => $item['name_en'],
                ]);
            }

            // ── DIAGNOSA ────────────────────────────────
            DiagnosaRM::updateOrCreate(
                ['rekam_medis_id' => $rm->id],
                ['diagnosa' => $this->diagnosa]
            );

            DB::commit();

            $this->dispatch('toast', [
                'type'    => 'success',
                'message' => 'Assessment rekam medis berhasil diperbarui.',
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
