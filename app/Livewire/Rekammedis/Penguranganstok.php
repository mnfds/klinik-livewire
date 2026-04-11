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
            // Rencana biasa (non-bundling)
            'rekamMedis.rencanaLayananRM.pelayanan.layananbahan.bahanbaku',
            'rekamMedis.rencanaTreatmentRM.treatment.treatmentbahan.bahanbaku',

            // Rencana bundling (dipakai untuk tampilan, bukan pengurangan stok langsung)
            'rekamMedis.rencanaBundlingRM.bundling.treatmentBundlings.treatment.treatmentbahan.bahanbaku',
            'rekamMedis.rencanaBundlingRM.bundling.pelayananBundlings.pelayanan.layananbahan.bahanbaku',

            // ✅ Item bundling yang benar-benar diambil pada kunjungan ini
            'rekamMedis.treatmentBundlingUsages.treatment.treatmentbahan.bahanbaku',
            'rekamMedis.treatmentBundlingUsages.bundling',
            'rekamMedis.pelayananBundlingUsages.pelayanan.layananbahan.bahanbaku',
            'rekamMedis.pelayananBundlingUsages.bundling',
        ])->find($this->pasien_terdaftar_id);
    }

    private function getRencanaDetail(): array
    {
        return [
            'pasien_terdaftar'       => $this->pasien_terdaftar_id,
            'rencana_layanan'        => $this->mapRencanaLayanan(),
            'rencana_treatment'      => $this->mapRencanaTreatment(),
            'rencana_bundling'       => $this->mapRencanaBundling(),     // untuk tampilan saja
            'bundling_usage_treatment' => $this->mapBundlingUsageTreatment(), // ✅ untuk stok
            'bundling_usage_pelayanan' => $this->mapBundlingUsagePelayanan(), // ✅ untuk stok
        ];
    }

    /* =========================================
     * MAPPING
     * ========================================= */

    private function mapRencanaLayanan()
    {
        return $this->rekammedis?->rencanaLayananRM
            ->map(fn($item) => [
                'nama_pelayanan' => $item->pelayanan?->nama_pelayanan,
                'jumlah'         => $item->jumlah_pelayanan,
                'bahan_baku'     => $item->pelayanan?->layananbahan
                    ->map(fn($lb) => [
                        'bahan_id'    => $lb->bahanbaku?->id,
                        'nama_bahan'  => $lb->bahanbaku?->nama,
                        'total_pakai' => ($lb->qty ?? 0) * $item->jumlah_pelayanan,
                    ]),
            ]) ?? collect();
    }

    private function mapRencanaTreatment()
    {
        return $this->rekammedis?->rencanaTreatmentRM
            ->map(fn($item) => [
                'nama_treatment' => $item->treatment?->nama_treatment,
                'jumlah'         => $item->jumlah_treatment,
                'bahan_baku'     => $item->treatment?->treatmentbahan
                    ->map(fn($tb) => [
                        'bahan_id'    => $tb->bahanbaku?->id,
                        'nama_bahan'  => $tb->bahanbaku?->nama,
                        'total_pakai' => ($tb->qty ?? 0) * $item->jumlah_treatment,
                    ]),
            ]) ?? collect();
    }

    private function mapRencanaBundling()
    {
        // Tetap ada untuk keperluan tampilan UI, tidak dipakai untuk pengurangan stok
        return $this->rekammedis?->rencanaBundlingRM
            ->map(fn($rb) => [
                'nama_bundling'   => $rb->bundling?->nama,
                'jumlah_bundling' => $rb->jumlah_bundling,
                'treatments'      => $rb->bundling?->treatmentBundlings
                    ->map(fn($tb) => [
                        'nama_treatment' => $tb->treatment?->nama_treatment,
                        'bahan_baku'     => $tb->treatment?->treatmentbahan
                            ->map(fn($tbb) => [
                                'bahan_id'    => $tbb->bahanbaku?->id,
                                'nama_bahan'  => $tbb->bahanbaku?->nama,
                                'total_pakai' => ($tbb->qty ?? 0) * $rb->jumlah_bundling,
                            ]),
                    ]),
                'pelayanans'      => $rb->bundling?->pelayananBundlings
                    ->map(fn($pb) => [
                        'nama_pelayanan' => $pb->pelayanan?->nama_pelayanan,
                        'bahan_baku'     => $pb->pelayanan?->layananbahan
                            ->map(fn($lb) => [
                                'bahan_id'    => $lb->bahanbaku?->id,
                                'nama_bahan'  => $lb->bahanbaku?->nama,
                                'total_pakai' => ($lb->qty ?? 0) * $rb->jumlah_bundling,
                            ]),
                    ]),
            ]) ?? collect();
    }

    /**
     * ✅ Map item treatment bundling yang benar-benar diambil pada kunjungan ini.
     * Sumber: treatmentBundlingUsages (1 record = 1 treatment diambil dari bundling tertentu).
     * qty bahan mengikuti treatmentbahan->qty karena 1 pengambilan = 1x treatment.
     */
    private function mapBundlingUsageTreatment(): \Illuminate\Support\Collection
    {
        return $this->rekammedis?->treatmentBundlingUsages
            ->map(fn($usage) => [
                'nama_bundling'  => $usage->bundling?->nama,
                'nama_treatment' => $usage->treatment?->nama_treatment,
                'bahan_baku'     => $usage->treatment?->treatmentbahan
                    ->map(fn($tb) => [
                        'bahan_id'    => $tb->bahanbaku?->id,
                        'nama_bahan'  => $tb->bahanbaku?->nama,
                        'total_pakai' => $tb->qty ?? 0,   // 1 kali pakai
                    ]) ?? collect(),
            ]) ?? collect();
    }

    /**
     * ✅ Map item pelayanan bundling yang benar-benar diambil pada kunjungan ini.
     * Sumber: pelayananBundlingUsages (1 record = 1 pelayanan diambil dari bundling tertentu).
     */
    private function mapBundlingUsagePelayanan(): \Illuminate\Support\Collection
    {
        return $this->rekammedis?->pelayananBundlingUsages
            ->map(fn($usage) => [
                'nama_bundling'  => $usage->bundling?->nama,
                'nama_pelayanan' => $usage->pelayanan?->nama_pelayanan,
                'bahan_baku'     => $usage->pelayanan?->layananbahan
                    ->map(fn($lb) => [
                        'bahan_id'    => $lb->bahanbaku?->id,
                        'nama_bahan'  => $lb->bahanbaku?->nama,
                        'total_pakai' => $lb->qty ?? 0,   // 1 kali pakai
                    ]) ?? collect(),
            ]) ?? collect();
    }

    /* =========================================
     * INITIALIZE INPUT + INDEX MAP
     * ========================================= */
    private function initializeBahanInputs(): void
    {
        $this->bahanInputs = [];

        // ── Rencana Layanan ──────────────────────────────────────────
        foreach ($this->rencanaDetail['rencana_layanan'] as $layanan) {
            foreach ($layanan['bahan_baku'] as $bahan) {
                if (!$bahan['bahan_id']) continue;

                $this->bahanInputs[] = $this->buildBahanInput(
                    bahan    : $bahan,
                    tindakan : $layanan['nama_pelayanan'],
                    kategori : 'pelayanan',
                );
            }
        }

        // ── Rencana Treatment ────────────────────────────────────────
        foreach ($this->rencanaDetail['rencana_treatment'] as $treatment) {
            foreach ($treatment['bahan_baku'] as $bahan) {
                if (!$bahan['bahan_id']) continue;

                $this->bahanInputs[] = $this->buildBahanInput(
                    bahan    : $bahan,
                    tindakan : $treatment['nama_treatment'],
                    kategori : 'treatment',
                );
            }
        }

        // ── Bundling Usage: Treatment ✅ ─────────────────────────────
        // Hanya item treatment yang benar-benar diambil dari sisa bundling kunjungan ini
        foreach ($this->rencanaDetail['bundling_usage_treatment'] as $usage) {
            foreach ($usage['bahan_baku'] as $bahan) {
                if (!$bahan['bahan_id']) continue;

                $this->bahanInputs[] = $this->buildBahanInput(
                    bahan    : $bahan,
                    tindakan : ($usage['nama_bundling'] ?? '-') . ' - ' . ($usage['nama_treatment'] ?? '-'),
                    kategori : 'bundling_usage',
                );
            }
        }

        // ── Bundling Usage: Pelayanan ✅ ─────────────────────────────
        // Hanya item pelayanan yang benar-benar diambil dari sisa bundling kunjungan ini
        foreach ($this->rencanaDetail['bundling_usage_pelayanan'] as $usage) {
            foreach ($usage['bahan_baku'] as $bahan) {
                if (!$bahan['bahan_id']) continue;

                $this->bahanInputs[] = $this->buildBahanInput(
                    bahan    : $bahan,
                    tindakan : ($usage['nama_bundling'] ?? '-') . ' - ' . ($usage['nama_pelayanan'] ?? '-'),
                    kategori : 'bundling_usage',
                );
            }
        }
    }

    /**
     * Helper: buat satu baris bahanInputs secara konsisten.
     */
    private function buildBahanInput(array $bahan, string $tindakan, string $kategori): array
    {
        return [
            'bahan_id'       => $bahan['bahan_id'],
            'nama_bahan'     => $bahan['nama_bahan'],
            'qty'            => $bahan['total_pakai'],
            'tindakan'       => $tindakan,
            'kategori'       => $kategori,
            'rekam_medis_id' => $this->rekammedis?->id,
            'nama_pasien'    => $this->pasien?->nama,
            'no_pasien'      => $this->pasien?->no_register,
        ];
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

                // 🔁 Konversi stok besar → kecil jika perlu
                while ($stokKecil < $jumlahKeluar) {
                    if ($stokBesar <= 0) {
                        throw new \Exception("Stok besar {$bahan->nama} habis");
                    }

                    $stokBesar--;
                    $this->simpanMutasi($bahan->id, 'keluar', 1, $bahan->satuan_besar, $item);

                    $stokKecil += $pengali;
                    $this->simpanMutasi($bahan->id, 'masuk', $pengali, $bahan->satuan_kecil, $item);
                }

                // ➖ Kurangi stok kecil
                $stokKecil -= $jumlahKeluar;
                $this->simpanMutasi($bahan->id, 'keluar', $jumlahKeluar, $bahan->satuan_kecil, $item);

                $bahan->update([
                    'stok_besar' => $stokBesar,
                    'stok_kecil' => $stokKecil,
                ]);
            }
        });

        $this->dispatch('toast', [
            'type'    => 'success',
            'message' => 'Pengurangan stok berhasil dicatat dengan detail tindakan.',
        ]);

        $this->reset();
        return redirect()->route('pendaftaran.data');
    }

    protected function simpanMutasi(int $bahanId, string $jenis, int $jumlah, string $satuan, array $item): void
    {
        $catatan = "Bahan digunakan untuk tindakan '{$item['tindakan']}' "
            . "pada pasien '{$item['nama_pasien']}' - '{$item['no_pasien']}'";

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
