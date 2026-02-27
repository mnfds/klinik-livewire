<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiKlinik extends Model
{
    protected $table = 'transaksi_kliniks';
    protected $guarded = ['id'];

    public function rekammedis()
    {
        return $this->belongsTo(RekamMedis::class, 'rekam_medis_id');
    }
    
    public function riwayatTransaksi()
    {
        return $this->hasMany(RiwayatTransaksiKlinik::class, 'transaksi_klinik_id');
    }

    public function getRiwayatTransaksiAttribute()
    {
        $rm = $this->rekammedis;

        if (!$rm) {
            return collect();
        }

        $items = collect();

        // ======================
        // PRODUK
        // ======================
        foreach ($rm->rencanaProdukRM ?? [] as $item) {
            $items->push((object)[
                'jenis_item' => 'produk',
                'nama_item'  => $item->produk->nama_dagang ?? '-',
                'qty'        => $item->jumlah_produk ?? 1,
                'harga_jual'   => $item->produk->harga_dasar ?? 0,
                'subtotal'   => $item->subtotal ?? 0,
                'diskon'     => $item->diskon ?? 0,
                'potongan'   => $item->potongan ?? 0,
            ]);
        }

        // ======================
        // PELAYANAN
        // ======================
        foreach ($rm->rencanaLayananRM ?? [] as $item) {
            $items->push((object)[
                'jenis_item' => 'pelayanan',
                'nama_item'  => $item->pelayanan->nama_pelayanan ?? '-',
                'qty'        => $item->jumlah_pelayanan,
                'harga_jual'   => $item->pelayanan->harga_pelayanan ?? 0,
                'subtotal'   => $item->pelayanan->harga_bersih ?? 0,
                'diskon'     => $item->diskon ?? 0,
                'potongan'   => $item->potongan ?? 0,
            ]);
        }

        // ======================
        // TREATMENT
        // ======================
        foreach ($rm->rencanaTreatmentRM ?? [] as $item) {
            $items->push((object)[
                'jenis_item' => 'treatment',
                'nama_item'  => $item->treatment->nama_treatment ?? '-',
                'qty'        => $item->jumlah_treatment,
                'harga_jual'   => $item->treatment->harga_treatment ?? 0,
                'subtotal'   => $item->subtotal ?? 0,
                'diskon'     => $item->diskon ?? 0,
                'potongan'   => $item->potongan ?? 0,
            ]);
        }
        
        // ======================
        // BUNDLING
        // ======================
        foreach ($rm->rencanaBundlingRM ?? [] as $item) {
            $items->push((object)[
                'jenis_item' => 'bundling',
                'nama_item'  => $item->bundling->nama ?? '-',
                'qty'        => $item->jumlah_bundling,
                'harga_jual'   => $item->bundling->harga ?? 0,
                'subtotal'   => $item->subtotal ?? 0,
                'diskon'     => $item->diskon ?? 0,
                'potongan'   => $item->potongan ?? 0,
            ]);
        }
        
        // ======================
        // OBAT FINAL
        // ======================
        foreach ($rm->obatFinal ?? [] as $item) {
            $items->push((object)[
                'jenis_item' => 'obat_non_racik',
                'nama_item'  => $item->produk->nama_dagang ?? '-',
                'qty'        => $item->qty ?? 1,
                'harga_jual'   => $item->produk->harga_dasar ?? 0,
                'subtotal'   => $item->subtotal ?? 0,
                'diskon'     => $item->diskon ?? 0,
                'potongan'   => $item->potongan ?? 0,
            ]);
        }

        return $items;
    }

}
