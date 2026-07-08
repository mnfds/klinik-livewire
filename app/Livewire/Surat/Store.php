<?php

namespace App\Livewire\Surat;

use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\PasienTerdaftar;
use App\Models\PoliKlinik;
use App\Models\RekamMedis;
use App\Models\SuratKeterangan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Store extends Component
{
    // DATA SURAT (masuk DB)
    public $no_surat, $mulai_berlaku, $selesai_berlaku, $tipe_ttd, $sakit;
    public $harga_surat;
    // DATA PASIEN TERDAFTAR (masuk DB)
    public $pasien_id, $pasien_nama = '';
    public $dokter_id;

    // Hanya untuk kontrol DOM, tidak disimpan ke DB
    public $jenis_surat = '';
    public $search_pasien = '';       // keyword pencarian
    public $show_dropdown = false;    // kontrol tampil dropdown
    public $hasil_pasien = [];        // hasil pencarian

    // Data statis
    public $daftar_dokter = [];

    public function mount()
    {
        $this->no_surat = '';
        $this->daftar_dokter = Dokter::select('id', 'nama_dokter')->orderBy('nama_dokter')->get()->toArray();
    }
    
    private function getRomanMonth(int $month): string
    {
        $roman = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];

        return $roman[$month];
    }

    private function generateNoSurat(): string
    {
        $lastNoSurat = SuratKeterangan::max('no_surat');
        $increment   = $lastNoSurat ? ((int) explode('/', $lastNoSurat)[0]) + 1 : 1;
        $nomor       = str_pad($increment, 3, '0', STR_PAD_LEFT);
        $tanggal     = \Carbon\Carbon::parse($this->mulai_berlaku);
        $bulan       = $this->getRomanMonth($tanggal->month);
        $tahun       = $tanggal->year;

        return "{$nomor}/KL-DRL/{$bulan}/{$tahun}";
    }

    public function updatedSearchPasien($value)
    {
        if (strlen($value) < 2) {
            $this->hasil_pasien = [];
            $this->show_dropdown = false;
            return;
        }

        $this->hasil_pasien = Pasien::select('id', 'nama', 'no_register')
            ->where('nama', 'like', "%{$value}%")
            ->orWhere('no_register', 'like', "%{$value}%")
            ->limit(10)
            ->get()
            ->toArray();

        $this->show_dropdown = true;
    }

    public function pilihPasien($id, $nama)
    {
        $this->pasien_id = $id;
        $this->pasien_nama = $nama;
        $this->search_pasien = $nama; // isi input dengan nama yang dipilih
        $this->show_dropdown = false;
        $this->hasil_pasien = [];
    }

    public function render()
    {
        return view('livewire.surat.store');
    }

    public function store()
    {
        $this->validate([
            'pasien_id'       => 'required|exists:pasiens,id',
            'dokter_id'       => 'required|exists:dokters,id',
            'tipe_ttd'        => 'required|in:digital,basah',
            'jenis_surat'     => 'required|in:standar,lengkap,sakit',
            'mulai_berlaku'   => 'required|date',
            'selesai_berlaku' => 'required|integer',
            'sakit'           => 'nullable|required_if:jenis_surat,sakit|string|max:255',
        ]);

        if (! Gate::allows('akses', 'Surat Keterangan Tambah')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        $tanggal_selesai_berlaku = Carbon::parse($this->mulai_berlaku)
            ->addDays((int) $this->selesai_berlaku)
            ->format('Y-m-d');
        $pasienTerdaftar = $this->pasienTerdaftarCreate();
        $rekamMedis = $this->rekamMedisCreate($pasienTerdaftar);
        $noSurat = $this->generateNoSurat();
        $harga = (int) ($this->harga_surat ?? 0);
        SuratKeterangan::create([
            'pasien_terdaftar_id' => $pasienTerdaftar->id,
            'no_surat'            => $noSurat,
            'mulai_berlaku'       => $this->mulai_berlaku,
            'selesai_berlaku'     => $tanggal_selesai_berlaku,
            'tipe_ttd'            => $this->tipe_ttd,
            'harga_surat'         => $harga,
            'jenis_surat'         => $this->jenis_surat,
            'sakit'               => $this->sakit,
        ]);

        $this->dispatch('closeStoreModal');
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Surat berhasil ditambahkan.'
        ]);
        $this->reset([
            'sakit', 'jenis_surat', 'mulai_berlaku', 'selesai_berlaku', 'tipe_ttd',
            'pasien_id', 'pasien_nama', 'search_pasien', 'dokter_id'
        ]);
        return redirect()->route('surat.data');
    }

    public function pasienTerdaftarCreate(): PasienTerdaftar
    {
        $poliId = PoliKlinik::where('kode', 'UMM')->value('id');

        return PasienTerdaftar::create([
            'pasien_id'         => $this->pasien_id,
            'poli_id'           => $poliId,
            'dokter_id'         => $this->dokter_id,
            'tanggal_kunjungan' => now()->setTimezone('Asia/Makassar')->toDateString(),
            'waktu_tiba'        => Carbon::now('Asia/Makassar')->setTimezone('UTC')->toIso8601String(),
            'jenis_kunjungan'   => $this->jenis_surat === 'sakit' ? 'sakit' : 'sehat',
            'status_terdaftar'  => 'pembayaran',
            'only_surat'        => true,
            'encounter_id'      => null,
        ]);
    }

    private function rekamMedisCreate(PasienTerdaftar $pasienTerdaftar): RekamMedis
    {
        $dokter = Dokter::find($this->dokter_id);

        return RekamMedis::create([
            'pasien_terdaftar_id' => $pasienTerdaftar->id,
            'nama_dokter'         => $dokter->nama_dokter,
            'keluhan_utama'       => $this->jenis_surat,
            'tingkat_kesadaran'   => 'Sadar Baik/Alert',
        ]);
    }
}
