<?php

namespace App\Livewire\Jadwal;

use App\Models\Absen;
use App\Models\Jadwal;
use App\Models\JamKerja;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Table extends Component
{
    public $bulan;
    public $role;
    public $users;
    public $jadwal;
    public $tanggal;
    public $editUserId = null;
    public $editTanggal = null;
    public $jamKerjaList = [];
    public $absen = [];
    public $kuotaLibur = 4;
    public $kuotaTerpakai = [];
    public $kuotaCuti = 12;
    public $kuotaCutiTerpakai = [];

    public function render()
    {
        return view('livewire.jadwal.table');
    }

    public function mount($bulan, $role)
    {
        $this->bulan = $bulan;
        $this->role = $role ?: auth()->User()->role->nama_role;

        $roleId = Role::where('nama_role', $this->role)->value('id');
        $this->users = User::where('role_id', $roleId)->with(['biodata', 'dokter'])->get();

        $this->tanggal = Carbon::createFromFormat('Y-m', $this->bulan);
        $this->jadwal = Jadwal::whereIn('user_id', $this->users->pluck('id'))
            ->whereYear('tanggal', $this->tanggal->year)
            ->whereMonth('tanggal', $this->tanggal->month)
            ->with('jamkerja')
            ->get()
            ->groupBy('user_id')
            ->map(fn ($items) => $items->toArray())
            ->toArray();

        $this->absen = Absen::whereIn('user_id', $this->users->pluck('id'))
            ->whereYear('tanggal_absen', $this->tanggal->year)
            ->whereMonth('tanggal_absen', $this->tanggal->month)
            ->get()
            ->groupBy('user_id')
            ->map(fn ($items) => $items->keyBy(fn ($item) => $item->tanggal_absen->format('Y-m-d'))->toArray())
            ->toArray();
            
        $today = today();
        if ($this->tanggal->isSameMonth($today) && $this->tanggal->isSameYear($today)) {
            $cutoff = $today;
        } elseif ($this->tanggal->lt($today)) {
            $cutoff = $this->tanggal->copy()->endOfMonth();
        } else {
            $cutoff = $this->tanggal->copy()->startOfMonth()->subDay(); // belum ada yang lewat
        }
        $this->kuotaTerpakai = collect($this->jadwal)->map(function ($items) use ($cutoff) {
            return collect($items)->filter(function ($item) use ($cutoff) {
                $isLibur = ($item['jamkerja']['tipe_shift'] ?? null) === 'libur';
                $tanggalItem = \Carbon\Carbon::parse($item['tanggal']);
                return $isLibur && $tanggalItem->lte($cutoff);
            })->count();
        })->toArray();

        $tahunDilihat = $this->tanggal->year;
        if ($tahunDilihat == $today->year) {
            $cutoffCuti = $today;
        } elseif ($tahunDilihat < $today->year) {
            $cutoffCuti = Carbon::create($tahunDilihat, 12, 31);
        } else {
            $cutoffCuti = Carbon::create($tahunDilihat, 1, 1)->subDay();
        }
        $this->kuotaCutiTerpakai = Jadwal::whereIn('user_id', $this->users->pluck('id'))
            ->whereYear('tanggal', $tahunDilihat)
            ->whereDate('tanggal', '<=', $cutoffCuti)
            ->whereHas('jamkerja', fn ($q) => $q->where('tipe_shift', 'cuti'))
            ->get()
            ->groupBy('user_id')
            ->map(fn ($items) => $items->count())
            ->toArray();

        $this->jamKerjaList = JamKerja::all();
    }

    public function editShift($userId, $tanggal)
    {
        $this->editUserId = $userId;
        $this->editTanggal = $tanggal;
        $this->dispatch('getupdatejadwal', userId: $this->editUserId, tanggal: $this->editTanggal)
        ->to(\App\Livewire\Jadwal\Update::class);
    }

    #[\Livewire\Attributes\On('shift-updated')]
    public function refreshShift($userId, $tanggal, $jadwal, $tipeShiftLama = null, $tipeShiftBaru = null)
    {
        if ($jadwal) {
            $this->jadwal[$userId][$tanggal] = $jadwal;
        } else {
            unset($this->jadwal[$userId][$tanggal]);
        }

        $tanggalDiubah = Carbon::parse($tanggal);
        $today = today();

        // cutoff bulan (untuk libur)
        if ($this->tanggal->isSameMonth($today) && $this->tanggal->isSameYear($today)) {
            $cutoffBulan = $today;
        } elseif ($this->tanggal->lt($today)) {
            $cutoffBulan = $this->tanggal->copy()->endOfMonth();
        } else {
            $cutoffBulan = $this->tanggal->copy()->startOfMonth()->subDay();
        }

        // cutoff tahun (untuk cuti)
        if ($this->tanggal->year == $today->year) {
            $cutoffTahun = $today;
        } elseif ($this->tanggal->year < $today->year) {
            $cutoffTahun = Carbon::create($this->tanggal->year, 12, 31);
        } else {
            $cutoffTahun = Carbon::create($this->tanggal->year, 1, 1)->subDay();
        }

        if ($tanggalDiubah->lte($cutoffBulan)) {
            $terpakai = $this->kuotaTerpakai[$userId] ?? 0;
            if ($tipeShiftLama === 'libur') $terpakai--;
            if ($tipeShiftBaru === 'libur') $terpakai++;
            $this->kuotaTerpakai[$userId] = max(0, $terpakai);
        }

        if ($tanggalDiubah->lte($cutoffTahun)) {
            $terpakaiCuti = $this->kuotaCutiTerpakai[$userId] ?? 0;
            if ($tipeShiftLama === 'cuti') $terpakaiCuti--;
            if ($tipeShiftBaru === 'cuti') $terpakaiCuti++;
            $this->kuotaCutiTerpakai[$userId] = max(0, $terpakaiCuti);
        }
    }
}
