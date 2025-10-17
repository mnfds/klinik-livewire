<?php

namespace App\Livewire\Reservasi;

use App\Models\Dokter;
use App\Models\Pasien;
use Livewire\Component;
use App\Models\Reservasi;
use App\Models\PoliKlinik;

class Update extends Component
{
    public $reservasi_id;
    public $pasien_id, $poli_id, $dokter_id, $tanggal_reservasi;
    public $jam_reservasi, $status, $nominal_pembayaran, $catatan;
    
    public $nama_pasien;
    public $polis = [];
    public $dokters = [];

    public function mount()
    {
        $this->polis = PoliKlinik::select('id', 'nama_poli')->get();
        $this->dokters = Dokter::select('id', 'nama_dokter')->get();
    }

    #[\Livewire\Attributes\On('editreservasi')]
    public function editreservasi($rowId): void
    {
        $this->reservasi_id = $rowId;

        $reservasi = Reservasi::findOrFail($rowId);

        $this->pasien_id = $reservasi->pasien_id;
        $this->nama_pasien = $reservasi->pasien->nama ?? '-';
        $this->poli_id = $reservasi->poli_id;
        $this->dokter_id = $reservasi->dokter_id;
        $this->tanggal_reservasi = $reservasi->tanggal_reservasi;
        $this->jam_reservasi = $reservasi->jam_reservasi;
        $this->status = $reservasi->status;
        $this->nominal_pembayaran = $reservasi->nominal_pembayaran;
        $this->catatan = $reservasi->catatan;

        $this->dispatch('openModal');
    }

    public function update()
    {
        $this->validate([
            'pasien_id'             => 'required|exists:pasiens,id',
            'poli_id'               => 'required|exists:poli_kliniks,id',
            'dokter_id'             => 'nullable|exists:dokters,id',
            'tanggal_reservasi'     => 'required',
            'jam_reservasi'         => 'nullable',
            'status'                => 'required|in:belum bayar,belum lunas,lunas,selesai,batal',
            'nominal_pembayaran'    => 'nullable|numeric|min:0',
            'catatan'               => 'nullable|string',
        ]);

        Reservasi::where('id', $this->reservasi_id)->update([
            'pasien_id'          => $this->pasien_id,
            'poli_id'            => $this->poli_id,
            'dokter_id'          => $this->dokter_id,
            'tanggal_reservasi'  => $this->tanggal_reservasi,
            'jam_reservasi'      => $this->jam_reservasi,
            'status'             => $this->status,
            'nominal_pembayaran' => $this->nominal_pembayaran,
            'catatan'            => $this->catatan,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil diperbarui.'
        ]);

        // ❌ Tutup modal via JS
        $this->dispatch('closeModal');

        // 🔄 Reset form
        $this->reset();

        return redirect()->route('reservasi.data'); // untuk PowerGrid refresh
    }

    public function render()
    {
        return view('livewire.reservasi.update');
    }
}
