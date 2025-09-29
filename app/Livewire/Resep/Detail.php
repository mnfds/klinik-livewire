<?php

namespace App\Livewire\Resep;

use Livewire\Component;
use App\Models\PasienTerdaftar;
use App\Models\ObatRacikanFinal;
use App\Models\BahanRacikanFinal;
use App\Models\ObatNonRacikanFinal;

class Detail extends Component
{
    public ?int $pasien_terdaftar_id = null;
    public ?PasienTerdaftar $pasienTerdaftar = null;

    // hanya untuk binding data ke form
    public array $obatNonRacikanItems = [];
    public array $obatRacikanItems = [];

    //input obat final
    public $rekammedis_id;
    public $tuslah;
    public $embalase;

    public $obatNonracikFinal = '[]';
    public $obatRacikanFinal = '[]';

    public $racikanInput = [
        [
            'obat_final_id', //ambil dari variable create obat_final
            'nama_racikan', // ambil dari obatRacikanItems
            'jumlah_racikan', //ambil dari form
            'satuan_racikan', //ambil dari form
            'total_racikan', //ambil dari form
            'dosis', // ambil dari obatRacikanItems
            'hari', // ambil dari obatRacikanItems
            'aturan_pakai', // ambil dari obatRacikanItems
            'metode_racikan', // ambil dari obatRacikanItems
        ]
    ];

    public $bahanRacikanInput = [
        [
            'produk_id',
            'obat_racikan_final_id',
            'jumlah_obat',
            'satuan_obat',
            'harga_obat',
            'total_obat',
        ]
    ];
    public $nonRacikanInput = [
        [
            'obat_final_id', //ambil dari variabel  create obat_final
            'produk_id', // ambil dari form
            'jumlah_obat', // ambil dari form
            'satuan_obat', // ambil dari form
            'harga_obat', // ambil dari form
            'total_obat',  // ambil dari form
            'dosis', // ambil dari obatNonRacikanItems
            'hari', // ambil dari obatNonRacikanItems
            'aturan_pakai', // ambil dari obatNonRacikanItems
            ]
    ];

    public function mount($pasien_terdaftar_id = null)
    {
        $this->pasien_terdaftar_id = $pasien_terdaftar_id;

        $this->pasienTerdaftar = PasienTerdaftar::with([
            'rekamMedis.obatNonRacikanRM',
            'rekamMedis.obatRacikanRM.bahanRacikan',
        ])->findOrFail($this->pasien_terdaftar_id);

        $this->rekammedis_id = $this->pasienTerdaftar->rekamMedis->id;

        // mapping data obat non racikan
        $this->obatNonRacikanItems = $this->pasienTerdaftar->rekamMedis->obatNonRacikanRM->map(fn($o) => [
                'id'   => $o->id,
                'nama_obat_non_racikan'   => $o->nama_obat_non_racikan,
                'jumlah_obat_non_racikan' => $o->jumlah_obat_non_racikan,
                'satuan_obat_non_racikan' => $o->satuan_obat_non_racikan,
                'dosis_obat_non_racikan'  => $o->dosis_obat_non_racikan,
                'hari_obat_non_racikan'   => $o->hari_obat_non_racikan,
                'aturan_pakai_obat_non_racikan' => $o->aturan_pakai_obat_non_racikan,
            ])->toArray();

        // mapping data obat racikan
        $this->obatRacikanItems = $this->pasienTerdaftar->rekamMedis->obatRacikanRM->map(fn($r) => [
                'id' => $r->id,
                'nama_racikan' => $r->nama_racikan,
                'jumlah_racikan' => $r->jumlah_racikan,
                'satuan_racikan' => $r->satuan_racikan,
                'dosis_obat_racikan' => $r->dosis_obat_racikan,
                'hari_obat_racikan' => $r->hari_obat_racikan,
                'aturan_pakai_racikan' => $r->aturan_pakai_racikan,
                'metode_racikan' => $r->metode_racikan,
                'bahan' => $r->bahanRacikan->map(fn($b) => [
                    'id' => $b->id,
                    'nama_obat_racikan'   => $b->nama_obat_racikan,
                    'jumlah_obat_racikan' => $b->jumlah_obat_racikan,
                    'satuan_obat_racikan' => $b->satuan_obat_racikan,
                ])->toArray(),
            ])->toArray();

    }

    public function render()
    {
        return view('livewire.resep.detail');
    }

    public function create()
    {
        $nonracik = json_decode($this->obatNonracikFinal, true);
        $racikan = json_decode($this->obatRacikanFinal, true);
        $datadokter = [
            'datanonracik' => $this->obatNonRacikanItems,
            'daataobatracik' => $this->obatRacikanItems,
        ];
        dd([
            'nonracik' => $nonracik,
            'racikan' => $racikan,
            'tuslah' => $this->tuslah,
            'embalase' => $this->embalase,
            'datadokter' => $datadokter,
        ]);
    }

}