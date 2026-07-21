<?php

namespace App\Livewire\Jadwal;

use App\Exports\JadwalExport;
use App\Models\Jadwal;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class Downloadjadwal extends Component
{
    public $role;
    public $bulanini;
    public $divisi;

    public function render()
    {
        return view('livewire.jadwal.downloadjadwal');
    }

    public function mount()
    {
        $this->role = Role::all();
        // $this->bulanini = today()->format('Y-m');
    }
    
    public function unduh()
    {
        $bulanini = $this->tanggal ?? today()->format('Y-m');

        if ($this->divisi === 'semua') {
            $users = User::with(['biodata', 'dokter', 'role'])
                ->orderBy('role_id')
                ->orderBy('name')
                ->get();
        } else {
            $roleId = Role::where('nama_role', $this->divisi)->value('id');
            $users = User::where('role_id', $roleId)
                ->with(['biodata', 'dokter', 'role'])
                ->orderBy('name')
                ->get();
        }

        $startDate = Carbon::parse($bulanini . '-01');
        $endDate   = $startDate->copy()->endOfMonth();

        // load jadwal + jamkerja sekaligus, per user
        $jadwals = Jadwal::whereIn('user_id', $users->pluck('id'))
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->with('jamkerja')
            ->get()
            ->groupBy('user_id');

        $fileName = "Jadwal_{$this->divisi}_{$bulanini}.xlsx";

        return Excel::download(new JadwalExport($users, $jadwals, $startDate, $endDate), $fileName);
    }
}
