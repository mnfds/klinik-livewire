<?php

namespace App\Livewire\Kunjungan\Rekapitulasi;

use App\Models\PasienTerdaftar;
use App\Models\PoliKlinik;
use Carbon\Carbon;
use Livewire\Component;

class GrafikTahunan extends Component
{
    public function render()
    {
        return view('livewire.kunjungan.rekapitulasi.grafik-tahunan');
    }
    public function mount()
    {
        $this->loadDefaultData();
    }

    private function loadDefaultData()
    {
        $datasets = $this->kunjunganBarTahunan();

        $this->dispatch('update-kunjungan-tahunan-bar', [
            'labelsTahunan' => [
                '2024','2025','2026','2027','2028','2029',
                '2030','2031','2032','2033','2034','2035'
            ],
            'datasets'  => $datasets,
        ]);
    }

    private function kunjunganBarTahunan(): array
    {
        $startYear = 2024;
        $endYear   = 2035;

        $polis = PoliKlinik::where('status', true)->get();

        // 🔥 ambil data mentah, GROUP BY TAHUN + POLI
        $rawData = PasienTerdaftar::query()
            ->selectRaw('
                YEAR(tanggal_kunjungan) as tahun,
                poli_id,
                COUNT(*) as total
            ')
            ->join('pasiens', 'pasiens.id', '=', 'pasien_terdaftars.pasien_id')
            ->whereBetween(
                'tanggal_kunjungan',
                [
                    Carbon::create($startYear, 1, 1)->startOfDay(),
                    Carbon::create($endYear, 12, 31)->endOfDay()
                ]
            )
            ->groupBy('tahun', 'poli_id')
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

            // 🔥 array dari 2024–2035
            $data = collect(range($startYear, $endYear))
                ->map(function ($year) use ($collection) {
                    return optional(
                        $collection->firstWhere('tahun', $year)
                    )->total ?? 0;
                })
                ->toArray();

            $datasets[] = [
                'label' => $poli->nama_poli,
                'data'  => $data,
                'backgroundColor' => $colors[$colorIndex % count($colors)],
                'borderColor'     => str_replace(
                    '0.6',
                    '1',
                    $colors[$colorIndex % count($colors)]
                ),
                'borderWidth'     => 2,
                'borderRadius'    => 3,
                'maxBarThickness' => 40,
            ];

            $colorIndex++;
        }

        return $datasets;
    }
}
