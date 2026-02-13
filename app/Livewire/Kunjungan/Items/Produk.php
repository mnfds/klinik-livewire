<?php

namespace App\Livewire\Kunjungan\Items;

use App\Models\RencanaProdukRM;
use App\Models\RiwayatTransaksiKlinik;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Produk extends Component
{
    public $topProduk = [];

    public function mount()
    {
        // 1️⃣ Produk dari rencana
        $directProduk = DB::table('rencana_produk_r_m_s')
            ->select(
                'produk_id as produk_ref_id',
                DB::raw('SUM(COALESCE(jumlah_produk,0)) as total')
            )
            ->groupBy('produk_id');

        // 2️⃣ Produk dari bundling
        $bundlingProduk = DB::table('produk_obat_bundling_r_m_s')
            ->select(
                'produk_obat_id as produk_ref_id',
                DB::raw('SUM(COALESCE(jumlah_awal,0)) as total')
            )
            ->groupBy('produk_obat_id');

        // 3️⃣ Produk dari transaksi apotik
        $apotikProduk = DB::table('riwayat_transaksi_apotiks')
            ->select(
                'produk_id as produk_ref_id',
                DB::raw('SUM(COALESCE(jumlah_produk,0)) as total')
            )
            ->groupBy('produk_id');

        // 4️⃣ UNION semua
        $union = $directProduk
            ->unionAll($bundlingProduk)
            ->unionAll($apotikProduk);

        // 5️⃣ Join master + filter skincare + sum ulang
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
