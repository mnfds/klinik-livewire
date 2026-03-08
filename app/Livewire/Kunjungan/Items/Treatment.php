<?php

namespace App\Livewire\Kunjungan\Items;

use App\Models\RencanaTreatmentRM;
use Livewire\Component;

use App\Models\RiwayatTransaksiKlinik;
use Illuminate\Support\Facades\DB;
class Treatment extends Component
{
    public $topTreatment = [];
    public $filter = 'all';

    public function mount()
    {
        $this->loadTopTreatment();
    }

    public function updatedFilter()
    {
        $this->loadTopTreatment();
    }

    private function loadTopTreatment()
    {
        // 1️⃣ Treatment langsung
        $directTreatment = DB::table('rencana_treatment_r_m_s')
            ->select(
                'treatments_id',
                DB::raw('SUM(COALESCE(jumlah_treatment,0)) as total')
            );

        // 2️⃣ Treatment dari bundling
        $bundlingTreatment = DB::table('treatment_bundling_r_m_s')
            ->select(
                'treatments_id',
                DB::raw('SUM(COALESCE(jumlah_awal,0)) as total')
            );

        // FILTER WAKTU
        if ($this->filter === 'weekly') {
            $range = [now()->startOfWeek(), now()->endOfWeek()];

            $directTreatment->whereBetween('created_at', $range);
            $bundlingTreatment->whereBetween('created_at', $range);
        }

        if ($this->filter === 'monthly') {
            $directTreatment->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year);

            $bundlingTreatment->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year);
        }

        $directTreatment->groupBy('treatments_id');
        $bundlingTreatment->groupBy('treatments_id');

        // 3️⃣ UNION
        $union = $directTreatment->unionAll($bundlingTreatment);

        // 4️⃣ Join master + sum ulang
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
