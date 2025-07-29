<?php

namespace App\Livewire\Kajianawal;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\PasienTerdaftar;

class Create extends Component
{
    public ?int $pasien_terdaftar_id = null;
    public ?PasienTerdaftar $pasienTerdaftar = null;

    public $pengkaji_id;
    public ?string $tanda_vital = null;
    public ?string $pemeriksaan_fisik = null;

    protected $listeners = [
        'tandaVitalUpdated' => 'updateTandaVital',
        'pemeriksaanFisikUpdated' => 'updatePemeriksaanFisik',
    ];

    public function mount($pasien_terdaftar_id = null)
    {
        $this->pasien_terdaftar_id = $pasien_terdaftar_id;

        if ($this->pasien_terdaftar_id) {
            $this->pasienTerdaftar = PasienTerdaftar::findOrFail($pasien_terdaftar_id);
        }
    }

    public function updateTandaVital($tanda_vital)
    {
        logger('Tanda Vital diterima di parent: ' . $tanda_vital);
        $this->tanda_vital = $tanda_vital;
    }

    public function updatePemeriksaanFisik($pemeriksaan_fisik)
    {
        logger('Pemeriksaan fisik diterima di parent: ' . $pemeriksaan_fisik);
        $this->pemeriksaan_fisik = $pemeriksaan_fisik;
    }

    public function submit()
    {
        dd([
            'tanda_vital' => $this->tanda_vital,
            'pemeriksaan_fisik' => $this->pemeriksaan_fisik,
        ]);
    }

    public function render()
    {
        return view('livewire.kajianawal.create');
    }
}
