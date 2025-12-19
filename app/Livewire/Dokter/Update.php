<?php

namespace App\Livewire\Dokter;

use App\Models\Dokter;
use Livewire\Component;
use App\Models\PoliKlinik;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class Update extends Component
{
    use WithFileUploads;

    public $dokterId;

    public $poli;
    public $poli_id;
    public $nama_dokter, $nik, $alamat_dokter, $jenis_kelamin, $telepon;
    public $tingkat_pendidikan, $institusi, $tahun_kelulusan;
    public $no_str, $ihs,$surat_izin_pratik, $masa_berlaku_sip;
    public $new_ttd_digital, $new_foto_wajah;
    public $ttd_digital_preview, $foto_wajah_preview;

    public function mount($id)
    {
        $this->poli = PoliKlinik::all();
        $this->dokterId = $id;

        $dokter = Dokter::findOrFail($id);
        $poli_id = $dokter->dokterpoli()->value('poli_id');

        $this->nama_dokter = $dokter->nama_dokter;
        $this->nik = $dokter->nik;
        $this->ihs = $dokter->ihs;
        $this->alamat_dokter = $dokter->alamat_dokter;
        $this->jenis_kelamin = $dokter->jenis_kelamin;
        $this->telepon = $dokter->telepon;

        $this->tingkat_pendidikan = $dokter->tingkat_pendidikan;
        $this->institusi = $dokter->institusi;
        $this->tahun_kelulusan = $dokter->tahun_kelulusan;
        
        $this->no_str = $dokter->no_str;
        $this->surat_izin_pratik = $dokter->surat_izin_pratik;
        $this->masa_berlaku_sip = $dokter->masa_berlaku_sip;
        
        $this->ttd_digital_preview = $dokter->ttd_digital;
        $this->foto_wajah_preview = $dokter->foto_wajah;
        $this->poli_id = $poli_id;

    }

    public function update()
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

            'new_foto_wajah' => 'nullable|image|max:1024',
            'new_ttd_digital' => 'nullable|file|max:1024',

            'poli_id' => 'required|exists:poli_kliniks,id',
        ]);
        
        if (! Gate::allows('akses', 'Dokter Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        $dokter = Dokter::findOrFail($this->dokterId);

        // Handle upload foto wajah
        if ($this->new_foto_wajah) {
            // Hapus foto lama jika ada
            if ($dokter->foto_wajah && Storage::exists($dokter->foto_wajah)) {
                Storage::delete($dokter->foto_wajah);
            }
            $fotoWajahPath = $this->new_foto_wajah->store('foto_wajah', 'public');
        } else {
            $fotoWajahPath = $dokter->foto_wajah;
        }

        // Handle upload ttd digital
        if ($this->new_ttd_digital) {
            if ($dokter->ttd_digital && Storage::exists($dokter->ttd_digital)) {
                Storage::delete($dokter->ttd_digital);
            }
            $ttdPath = $this->new_ttd_digital->store('ttd_digital', 'public');
        } else {
            $ttdPath = $dokter->ttd_digital;
        }

        // Update data dokter
        $dokter->update([
            'nama_dokter' => $this->nama_dokter,
            'nik' => $this->nik,
            'alamat_dokter' => $this->alamat_dokter,
            'telepon' => $this->telepon,
            'jenis_kelamin' => $this->jenis_kelamin,

            'tingkat_pendidikan' => $this->tingkat_pendidikan,
            'institusi' => $this->institusi,
            'tahun_kelulusan' => $this->tahun_kelulusan,

            'ihs' => $this->ihs,
            'no_str' => $this->no_str,
            'surat_izin_pratik' => $this->surat_izin_pratik,
            'masa_berlaku_sip' => $this->masa_berlaku_sip,

            'foto_wajah' => $fotoWajahPath,
            'ttd_digital' => $ttdPath,
        ]);

        // Update relasi dokter-poli
        $dokter->dokterpoli()->updateOrCreate(
            ['dokter_id' => $dokter->id],
            ['poli_id' => $this->poli_id]
        );

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data Dokter berhasil diperbarui.',
        ]);
        return redirect()->route('dokter.data'); // ganti sesuai route-mu
    }

    public function render()
    {
        return view('livewire.dokter.update');
    }
}
