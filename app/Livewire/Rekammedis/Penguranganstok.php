<?php

namespace App\Livewire\Rekammedis;

use App\Models\Pasien;
use Livewire\Component;
use App\Models\PasienTerdaftar;

class Penguranganstok extends Component
{
    public ?int $pasien_terdaftar_id = null;
    public ?PasienTerdaftar $pasienTerdaftar = null;
    public ?Pasien $pasien = null;
    public $rekammedis = null;
    public $rencanaDetail = [];

    public function mount($pasien_terdaftar_id = null)
    {
        $this->pasien_terdaftar_id = $pasien_terdaftar_id;

        $this->loadRekamMedis();

        $this->pasien     = $this->pasienTerdaftar?->pasien;
        $this->rekammedis = $this->pasienTerdaftar?->rekamMedis;
        $this->rencanaDetail = $this->getRencanaDetail();
        // dd($this->getRencanaDetail());
    }

    public function render()
    {
        // if (! Gate::allows('akses', 'Detail Rekam Medis')) {
        //     session()->flash('toast', [
        //         'type' => 'error',
        //         'message' => 'Anda tidak memiliki akses.',
        //     ]);
        //     $this->redirectRoute('dashboard');
        // }
        return view('livewire.rekammedis.penguranganstok', [
            'pasienTerdaftar' => $this->pasienTerdaftar,
            'pasien'          => $this->pasien,
            'rekammedis'      => $this->rekammedis,
        ]);
    }

    private function loadRekamMedis(): void
    {
        $this->pasienTerdaftar = PasienTerdaftar::with([
            'rekamMedis.rencanaLayananRM.pelayanan.layananbahan.bahanbaku',
            'rekamMedis.rencanaTreatmentRM.treatment.treatmentbahan.bahanbaku',
            'rekamMedis.rencanaBundlingRM.bundling.treatmentBundlings.treatment.treatmentbahan.bahanbaku',
            'rekamMedis.rencanaBundlingRM.bundling.pelayananBundlings.pelayanan.layananbahan.bahanbaku',
        ])->find($this->pasien_terdaftar_id);
    }

    private function getRencanaDetail(): array
    {
        return [
            'pasien_terdaftar'   => $this->pasien_terdaftar_id,
            'rencana_layanan'    => $this->mapRencanaLayanan(),
            'rencana_treatment'  => $this->mapRencanaTreatment(),
            'rencana_bundling'   => $this->mapRencanaBundling(),
        ];
    }

    private function mapRencanaLayanan()
    {
        return $this->rekammedis?->rencanaLayananRM
            ->map(function ($item) {
                return [
                    'nama_pelayanan' => $item->pelayanan?->nama_pelayanan,
                    'jumlah' => $item->jumlah_pelayanan,
                    'bahan_baku' => $item->pelayanan?->layananbahan
                        ->map(fn($lb) => [
                            'nama_bahan' => $lb->bahanbaku?->nama,
                            'qty_default' => $lb->qty ?? 1,
                            'total_pakai' => ($lb->qty ?? 1) * $item->jumlah_pelayanan,
                        ])
                ];
            }) ?? collect();
    }

    private function mapRencanaTreatment()
    {
        return $this->rekammedis?->rencanaTreatmentRM
            ->map(function ($item) {
                return [
                    'nama_treatment' => $item->treatment?->nama_treatment,
                    'jumlah' => $item->jumlah_treatment,
                    'bahan_baku' => $item->treatment?->treatmentbahan
                        ->map(fn($tb) => [
                            'nama_bahan' => $tb->bahanbaku?->nama,
                            'qty_default' => $tb->qty ?? 1,
                            'total_pakai' => ($tb->qty ?? 1) * $item->jumlah_treatment,
                        ])
                ];
            }) ?? collect();
    }

    private function mapRencanaBundling()
    {
        return $this->rekammedis?->rencanaBundlingRM
            ->map(function ($rb) {
                return [
                    'nama_bundling' => $rb->bundling?->nama,
                    'jumlah_bundling' => $rb->jumlah_bundling,
                    'treatments' => $rb->bundling?->treatmentBundlings
                        ->map(function ($tb) use ($rb) {
                            return [
                                'nama_treatment' => $tb->treatment?->nama_treatment,
                                'bahan_baku' => $tb->treatment?->treatmentbahan
                                    ->map(fn($tbb) => [
                                        'nama_bahan' => $tbb->bahanbaku?->nama,
                                        'qty_default' => $tbb->qty ?? 1,
                                        'total_pakai' => ($tbb->qty ?? 1) * $rb->jumlah_bundling,
                                    ])
                            ];
                        }),
                    'pelayanans' => $rb->bundling?->pelayananBundlings
                        ->map(function ($pb) use ($rb) {
                            return [
                                'nama_pelayanan' => $pb->pelayanan?->nama_pelayanan,
                                'bahan_baku' => $pb->pelayanan?->layananbahan
                                    ->map(fn($lb) => [
                                        'nama_bahan' => $lb->bahanbaku?->nama,
                                        'qty_default' => $lb->qty ?? 1,
                                        'total_pakai' => ($lb->qty ?? 1) * $rb->jumlah_bundling,
                                    ])
                            ];
                        }),
                ];
            }) ?? collect();
    }
}
