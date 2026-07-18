<?php

namespace App\Livewire\Jadwal;

use App\Models\Absen;
use App\Models\Jadwal;
use App\Models\JamKerja;
use App\Models\Kuotacuti;
use App\Models\Kuotalibur;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Table extends Component
{
    public $bulan;
    public $role;
    public $users;
    public $jadwal;
    public $tanggal;
    public $editUserId = null;
    public $editRoleId = null;
    public $editTanggal = null;
    public $jamKerjaList = [];
    public $absen = [];
    public $kuotaLibur = [];
    public $kuotaSisa = [];
    public $kuotaTerpakai = [];
    public $kuotaCuti = [];
    public $kuotaCutiTerpakai = [];

    public function render()
    {
        if (! Gate::allows('akses', 'Jadwal Tabel')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.jadwal.table');
    }

    public function mount($bulan, $role)
    {
        $this->bulan = $bulan;
        $this->role = $role ?: auth()->User()->role->nama_role;

        if ($this->role === 'semua') {
            $this->users = User::with(['biodata', 'dokter', 'role'])
                ->orderBy('role_id')
                ->orderBy('name')
                ->get();
        } else {
            $roleId = Role::where('nama_role', $this->role)->value('id');
            $this->users = User::where('role_id', $roleId)
                ->with(['biodata', 'dokter', 'role'])
                ->orderBy('name')
                ->get();
        }
        
        $userIds = $this->users->pluck('id');
        
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

        // ambil kuota libur bulan ini per user
        $kuotaLiburRows = Kuotalibur::whereIn('user_id', $userIds)
            ->where('bulan', $this->tanggal->month)
            ->where('tahun', $this->tanggal->year)
            ->get()
            ->keyBy('user_id');

        $this->kuotaLibur = $userIds->mapWithKeys(function ($id) use ($kuotaLiburRows) {
            return [$id => $kuotaLiburRows[$id]->kuota_dimiliki ?? 0];
        })->toArray();
        $this->kuotaSisa = $userIds->mapWithKeys(function ($id) use ($kuotaLiburRows) {
            return [$id => $kuotaLiburRows[$id]->kuota_sisa_bulan_sebelumnya ?? 0];
        })->toArray();

        // ambil kuota cuti tahun ini per user
        $kuotaCutiRows = Kuotacuti::whereIn('user_id', $userIds)
            ->where('tahun', $this->tanggal->year)
            ->pluck('kuota_dimiliki', 'user_id');

        $this->kuotaCuti = $userIds->mapWithKeys(function ($id) use ($kuotaCutiRows) {
            return [$id => $kuotaCutiRows[$id] ?? 0];
        })->toArray();

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

    public function editShift($userId, $tanggal, $roleId)
    {
        $this->editUserId = $userId;
        $this->editRoleId = $roleId;
        $this->editTanggal = $tanggal;
        $this->dispatch('getupdatejadwal', userId: $this->editUserId, tanggal: $this->editTanggal, roleId: $this->editRoleId)
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
