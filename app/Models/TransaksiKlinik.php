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
        // Obat Non Racik
        foreach ($rm->obatFinal ?? [] as $obatFinal) {
            // Obat Non Racik
            foreach ($obatFinal->obatNonRacikanFinals ?? [] as $item) {
                $items->push((object)[
                    'jenis_item' => 'obat_non_racik',
                    'nama_item'  => $item->produk->nama_dagang ?? '-',
                    'qty'        => $item->jumlah_obat ?? 1,
                    'harga_jual' => $item->harga_obat ?? 0,
                    'subtotal'   => $item->total_obat ?? 0,
                    'diskon'     => 0,
                    'potongan'   => 0,
                ]);
            }

            // Obat Racik
            foreach ($obatFinal->obatRacikanFinals ?? [] as $item) {
                $items->push((object)[
                    'jenis_item' => 'obat_racik',
                    'nama_item'  => $item->nama_racikan ?? '-',
                    'qty'        => $item->jumlah_racikan ?? 1,
                    'harga_jual' => $item->jumlah_racikan > 0  // ✅ harga per satuan
                                    ? ($item->total_racikan / $item->jumlah_racikan)
                                    : 0,
                    'subtotal'   => $item->total_racikan ?? 0,
                    'diskon'     => 0,
                    'potongan'   => 0,
                ]);
            }

            // Embalase & Tuslah — hanya muncul jika ada obat racik
            if ($obatFinal->obatRacikanFinals->isNotEmpty()) {
                if (($obatFinal->embalase ?? 0) > 0) {
                    $items->push((object)[
                        'jenis_item' => 'embalase',
                        'nama_item'  => 'Embalase',
                        'qty'        => 1,
                        'harga_jual' => $obatFinal->embalase,
                        'subtotal'   => $obatFinal->embalase,
                        'diskon'     => 0,
                        'potongan'   => 0,
                    ]);
                }

                if (($obatFinal->tuslah ?? 0) > 0) {
                    $items->push((object)[
                        'jenis_item' => 'tuslah',
                        'nama_item'  => 'Tuslah',
                        'qty'        => 1,
                        'harga_jual' => $obatFinal->tuslah,
                        'subtotal'   => $obatFinal->tuslah,
                        'diskon'     => 0,
                        'potongan'   => 0,
                    ]);
                }
            }
        }

        // ======================
        // PRODUK TAMBAHAN & BARANG TAMBAHAN
        // Ambil langsung dari tabel riwayat_transaksi_kliniks
        // ======================
        $fromDb = $this->riwayatTransaksi() // pakai () untuk query builder, bukan accessor
            ->whereIn('jenis_item', ['produk_tambahan', 'barang_tambahan'])
            ->get();

        foreach ($fromDb as $item) {
            $items->push((object)[
                'jenis_item' => $item->jenis_item,
                'nama_item'  => $item->nama_item,
                'qty'        => $item->qty,
                'harga_jual' => $item->harga,
                'subtotal'   => $item->subtotal,
                'diskon'     => $item->diskon ?? 0,
                'potongan'   => $item->potongan ?? 0,
            ]);
        }
    
        return $items;
    }

}
