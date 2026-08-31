<?php

namespace App\Livewire\Cuti;

use App\Models\Pengajuancuti;
use App\Models\Pengajuancutitanggal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class Update extends Component
{
    public ?Pengajuancuti $pengajuan = null;

    public $user_id = '';
    public $tanggal_mulai = '';
    public $tanggal_selesai = '';
    public $alasan = '';

    public $users = [];

    public function mount(): void
    {
        $this->users = User::with(['biodata', 'dokter'])->get()
            ->map(fn ($user) => [
                'id' => $user->id,
                'nama' => $user->biodata?->nama_lengkap
                    ?? $user->dokter?->nama_dokter
                    ?? '-',
            ])
            ->toArray();
    }

    #[On('getupdatepengajuancuti')]
    public function getUpdatePengajuanCuti(int $rowId): void
    {
        $this->pengajuan = Pengajuancuti::findOrFail($rowId);

        $this->resetValidation();

        $this->user_id = $this->pengajuan->user_id;
        $this->alasan = $this->pengajuan->alasan;

        // derive rentang tanggal dari tanggal-tanggal yang sudah tersimpan
        $tanggals = $this->pengajuan->tanggals()->pluck('tanggal')->sort();
        $this->tanggal_mulai = $tanggals->first();
        $this->tanggal_selesai = $tanggals->last();
    }

    public function update()
    {
        $this->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|max:500',
        ]);

        $periode = Carbon::parse($this->tanggal_mulai)->toPeriod($this->tanggal_selesai);
        $tanggals = collect($periode)->map(fn ($t) => $t->format('Y-m-d'))->values();

        // bentrok dengan pengajuan lain yang masih aktif di DB (kecuali dirinya sendiri)
        $bentrokDb = Pengajuancutitanggal::whereIn('tanggal', $tanggals)
            ->whereHas('pengajuanCuti', fn ($q) => $q->where('user_id', $this->user_id)
                ->where('id', '!=', $this->pengajuan->id)
                ->whereIn('status', ['diajukan', 'disetujui']))
            ->exists();

        if ($bentrokDb) {
            $this->addError('tanggal_mulai', 'Terdapat tanggal yang sudah diajukan/disetujui sebelumnya untuk karyawan ini.');
            return;
        }

        DB::transaction(function () use ($tanggals) {
            $this->pengajuan->update([
                'user_id' => $this->user_id,
                'alasan' => $this->alasan,
            ]);

            // ganti tanggal lama dengan yang baru
            $this->pengajuan->tanggals()->delete();
            $this->pengajuan->tanggals()->createMany(
                $tanggals->map(fn ($t) => ['tanggal' => $t])->toArray()
            );
        });

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Pengajuan Cuti Berhasil Diperbarui',
        ]);
        $this->dispatch('closemodaleditpengajuancuti');
        return redirect()->route('cuti.data');
    }

    public function render()
    {
        return view('livewire.cuti.update');
    }
}