<?php

namespace App\Livewire\Kunjungan\Items;

use App\Models\RencanaLayananRM;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Pelayanan extends Component
{
    public $topPelayanan = [];

    public function mount()
    {
        // 1️⃣ Pelayanan langsung
        $directPelayanan = DB::table('rencana_layanan_r_m_s')
            ->select(
                'pelayanan_id as pelayanan_ref_id',
                DB::raw('SUM(COALESCE(jumlah_pelayanan,0)) as total')
            )
            ->groupBy('pelayanan_id');

        // 2️⃣ Pelayanan dari bundling
        $bundlingPelayanan = DB::table('pelayanan_bundling_r_m_s')
            ->select(
                'pelayanan_id as pelayanan_ref_id',
                DB::raw('SUM(COALESCE(jumlah_awal,0)) as total')
            )
            ->groupBy('pelayanan_id');

        // 3️⃣ UNION
        $union = $directPelayanan->unionAll($bundlingPelayanan);

        // 4️⃣ SUM ulang & join master
        $this->topPelayanan = DB::query()
            ->fromSub($union, 'pelayanan_totals')
            ->join('pelayanans', 'pelayanan_totals.pelayanan_ref_id', '=', 'pelayanans.id')
            ->select(
                'pelayanans.nama_pelayanan',
                DB::raw('SUM(pelayanan_totals.total) as total_terjual')
            )
            ->groupBy('pelayanans.id', 'pelayanans.nama_pelayanan')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.kunjungan.items.pelayanan');
    }
}
