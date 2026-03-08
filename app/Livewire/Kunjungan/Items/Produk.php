<?php

namespace App\Livewire\Kunjungan\Items;

use App\Models\RencanaProdukRM;
use App\Models\RiwayatTransaksiKlinik;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Produk extends Component
{
    public $topProduk = [];
    public $filter = 'all';

    public function mount()
    {
        $this->loadTopProduk();
    }

    public function updatedFilter()
    {
        $this->loadTopProduk();
    }

    private function loadTopProduk()
    {
        // 1️⃣ Produk dari rencana
        $directProduk = DB::table('rencana_produk_r_m_s')
            ->select(
                'produk_id as produk_ref_id',
                DB::raw('SUM(COALESCE(jumlah_produk,0)) as total')
            );

        // 2️⃣ Produk dari bundling
        $bundlingProduk = DB::table('produk_obat_bundling_r_m_s')
            ->select(
                'produk_obat_id as produk_ref_id',
                DB::raw('SUM(COALESCE(jumlah_awal,0)) as total')
            );

        // 3️⃣ Produk dari transaksi apotik
        $apotikProduk = DB::table('riwayat_transaksi_apotiks')
            ->select(
                'produk_id as produk_ref_id',
                DB::raw('SUM(COALESCE(jumlah_produk,0)) as total')
            );

        // FILTER WAKTU
        if ($this->filter === 'weekly') {
            $range = [now()->startOfWeek(), now()->endOfWeek()];

            $directProduk->whereBetween('created_at', $range);
            $bundlingProduk->whereBetween('created_at', $range);
            $apotikProduk->whereBetween('created_at', $range);
        }

        if ($this->filter === 'monthly') {
            $directProduk->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year);

            $bundlingProduk->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year);

            $apotikProduk->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year);
        }

        $directProduk->groupBy('produk_id');
        $bundlingProduk->groupBy('produk_obat_id');
        $apotikProduk->groupBy('produk_id');

        // 4️⃣ UNION semua
        $union = $directProduk
            ->unionAll($bundlingProduk)
            ->unionAll($apotikProduk);

        // 5️⃣ Join master + filter skincare
        $this->topProduk = DB::query()
            ->fromSub($union, 'produk_totals')
            ->join('produk_dan_obats', 'produk_totals.produk_ref_id', '=', 'produk_dan_obats.id')
            ->where('produk_dan_obats.golongan', 'Skincare')
            ->select(
                'produk_dan_obats.nama_dagang',
                DB::raw('SUM(produk_totals.total) as total_terjual')
            )
            ->groupBy('produk_dan_obats.id', 'produk_dan_obats.nama_dagang')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.kunjungan.items.produk');
    }
}
