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
            'potongan' => 0,
            'diskon' => 0,
            'harga_asli' => 0,
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

        if (in_array($field, ['produk_id', 'jumlah_produk', 'potongan', 'diskon'])) {
            $row = $this->obat_estetika[$uuid];

            if ($row['produk_id']) {
                $produk   = $this->produk->find($row['produk_id']);
                $harga    = (int) ($produk->harga_dasar ?? 0);
                $jumlah   = (int) ($row['jumlah_produk'] ?? 1);
                $potongan = (int) ($row['potongan'] ?? 0);
                $diskon   = (float) ($row['diskon'] ?? 0);

                $hargaAsli = $harga * $jumlah;
                $subtotal  = $hargaAsli - $potongan;
                $subtotal -= $subtotal * ($diskon / 100);

                $this->obat_estetika[$uuid]['harga_asli'] = (int) max($hargaAsli, 0);
                $this->obat_estetika[$uuid]['subtotal']   = (int) max($subtotal, 0);
            } else {
                $this->obat_estetika[$uuid]['harga_asli'] = 0;
                $this->obat_estetika[$uuid]['subtotal']   = 0;
            }
        }
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
        // Hitung total harga
        $total = collect($this->obat_estetika)
            ->sum(fn($item) => (int) $item['subtotal']);

        // Generate no_transaksi unik
        $noTransaksi = 'TRX-' . now()->format('YmdHis');

        // Simpan transaksi utama
        $transaksi = TransaksiApotik::create([
            'no_transaksi' => $noTransaksi,
            'kasir_nama'   => Auth::user()->biodata?->nama_lengkap ?? Auth::user()->name ?? 'Kasir',
            'tanggal'      => now(),
            'total_harga'  => $total,
            'pasien_id'  => $this->pasien_id,
        ]);

        // Simpan riwayat detail
        foreach ($this->obat_estetika as $row) {
            $transaksi->riwayat()->create([
                'produk_id'     => $row['produk_id'],
                'jumlah_produk' => $row['jumlah_produk'] ?? 0,
                'potongan'      => $row['potongan'] ?: 0,
                'diskon'        => $row['diskon'] ?: 0,
                'subtotal'      => $row['subtotal'] ?? 0,
            ]);
        }

        // Reset form setelah simpan
        $this->reset('obat_estetika');

        // Optional: tampilkan notifikasi
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Transaksi berhasil disimpan!',
        ]);

        return redirect()->route('apotik.kasir');
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