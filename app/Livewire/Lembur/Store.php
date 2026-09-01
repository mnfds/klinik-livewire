<?php

namespace App\Livewire\Lembur;

use App\Models\Lembur;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Store extends Component
{
    public $user_id, $tanggal_lembur, $perkiraan_durasi, $keperluan, $disetujui_oleh;
    public $status = 'pending';
    public $users = [];

    public function render()
    {
        return view('livewire.lembur.store');
    }
    
    public function mount()
    {
        $this->users = User::with(['biodata', 'dokter'])->get()
            ->map(fn ($user) => [
                'id' => $user->id,
                'nama' => $user->biodata?->nama_lengkap
                    ?? $user->dokter?->nama_dokter
                    ?? '-',
            ])
            ->toArray();

        $this->user_id = Auth::user()->id;

        if (!auth()->user()->can('akses', 'Persetujuan Ajuan Lembur')) {
            $this->user_id = auth()->id();
        }
    }

    public function store()
    {

        $this->validate([
            'user_id'           => 'required',
            'tanggal_lembur'    => 'required',
            'perkiraan_durasi' => 'required',
            'keperluan'         => 'required',
            'status'            => 'required',
        ]);
        
        if (! Gate::allows('akses', 'Pengajuan Lembur Tambah')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        $data = Lembur::create([
            'user_id'           => $this->user_id,
            'tanggal_lembur'    => Carbon::now(),
            'perkiraan_durasi' => (int)$this->perkiraan_durasi,
            'durasi'            => null,
            'keperluan'         => $this->keperluan,
            'status'            => $this->status,
            'disetujui_oleh'    => $this->disetujui_oleh,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Pengajuan Lembur Berhasil Disetujui.'
        ]);
        $this->dispatch('storeModalLembur');
        $this->reset();
        return redirect()->route('lembur.data');
    }
}
