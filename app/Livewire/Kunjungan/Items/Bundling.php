<?php

namespace App\Livewire\Kunjungan\Items;

use App\Models\RencananaBundlingRM;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Bundling extends Component
{
    public $topBundlings = [];

    public function mount()
    {
        $this->topBundlings = RencananaBundlingRM::query()
            ->join('bundlings', 'rencana_bundling_r_m_s.bundling_id', '=', 'bundlings.id')
            ->select(
                'bundlings.nama',
                DB::raw('SUM(COALESCE(rencana_bundling_r_m_s.jumlah_bundling,0)) as total_terjual')
            )
            ->groupBy('rencana_bundling_r_m_s.bundling_id', 'bundlings.nama')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.kunjungan.items.bundling');
    }
}
