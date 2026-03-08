<?php

namespace App\Livewire\Kunjungan\Items;

use App\Models\RencanaLayananRM;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Pelayanan extends Component
{
    public $topPelayanan = [];
    public $filter = 'all';

    public function mount()
    {
        $this->loadTopPelayanan();
    }

    public function updatedFilter()
    {
        $this->loadTopPelayanan();
    }

    private function loadTopPelayanan()
    {
        $directPelayanan = DB::table('rencana_layanan_r_m_s')
            ->select(
                'pelayanan_id as pelayanan_ref_id',
                DB::raw('SUM(COALESCE(jumlah_pelayanan,0)) as total')
            );

        $bundlingPelayanan = DB::table('pelayanan_bundling_r_m_s')
            ->select(
                'pelayanan_id as pelayanan_ref_id',
                DB::raw('SUM(COALESCE(jumlah_awal,0)) as total')
            );

        // FILTER WAKTU
        if ($this->filter === 'weekly') {
            $directPelayanan->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            $bundlingPelayanan->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        }

        if ($this->filter === 'monthly') {
            $directPelayanan->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year);

            $bundlingPelayanan->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year);
        }

        $directPelayanan->groupBy('pelayanan_id');
        $bundlingPelayanan->groupBy('pelayanan_id');

        $union = $directPelayanan->unionAll($bundlingPelayanan);

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
