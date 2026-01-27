<?php

namespace App\Livewire\Kunjungan\Rekapitulasi;

use App\Models\PasienTerdaftar;
use App\Models\PoliKlinik;
use Carbon\Carbon;
use Livewire\Component;

class GrafikBulanan extends Component
{
    public $tahun;

    public function render()
    {
        return view('livewire.kunjungan.rekapitulasi.grafik-bulanan');
    }

    public function mount()
    {
        $this->loadDefaultData();
    }

    public function tahunDipilih()
    {
        $year = $this->tahun;

        [$datasets] = $this->kunjunganBarBulanan((int) $year);

        // ===== KIRIM KE JS =====
        $this->dispatch('update-kunjungan-bulanan-bar', [
            'labelsBulan' => [
                'Jan','Feb','Mar','Apr','Mei','Jun',
                'Jul','Agu','Sep','Okt','Nov','Des'
            ],
            'datasets'  => $datasets,
        ]);
    }

    private function loadDefaultData()
    {
        $this->tahun = now()->year;
        $year = $this->tahun;
        [$datasets] = $this->kunjunganBarBulanan((int) $year);

        // ===== KIRIM KE JS =====
        $this->dispatch('update-kunjungan-bulanan-bar', [
            'labelsBulan' => [
                'Jan','Feb','Mar','Apr','Mei','Jun',
                'Jul','Agu','Sep','Okt','Nov','Des'
            ],
            'datasets'  => $datasets,
        ]);
    }

    private function kunjunganBarBulanan(int $year)
    {
        $start = Carbon::create($year, 1, 1)->startOfDay();
        $end   = Carbon::create($year, 12, 31)->endOfDay();

        $polis = PoliKlinik::where('status', true)->get();

        $rawData = PasienTerdaftar::query()
            ->selectRaw('
                MONTH(tanggal_kunjungan) as bulan,
                poli_id,
                COUNT(*) as total
            ')
            ->join('pasiens', 'pasiens.id', '=', 'pasien_terdaftars.pasien_id')
            ->whereBetween('tanggal_kunjungan', [$start, $end])
            ->groupBy('bulan', 'poli_id')
            ->get()
            ->groupBy('poli_id');

        $colors = [
            'rgba(34,197,94,0.6)',
            'rgba(59,130,246,0.6)',
            'rgba(234,179,8,0.6)',
            'rgba(239,68,68,0.6)',
        ];

        $datasets = [];
        $colorIndex = 0;

        foreach ($polis as $poli) {
            $collection = $rawData[$poli->id] ?? collect();

            // 🔥 12 bulan
            $data = collect(range(1, 12))->map(function ($bulan) use ($collection) {
                return optional(
                    $collection->firstWhere('bulan', $bulan)
                )->total ?? 0;
            })->toArray();

            $datasets[] = [
                'label' => $poli->nama_poli,
                'data' => $data,
                'backgroundColor' => $colors[$colorIndex % count($colors)],
                'borderColor' => str_replace('0.6', '1', $colors[$colorIndex % count($colors)]),
                'borderWidth' => 2,
                'borderRadius' => 3,
                'maxBarThickness' => 40,
            ];

            $colorIndex++;
        }

        return [$datasets];
    }
    
    public function resetData()
    {
        $this->loadDefaultData();
    }
}
