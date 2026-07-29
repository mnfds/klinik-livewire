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
use Illuminate\Support\Facades\Auth;
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
    public $batasEditHari = 1;

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
                ->whereNotIn('id', [1]) // pengecuali user id
                ->where('role_id', '!=', 2) // pengecuali role id
                ->orderBy('role_id')
                ->orderBy('name')
                ->get();
        } else {
            $roleId = Role::where('nama_role', $this->role)->value('id');
            $this->users = User::where('role_id', $roleId)
                ->whereNotIn('id', [1]) //pengecuali user id
                ->where('role_id', '!=', 2) // pengecuali role id
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

        //generate kuota libur
        if($today->day === 1){
            $this->generateKuotaLibur();
        }
    }

    private function isShiftTerkunci($tglCell, $userId): bool
    {
        // Punya akses penuh -> tidak pernah terkunci, apapun kondisinya
        if (Gate::allows('akses', 'Jadwal Edit')) {
            return false;
        }

        $tglCell = \Carbon\Carbon::parse($tglCell);
        $today = today();

        // Sudah lewat
        if ($tglCell->lt($today)) {
            return true;
        }

        // Hari ini selalu terkunci
        if ($tglCell->isSameDay($today)) {
            return true;
        }

        // Cek apakah hari ini termasuk awal bulan (tgl 1) atau akhir bulan (tgl terakhir)
        $isAwalBulan  = $today->day === 1;
        $isAkhirBulan = $today->day === $today->daysInMonth;

        // Lookahead N hari ke depan HANYA berlaku kalau bukan di boundary awal/akhir bulan
        if (!$isAwalBulan && !$isAkhirBulan) {
            if ($tglCell->lte($today->copy()->addDays($this->batasEditHari))) {
                return true;
            }
        }

        // Bukan pemilik jadwal
        if ($userId !== Auth::id()) {
            return true;
        }

        return false;
    }
    
    // public function editShift($userId, $tanggal, $roleId)
    // {
    //     $this->editUserId = $userId;
    //     $this->editRoleId = $roleId;
    //     $this->editTanggal = $tanggal;
    //     $this->dispatch('getupdatejadwal', userId: $this->editUserId, tanggal: $this->editTanggal, roleId: $this->editRoleId)
    //     ->to(\App\Livewire\Jadwal\Update::class);
    // }

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

    private function hitungTerpakai($userId, $bulan, $tahun)
    {
        $today = today();
        $bulanIni = Carbon::create($tahun, $bulan, 1);

        $cutoff = match (true) {
            $bulanIni->isSameMonth($today) && $bulanIni->isSameYear($today) => $today,
            $bulanIni->lt($today) => $bulanIni->copy()->endOfMonth(),
            default => $bulanIni->copy()->startOfMonth()->subDay(),
        };

        return Jadwal::where('user_id', $userId)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->whereDate('tanggal', '<=', $cutoff)
            ->whereHas('jamkerja', fn ($q) => $q->where('tipe_shift', 'libur'))
            ->count();
    }

    private function hitungSisaKuotaBulanLalu($userId, $bulan, $tahun)
    {
        $bulanDipilih = Carbon::create($tahun, $bulan, 1);
        $bulanLalu = $bulanDipilih->copy()->subMonth();

        $kuotaLalu = Kuotalibur::where('user_id', $userId)
            ->where('bulan', $bulanLalu->month)
            ->where('tahun', $bulanLalu->year)
            ->first();

        $dimilikiLalu = $kuotaLalu->kuota_dimiliki ?? 0;
        $sisaCarryLalu = $kuotaLalu->kuota_sisa_bulan_sebelumnya ?? 0;
        $totalLalu = $dimilikiLalu + $sisaCarryLalu;

        $terpakaiLalu = $this->hitungTerpakai($userId, $bulanLalu->month, $bulanLalu->year);

        return max(0, $totalLalu - $terpakaiLalu);
    }
    
    private function generateKuotaLibur(): void
    {
        $bulanIni = today()->startOfMonth();
        $bulan = $bulanIni->month;
        $tahun = $bulanIni->year;

        $jumlahMinggu = $this->hitungJumlahHariMinggu($bulanIni);

        $userIds = User::whereNotIn('id', [1])
            ->where('role_id', '!=', 2)
            ->pluck('id');

        // ambil user yang SUDAH punya kuota bulan ini, biar nggak diproses ulang
        $sudahAda = Kuotalibur::whereIn('user_id', $userIds)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->pluck('user_id')
            ->toArray();

        foreach ($userIds as $userId) {
            if (in_array($userId, $sudahAda)) {
                continue; // sudah ada, skip
            }

            $sisaBulanLalu = $this->hitungSisaKuotaBulanLalu($userId, $bulan, $tahun);

            Kuotalibur::create([
                'user_id' => $userId,
                'bulan'   => $bulan,
                'tahun'   => $tahun,
                'kuota_dimiliki' => $jumlahMinggu,
                'kuota_sisa_bulan_sebelumnya' => $sisaBulanLalu,
            ]);
        }
    }

    private function hitungJumlahHariMinggu(Carbon $bulan): int
    {
        $awal = $bulan->copy()->startOfMonth();
        $akhir = $bulan->copy()->endOfMonth();

        $jumlah = 0;
        for ($tanggal = $awal->copy(); $tanggal->lte($akhir); $tanggal->addDay()) {
            if ($tanggal->isSunday()) {
                $jumlah++;
            }
        }

        return $jumlah;
    }
}
