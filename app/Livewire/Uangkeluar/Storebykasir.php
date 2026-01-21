<?php

namespace App\Livewire\Uangkeluar;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use App\Models\Uangkeluar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Storebykasir extends Component
{
    public $diajukan_oleh, $role, $keterangan, $jumlah_uang, $jenis_pengeluaran, $unit_usaha;
    public $status = 'Disetujui';
    public $user_id;

    public function store()
    {
        $user = User::with(['biodata', 'role'])->findOrFail($this->user_id);

        $this->diajukan_oleh = $user->biodata->nama_lengkap;
        $this->role = $user->role->nama_role;

        $this->validate([
            'diajukan_oleh'     => 'required',
            'role'              => 'required',
            'keterangan'        => 'required',
            'jumlah_uang'       => 'required',
            'jenis_pengeluaran' => 'required',
            'unit_usaha'        => 'required',
            'status'            => 'required',
        ]);
        
        if (! Gate::allows('akses', 'Pengajuan Pengeluaran Disetujui Tambah')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        $data = Uangkeluar::create([
            'diajukan_oleh'     => $this->diajukan_oleh,
            'role'              => $this->role,
            'keterangan'        => $this->keterangan,
            'jumlah_uang'       => $this->jumlah_uang,
            'jenis_pengeluaran' => $this->jenis_pengeluaran,
            'unit_usaha'        => $this->unit_usaha,
            'status'            => $this->status,
            'tanggal_pengajuan' => Carbon::now(),
        ]);

        // dd($data);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Pengajuan berhasil Dibuat.'
        ]);
        $this->dispatch('storeModalUangKeluarKasir');
        $this->dispatch('uangkeluar-disetujui');
        $this->reset();
        // return redirect()->route('uangkeluar.data');
        return redirect()->route('aruskas.data');
    }
    public function render()
    {
        return view('livewire.uangkeluar.storebykasir');
    }
}
