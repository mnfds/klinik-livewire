<?php

namespace App\Livewire\Rekammedis;

use App\Models\KajianAwal;
use App\Models\Pasien;
use App\Models\PasienTerdaftar;
use App\Models\PelayananBundlingRM;
use App\Models\ProdukObatBundlingRM;
use App\Models\RekamMedis;
use App\Models\TreatmentBundlingRM;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Detail extends Component
{
    public ?int $pasien_terdaftar_id = null;
    public ?PasienTerdaftar $pasienTerdaftar = null;
    public ?Pasien $pasien = null;
    public $kajian = null;
    public $rekammedis = null;

    public function mount($pasien_terdaftar_id = null)
    {
        $this->pasien_terdaftar_id = $pasien_terdaftar_id;

        // 🔥 Eager load semua relasi dalam satu query
        $this->pasienTerdaftar = PasienTerdaftar::with([
            'pasien',
            'kajianAwal.pemeriksaanFisik',
            'kajianAwal.tandaVital',
            'kajianAwal.dataKesehatan',
            'kajianAwal.dataEstetika',
            'rekamMedis.dataKesehatanRM',
            'rekamMedis.dataEstetikaRM',
            'rekamMedis.tandaVitalRM',
            'rekamMedis.pemeriksaanFisikRM',
            'rekamMedis.pemeriksaanKulitRM',
            'rekamMedis.diagnosaRM',
            'rekamMedis.icdRM',
            'rekamMedis.rencanaLayananRM',
            'rekamMedis.rencanaTreatmentRM',
            'rekamMedis.rencanaProdukRM',
            'rekamMedis.rencanaBundlingRM.bundling',
            'rekamMedis.obatNonRacikanRM',
            'rekamMedis.obatRacikanRM',
            'rekamMedis.transaksi',
        ])->find($this->pasien_terdaftar_id);

        // Simpan shortcut ke variabel agar gampang dipakai di blade
        $this->pasien     = $this->pasienTerdaftar?->pasien;
        $this->kajian     = $this->pasienTerdaftar?->kajianAwal;
        $this->rekammedis = $this->pasienTerdaftar?->rekamMedis;

        $pasienId     = $this->pasien?->id;
        $rekamMedisId = $this->rekammedis->id;

        // ── 1. Proses rencanaBundlingRM (pembelian baru) ──────────────────────────
        $rencanaBundlingBaru = collect();

        if ($this->rekammedis?->rencanaBundlingRM) {
            foreach ($this->rekammedis->rencanaBundlingRM as $rencana) {
                $groupBundling = $rencana->group_bundling;

                $this->setBundlingRelations($rencana, $rekamMedisId, $pasienId, $groupBundling);

                $rencana->is_pembelian_baru = true;
                $rencanaBundlingBaru->push($rencana);
            }
        }

        // ── 2. Ambil bundling lama dari usage (is_pembelian_baru = false) ─────────
        $rencanaBundlingLama = collect();

        $groupBundlingLamaList = \App\Models\TreatmentBundlingUsage::where('rekam_medis_id', $rekamMedisId)
            ->where('pasien_id', $pasienId) // ← tambah filter pasien_id
            ->where('is_pembelian_baru', false)
            ->pluck('group_bundling')
            ->merge(
                \App\Models\PelayananBundlingUsage::where('rekam_medis_id', $rekamMedisId)
                    ->where('pasien_id', $pasienId) // ← tambah filter pasien_id
                    ->where('is_pembelian_baru', false)
                    ->pluck('group_bundling')
            )
            ->merge(
                \App\Models\ProdukBundlingUsage::where('rekam_medis_id', $rekamMedisId)
                    ->where('pasien_id', $pasienId) // ← tambah filter pasien_id
                    ->where('is_pembelian_baru', false)
                    ->pluck('group_bundling')
            )
            ->unique()
            ->values();

        foreach ($groupBundlingLamaList as $groupBundling) {
            // Ambil rencana bundling dari rekam medis asal (transaksi pertama)
            $rencanaLama = \App\Models\RencananaBundlingRM::with('bundling')
                ->where('group_bundling', $groupBundling)
                ->first();

            if (!$rencanaLama) continue;

            $this->setBundlingRelations($rencanaLama, $rekamMedisId, $pasienId, $groupBundling);

            $rencanaLama->is_pembelian_baru = false;
            $rencanaBundlingLama->push($rencanaLama);
        }

        $this->rekammedis->setRelation('rencanaBundlingBaru', $rencanaBundlingBaru);
        $this->rekammedis->setRelation('rencanaBundlingLama', $rencanaBundlingLama);
    }

    private function setBundlingRelations($rencana, $rekamMedisId, $pasienId, $groupBundling): void
    {
        // ── Treatment ────────────────────────────────────────────────────────
        $treatmentUsages = \App\Models\TreatmentBundlingUsage::where('rekam_medis_id', $rekamMedisId)
            ->where('group_bundling', $groupBundling)
            ->get()
            ->keyBy('treatments_id');

        // Total dipakai di SEMUA rekam medis SEBELUM rekam medis ini
        $treatmentUsagesSebelumnya = \App\Models\TreatmentBundlingUsage::where('group_bundling', $groupBundling)
            ->where('rekam_medis_id', '<', $rekamMedisId)
            ->get()
            ->groupBy('treatments_id')
            ->map(fn($rows) => $rows->sum('jumlah_dipakai'));

        $treatments = TreatmentBundlingRM::with('treatment')
            ->where('pasien_id', $pasienId)
            ->where('group_bundling', $groupBundling)
            ->get()
            ->map(function ($item) use ($treatmentUsages, $treatmentUsagesSebelumnya) {
                $dipakaiSebelumnya  = $treatmentUsagesSebelumnya->get($item->treatments_id, 0);
                $usage              = $treatmentUsages->get($item->treatments_id);
                $dipakaiKunjunganIni = $usage?->jumlah_dipakai ?? 0;

                $item->jumlah_tersedia_rm  = $item->jumlah_awal - $dipakaiSebelumnya; // stok masuk kunjungan ini
                $item->jumlah_terpakai_rm  = $dipakaiKunjunganIni;
                $item->jumlah_sisa_rm      = $item->jumlah_tersedia_rm - $dipakaiKunjunganIni;
                return $item;
            });

        $rencana->bundling->setRelation('treatmentBundlingRM', $treatments);

        // ── Pelayanan ─────────────────────────────────────────────────────────
        $pelayananUsages = \App\Models\PelayananBundlingUsage::where('rekam_medis_id', $rekamMedisId)
            ->where('group_bundling', $groupBundling)
            ->get()
            ->keyBy('pelayanan_id');

        $pelayananUsagesSebelumnya = \App\Models\PelayananBundlingUsage::where('group_bundling', $groupBundling)
            ->where('rekam_medis_id', '<', $rekamMedisId)
            ->get()
            ->groupBy('pelayanan_id')
            ->map(fn($rows) => $rows->sum('jumlah_dipakai'));
        $pelayanan = PelayananBundlingRM::with('pelayanan')
            ->where('pasien_id', $pasienId)
            ->where('group_bundling', $groupBundling)
            ->get()
            ->map(function ($item) use ($pelayananUsages, $pelayananUsagesSebelumnya) {
                $dipakaiSebelumnya   = $pelayananUsagesSebelumnya->get($item->pelayanan_id, 0);
                $usage               = $pelayananUsages->get($item->pelayanan_id);
                $dipakaiKunjunganIni = $usage?->jumlah_dipakai ?? 0;

                $item->jumlah_tersedia_rm = $item->jumlah_awal - $dipakaiSebelumnya;
                $item->jumlah_terpakai_rm = $dipakaiKunjunganIni;
                $item->jumlah_sisa_rm     = $item->jumlah_tersedia_rm - $dipakaiKunjunganIni;
                return $item;
            });

        $rencana->bundling->setRelation('pelayananBundlingRM', $pelayanan);

        // ── Produk ────────────────────────────────────────────────────────────
        $produkUsages = \App\Models\ProdukBundlingUsage::where('rekam_medis_id', $rekamMedisId)
            ->where('group_bundling', $groupBundling)
            ->get()
            ->keyBy('produk_obat_id');

        $produkUsagesSebelumnya = \App\Models\ProdukBundlingUsage::where('group_bundling', $groupBundling)
            ->where('rekam_medis_id', '<', $rekamMedisId)
            ->get()
            ->groupBy('produk_obat_id')
            ->map(fn($rows) => $rows->sum('jumlah_dipakai'));

        $produks = ProdukObatBundlingRM::with('produk')
            ->where('pasien_id', $pasienId)
            ->where('group_bundling', $groupBundling)
            ->get()
            ->map(function ($item) use ($produkUsages, $produkUsagesSebelumnya) {
                $dipakaiSebelumnya   = $produkUsagesSebelumnya->get($item->produk_obat_id, 0);
                $usage               = $produkUsages->get($item->produk_obat_id);
                $dipakaiKunjunganIni = $usage?->jumlah_dipakai ?? 0;

                $item->jumlah_tersedia_rm = $item->jumlah_awal - $dipakaiSebelumnya;
                $item->jumlah_terpakai_rm = $dipakaiKunjunganIni;
                $item->jumlah_sisa_rm     = $item->jumlah_tersedia_rm - $dipakaiKunjunganIni;
                return $item;
            });

        $rencana->bundling->setRelation('produkObatBundlingRM', $produks);
    }

    public function render()
    {
        if (! Gate::allows('akses', 'Detail Rekam Medis')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.rekammedis.detail', [
            'pasienTerdaftar' => $this->pasienTerdaftar,
            'pasien'          => $this->pasien,
            'kajian'          => $this->kajian,
            'rekammedis'      => $this->rekammedis,
        ]);
    }
}