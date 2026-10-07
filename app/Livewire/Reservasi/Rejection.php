<?php

namespace App\Livewire\Reservasi;

use App\Models\PermintaanReservasi;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;

class Rejection extends Component
{
    public const ALASAN = [
        'jadwal_penuh' => 'Jadwal dokter penuh',
        'dokter_tidak_praktik' => 'Dokter tidak praktik pada tanggal tersebut',
        'poli_tutup' => 'Poliklinik tutup pada tanggal tersebut',
        'data_tidak_valid' => 'Data pasien tidak valid',
        'duplikat' => 'Permintaan duplikat',
        'lainnya' => 'Lainnya',
    ];

    public ?PermintaanReservasi $permintaan = null;

    public $alasan_penolakan;

    #[On('gettolak')]
    public function getTolak(int $rowId): void
    {
        $this->resetValidation();

        $this->permintaan = PermintaanReservasi::findOrFail($rowId);
        $this->alasan_penolakan = null;
    }

    public function confirm(): void
    {
        if (! Gate::allows('akses', 'Persetujuan Reservasi')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        if (! $this->permintaan || $this->permintaan->status !== 'menunggu') {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Permintaan ini sudah diproses.',
            ]);
            return;
        }

        $validated = $this->validate([
            'alasan_penolakan' => 'required|in:' . implode(',', array_keys(self::ALASAN)),
        ], [
            'alasan_penolakan.required' => 'Pilih alasan penolakan terlebih dahulu.',
            'alasan_penolakan.in' => 'Alasan penolakan tidak valid.',
        ]);

        $this->permintaan->update([
            'status' => 'ditolak',
            'alasan_penolakan' => $validated['alasan_penolakan'],
        ]);

        $this->selesai('Permintaan reservasi berhasil ditolak.');
    }

    protected function selesai(string $pesan): void
    {
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $pesan,
        ]);
        $this->dispatch('closemodaltolakreservasi');
        $this->dispatch('refresh-PermintaanTable');
    }

    public function render()
    {
        return view('livewire.reservasi.rejection');
    }
}