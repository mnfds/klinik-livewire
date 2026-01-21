<?php

namespace App\Livewire\Izinkeluar;

use App\Models\Izinkeluar;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Update extends Component
{
    public $izinDisetujui, $izinSelesai;
    public $user_id, $tanggal_izin, $jam_keluar, $jam_kembali, $keperluan, $status, $disetujui_oleh;

    public function render()
    {
        return view('livewire.izinkeluar.update');
    }

    #[\Livewire\Attributes\On('getupdateizindisetujui')]
    public function getupdateizindisetujui($rowId): void
    {
        $this->izinDisetujui = $rowId;

        $disetujui = Izinkeluar::findOrFail($rowId);

        $this->user_id              = $disetujui->user_id;
        $this->tanggal_izin         = $disetujui->tanggal_izin;
        $this->jam_keluar           = $disetujui->jam_keluar;
        $this->keperluan            = $disetujui->keperluan;
        $this->dispatch('openModal');
    }

    #[\Livewire\Attributes\On('getupdateizinselesai')]
    public function getupdateizinselesai($rowId): void
    {
        $this->izinSelesai = $rowId;

        $izinSelesai = Izinkeluar::findOrFail($rowId);

        $this->user_id              = $izinSelesai->user_id;
        $this->tanggal_izin         = $izinSelesai->tanggal_izin;
        $this->jam_keluar           = $izinSelesai->jam_keluar;
        $this->jam_kembali          = $izinSelesai->jam_kembali;
        $this->keperluan            = $izinSelesai->keperluan;
        $this->dispatch('openModal');
    }

    public function updateDisetujui()
    {
        $this->validate([
            'user_id'       => 'required',
            'tanggal_izin'  => 'required',
            'jam_keluar'    => 'required',
            'keperluan'     => 'required',
        ]);
        if (! Gate::allows('akses', 'Pengajuan Izin Keluar Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        Izinkeluar::where('id', $this->izinDisetujui)->update([
            'user_id' => $this->user_id,
            'tanggal_izin' => $this->tanggal_izin,
            'jam_keluar' => $this->jam_keluar,
            'keperluan' => $this->keperluan,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);
        $this->dispatch('closemodaleditditerima');
        $this->reset();

        return redirect()->route('izinkeluar.data'); // untuk PowerGrid refresh
    }

    public function updateSelesai()
    {
        $this->validate([
            'user_id'       => 'required',
            'tanggal_izin'  => 'required',
            'jam_keluar'    => 'required',
            'jam_kembali'   => 'required',
            'keperluan'     => 'required',
        ]);
        if (! Gate::allows('akses', 'Pengajuan Riwayat Izin Keluar Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        Izinkeluar::where('id', $this->izinSelesai)->update([
            'user_id' => $this->user_id,
            'tanggal_izin' => $this->tanggal_izin,
            'jam_keluar' => $this->jam_keluar,
            'jam_kembali' => $this->jam_kembali,
            'keperluan' => $this->keperluan,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);
        $this->dispatch('closemodaleditditerima');
        $this->reset();

        return redirect()->route('izinkeluar.data'); // untuk PowerGrid refresh
    }
}
