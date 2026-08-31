<?php

namespace App\Livewire\Cuti;

use App\Models\Jadwal;
use App\Models\JamKerja;
use App\Models\Kuotacuti;
use App\Models\Pengajuancuti;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;

class Approval extends Component
{
    public ?Pengajuancuti $pengajuan = null;

    // 'approve' | 'tolak'
    public string $mode = '';

    public string $catatan_admin = '';

    #[On('getapprovecuti')]
    public function getApproveCuti(int $rowId): void
    {
        $this->mode = 'approve';
        $this->loadPengajuan($rowId);
    }

    #[On('getdeniedcuti')]
    public function getDeniedCuti(int $rowId): void
    {
        $this->mode = 'tolak';
        $this->loadPengajuan($rowId);
    }

    protected function loadPengajuan(int $rowId): void
    {
        $this->resetValidation();
        $this->catatan_admin = '';
        $this->pengajuan = Pengajuancuti::with(['user.biodata', 'user.dokter', 'tanggals'])->findOrFail($rowId);
    }

    public function confirm()
    {
        Gate::authorize('akses', 'Persetujuan Pengajuan Cuti');

        if ($this->mode === 'approve') {
            $this->approve();
        } elseif ($this->mode === 'tolak') {
            $this->tolak();
        }
    }

    protected function approve(): void
    {
        $cutiJamKerja = JamKerja::where('tipe_shift', 'cuti')->first();

        if (! $cutiJamKerja) {
            $this->addError('mode', 'Jam Kerja dengan tipe shift "cuti" belum tersedia. Hubungi admin sistem.');
            return;
        }

        // hitung kebutuhan kuota per tahun (rentang cuti bisa melewati pergantian tahun)
        $tanggals = $this->pengajuan->tanggals;
        $kebutuhanPerTahun = $tanggals->groupBy(fn ($t) => $t->tanggal->year)
            ->map(fn ($group) => $group->count());

        foreach ($kebutuhanPerTahun as $tahun => $jumlahHari) {
            $kuota = Kuotacuti::where('user_id', $this->pengajuan->user_id)
                ->where('tahun', $tahun)
                ->first();

            if (! $kuota || $kuota->kuota_sisa < $jumlahHari) {
                $sisa = $kuota->kuota_sisa ?? 0;
                $this->addError('mode', "Kuota cuti tahun {$tahun} tidak mencukupi (sisa {$sisa}, butuh {$jumlahHari}).");
                return;
            }
        }

        DB::transaction(function () use ($cutiJamKerja, $kebutuhanPerTahun) {
            foreach ($this->pengajuan->tanggals as $tgl) {
                $jadwal = Jadwal::firstOrNew([
                    'user_id' => $this->pengajuan->user_id,
                    'tanggal' => $tgl->tanggal,
                ]);

                $jamkerjaSebelumnya = $jadwal->exists ? $jadwal->jamkerja_id : null;

                $jadwal->jamkerja_id = $cutiJamKerja->id;
                $jadwal->save();

                $tgl->update([
                    'jadwal_id' => $jadwal->id,
                    'jamkerja_id_sebelumnya' => $jamkerjaSebelumnya,
                ]);
            }

            foreach ($kebutuhanPerTahun as $tahun => $jumlahHari) {
                Kuotacuti::where('user_id', $this->pengajuan->user_id)
                    ->where('tahun', $tahun)
                    ->increment('kuota_terpakai', $jumlahHari);
            }

            $this->pengajuan->update([
                'status' => 'disetujui',
                'catatan_admin' => $this->catatan_admin ?: null,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        });

        $this->selesai('Pengajuan Cuti Berhasil Disetujui');
    }

    protected function tolak(): void
    {
        $this->validate([
            'catatan_admin' => 'required|string|max:500',
        ], [
            'catatan_admin.required' => 'Catatan penolakan wajib diisi.',
        ]);

        $this->pengajuan->update([
            'status' => 'ditolak',
            'catatan_admin' => $this->catatan_admin,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->selesai('Pengajuan Cuti Ditolak');
    }

    protected function selesai(string $pesan): void
    {
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $pesan,
        ]);
        $this->dispatch('closemodalapprovalpengajuancuti');
        $this->reset(['pengajuan', 'mode', 'catatan_admin']);
    }

    public function render()
    {
        return view('livewire.cuti.approval');
    }
}