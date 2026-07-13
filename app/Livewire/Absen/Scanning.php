<?php

namespace App\Livewire\Absen;

use App\Models\Absen;
use App\Models\Biodata;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Scanning extends Component
{
    public $user_code_qr;
    public $scannedData; // hasil scan tampil dari sini
    public $scannedUserId;
    public $booleanScan = false; //default warna
    public $absenStatus;

    public $staff = [];

    public function mount()
    {
        $this->staff = Biodata::all();
    }
    
    // =====================
    // ABSENSI DENGAN SCAN
    // =====================
    #[\Livewire\Attributes\On('qrScanned')]
    public function handleQrScanned(string $result): void
    {
        $biodata_ditemukan = Biodata::where('user_code_qr', $result)->first();

        if ($biodata_ditemukan) {
            $this->user_code_qr  = $biodata_ditemukan->user_code_qr;
            $this->scannedData   = $biodata_ditemukan->nama_lengkap;
            $this->scannedUserId = $biodata_ditemukan->user_id;
            $this->booleanScan   = true;

            $this->prosesAbsen(); // langsung teruskan ke proses absen

            $this->dispatch('openTakeModal');
        } else {
            $this->scannedData   = "QR Code Salah / Tidak Dikenali";
            $this->scannedUserId = null;
            $this->booleanScan   = false;

            $this->dispatch('openTakeModal'); // tetap buka modal supaya pesan error terlihat
        }
    }

    public function prosesAbsen()
    {
        if (!$this->scannedUserId) {
            $this->dispatch('notify', type: 'error', message: 'Data user tidak valid.');
            return;
        }

        $absen = Absen::where('user_id', $this->scannedUserId)
            ->where('tanggal_absen', today())
            ->first();

        // Belum ada record hari ini -> absen masuk
        if (!$absen) {
            Absen::create([
                'user_id'       => $this->scannedUserId,
                'tanggal_absen' => today(),
                'jam_masuk'     => now(),
            ]);

            $this->absenStatus = 'masuk';
            $this->dispatch('notify', type: 'success', message: "{$this->scannedData} berhasil absen masuk.");
            return;
        }

        // Sudah masuk, belum pulang -> absen pulang
        if ($absen->jam_masuk && !$absen->jam_pulang) {
            $absen->update(['jam_pulang' => now()]);

            $this->absenStatus = 'pulang';
            $this->dispatch('notify', type: 'success', message: "{$this->scannedData} berhasil absen pulang.");
            return;
        }

        // Sudah masuk & pulang -> tolak
        $this->absenStatus = null;
        $this->dispatch('notify', type: 'error', message: "{$this->scannedData} sudah menyelesaikan absen hari ini.");
    }

    public function resetScan(): void
    {
        $this->scannedData = null;
        $this->booleanScan = false;
        $this->dispatch('startScanner');
    }

    // =====================
    // ABSENSI DENGAN BUTTON
    // =====================
    public function absenMasuk()
    {
        // Cek apakah user sudah absen masuk hari ini
        $sudahAbsen = Absen::where('user_id', Auth::id())
            ->where('tanggal_absen', today())
            ->exists();

        if ($sudahAbsen) {
            $this->dispatch('toast', ['type' => 'info', 'message' => 'Anda Sudah Melakukan Absen Masuk.']);
            return;
        }

        Absen::create([
            'user_id'       => Auth::id(),
            'tanggal_absen' => today(),
            'jam_masuk'     => now(),
        ]);

        $this->dispatch('toast', ['type' => 'success', 'message' => 'Absen Masuk Berhasil Dilakukan.']);

        // refresh data yang ditampilkan di card
        $this->mount(); // atau panggil ulang method yang set $jamMasuk
    }

    public function absenPulang()
    {
        $absen = Absen::where('user_id', Auth::id())
            ->where('tanggal_absen', today())
            ->first();

        // Belum absen masuk sama sekali
        if (!$absen) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Anda Belum Melakukan Absen Masuk Hari Ini.']);
            return;
        }

        // Sudah absen masuk tapi jam_masuk kosong (edge case, harusnya tidak terjadi)
        if (!$absen->jam_masuk) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Data Absen Masuk Tidak Valid.']);
            return;
        }

        // Sudah absen pulang sebelumnya
        if ($absen->jam_pulang) {
            $this->dispatch('toast', ['type' => 'info', 'message' => 'Absen Sudah Melakukan Absen Pulang']);
            return;
        }

        $absen->update([
            'jam_pulang' => now(),
        ]);

        $this->dispatch('toast', type: 'success', message: 'Absen Pulang Berhasil Dilakukan.');
    }

    public function render()
    {
        return view('livewire.absen.scanning');
    }
}
