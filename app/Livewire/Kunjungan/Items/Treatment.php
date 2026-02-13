<?php

namespace App\Livewire\Kunjungan\Items;

use App\Models\RencanaTreatmentRM;
use Livewire\Component;

use App\Models\RiwayatTransaksiKlinik;
use Illuminate\Support\Facades\DB;
class Treatment extends Component
{
    public $topTreatment = [];

    public function mount()
    {
        // 1️⃣ Treatment langsung
        $directTreatment = DB::table('rencana_treatment_r_m_s')
            ->select(
                'treatments_id',
                DB::raw('SUM(COALESCE(jumlah_treatment,0)) as total')
            )
            ->groupBy('treatments_id');

        // 2️⃣ Treatment dari bundling
        $bundlingTreatment = DB::table('treatment_bundling_r_m_s')
            ->select(
                'treatments_id',
                DB::raw('SUM(COALESCE(jumlah_awal,0)) as total')
            )
            ->groupBy('treatments_id');

        // 3️⃣ Union keduanya
        $union = $directTreatment->unionAll($bundlingTreatment);

        // 4️⃣ Bungkus lagi dan SUM total akhirnya
        $this->topTreatment = DB::query()
            ->fromSub($union, 'treatment_totals')
            ->join('treatments', 'treatment_totals.treatments_id', '=', 'treatments.id')
            ->select(
                'treatments.nama_treatment',
                DB::raw('SUM(treatment_totals.total) as total_terjual')
            )
            ->groupBy('treatments.id', 'treatments.nama_treatment')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.kunjungan.items.treatment');
    }
}
