<?php

namespace App\Livewire\Apotik;

use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\ProdukDanObat;
use App\Models\TransaksiApotik;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Create extends Component
{
    public $produk;
    public $pasien_id;
    public $obat_estetika = [];

    public function mount()
    {
        $this->produk = ProdukDanObat::all();
        $uuid = (string) Str::uuid();
        $this->obat_estetika[$uuid] = $this->emptyRowWithUuid($uuid);
    }

    private function emptyRowWithUuid($uuid)
    {
        return [
            'produk_id' => null,
            'jumlah_produk' => 1,
            'harga_satuan' => 0,
            'potongan' => 0,
            'diskon' => 0,
            // 'harga_asli' => 0,
            'subtotal' => 0,
            'uuid' => $uuid,
        ];
    }

    public function addRow()
    {
        $uuid = (string) Str::uuid();
        $this->obat_estetika[$uuid] = $this->emptyRowWithUuid($uuid);
    }

    public function removeRow($uuid)
    {
        unset($this->obat_estetika[$uuid]);
    }

    public function updatedObatEstetika($value, $key)
    {
        [$uuid, $field] = explode('.', $key);

        if (!isset($this->obat_estetika[$uuid])) return;

        $row = $this->obat_estetika[$uuid];

        // hanya respon field penting
        if (!in_array($field, ['produk_id', 'jumlah_produk', 'potongan', 'diskon'])) {
            return;
        }

        if (!$row['produk_id']) {
            $this->obat_estetika[$uuid]['harga_satuan'] = 0;
            $this->obat_estetika[$uuid]['subtotal'] = 0;
            return;
        }

        $produk = $this->produk->find($row['produk_id']);
        if (!$produk) return;

        // ✅ Ambil dari DB
        $hargaSatuan = (int) ($produk->harga_dasar ?? 0);
        $defaultDiskon = (float) ($produk->diskon ?? 0);
        $defaultPotongan = (int) ($produk->potongan ?? 0);

        // 🔥 HANYA set default saat produk dipilih
        if ($field === 'produk_id') {
            $this->obat_estetika[$uuid]['diskon'] = $defaultDiskon;
            $this->obat_estetika[$uuid]['potongan'] = $defaultPotongan;
        }

        $jumlah   = (int) ($this->obat_estetika[$uuid]['jumlah_produk'] ?? 1);
        $potongan = (int) ($this->obat_estetika[$uuid]['potongan'] ?? 0);
        $diskon   = (float) ($this->obat_estetika[$uuid]['diskon'] ?? 0);

        $total = $hargaSatuan * $jumlah;
        $total -= ($total * $diskon / 100);
        $total -= $potongan;

        $this->obat_estetika[$uuid]['harga_satuan'] = $hargaSatuan;
        $this->obat_estetika[$uuid]['subtotal'] = max(0, (int) $total);
        // dd($this->obat_estetika);
    }

    public function create()
    {
        if (! Gate::allows('akses', 'Transaksi Apotik Tambah')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        DB::transaction(function () {

            // Hitung total harga
            $total = collect($this->obat_estetika)
                ->sum(fn($item) => (int) $item['subtotal']);

            // Generate no_transaksi unik
            $noTransaksi = 'TRX-' . now()->format('YmdHis');

            // Simpan transaksi utama
            $transaksi = TransaksiApotik::create([
                'no_transaksi' => $noTransaksi,
                'kasir_nama'   => Auth::user()->biodata?->nama_lengkap 
                                ?? Auth::user()->name 
                                ?? 'Kasir Apotik',
                'tanggal'      => now(),
                'total_harga'  => $total,
                'pasien_id'    => $this->pasien_id,
            ]);

            // Simpan detail
            foreach ($this->obat_estetika as $row) {
                $transaksi->riwayat()->create([
                    'produk_id'     => $row['produk_id'],
                    'jumlah_produk' => $row['jumlah_produk'] ?? 0,
                    'potongan'      => $row['potongan'] ?: 0,
                    'diskon'        => $row['diskon'] ?: 0,
                    'subtotal'      => $row['subtotal'] ?? 0,
                ]);
            }

            // 🔥 Kurangi stok + catat mutasi
            $this->kurangiStokApotik($transaksi, $this->obat_estetika);

            // Reset form
            $this->reset('obat_estetika');
        });

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Transaksi berhasil disimpan!',
        ]);

        return redirect()->route('apotik.kasir');
    }

    protected function kurangiStokApotik($transaksi, array $items)
    {
        foreach ($items as $row) {

            if (!isset($row['produk_id']) || ($row['jumlah_produk'] ?? 0) <= 0) {
                continue;
            }

            $produk = ProdukDanObat::lockForUpdate()->find($row['produk_id']);
            if (! $produk) continue;

            $jumlah = (int) $row['jumlah_produk'];
            if ($jumlah <= 0) continue;

            $stokBaru = max($produk->stok - $jumlah, 0);

            // Update stok
            $produk->update([
                'stok' => $stokBaru,
            ]);

            // Catat mutasi
            $produk->mutasiproduk()->create([
                'tipe' => 'keluar',
                'jumlah' => $jumlah,
                'diajukan_oleh' => Auth::user()->biodata?->nama_lengkap ?? Auth::user()->name,
                'catatan' => 'Transaksi Apotik - No: ' . $transaksi->no_transaksi,
            ]);
        }
    }

    public function render()
    {
        if (! Gate::allows('akses', 'Transaksi Apotik Tambah')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.apotik.create');
    }
}