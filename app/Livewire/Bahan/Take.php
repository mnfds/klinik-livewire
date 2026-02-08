<?php

namespace App\Livewire\Bahan;

use Livewire\Component;
use App\Models\BahanBaku;
use App\Models\MutasiBahanbaku;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class Take extends Component
{
    public $bahan_baku_id, $jumlah, $satuan, $diajukan_oleh, $catatan;
    public $tipe = 'keluar';

    public $bahan = [];

    public function mount()
    {
        $this->bahan = BahanBaku::all();
    }

    public function render()
    {
        return view('livewire.bahan.take');
    }

    public function store()
    {
        $this->validate([
            'bahan_baku_id'   => 'required',
            'jumlah'   => 'required|numeric',
            'catatan'   => 'nullable',
        ]);
        if (! Gate::allows('akses', 'Persediaan Bahan Baku Keluar')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        $this->hitungStokBahanBaku();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data Bahan Baku berhasil Diperbarui.'
        ]);

        $this->reset();

        $this->dispatch('pg:eventRefresh-DishTable');

        $this->dispatch('closetakeModalBahanbaku');

        return redirect()->route('bahanbaku.data');
    }

    protected function hitungStokBahanBaku()
    {
        DB::transaction(function () {

            $bahan = BahanBaku::lockForUpdate()->findOrFail($this->bahan_baku_id);

            $jumlahKeluar = (int) $this->jumlah;
            $pengali      = (int) $bahan->pengali;

            $stokBesar = (int) $bahan->stok_besar;
            $stokKecil = (int) $bahan->stok_kecil;

            // 1️⃣ Pastikan stok total cukup (dalam satuan kecil)
            $totalStokKecil = ($stokBesar * $pengali) + $stokKecil;

            if ($totalStokKecil < $jumlahKeluar) {
                throw new \Exception('Stok bahan baku tidak mencukupi');
            }

            // 2️⃣ Jika stok kecil kurang, konversi stok besar → kecil
            while ($stokKecil < $jumlahKeluar) {

                if ($stokBesar <= 0) {
                    throw new \Exception('Stok besar habis');
                }

                // stok besar keluar 1
                $stokBesar--;

                $this->simpanMutasi(
                    $bahan->id,
                    'keluar',
                    1,
                    $bahan->satuan_besar,
                    'Konversi stok besar ke kecil'
                );

                // stok kecil masuk sesuai pengali
                $stokKecil += $pengali;

                $this->simpanMutasi(
                    $bahan->id,
                    'masuk',
                    $pengali,
                    $bahan->satuan_kecil,
                    'Hasil konversi dari stok besar'
                );
            }

            // 3️⃣ Kurangi stok kecil sesuai pemakaian
            $stokKecil -= $jumlahKeluar;

            $this->simpanMutasi(
                $bahan->id,
                'keluar',
                $jumlahKeluar,
                $bahan->satuan_kecil,
                'Bahan Baku Outstock'
            );

            // 4️⃣ Update stok akhir di tabel bahan
            $bahan->update([
                'stok_besar' => $stokBesar,
                'stok_kecil' => $stokKecil,
            ]);
        });
    }

    protected function simpanMutasi(
        int $bahanId,
        string $jenis,     // masuk | keluar
        int $jumlah,
        string $satuan,    // besar | kecil
        string $keterangan,
    ) {
        MutasiBahanBaku::create([
            'bahan_baku_id' => $bahanId,
            'tipe'         => $jenis,
            'jumlah'        => $jumlah,
            'satuan'        => $satuan,
            'diajukan_oleh' => Auth::user()->biodata?->nama_lengkap,
            'catatan'    => $keterangan,
        ]);
    }

}
