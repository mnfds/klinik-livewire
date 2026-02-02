<?php

namespace App\Livewire\Biodata;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Dokter extends Component
{

    use WithFileUploads;

    public $nama_dokter, $nik, $alamat_dokter, $jenis_kelamin, $telepon;
    public $tingkat_pendidikan, $institusi, $tahun_kelulusan;
    public $no_str, $ihs, $surat_izin_pratik, $masa_berlaku_sip;
    public $ttd_digital, $foto_wajah;
    public $ttd_digital_preview, $foto_wajah_preview;

    public function mount()
    {
        $dokter = Auth::user()->dokter;

        if ($dokter) {
            $this->nama_dokter = $dokter->nama_dokter;
            $this->nik = $dokter->nik;
            $this->alamat_dokter = $dokter->alamat_dokter;
            $this->telepon = $dokter->telepon;
            $this->jenis_kelamin = $dokter->jenis_kelamin;

            $this->tingkat_pendidikan = $dokter->tingkat_pendidikan;
            $this->institusi = $dokter->institusi;
            $this->tahun_kelulusan = $dokter->tahun_kellulusan;

            $this->ihs = $dokter->ihs;
            $this->no_str = $dokter->no_str;
            $this->surat_izin_pratik = $dokter->surat_izin_pratik;
            $this->masa_berlaku_sip = $dokter->masa_berlaku_sip;
            
            $this->foto_wajah_preview = $dokter->foto_wajah;
            $this->ttd_digital_preview = $dokter->ttd_digital;
        }
    }

    public function save()
    {
        $this->validate([
            'nama_dokter' => 'required|string|max:255',
            'nik' => 'nullable|string|max:255',
            'alamat_dokter' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'jenis_kelamin' => 'required|in:L,P',

            'tingkat_pendidikan' => 'nullable|string',
            'institusi' => 'nullable|string',
            'tahun_kelulusan' => 'nullable|date',

            'ihs' => 'nullable|string',
            'no_str' => 'nullable|string',
            'surat_izin_pratik' => 'nullable|string',
            'masa_berlaku_sip' => 'nullable|date',

            'foto_wajah' => 'nullable|image|max:1024',
            'ttd_digital' => 'nullable|file|max:1024',

        ]);

        $user = Auth::user();
        $dokter = $user->dokter;

        $data = [
            'nama_dokter'   => $this->nama_dokter,
            'nik'         => $this->nik,
            'alamat_dokter'         => $this->alamat_dokter,
            'telepon'        => $this->telepon,
            'jenis_kelamin'  => $this->jenis_kelamin,

            'tingkat_pendidikan'   => $this->tingkat_pendidikan,
            'institusi'  => $this->institusi,
            'tahun_kelulusan'  => $this->tahun_kelulusan,

            'ihs' => $this->ihs,
            'no_str' => $this->no_str,
            'surat_izin_pratik' => $this->surat_izin_pratik,
            'masa_berlaku_sip' => $this->masa_berlaku_sip,
        ];

        // Simpan file foto jika diunggah
        if ($this->foto_wajah) {
            // Hapus foto lama jika ada
            if ($dokter && $dokter->foto_wajah && Storage::disk('public')->exists($dokter->doto_wajah)) {
                Storage::disk('public')->delete($dokter->foto_wajah);
            }

            // Simpan foto baru
            $path = $this->foto_wajah->store('foto_wajah', 'public');
            $data['foto_wajah'] = $path;
        }

        // Simpan file TTD jika diunggah
        if ($this->ttd_digital) {
            // Hapus ttd lama jika ada
            if ($dokter && $dokter->ttd_digital && Storage::disk('public')->exists($dokter->ttd_digital)) {
                Storage::disk('public')->delete($dokter->ttd_digital);
            }

            // Simpan ttd baru
            $path = $this->ttd_digital->store('ttd_digital', 'public');
            $data['ttd_digital'] = $path;
        }

        if ($dokter) {
            $dokter->update($data);
        } else {
            $data['user_id'] = $user->id;
            Dokter::create($data);
        }

        session()->flash('message', 'Biodata berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.biodata.dokter');
    }
}
