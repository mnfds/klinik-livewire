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
    public $user_id, $tanggal_lembur, $jam_mulai, $keperluan, $disetujui_oleh;
    public $status = 'pending';
    public $users;

    public function render()
    {
        return view('livewire.lembur.store');
    }
    
    public function mount(){
        $this->users = User::with(['dokter', 'biodata', 'role'])->get();
    }

    public function store()
    {
        $this->disetujui_oleh = Auth::user()->id;

        $this->validate([
            'user_id'       => 'required',
            'tanggal_lembur'=> 'required',
            'jam_mulai'     => 'required',
            'keperluan'     => 'required',
            'disetujui_oleh'=> 'required',
            'status'        => 'required',
        ]);
        
        if (! Gate::allows('akses', 'Pengajuan Lembur Tambah')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        $data = Lembur::create([
            'user_id'       => $this->user_id,
            'tanggal_lembur'=> Carbon::now(),
            'jam_mulai'     => $this->jam_mulai,
            'jam_kembali'   => null,
            'keperluan'     => $this->keperluan,
            'status'        => $this->status,
            'disetujui_oleh'=> $this->disetujui_oleh,
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
