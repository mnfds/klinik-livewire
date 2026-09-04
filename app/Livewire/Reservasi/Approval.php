<?php

namespace App\Livewire\Reservasi;

use App\Models\Dokter;
use App\Models\PermintaanReservasi;
use App\Models\PoliKlinik;
use App\Models\Reservasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;

class Approval extends Component
{
    public ?PermintaanReservasi $permintaan = null;

    public $pasien_id;
    public $poli_id;
    public $dokter_id;
    public $tanggal_reservasi;
    public $jam_reservasi;
    public $catatan;

    public $polis = [];
    public $dokters = [];

    #[On('getapprove')]
    public function getApprove(int $rowId): void
    {
        $this->resetValidation();

        $this->permintaan = PermintaanReservasi::with(['poliklinik', 'dokter'])->findOrFail($rowId);

        $this->pasien_id = null;
        $this->poli_id = $this->permintaan->poli_id;
        $this->dokter_id = $this->permintaan->dokter_id;
        $this->tanggal_reservasi = \Carbon\Carbon::parse($this->permintaan->tanggal_reservasi)->format('Y-m-d');
        $this->jam_reservasi = $this->permintaan->jam_reservasi
            ? \Carbon\Carbon::parse($this->permintaan->jam_reservasi)->format('H:i')
            : null;
        $this->catatan = $this->permintaan->catatan;

        $this->polis = PoliKlinik::where('status', true)->select('id', 'nama_poli')->get();
        $this->dokters = Dokter::select('id', 'nama_dokter')->get();
    }

    public function confirm(): void
    {
        // Gate::authorize('akses', 'Persetujuan Reservasi');

        $validated = $this->validate([
            'pasien_id' => 'required|exists:pasiens,id',
            'poli_id' => 'required|exists:poli_kliniks,id',
            'dokter_id' => 'nullable|exists:dokters,id',
            'tanggal_reservasi' => 'required|date',
            'jam_reservasi' => 'nullable|date_format:H:i',
            'catatan' => 'nullable|string',
        ], [
            'pasien_id.required' => 'Pilih pasien terlebih dahulu.',
            'pasien_id.exists' => 'Pasien tidak ditemukan, silakan cari ulang.',
        ]);

        DB::transaction(function () use ($validated) {
            Reservasi::create($validated);

            $this->permintaan->update(['status' => 'disetujui']);
        });

        $this->selesai('Reservasi berhasil disetujui dan disimpan.');
    }

    protected function selesai(string $pesan): void
    {
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $pesan,
        ]);
        $this->dispatch('closemodalsetujuireservasi');
        $this->dispatch('refresh-PermintaanTable');
        $this->dispatch('refresh-ReservasiTable');
    }

    public function render()
    {
        return view('livewire.reservasi.approval');
    }
}