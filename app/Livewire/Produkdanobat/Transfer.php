<?php

namespace App\Livewire\Produkdanobat;

use App\Models\BahanBaku;
use App\Models\MutasiProdukDanObat;
use Livewire\Component;
use App\Models\ProdukDanObat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class Transfer extends Component
{
    public int $activeTab = 0;
    public array $items = [
        ['produk_id' => '', 'jumlah' => '', 'catatan' => ''],
    ];
    public $produk = [];

    public function mount(): void
    {
        $this->produk = ProdukDanObat::all();
    }

    public function render()
    {
        return view('livewire.produkdanobat.transfer');
    }

    public function addTab(): void
    {
        $this->items[] = ['produk_id' => '', 'jumlah' => '', 'catatan' => ''];
        $this->activeTab = count($this->items) - 1;
    }

    public function removeTab(int $index): void
    {
        if (count($this->items) <= 1) return;

        array_splice($this->items, $index, 1);

        if ($this->activeTab >= count($this->items)) {
            $this->activeTab = count($this->items) - 1;
        }
    }

    public function store()
    {
        $this->validate([
            'items'             => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produk_dan_obats,id',
            'items.*.jumlah'    => 'required|numeric|min:1',
            'items.*.catatan'   => 'nullable|string',
        ], [
            'items.*.produk_id.required' => 'Nama produk/obat wajib dipilih.',
            'items.*.produk_id.exists'   => 'Produk/obat tidak valid.',
            'items.*.jumlah.required'    => 'Jumlah wajib diisi.',
            'items.*.jumlah.min'         => 'Jumlah minimal 1.',
        ]);

        if (! Gate::allows('akses', 'Persediaan Bahan Baku Masuk')) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Anda tidak memiliki akses.']);
            return;
        }

        foreach ($this->items as $item) {
            $this->prosesTransfer($item);
        }

        $this->items     = [['produk_id' => '', 'jumlah' => '', 'catatan' => '']];
        $this->activeTab = 0;

        $this->dispatch('toast', ['type' => 'success', 'message' => 'Data Produk/Obat berhasil Diperbarui.']);
        $this->dispatch('closetransferModal');

        return redirect()->route('produk-obat.data');
    }

    protected function prosesTransfer(array $item): void
    {
        DB::transaction(function () use ($item) {

            $pengaju = Auth::user()->biodata?->nama_lengkap;
            $jumlah  = (int) $item['jumlah'];
            $catatan = $item['catatan'] ?? '';

            // 1️⃣ Lock & ambil data produk
            $produk = ProdukDanObat::lockForUpdate()->findOrFail($item['produk_id']);

            // 2️⃣ Ambil bahan yang sudah ada, atau buat baru jika belum ada
            $bahanbaku = BahanBaku::firstOrCreate(
                ['kode' => $produk->kode], // ← cari berdasarkan kode
                [
                    'nama'         => $produk->nama_dagang,
                    'stok_besar'   => 0,
                    'satuan_besar' => $produk->sediaan,
                    'pengali'      => 1,
                    'stok_kecil'   => 0, // ← mulai dari 0, nanti di-increment
                    'satuan_kecil' => $produk->sediaan,
                    'lokasi'       => $produk->lokasi,
                    'expired_at'   => $produk->expired_at,
                    'reminder'     => (int) $produk->reminder,
                    'keterangan'   => null,
                ]
            );

            // 3️⃣ Tambah stok kecil (baik baru maupun yang sudah ada)
            $bahanbaku->increment('stok_kecil', $jumlah);

            // 4️⃣ Mutasi masuk bahan baku
            $bahanbaku->mutasibahan()->create([
                'bahan_bakus_id' => $bahanbaku->id,
                'tipe'           => 'masuk',
                'jumlah'         => $jumlah,
                'satuan'         => $bahanbaku->satuan_kecil,
                'diajukan_oleh'  => $pengaju,
                'catatan'        => $catatan . ' (Ditransfer Persediaan Dari Apotik)',
            ]);

            // 5️⃣ Mutasi keluar produk
            MutasiProdukDanObat::create([
                'produk_id'     => $produk->id,
                'tipe'          => 'keluar',
                'jumlah'        => $jumlah,
                'catatan'       => $catatan . ' (Transfer Persediaan Ke Poli)',
                'diajukan_oleh' => $pengaju,
            ]);

            // 6️⃣ Kurangi stok produk
            $produk->decrement('stok', $jumlah);
        });
    }
}
