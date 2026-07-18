<?php

namespace App\Livewire\Absen;

use App\Models\Absen;
use App\Models\Biodata;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Component;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ScanSistem extends Component
{
    public $sistem_code_qr;
    public $user_code_qr;
    public $scannedData; // hasil scan tampil dari sini
    public $scannedUserId;
    public $booleanScan = false; //default warna
    public $absenStatus;

    public $staff = [];

    public function mount()
    {
        $this->staff = Biodata::all();
        $this->sistem_code_qr = today()->format('Ymd');
    }

    public function generateQrCodeUser(): string
    {
        return QrCode::size(200)
            ->errorCorrection('H')
            ->generate($this->sistem_code_qr);
    }
    // =====================
    // ABSENSI DENGAN SCAN
    // =====================
    #[\Livewire\Attributes\On('qrScanned')]
    public function handleQrScanned(string $result): void
    {
        $biodata_ditemukan = Biodata::where('user_code_qr', $result)->first();
        if (! Gate::allows('akses', 'Absen Scan Sistem')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->scannedData   = "Anda Tidak Memiliki Akses";
            return;
        }
        if ($biodata_ditemukan) {
            $this->user_code_qr  = $biodata_ditemukan->user_code_qr;
            $this->scannedData   = $biodata_ditemukan->nama_lengkap;
            $this->scannedUserId = $biodata_ditemukan->user_id;
            $this->booleanScan   = true;

            $this->prosesAbsen(); // langsung teruskan ke proses absen
        } else {
            $this->scannedData   = "QR Code Salah / Tidak Dikenali";
            $this->scannedUserId = null;
            $this->booleanScan   = false;
        }
    }

    public function prosesAbsen()
    {
        if (!$this->scannedUserId) {
            $this->dispatch('toast', type: 'error', message: 'QR Tidak Dikenali.');
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
            $this->dispatch('toast', type: 'success', message: "{$this->scannedData} Berhasil Melakukan Absen Masuk.");
            return;
        }

        // Sudah masuk, belum pulang -> absen pulang
        if ($absen->jam_masuk && !$absen->jam_pulang) {
            $absen->update(['jam_pulang' => now()]);

            $this->absenStatus = 'pulang';
            $this->dispatch('toast', type: 'success', message: "{$this->scannedData} Berhasil Melakukan Absen Keluar.");
            return;
        }

        // Sudah masuk & pulang -> tolak
        $this->absenStatus = null;
        $this->dispatch('toast', type: 'error', message: "{$this->scannedData} Sudah Melakukan Absen Hari Ini.");
    }

    public function resetScan(): void
    {
        $this->scannedData = null;
        $this->booleanScan = false;
        $this->dispatch('startScanner');
    }

    public function render()
    {
        return view('livewire.absen.scan-sistem',[
            'qrUserImage' => $this->generateQrCodeUser(),
        ]);
    }
}
