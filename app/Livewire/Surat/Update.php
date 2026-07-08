<?php

namespace App\Livewire\Surat;

use App\Models\SuratKeterangan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Update extends Component
{
    public $suratId;
    public $mulai_berlaku, $selesai_berlaku, $tipe_ttd, $harga_surat, $jenis_surat, $sakit;
    public $jumlah_hari_berlaku;

    #[\Livewire\Attributes\On('getupdatesurat')]
    public function getSurat($rowId): void
    {
        $this->suratId = $rowId;

        $surat = SuratKeterangan::findOrFail($rowId);

        $this->mulai_berlaku   = $surat->mulai_berlaku;
        $this->selesai_berlaku = $surat->selesai_berlaku;
        $this->jumlah_hari_berlaku = Carbon::parse($surat->mulai_berlaku)->diffInDays(Carbon::parse($surat->selesai_berlaku));
        $this->tipe_ttd        = $surat->tipe_ttd;
        $this->harga_surat     = $surat->harga_surat;
        $this->jenis_surat     = $surat->jenis_surat;
        $this->sakit           = $surat->sakit;

        $this->dispatch('openUpdateModal');
    }

    public function update()
    {
        if (! Gate::allows('akses', 'Surat Keterangan Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        $this->validate([
            'tipe_ttd'              => 'required|in:digital,basah',
            'jenis_surat'           => 'required|in:standar,lengkap,sakit',
            'mulai_berlaku'         => 'required|date',
            'jumlah_hari_berlaku'   => 'required|integer',
            'harga_surat'           => 'nullable|numeric|min:0',
            'sakit'                 => 'nullable|required_if:jenis_surat,sakit|string|max:255',
        ]);
        
        $this->selesai_berlaku = Carbon::parse($this->mulai_berlaku)
            ->addDays((int) $this->jumlah_hari_berlaku)
            ->format('Y-m-d');
        SuratKeterangan::where('id', $this->suratId)->update([
            'mulai_berlaku'   => $this->mulai_berlaku,
            'selesai_berlaku' => $this->selesai_berlaku,
            'tipe_ttd'        => $this->tipe_ttd,
            'harga_surat'     => (int) ($this->harga_surat ?? 0),
            'jenis_surat'     => $this->jenis_surat,
            'sakit'           => $this->jenis_surat === 'sakit' ? $this->sakit : null,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Surat keterangan berhasil diperbarui.',
        ]);

        $this->dispatch('closeUpdateModal');
        $this->reset();

        return redirect()->route('surat.data');
    }

    public function render()
    {
        return view('livewire.surat.update');
    }
}
