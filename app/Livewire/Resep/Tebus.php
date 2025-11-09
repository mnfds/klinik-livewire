<?php

namespace App\Livewire\Resep;

use Livewire\Component;
use App\Models\PasienTerdaftar;

class Tebus extends Component
{
    public ?int $pasien_terdaftar_id = null;
    public ?PasienTerdaftar $pasienTerdaftar = null;

    public array $obatNonRacikanItems = [];
    public array $obatRacikanItems = [];
    public array $produkRencanaItems = [];
    public array $produkBundlingItems = [];
    public array $produkBundlingUsageItems = [];

    public $rekammedis_id;

    public $nama;

    public function mount($pasien_terdaftar_id = null)
    {
        $this->pasien_terdaftar_id = $pasien_terdaftar_id;

        $this->pasienTerdaftar = PasienTerdaftar::with([
            'rekamMedis.rencanaProdukRM',
            'rekamMedis.obatFinal.obatNonRacikanFinals',
            'rekamMedis.obatFinal.obatRacikanFinals.bahanRacikanFinals',
            'rekamMedis.rencanaBundlingRM.bundling.produkObatBundlings.produk',
            'rekamMedis.produkBundlingUsages.produk',
            'rekamMedis.produkBundlingUsages.bundling',
        ])->findOrFail($this->pasien_terdaftar_id);

        $this->rekammedis_id = $this->pasienTerdaftar->rekamMedis->id;
        
        $final = $this->pasienTerdaftar?->rekamMedis?->obatFinal?->first();

        // mapping data obat non racikan
        $this->obatNonRacikanItems = $final
            ?->obatNonRacikanFinals()
            ->where('konfirmasi', 'terkonfirmasi')
            ->get()
            ->map(fn($o) => [
                'id'   => $o->id,
                'nama_obat'   => $o->produk->nama_dagang,
                'jumlah_obat'   => $o->jumlah_obat,
                'satuan_obat'   => $o->satuan_obat,
                'harga_obat'   => $o->harga_obat,
                'total_obat' => $o->total_obat,
                'dosis' => $o->dosis,
                'hari'  => $o->hari,
                'aturan_pakai'   => $o->aturan_pakai,
                'konfirmasi' => $o->konfirmasi,
            ])
        ->toArray() ?? [];


        // mapping data obat racikan
        $this->obatRacikanItems = $final
            ?->obatRacikanFinals()
            ->where('konfirmasi', 'terkonfirmasi')
            ->get()
            ->map(fn($r) => [
                'id' => $r->id,
                'nama_racikan' => $r->nama_racikan,
                'jumlah_racikan' => $r->jumlah_racikan,
                'satuan_racikan' => $r->satuan_racikan,
                'total_racikan' => $r->total_racikan,
                'dosis' => $r->dosis,
                'hari' => $r->hari,
                'aturan_pakai' => $r->aturan_pakai,
                'metode_racikan' => $r->metode_racikan,
                'konfirmasi' => $r->konfirmasi,
                'bahan' => $r->bahanRacikanFinals->map(fn($b) => [
                    'id' => $b->id,
                    'nama_obat'   => $b->produk->nama_dagang,
                    'jumlah_obat' => $b->jumlah_obat,
                    'satuan_obat' => $b->satuan_obat,
                    'harga_obat' => $b->harga_obat,
                    'total_obat' => $b->total_obat,
                ])->toArray(),
            ])
        ->toArray() ?? [];


        // mapping data produk individual
        $this->produkRencanaItems = $this->pasienTerdaftar->rekamMedis->rencanaProdukRM->map(fn($p) => [
            'id' => $p->id,
            'nama_produk' => $p->produk->nama_dagang,
            'jumlah' => $p->jumlah_produk,
            'satuan' => $p->produk->sediaan,
        ])->toArray();

        // mapping data produk bundling
        $this->produkBundlingItems = $this->pasienTerdaftar
            ->rekamMedis
            ->rencanaBundlingRM
            ->flatMap(function ($rencanaBundling) {
                $bundling = $rencanaBundling->bundling;

                return $bundling->produkObatBundlings->map(function ($p) use ($bundling) {
                    // Cari record produk bundling RM yang cocok (berdasarkan produk_id dan bundling_id)
                    $produkRM = $bundling->produkObatBundlingRM
                        ->firstWhere('produk_obat_id', $p->produk_id);

                    return [
                        'bundling_id' => $bundling->id,
                        'nama_bundling' => $bundling->nama,
                        'produk_id' => $p->produk->id ?? null,
                        'nama_produk' => $p->produk->nama_dagang ?? '-',
                        // Ambil jumlah_terpakai dari ProdukObatBundlingRM jika ada
                        'jumlah' => ($produkRM && $produkRM->jumlah_terpakai > 0)
                                    ? $produkRM->jumlah_terpakai
                                    : 'Tidak Diambil',
                        'satuan' => $p->produk->sediaan ?? '-',
                    ];
                });
            })
        ->toArray();
        
        $this->produkBundlingUsageItems = $this->pasienTerdaftar
            ->rekamMedis
            ->produkBundlingUsages
            ->map(fn($u) => [
                'bundling_id' => $u->bundling->id ?? null,
                'nama_bundling' => $u->bundling->nama ?? '-',
                'produk_id' => $u->produk->id ?? null,
                'nama_produk' => $u->produk->nama_dagang ?? '-',
                'jumlah_dipakai' => $u->jumlah_dipakai ?? 0,
                'satuan' => $u->produk->sediaan ?? '-',
                'tipe' => 'usage',
                'is_pembelian_baru' => (bool) ($u->is_pembelian_baru ?? false),
            ])
            ->toArray();
            // dd(
            //     $this->obatNonRacikanItems,
            //     $this->obatRacikanItems,
            //     $this->produkRencanaItems,
            //     $this->produkBundlingItems,
            //     $this->produkBundlingUsageItems,
            // );
    }
    
    public function render()
    {
        return view('livewire.resep.tebus');
    }

    public function create()
    {
        PasienTerdaftar::findOrFail($this->pasien_terdaftar_id)->update(['status_terdaftar' => 'selesai']);
        // dd([$this->nama]);
        $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Transaksi Selesai.'
        ]);

        $this->reset();
        
        return redirect()->route('resep.data');
    }
}
