<?php

namespace App\Livewire\Uangkeluar;

use App\Models\Uangkeluar;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Update extends Component
{
    public $pending_id, $diterima_id, $ditolak_id;
    public $diajukan_oleh, $role, $keterangan, $jumlah_uang, $jenis_pengeluaran, $unit_usaha;

    public function render()
    {
        return view('livewire.uangkeluar.update');
    }

    #[\Livewire\Attributes\On('getupdatepending')]
    public function getupdatepending($rowId): void
    {
        $this->pending_id = $rowId;

        $pending = Uangkeluar::findOrFail($rowId);

        $this->jumlah_uang          = $pending->jumlah_uang;
        $this->jenis_pengeluaran    = $pending->jenis_pengeluaran;
        $this->keterangan           = $pending->keterangan;
        $this->unit_usaha           = $pending->unit_usaha;
        $this->dispatch('setJumlahUang', $this->jumlah_uang);
        $this->dispatch('openModal');
    }

    public function updatepending()
    {
        $this->validate([
            'jumlah_uang' => 'nullable',
            'jenis_pengeluaran' => 'nullable',
            'keterangan' => 'nullable',
        ]);
        if (! Gate::allows('akses', 'Pengajuan Pengeluaran Pending Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        Uangkeluar::where('id', $this->pending_id)->update([
            'jumlah_uang' => $this->jumlah_uang,
            'jenis_pengeluaran' => $this->jenis_pengeluaran,
            'keterangan' => $this->keterangan,
            'unit_usaha' => $this->unit_usaha,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);
        $this->dispatch('closemodaleditpending');
        $this->reset();

        // return redirect()->route('uangkeluar.data'); // untuk PowerGrid refresh
        return redirect()->route('aruskas.data');
    
    }

    #[\Livewire\Attributes\On('getupdatediterima')]
    public function getupdatediterima($rowId): void
    {
        $this->diterima_id = $rowId;

        $diterima = Uangkeluar::findOrFail($rowId);

        $this->jumlah_uang          = $diterima->jumlah_uang;
        $this->jenis_pengeluaran    = $diterima->jenis_pengeluaran;
        $this->keterangan           = $diterima->keterangan;
        $this->unit_usaha           = $diterima->unit_usaha;
        $this->dispatch('setJumlahUang', $this->jumlah_uang);
        $this->dispatch('openModal');
    }

    public function updateDiterima()
    {
        $this->validate([
            'jumlah_uang' => 'required',
            'jenis_pengeluaran' => 'required',
            'keterangan' => 'required',
            'unit_usaha' => 'required',
        ]);
        // if (! Gate::allows('akses', 'Pengajuan Pengeluaran Disetujui Edit')) {
        //     $this->dispatch('toast', [
        //         'type' => 'error',
        //         'message' => 'Anda tidak memiliki akses.',
        //     ]);
        //     return;
        // }
        if (! Gate::allows('akses', 'Pengeluaran Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        Uangkeluar::where('id', $this->diterima_id)->update([
            'jumlah_uang' => $this->jumlah_uang,
            'jenis_pengeluaran' => $this->jenis_pengeluaran,
            'keterangan' => $this->keterangan,
            'unit_usaha' => $this->unit_usaha,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);
        $this->dispatch('closemodaleditditerima');
        $this->reset();

        // return redirect()->route('uangkeluar.data'); // untuk PowerGrid refresh
        return redirect()->route('aruskas.data');

    }
    
    #[\Livewire\Attributes\On('getupdateditolak')]
    public function getupdateditolak($rowId): void
    {
        $this->ditolak_id = $rowId;

        $ditolak = Uangkeluar::findOrFail($rowId);

        $this->jumlah_uang          = $ditolak->jumlah_uang;
        $this->jenis_pengeluaran    = $ditolak->jenis_pengeluaran;
        $this->keterangan           = $ditolak->keterangan;
        $this->unit_usaha           = $ditolak->unit_usaha;
        $this->dispatch('setJumlahUang', $this->jumlah_uang);
        $this->dispatch('openModal');
    }

    public function updateDitolak()
    {
        $this->validate([
            'jumlah_uang' => 'nullable',
            'jenis_pengeluaran' => 'nullable',
            'keterangan' => 'nullable',
        ]);
        if (! Gate::allows('akses', 'Pengajuan Pengeluaran Ditolak Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        Uangkeluar::where('id', $this->ditolak_id)->update([
            'jumlah_uang' => $this->jumlah_uang,
            'jenis_pengeluaran' => $this->jenis_pengeluaran,
            'keterangan' => $this->keterangan,
            'unit_usaha' => $this->unit_usaha,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);
        $this->dispatch('closemodaleditditolak');
        $this->reset();

        // return redirect()->route('uangkeluar.data');
        return redirect()->route('aruskas.data');
    }
}
