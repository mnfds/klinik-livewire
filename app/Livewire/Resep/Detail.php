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

    // DATA DOKTER DARI SOAP
    public array $obat_dokter_nonracikan = [];
    public array $obat_dokter_racikan = [];

    // DATA APOTEKER YANG AKAN DI STORE
    public array $input_apoteker_nonracikan = [
        'nama_obat' => '',
        'jumlah_obat' => '',
        'satuan_obat' => '',
        'harga_obat' => '',
        'total_obat' => '',
        'dosis' => '',
        'hari' => '',
        'aturan_pakai' => '',
    ];
    public array $input_apoteker_racikan = [
        'nama_racikan' => '',
        'jumlah_racikan' => '',
        'satuan_racikan' => '',
        'total_racikan' => '',
        'dosis' => '',
        'hari' => '',
        'aturan_pakai' => '',
        'metode_racikan' => '',
        'bahan' => [
            'produk_id' => '',
            'jumlah_obat' => '',
            'satuan_obat' => '',
            'harga_obat' => '',
            'total_obat' => '',
        ],
        
    ];
    public $data_apoteker = [
        'rekam_medis_id' => '',
        'tuslah' => '',
        'embalase' => '',
    ];

    public function mount($pasien_terdaftar_id = null)
    {
        $this->pasien_terdaftar_id = $pasien_terdaftar_id;

        $this->pasienTerdaftar = PasienTerdaftar::with([
            'rekamMedis.obatNonRacikanRM',
            'rekamMedis.obatRacikanRM.bahanRacikan',
        ])->findOrFail($this->pasien_terdaftar_id);

        $rekamMedis = $this->pasienTerdaftar->rekamMedis;
        $this->data_apoteker['rekam_medis_id'] =  $rekamMedis->id ?? null;

        if ($rekamMedis) {
            // Obat non racikan
            $this->obat_dokter_nonracikan = $rekamMedis->obatNonRacikanRM
                ->map(function ($obat) {
                    return [
                        'id'                            => $obat->id,
                        'nama_obat_non_racikan'         => $obat->nama_obat_non_racikan ?? '-',
                        'jumlah_obat_non_racikan'       => $obat->jumlah_obat_non_racikan ?? '-',
                        'satuan_obat_non_racikan'       => $obat->satuan_obat_non_racikan ?? '-',
                        'dosis_obat_non_racikan'        => $obat->dosis_obat_non_racikan ?? '-',
                        'hari_obat_non_racikan'         => $obat->hari_obat_non_racikan ?? '-',
                        'aturan_pakai_obat_non_racikan' => $obat->aturan_pakai_obat_non_racikan ?? '-',
                    ];
                })->toArray();

            // Obat racikan
            $this->obat_dokter_racikan = $rekamMedis->obatRacikanRM
                ->map(function ($racikan) {
                    return [
                        'id' => $racikan->id,
                        'nama_racikan'          => $racikan->nama_racikan,
                        'jumlah_racikan'        => $racikan->jumlah_racikan,
                        'satuan_racikan'        => $racikan->satuan_racikan,
                        'dosis_obat_racikan'    => $racikan->dosis_obat_racikan,
                        'hari_obat_racikan'     => $racikan->hari_obat_racikan,
                        'aturan_pakai_racikan'  => $racikan->aturan_pakai_racikan,
                        'metode_racikan'        => $racikan->metode_racikan,
                        'bahan'                 => $racikan->bahanRacikan
                        ->map(function ($bahan) {
                            return [
                                'nama_obat_racikan' => $bahan->nama_obat_racikan,
                                'jumlah_obat_racikan' => $bahan->jumlah_obat_racikan,
                                'satuan_obat_racikan' => $bahan->satuan_obat_racikan,
                            ];
                        })->toArray(),
                    ];
                })->toArray();

            // dd($this->obat_dokter_nonracikan);
        }
    }

    public function render()
    {
        return view('livewire.resep.detail');
    }

    public function store(){
        dd([
            $this->input_apoteker_nonracikan,
            $this->input_apoteker_racikan,
            $this->data_apoteker,
        ]);
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