<div wire:init="loadGrafik" class="mt-4">

    {{-- Spinner Loading Awal --}}
    <div wire:loading wire:target="loadGrafik" class="flex flex-col items-center justify-center min-h-[200px] gap-3">
        <span class="loading loading-spinner loading-lg text-primary"></span>
        <p class="text-base-content/50 text-sm">Memuat grafik bulanan...</p>
    </div>

    {{-- Konten Utama --}}
    <div wire:loading.remove wire:target="loadGrafik">
        <div class="mb-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between bg-base-100 p-4 rounded-lg shadow-sm border border-primary/50">

                <div class="flex items-center gap-2 shrink-0">
                    <i class="fa-solid fa-calendar-days text-primary"></i>
                    <h2 class="text-sm font-semibold uppercase tracking-wide">
                        Set Tahun Grafik Bulanan
                    </h2>
                </div>

                <div class="flex flex-col gap-2 w-full lg:w-auto lg:items-end">

                    <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                        <select class="select select-bordered select-primary w-full sm:w-40" wire:model.lazy="tahun" wire:change="tahunDipilih">
                            <option value="">Pilih Tahun</option>
                            @for ($y = now()->year; $y >= now()->year - 10; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>

                        <button type="button" wire:click="openFilterModal" onclick="document.getElementById('applyFilterModalBulanan').showModal()" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-filter"></i> Filter
                        </button>

                        <button type="button" class="btn btn-error btn-sm flex items-center gap-1" wire:click="resetAll">
                            <i class="fa-solid fa-trash-can"></i>
                            Clear
                        </button>
                    </div>

                    @if ($tipe || $filterUnitUsaha || $filterMetodePembayaran || $filterJenisPengeluaran)
                        <div class="flex flex-wrap items-center justify-end gap-2 text-sm bg-base-200/60 px-3 py-1.5 rounded-lg">
                            <span class="text-base-content/60 text-xs">Filter aktif:</span>

                            @if ($tipe)
                                <span class="badge badge-primary badge-sm">{{ $tipe === 'masuk' ? 'Uang Masuk' : 'Uang Keluar' }}</span>
                            @endif
                            @if ($filterUnitUsaha)
                                <span class="badge badge-primary badge-sm">{{ $filterUnitUsaha }}</span>
                            @endif
                            @if ($filterMetodePembayaran)
                                <span class="badge badge-primary badge-sm">{{ $filterMetodePembayaran }}</span>
                            @endif
                            @if ($filterJenisPengeluaran)
                                <span class="badge badge-primary badge-sm">{{ $filterJenisPengeluaran }}</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4">
            <div class="card bg-base-100 shadow-md border border-info/50">
                <div class="card-body">
                    <h3 class="text-sm font-semibold mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-chart-column text-info"></i>
                        Grafik Rekapitulasi Bulanan
                    </h3>
                    {{-- Spinner saat ganti tahun --}}
                    <div wire:loading wire:target="tahunDipilih,resetData,filter" class="flex items-center justify-center h-[120px] sm:h-[160px]">
                        <span class="loading loading-spinner loading-md text-info"></span>
                    </div>
                    <div wire:loading.remove wire:target="tahunDipilih,resetData,filter" class="relative w-full h-[120px] sm:h-[160px]">
                        <canvas id="grafikRekapBulananBar" class="absolute inset-0 w-full h-full"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- MODAL FILTER --}}
    <dialog id="applyFilterModalBulanan" class="modal" wire:ignore.self>
        <div class="modal-box w-full max-w-xl">
            <h3 class="text-xl font-semibold mb-4">Filter Grafik Bulanan</h3>

            <form x-on:submit.prevent="await $wire.filter(); document.getElementById('applyFilterModalBulanan').close();" class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="col-span-1 sm:col-span-2">
                    <label class="label font-medium text-sm">Tipe</label>
                    <div class="join w-full">
                        <button type="button" wire:click="$set('draftTipe', '')"
                            class="btn btn-sm join-item flex-1 {{ $draftTipe === '' ? 'btn-primary' : 'btn-ghost' }}">
                            Semua
                        </button>
                        <button type="button" wire:click="$set('draftTipe', 'masuk')"
                            class="btn btn-sm join-item flex-1 {{ $draftTipe === 'masuk' ? 'btn-success' : 'btn-ghost' }}">
                            Uang Masuk
                        </button>
                        <button type="button" wire:click="$set('draftTipe', 'keluar')"
                            class="btn btn-sm join-item flex-1 {{ $draftTipe === 'keluar' ? 'btn-error' : 'btn-ghost' }}">
                            Uang Keluar
                        </button>
                    </div>
                </div>

                <div>
                    <label class="label font-medium text-sm">Unit Usaha</label>
                    <select wire:model.defer="draftUnitUsaha" class="select select-bordered w-full">
                        <option value="">Semua Unit Usaha</option>
                        <option value="Klinik">Klinik</option>
                        <option value="Apotik">Apotik</option>
                        @if ($draftTipe !== 'keluar')
                            <option value="Sewa Multifunction">Sewa Multifunction</option>
                            <option value="Coffeshop">Coffeshop</option>
                            <option value="Dll">Dll</option>
                        @endif
                    </select>
                </div>

                <div>
                    <label class="label font-medium text-sm">Metode Pembayaran</label>
                    <select wire:model.defer="draftMetodePembayaran" class="select select-bordered w-full">
                        <option value="">Semua Metode Pembayaran</option>
                        <option value="Tunai">Tunai</option>
                        <option value="Qris">Qris</option>
                        <option value="Shopeepay">Shopeepay</option>
                        <option value="Mandiri">Mandiri</option>
                        <option value="BCA">BCA</option>
                        <option value="BRI">BRI</option>
                        <option value="BNI">BNI</option>
                    </select>
                </div>

                @if ($draftTipe === 'keluar')
                    <div class="col-span-1 sm:col-span-2">
                        <label class="label font-medium text-sm">Jenis Pengeluaran</label>
                        <select wire:model.defer="draftJenisPengeluaran" class="select select-bordered w-full">
                            <option value="">Semua Jenis Pengeluaran</option>
                            <option value="SDM">SDM</option>
                            <option value="Administrasi">Administrasi</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Operasional">Operasional</option>
                            <option value="Fasilitas Dan Bangunan">Fasilitas Dan Bangunan</option>
                            <option value="Rumah Tangga">Rumah Tangga</option>
                            <option value="Dll">Dll</option>
                        </select>
                    </div>
                @endif

                <div class="col-span-1 sm:col-span-2 flex justify-end gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-neutral" onclick="document.getElementById('applyFilterModalBulanan').close()">Batal</button>
                </div>
            </form>
        </div>
    </dialog>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let dataRekapBulananBar = null;

        Livewire.on('update-rekap-bulanan-bar', data => {
            const payload = data[0];
            const ctxBar = document.getElementById('grafikRekapBulananBar');
            if (!ctxBar) return;

            if (dataRekapBulananBar) dataRekapBulananBar.destroy();

            const datasets = [
                {
                    label: 'Pendapatan',
                    data: payload.rekapBulananBarMasuk,
                    backgroundColor: 'rgba(34,197,94,0.6)',
                    borderColor: 'rgba(34,197,94,1)',
                    borderWidth: 2,
                    borderRadius: 3,
                    maxBarThickness: 50
                },
                {
                    label: 'Pengeluaran',
                    data: payload.rekapBulananBarKeluar,
                    backgroundColor: 'rgba(239,68,68,0.6)',
                    borderColor: 'rgba(239,68,68,1)',
                    borderWidth: 2,
                    borderRadius: 3,
                    maxBarThickness: 50
                }
            ];

            if (payload.tampilkanSisa) {
                datasets.push({
                    label: 'Uang Tersisa',
                    data: payload.rekapBulananBarSisa,
                    backgroundColor: 'rgba(59,130,246,0.6)',
                    borderColor: 'rgba(59,130,246,1)',
                    borderWidth: 2,
                    borderRadius: 3,
                    maxBarThickness: 50
                });
            }

            dataRekapBulananBar = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: payload.labelsBulan,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: value => 'Rp ' + value.toLocaleString('id-ID')
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: ctx => `${ctx.dataset.label}: Rp ${ctx.raw.toLocaleString('id-ID')}`
                            }
                        }
                    }
                }
            });
        });
    });
</script>