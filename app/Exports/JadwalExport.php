<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;

class JadwalExport implements FromView, ShouldAutoSize
{
    public function __construct(
        protected $users,
        protected $jadwals,
        protected $startDate,
        protected $endDate
    ) {}

    public function view(): View
    {
        $days = collect(CarbonPeriod::create($this->startDate, $this->endDate));

        return view('exports.jadwal', [
            'users'   => $this->users,
            'jadwals' => $this->jadwals,
            'days'    => $days,
        ]);
    }
}