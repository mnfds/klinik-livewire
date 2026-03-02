<?php

namespace App\Livewire\Rekammedis;

use App\Models\Pasien;
use Livewire\Component;
use App\Models\PasienTerdaftar;
use Illuminate\Support\Facades\DB;
use App\Models\BahanBaku;
use App\Models\MutasiBahanbaku;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Penguranganstok extends Component
{
    public ?int $pasien_terdaftar_id = null;
    public ?PasienTerdaftar $pasienTerdaftar = null;
    public ?Pasien $pasien = null;

    public $rekammedis = null;
    public $rencanaDetail = [];

    public $bahanInputs = [];

    public function mount($pasien_terdaftar_id = null)
    {
        $this->pasien_terdaftar_id = $pasien_terdaftar_id;

        $this->loadRekamMedis();

        $this->pasien     = $this->pasienTerdaftar?->pasien;
        $this->rekammedis = $this->pasienTerdaftar?->rekamMedis;

        $this->rencanaDetail = $this->getRencanaDetail();

        $this->initializeBahanInputs();
    }

    public function render()
    {
        return view('livewire.rekammedis.penguranganstok', [
            'pasienTerdaftar' => $this->pasienTerdaftar,
            'pasien'          => $this->pasien,
            'rekammedis'      => $this->rekammedis,
        ]);
    }

    /* =========================================
     * LOAD DATA
     * ========================================= */
    private function loadRekamMedis(): void
    {
        $this->pasienTerdaftar = PasienTerdaftar::with([
            'rekamMedis.rencanaLayananRM.pelayanan.layananbahan.bahanbaku',
            'rekamMedis.rencanaTreatmentRM.treatment.treatmentbahan.bahanbaku',
            'rekamMedis.rencanaBundlingRM.bundling.treatmentBundlings.treatment.treatmentbahan.bahanbaku',
            'rekamMedis.rencanaBundlingRM.bundling.pelayananBundlings.pelayanan.layananbahan.bahanbaku',
        ])->find($this->pasien_terdaftar_id);
    }

    private function getRencanaDetail(): array
    {
        return [
            'pasien_terdaftar'  => $this->pasien_terdaftar_id,
            'rencana_layanan'   => $this->mapRencanaLayanan(),
            'rencana_treatment' => $this->mapRencanaTreatment(),
            'rencana_bundling'  => $this->mapRencanaBundling(),
        ];
    }

    /* =========================================
     * MAPPING
     * ========================================= */

    private function mapRencanaLayanan()
    {
        return $this->rekammedis?->rencanaLayananRM
            ->map(function ($item) {
                return [
                    'nama_pelayanan' => $item->pelayanan?->nama_pelayanan,
                    'jumlah' => $item->jumlah_pelayanan,
                    'bahan_baku' => $item->pelayanan?->layananbahan
                        ->map(fn($lb) => [
                            'bahan_id'   => $lb->bahanbaku?->id,
                            'nama_bahan' => $lb->bahanbaku?->nama,
                            'total_pakai'=> ($lb->qty ?? 0) * $item->jumlah_pelayanan,
                        ])
                ];
            }) ?? collect();
    }

    private function mapRencanaTreatment()
    {
        return $this->rekammedis?->rencanaTreatmentRM
            ->map(function ($item) {
                return [
                    'nama_treatment' => $item->treatment?->nama_treatment,
                    'jumlah' => $item->jumlah_treatment,
                    'bahan_baku' => $item->treatment?->treatmentbahan
                        ->map(fn($tb) => [
                            'bahan_id'   => $tb->bahanbaku?->id,
                            'nama_bahan' => $tb->bahanbaku?->nama,
                            'total_pakai'=> ($tb->qty ?? 0) * $item->jumlah_treatment,
                        ])
                ];
            }) ?? collect();
    }

    private function mapRencanaBundling()
    {
        return $this->rekammedis?->rencanaBundlingRM
            ->map(function ($rb) {
                return [
                    'nama_bundling' => $rb->bundling?->nama,
                    'jumlah_bundling' => $rb->jumlah_bundling,

                    'treatments' => $rb->bundling?->treatmentBundlings
                        ->map(fn($tb) => [
                            'nama_treatment' => $tb->treatment?->nama_treatment,
                            'bahan_baku' => $tb->treatment?->treatmentbahan
                                ->map(fn($tbb) => [
                                    'bahan_id'   => $tbb->bahanbaku?->id,
                                    'nama_bahan' => $tbb->bahanbaku?->nama,
                                    'total_pakai'=> ($tbb->qty ?? 0) * $rb->jumlah_bundling,
                                ])
                        ]),

                    'pelayanans' => $rb->bundling?->pelayananBundlings
                        ->map(fn($pb) => [
                            'nama_pelayanan' => $pb->pelayanan?->nama_pelayanan,
                            'bahan_baku' => $pb->pelayanan?->layananbahan
                                ->map(fn($lb) => [
                                    'bahan_id'   => $lb->bahanbaku?->id,
                                    'nama_bahan' => $lb->bahanbaku?->nama,
                                    'total_pakai'=> ($lb->qty ?? 0) * $rb->jumlah_bundling,
                                ])
                        ]),
                ];
            }) ?? collect();
    }

    /* =========================================
     * INITIALIZE INPUT + INDEX MAP
     * ========================================= */
    private function initializeBahanInputs()
    {
        $this->bahanInputs = [];

        // Rencana Layanan
        foreach ($this->rencanaDetail['rencana_layanan'] as $layanan) {
            foreach ($layanan['bahan_baku'] as $bahan) {

                if (!$bahan['bahan_id']) continue;

                $this->bahanInputs[] = [
                    'bahan_id' => $bahan['bahan_id'],
                    'nama_bahan' => $bahan['nama_bahan'],
                    'qty' => $bahan['total_pakai'],
                    'tindakan'   => $layanan['nama_pelayanan'],
                    'kategori'   => 'pelayanan',
                    'rekam_medis_id' => $this->rekammedis?->id,
                ];
            }
        }

        // Rencana Treatment
        foreach ($this->rencanaDetail['rencana_treatment'] as $treatment) {
            foreach ($treatment['bahan_baku'] as $bahan) {

                if (!$bahan['bahan_id']) continue;

                $this->bahanInputs[] = [
                    'bahan_id' => $bahan['bahan_id'],
                    'nama_bahan' => $bahan['nama_bahan'],
                    'qty' => $bahan['total_pakai'],
                    'tindakan'   => $treatment['nama_treatment'],
                    'kategori'   => 'treatment',
                    'rekam_medis_id' => $this->rekammedis?->id,
                ];
            }
        }

        // Rencana Bundling
        foreach ($this->rencanaDetail['rencana_bundling'] as $bundling) {

            foreach ($bundling['treatments'] as $treatment) {
                foreach ($treatment['bahan_baku'] as $bahan) {

                    if (!$bahan['bahan_id']) continue;

                    $this->bahanInputs[] = [
                        'bahan_id' => $bahan['bahan_id'],
                        'nama_bahan' => $bahan['nama_bahan'],
                        'qty' => $bahan['total_pakai'],
                        'tindakan' => $bundling['nama_bundling'] . ' - ' . $treatment['nama_treatment'],
                        'kategori' => 'bundling',
                        'rekam_medis_id' => $this->rekammedis?->id,
                    ];
                }
            }

            foreach ($bundling['pelayanans'] as $pelayanan) {
                foreach ($pelayanan['bahan_baku'] as $bahan) {

                    if (!$bahan['bahan_id']) continue;

                    $this->bahanInputs[] = [
                        'bahan_id' => $bahan['bahan_id'],
                        'nama_bahan' => $bahan['nama_bahan'],
                        'qty' => $bahan['total_pakai'],
                        'tindakan' => $bundling['nama_bundling'] . ' - ' . $pelayanan['nama_pelayanan'],
                        'kategori' => 'bundling',
                        'rekam_medis_id' => $this->rekammedis?->id,
                    ];
                }
            }
        }
    }

    private function accumulateBahan(&$temp, $bahan)
    {
        $id = $bahan['bahan_id'] ?? null;
        if (!$id) return;

        $temp[$id] = ($temp[$id] ?? 0) + ($bahan['total_pakai'] ?? 0);
    }

    /* =========================================
     * SAVE (PRODUCTION SAFE)
     * ========================================= */
    public function saved()
    {
        $this->validate([
            'bahanInputs.*.bahan_id' => 'required|exists:bahan_bakus,id',
            'bahanInputs.*.qty'      => 'required|numeric|min:0',
        ]);

        DB::transaction(function () {

            foreach ($this->bahanInputs as $item) {

                $jumlahKeluar = (int) $item['qty'];
                if ($jumlahKeluar <= 0) continue;

                $bahan = BahanBaku::lockForUpdate()->findOrFail($item['bahan_id']);

                $pengali   = (int) $bahan->pengali;
                $stokBesar = (int) $bahan->stok_besar;
                $stokKecil = (int) $bahan->stok_kecil;

                $totalStokKecil = ($stokBesar * $pengali) + $stokKecil;

                if ($totalStokKecil < $jumlahKeluar) {
                    throw new \Exception("Stok {$bahan->nama} tidak mencukupi");
                }

                // 🔁 Konversi
                while ($stokKecil < $jumlahKeluar) {

                    if ($stokBesar <= 0) {
                        throw new \Exception("Stok besar {$bahan->nama} habis");
                    }

                    $stokBesar--;

                    $this->simpanMutasi(
                        $bahan->id,
                        'keluar',
                        1,
                        $bahan->satuan_besar,
                        $item
                    );

                    $stokKecil += $pengali;

                    $this->simpanMutasi(
                        $bahan->id,
                        'masuk',
                        $pengali,
                        $bahan->satuan_kecil,
                        $item
                    );
                }

                // ➖ Kurangi stok kecil
                $stokKecil -= $jumlahKeluar;

                $this->simpanMutasi(
                    $bahan->id,
                    'keluar',
                    $jumlahKeluar,
                    $bahan->satuan_kecil,
                    $item
                );

                $bahan->update([
                    'stok_besar' => $stokBesar,
                    'stok_kecil' => $stokKecil,
                ]);
            }
        });

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Pengurangan stok berhasil dicatat dengan detail tindakan.',
        ]);
        $this->reset();
        return redirect()->route('pendaftaran.data');
    }

    protected function simpanMutasi(int $bahanId, string $jenis, int $jumlah, string $satuan, array $item) {
        $catatan = "Bahan digunakan untuk tindakan '{$item['tindakan']}' "
            . "pada rekam medis '{$item['rekam_medis_id']}' "
            . "kategori : '{$item['kategori']}'";

        MutasiBahanbaku::create([
            'bahan_baku_id' => $bahanId,
            'tipe'          => $jenis,
            'jumlah'        => $jumlah,
            'satuan'        => $satuan,
            'diajukan_oleh' => Auth::user()->biodata?->nama_lengkap ?? null,
            'catatan'       => $catatan,
        ]);
    }

}
