<?php

namespace App\Livewire\Pasien;

use App\Models\Pasien;
use Livewire\Component;
use App\Models\PelayananBundlingRM;
use App\Models\TreatmentBundlingRM;
use App\Models\ProdukObatBundlingRM;
use Illuminate\Support\Facades\Gate;

class Detail extends Component
{
    public $id;
    public $pasien;

    public $bundlingPasien = [
        'treatments' => [],
        'pelayanans' => [],
        'produks' => [],
    ];

    public function mount($id)
    {
        $this->id = $id;
        $this->pasien = Pasien::findOrFail($id);

        if ($this->pasien) {
            // Ambil treatment bundling pasien
            $this->bundlingPasien['treatments'] = TreatmentBundlingRM::with('bundling', 'treatment')
                ->where('pasien_id', $this->pasien->id)
                ->get();

            // Ambil pelayanan bundling pasien
            $this->bundlingPasien['pelayanans'] = PelayananBundlingRM::with('bundling', 'pelayanan')
                ->where('pasien_id', $this->pasien->id)
                ->get();

            // Ambil produk/obat bundling pasien
            $this->bundlingPasien['produks'] = ProdukObatBundlingRM::with('bundling', 'produk',)
                ->where('pasien_id', $this->pasien->id)
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.pasien.detail');
    }
}
