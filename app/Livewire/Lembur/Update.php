<?php

namespace App\Livewire\Lembur;

use App\Models\Lembur;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Update extends Component
{
    public $pending_id, $approve_id, $history_id;
    public $user_id, $tanggal_lembur, $jam_mulai, $jam_selesai, $keperluan, $status, $disetujui_oleh;
    public $users;

    public function render()
    {
        return view('livewire.lembur.update');
    }

    public function mount(){
        $this->users = User::with(['dokter', 'biodata', 'role'])->get();
    }

    #[\Livewire\Attributes\On('getPendingLembur')]
    public function getPendingLembur($rowId): void
    {
        $this->pending_id = $rowId;

        $pending = Lembur::findOrFail($rowId);

        $this->user_id         = $pending->user_id;
        $this->tanggal_lembur  = $pending->tanggal_lembur;
        $this->jam_mulai       = $pending->jam_mulai;
        $this->keperluan       = $pending->keperluan;
        $this->dispatch('openModal');
    }

    public function updatePending()
    {
        $this->validate([
            'user_id'        => 'required',
            'tanggal_lembur' => 'required',
            'jam_mulai'      => 'required',
            'keperluan'      => 'required',
        ]);
        if (! Gate::allows('akses', 'Pengajuan Lembur Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        Lembur::where('id', $this->pending_id)->update([
            'user_id' => $this->user_id,
            'tanggal_lembur' => $this->tanggal_lembur,
            'jam_mulai' => $this->jam_mulai,
            'keperluan' => $this->keperluan,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);
        $this->dispatch('closemodalUpdatePending');
        $this->reset();

        return redirect()->route('lembur.data');
    }

    // ======== APPROVE UPDATE ===============
    #[\Livewire\Attributes\On('getApproveLembur')]
    public function getApproveLembur($rowId): void
    {
        $this->approve_id = $rowId;

        $approve = Lembur::findOrFail($rowId);

        $this->user_id         = $approve->user_id;
        $this->tanggal_lembur  = $approve->tanggal_lembur;
        $this->jam_mulai       = $approve->jam_mulai;
        $this->keperluan       = $approve->keperluan;
        $this->dispatch('openModal');
    }    

    public function updateApprove()
    {
        $this->validate([
            'user_id'        => 'required',
            'tanggal_lembur' => 'required',
            'jam_mulai'      => 'required',
            'keperluan'      => 'required',
        ]);
        if (! Gate::allows('akses', 'Pengajuan Lembur Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        Lembur::where('id', $this->approve_id)->update([
            'user_id' => $this->user_id,
            'tanggal_lembur' => $this->tanggal_lembur,
            'jam_mulai' => $this->jam_mulai,
            'keperluan' => $this->keperluan,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);
        $this->dispatch('closemodalUpdateApprove');
        $this->reset();

        return redirect()->route('lembur.data');
    }
    // ======== APPROVE UPDATE ===============

    // ======== HISTORY UPDATE ===============
    #[\Livewire\Attributes\On('getHistoryLembur')]
    public function getHistoryLembur($rowId): void
    {
        $this->history_id = $rowId;

        $history = Lembur::findOrFail($rowId);

        $this->user_id         = $history->user_id;
        $this->tanggal_lembur  = $history->tanggal_lembur;
        $this->jam_mulai       = $history->jam_mulai;
        $this->jam_selesai     = $history->jam_selesai;
        $this->keperluan       = $history->keperluan;
        $this->dispatch('openModal');
    }    

    public function updateHistory()
    {
        $this->validate([
            'user_id'        => 'required',
            'tanggal_lembur' => 'required',
            'jam_mulai'      => 'required',
            'jam_selesai'    => 'required',
            'keperluan'      => 'required',
        ]);
        if (! Gate::allows('akses', 'Pengajuan Riwayat Lembur Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        Lembur::where('id', $this->history_id)->update([
            'user_id' => $this->user_id,
            'tanggal_lembur' => $this->tanggal_lembur,
            'jam_mulai' => $this->jam_mulai,
            'jam_selesai' => $this->jam_selesai,
            'keperluan' => $this->keperluan,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);
        $this->dispatch('closemodalUpdateHistory');
        $this->reset();

        return redirect()->route('lembur.data');
    }
    // ======== HISTORY UPDATE ===============
}
