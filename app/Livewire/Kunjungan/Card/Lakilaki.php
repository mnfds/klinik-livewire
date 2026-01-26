<?php

namespace App\Livewire\Kunjungan\Card;

use App\Models\PoliKlinik;
use App\Models\TransaksiKlinik;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Lakilaki extends Component
{
    public $startDate;
    public $endDate;

    public $kunjunganLakilaki = [];

    public function render()
    {
        return view('livewire.kunjungan.card.lakilaki');
    }

    public function mount()
    {
        $this->loadDefaultData();
    }

    public function tanggalDipilih()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end   = Carbon::parse($this->endDate)->endOfDay();

        $this->kunjunganLakilaki = DB::table('poli_kliniks')
            ->leftJoin('pasien_terdaftars', function ($join) use ($start, $end) {
                $join->on('pasien_terdaftars.poli_id', '=', 'poli_kliniks.id')
                    ->where('pasien_terdaftars.status_terdaftar', 'selesai')
                    ->whereBetween(
                        'pasien_terdaftars.tanggal_kunjungan',
                        [$start->toDateString(), $end->toDateString()]
                    );
            })
            ->leftJoin('pasiens', 'pasiens.id', '=', 'pasien_terdaftars.pasien_id')
            ->selectRaw("
                poli_kliniks.nama_poli,
                COUNT(
                    CASE
                        WHEN pasiens.jenis_kelamin = 'Laki-laki'
                        THEN pasien_terdaftars.id
                    END
                ) as total
            ")
            ->groupBy('poli_kliniks.nama_poli')
            ->pluck('total', 'nama_poli');


        return $this->kunjunganLakilaki;
    }

    private function loadDefaultData()
    {
        $this->startDate = now()->format('Y-m-d');
        $this->endDate   = now()->format('Y-m-d');

        $start = Carbon::parse($this->startDate)->startOfDay();
        $end   = Carbon::parse($this->endDate)->endOfDay();

        $this->kunjunganLakilaki = DB::table('poli_kliniks')
            ->leftJoin('pasien_terdaftars', function ($join) use ($start, $end) {
                $join->on('pasien_terdaftars.poli_id', '=', 'poli_kliniks.id')
                    ->where('pasien_terdaftars.status_terdaftar', 'selesai')
                    ->whereBetween(
                        'pasien_terdaftars.tanggal_kunjungan',
                        [$start->toDateString(), $end->toDateString()]
                    );
            })
            ->leftJoin('pasiens', 'pasiens.id', '=', 'pasien_terdaftars.pasien_id')
            ->selectRaw("
                poli_kliniks.nama_poli,
                COUNT(
                    CASE
                        WHEN pasiens.jenis_kelamin = 'Laki-laki'
                        THEN pasien_terdaftars.id
                    END
                ) as total
            ")
            ->groupBy('poli_kliniks.nama_poli')
            ->pluck('total', 'nama_poli');

       
        return $this->kunjunganLakilaki;
    }

    public function resetData()
    {
        $this->loadDefaultData();
    }

}
