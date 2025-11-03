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
                return $rencanaBundling->bundling->produkObatBundlings->map(function ($p) use ($rencanaBundling) {
                    return [
                        'bundling_id' => $rencanaBundling->bundling->id,
                        'nama_bundling' => $rencanaBundling->bundling->nama,
                        'produk_id' => $p->produk->id ?? null,
                        'nama_produk' => $p->produk->nama_dagang ?? '-',
                        'jumlah' => $p->jumlah ?? '-',
                        'satuan' => $p->produk->sediaan ?? '-',
                    ];
                });
        })->toArray();

        // dd(
        //     $this->obatNonRacikanItems,
        //     $this->obatRacikanItems,
        //     $this->produkRencanaItems,
        //     $this->produkBundlingItems,
        // );
    }
    
    public function render()
    {
        return view('livewire.resep.tebus');
    }

    public function create()
    {
        PasienTerdaftar::findOrFail($this->pasien_terdaftar_id)->update(['status_terdaftar' => 'selesai']);
        
        $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Transaksi Selesai.'
        ]);

        $this->reset();
        
        return redirect()->route('resep.data');
    }
}
