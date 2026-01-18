<?php

namespace App\Livewire\Izinkeluar;

use App\Models\Izinkeluar;
use Livewire\Component;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class Store extends Component
{
    public $user_id, $tanggal_izin, $jam_keluar, $keperluan, $disetujui_oleh;
    public $status = 'Disetujui';

    public function render()
    {
        return view('livewire.izinkeluar.store');
    }

    public function store()
    {
        $this->disetujui_oleh = Auth::user()->id;

        $this->validate([
            'user_id'       => 'required',
            'tanggal_izin'  => 'required',
            'jam_keluar'    => 'required',
            'keperluan'     => 'required',
            'disetujui_oleh'=> 'required',
            'status'        => 'required',
        ]);
        
        if (! Gate::allows('akses', 'Pengajuan Pengeluaran Disetujui Tambah')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        $data = Izinkeluar::create([
            'user_id'       => $this->user_id,
            'tanggal_izin'  => Carbon::now(),
            'jam_keluar'    => $this->jam_keluar,
            'jam_kembali'   => null,
            'keperluan'     => $this->keperluan,
            'status'        => $this->status,
            'disetujui_oleh'=> $this->disetujui_oleh,
        ]);

        // dd($data);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Izin Keluar Berhasil Disetujui.'
        ]);
        $this->dispatch('storeModalIzinKeluar');
        $this->reset();
        return redirect()->route('izinkeluar.data');
    }
}
