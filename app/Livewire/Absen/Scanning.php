<?php

namespace App\Livewire\Absen;

use App\Models\Absen;
use App\Models\Biodata;
use App\Models\Jadwal;
use App\Models\Kuotalibur;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Component;

class Scanning extends Component
{
    public $sistem_code_qr;
    public $scannedData; // hasil scan tampil dari sini
    public $scannedUserId;
    public $booleanScan = false; //default warna
    public $absenStatus;
    public $absen;

    //DATA ABSEN
    public $sisaKuotaLibur = 0;
    public $jumlahTerlambat = 0;
    public $jumlahTepatWaktu = 0;
    public $jumlahAlpha = 0;

    public $staff = [];

    public function mount()
    {
        $this->staff = Biodata::all();
        $this->absen = Absen::where('user_id', Auth::id())
        ->whereDate('tanggal_absen', today())
        ->first();
        $this->hitungStatistikAbsen();
    }
    
    // =====================
    // ABSENSI DENGAN SCAN
    // =====================
    #[\Livewire\Attributes\On('qrScanned')]
    public function handleQrScanned(string $result): void
    {
        $this->sistem_code_qr = today()->format('Ymd');
        if (! Gate::allows('akses', 'Absen Scan User')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->scannedData   = "Anda Tidak Memiliki Akses";
            return;
        }
        if ($result === $this->sistem_code_qr) {
            $this->scannedUserId = Auth::id();
            $this->scannedData   = Auth::user()->biodata->nama_lengkap;
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

    // =====================
    // ABSENSI DENGAN BUTTON
    // =====================
    public function absenMasuk()
    {
        if (! Gate::allows('akses', 'Absen Button')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
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
        if (! Gate::allows('akses', 'Absen Button')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
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

    protected function hitungStatistikAbsen(): void
    {
        $userId = Auth::id();
        $awalBulan = Carbon::now()->startOfMonth();
        $akhirBulan = Carbon::now()->endOfMonth();

        // Ambil semua jadwal user bulan ini beserta jam kerjanya
        $jadwalBulanIni = Jadwal::where('user_id', $userId)
            ->whereBetween('tanggal', [$awalBulan, $akhirBulan])
            ->with('jamkerja')
            ->get()
            ->keyBy(fn($j) => Carbon::parse($j->tanggal)->format('Y-m-d'));

        // Ambil semua absen user bulan ini
        $absenBulanIni = Absen::where('user_id', $userId)
            ->whereBetween('tanggal_absen', [$awalBulan, $akhirBulan])
            ->get()
            ->keyBy(fn($a) => $a->tanggal_absen->format('Y-m-d'));

        $liburTerpakaiBulanIni = 0;
        $terlambat = 0;
        $tepatWaktu = 0;
        $alpha = 0;

        // Loop tiap hari dari awal bulan sampai hari ini (atau akhir bulan)
        $periode = Carbon::now()->isSameMonth($awalBulan)
            ? Carbon::today()
            : $akhirBulan;

        for ($tanggal = $awalBulan->copy(); $tanggal->lte($periode); $tanggal->addDay()) {
            $key = $tanggal->format('Y-m-d');
            $jadwal = $jadwalBulanIni->get($key);
            $absen = $absenBulanIni->get($key);

            // Hari libur sesuai jadwal
            if ($jadwal && $jadwal->jamkerja?->tipe_shift === 'libur') {
                $liburTerpakaiBulanIni++;
                continue;
            }

            // Tidak ada jadwal kerja sama sekali, skip (bukan tanggung jawab user)
            if (!$jadwal) {
                continue;
            }

            // Ada jadwal kerja tapi tidak ada absen -> alpha
            if (!$absen) {
                $alpha++;
                continue;
            }

            // Bandingkan jam masuk absen vs jam mulai kerja
            $jamMulaiJadwal = $jadwal->jamkerja?->jam_mulai;
            if ($jamMulaiJadwal && $absen->jam_masuk) {
                $batasTepatWaktu = Carbon::parse($jamMulaiJadwal);
                $jamMasukAktual = Carbon::parse($absen->jam_masuk);

                if ($jamMasukAktual->gt($batasTepatWaktu)) {
                    $terlambat++;
                } else {
                    $tepatWaktu++;
                }
            }
        }

        // Ambil data kuota libur bulan ini
        $kuotaLibur = Kuotalibur::where('user_id', $userId)
            ->where('bulan', Carbon::now()->month)
            ->where('tahun', Carbon::now()->year)
            ->first();

        $totalKuota = ($kuotaLibur->kuota_dimiliki ?? 4) + ($kuotaLibur->kuota_sisa_bulan_sebelumnya ?? 0);
        $this->sisaKuotaLibur = max(0, $totalKuota - $liburTerpakaiBulanIni);
        $this->jumlahTerlambat = $terlambat;
        $this->jumlahTepatWaktu = $tepatWaktu;
        $this->jumlahAlpha = $alpha;
    }

    public function render()
    {
        return view('livewire.absen.scanning');
    }
}
