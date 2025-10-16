<?php

namespace App\Livewire\Reservasi;

use App\Models\Dokter;
use App\Models\Pasien;
use Livewire\Component;
use App\Models\Reservasi;
use App\Models\PoliKlinik;
use Livewire\Attributes\On;

class Store extends Component
{
    public $pasien_id;
    public $pasien_nama; // untuk menampilkan nama yang dipilih via AJAX
    public $poli_id;
    public $dokter_id;
    public $tanggal_reservasi;
    public $jam_reservasi;
    public $status = 'belum bayar';
    public $nominal_pembayaran;
    public $catatan;

    public $polis = [];
    public $dokters = [];

    public function mount()
    {
        $this->polis = PoliKlinik::select('id', 'nama_poli')->get();
        $this->dokters = Dokter::select('id', 'nama_dokter')->get();
    }

    public function store()
    {
        // ✅ Validasi sesuai struktur tabel
        $validated = $this->validate([
            'pasien_id' => 'required|exists:pasiens,id',
            'poli_id' => 'required|exists:poli_kliniks,id',
            'dokter_id' => 'nullable|exists:dokters,id',
            'tanggal_reservasi' => 'required|date',
            'jam_reservasi' => 'nullable|date_format:H:i',
            'status' => 'required|in:belum bayar,belum lunas,lunas,selesai,batal',
            'nominal_pembayaran' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        // ✅ Simpan ke database
        Reservasi::create($validated);

        // ✅ Reset form (status dikembalikan ke default)
        $this->reset([
            'pasien_id', 'poli_id', 'dokter_id',
            'tanggal_reservasi', 'jam_reservasi',
            'status', 'nominal_pembayaran', 'catatan',
        ]);
        $this->status = 'belum bayar';

        // ✅ Kirim notifikasi ke frontend (toast + tutup modal)
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Reservasi berhasil disimpan!',
        ]);

        $this->dispatch('closeStoreModalReservasi');

        // ✅ Redirect setelah semua event dikirim
        return $this->redirectRoute('reservasi.data', navigate: true);
    }

    public function render()
    {
        return view('livewire.reservasi.store');
    }
}
