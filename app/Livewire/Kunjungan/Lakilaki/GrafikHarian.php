<?php

namespace App\Livewire\Kunjungan\Lakilaki;

use App\Models\PasienTerdaftar;
use App\Models\PoliKlinik;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\Component;

class GrafikHarian extends Component
{
    public $startDate;
    public $endDate;

    public function render()
    {
        return view('livewire.kunjungan.lakilaki.grafik-harian');
    }

    public function mount()
    {
        $this->loadDefaultData();
    }

    public function tanggalDipilih()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end   = Carbon::parse($this->endDate)->endOfDay();

        [$labelsTanggal, $datasets] = $this->kunjunganLakiBarHarian($start, $end);
        $pieData = $this->kunjunganLakiPieHarian($start, $end);

        // ===== KIRIM KE JS =====
        $this->dispatch('update-kunjungan-laki-harian-bar', [
            'labelstanggal'  => $labelsTanggal,
            'datasets'  => $datasets,
        ]);

        $this->dispatch('update-kunjungan-laki-harian-pie', [
            'labels'   => $pieData['labels'],
            'datasets' => $pieData['datasets'],
        ]);
    }

    private function loadDefaultData()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate   = now()->endOfMonth()->format('Y-m-d');

        $start = Carbon::parse($this->startDate)->startOfDay();
        $end   = Carbon::parse($this->endDate)->endOfDay();

        [$labelsTanggal, $datasets] = $this->kunjunganLakiBarHarian($start, $end);
        $pieData = $this->kunjunganLakiPieHarian($start, $end);
        // ===== KIRIM KE JS =====
        $this->dispatch('update-kunjungan-laki-harian-bar', [
            'labelstanggal'  => $labelsTanggal,
            'datasets'  => $datasets,
        ]);

        $this->dispatch('update-kunjungan-laki-harian-pie', [
            'labels'   => $pieData['labels'],
            'datasets' => $pieData['datasets'],
        ]);
    }

    private function kunjunganLakiBarHarian(Carbon $start, Carbon $end)
    {
        $dates = collect(CarbonPeriod::create($start, $end))
            ->map(fn ($d) => $d->format('Y-m-d'));

        $labelsTanggal = $dates->map(fn ($d) => Carbon::parse($d)->format('d'))->toArray();

        $polis = PoliKlinik::where('status', true)->get();

        $rawData = PasienTerdaftar::query()
            ->selectRaw('DATE(tanggal_kunjungan) as tanggal, poli_id, COUNT(*) as total')
            ->join('pasiens', 'pasiens.id', '=', 'pasien_terdaftars.pasien_id')
            ->whereBetween('tanggal_kunjungan', [$start, $end])
            ->where('pasiens.jenis_kelamin', 'Laki-laki')
            ->groupBy('tanggal', 'poli_id')
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

            $data = $dates->map(function ($tanggal) use ($collection) {
                return optional(
                    $collection->firstWhere('tanggal', $tanggal)
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

        return [$labelsTanggal, $datasets];
    }

    private function kunjunganLakiPieHarian(Carbon $start, Carbon $end)
    {
        $polis = PoliKlinik::where('status', true)->get();

        $rawPie = PasienTerdaftar::query()
            ->selectRaw('pasien_terdaftars.poli_id, COUNT(*) as total')
            ->join('pasiens', 'pasiens.id', '=', 'pasien_terdaftars.pasien_id')
            ->whereBetween('pasien_terdaftars.tanggal_kunjungan', [$start, $end])
            ->where('pasiens.jenis_kelamin', 'Laki-laki')
            ->groupBy('pasien_terdaftars.poli_id')
            ->pluck('total', 'poli_id');

        $labelsPoli = [];
        $dataPie = [];
        $backgroundColors = [];
        $borderColors = [];

        $colors = [
            'rgba(34,197,94,0.6)',
            'rgba(59,130,246,0.6)',
            'rgba(234,179,8,0.6)',
            'rgba(239,68,68,0.6)',
        ];

        $colorIndex = 0;

        foreach ($polis as $poli) {
            $total = $rawPie[$poli->id] ?? 0;
            if ($total === 0) continue;

            $labelsPoli[] = $poli->nama_poli;
            $dataPie[] = $total;

            $bg = $colors[$colorIndex % count($colors)];
            $backgroundColors[] = $bg;
            $borderColors[] = str_replace('0.6', '1', $bg);

            $colorIndex++;
        }

        return [
            'labels' => $labelsPoli,
            'datasets' => [
                [
                    'data' => $dataPie,
                    'backgroundColor' => $backgroundColors,
                    'borderColor' => $borderColors,
                    'borderWidth' => 2,
                ]
            ]
        ];
    }

    public function resetData()
    {
        $this->loadDefaultData();
    }
}
