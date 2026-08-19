<div wire:init="loadGrafik" class="mt-4">

    {{-- Loading Awal --}}
    <div wire:loading wire:target="loadGrafik" class="flex flex-col items-center justify-center min-h-[400px] gap-3">
        <span class="loading loading-spinner loading-lg text-primary"></span>
        <p class="text-base-content/50 text-sm">Memuat grafik harian...</p>
    </div>

    {{-- Konten Utama --}}
    <div wire:loading.remove wire:target="loadGrafik">

        {{-- Filter Tanggal --}}
        <div class="mb-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between bg-base-100 p-4 rounded-lg shadow-sm border border-primary/50">

                <div class="flex items-center gap-2 shrink-0">
                    <i class="fa-solid fa-calendar-days text-primary"></i>
                    <h2 class="text-sm font-semibold uppercase tracking-wide">
                        Set Tanggal Grafik Harian
                    </h2>
                </div>

                <div class="flex flex-col gap-2 w-full lg:w-auto lg:items-end">

                    <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto" wire:ignore x-data="{ picker: null }"
                        x-init="
                            picker = flatpickr($refs.range, {
                                mode: 'range',
                                dateFormat: 'Y-m-d',
                                onChange(selectedDates, dateStr, instance) {
                                    if (selectedDates.length === 2) {
                                        @this.set('startDate', instance.formatDate(selectedDates[0], 'Y-m-d'))
                                        @this.set('endDate', instance.formatDate(selectedDates[1], 'Y-m-d'))
                                        @this.call('tanggalDipilih')
                                    }
                                }
                            })
                        ">
                        <input x-ref="range" type="text" class="input input-bordered input-primary w-full sm:w-40" placeholder="Pilih rentang tanggal" readonly>

                        <button type="button" wire:click="openFilterModal" onclick="document.getElementById('applyFilterModalHarian').showModal()" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-filter"></i> Filter
                        </button>

                        <button type="button" wire:click="resetAll" @click="picker.clear()" class="btn btn-error btn-sm flex items-center gap-1">
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

        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">

            {{-- BAR CHART --}}
            <div class="card bg-base-100 shadow-md border border-success/50 lg:col-span-3">
                <div class="card-body">
                    <h3 class="text-sm font-semibold mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-chart-column text-success"></i>
                        Grafik Rekapitulasi Harian
                    </h3>
                    <div wire:loading wire:target="tanggalDipilih,resetData,filter" class="flex items-center justify-center h-[260px] sm:h-[320px]">
                        <span class="loading loading-spinner loading-md text-success"></span>
                    </div>
                    <canvas wire:loading.remove wire:target="tanggalDipilih,resetData" id="grafikRekapHarianBar" class="w-full h-[260px] sm:h-[320px]"></canvas>
                </div>
            </div>

            {{-- PIE CHART --}}
            <div class="card bg-base-100 shadow-md border border-error/50">
                <div class="card-body flex flex-col items-center justify-center">
                    <h3 class="text-sm font-semibold mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-chart-pie text-error"></i>
                        Diagram Perbandingan Harian
                    </h3>
                    <div wire:loading wire:target="tanggalDipilih,resetData,filter" class="flex items-center justify-center w-[180px] h-[180px]">
                        <span class="loading loading-spinner loading-md text-error"></span>
                    </div>
                    <canvas wire:loading.remove wire:target="tanggalDipilih,resetData" id="grafikRekapHarianPie" class="w-[180px] h-[180px] sm:w-[220px] sm:h-[220px]"></canvas>
                </div>
            </div>

        </div>
    </div>
    
    {{-- MODAL FILTER --}}
    <dialog id="applyFilterModalHarian" class="modal" wire:ignore.self>
        <div class="modal-box w-full max-w-xl">
            <h3 class="text-xl font-semibold mb-4">Filter Grafik Harian</h3>

            <form x-on:submit.prevent="await $wire.filter(); document.getElementById('applyFilterModalHarian').close();" class="grid grid-cols-1 sm:grid-cols-2 gap-4">

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
                    <button type="button" class="btn btn-neutral" onclick="document.getElementById('applyFilterModalHarian').close()">Batal</button>
                </div>
            </form>
        </div>
    </dialog>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let dataRekapHarianBar = null;
        let dataRekapHarianPie = null;

        Livewire.on('update-rekap-harian-bar', (data) => {
            const payload = data[0];
            const ctxBar = document.getElementById('grafikRekapHarianBar');
            if (!ctxBar) return;

            if (dataRekapHarianBar) dataRekapHarianBar.destroy();

            dataRekapHarianBar = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: payload.labelstanggal,
                    datasets: [
                        {
                            label: 'Pendapatan',
                            data: payload.rekapHarianBarMasuk,
                            backgroundColor: 'rgba(34,197,94,0.6)',
                            borderColor: 'rgba(34,197,94,1)',
                            borderWidth: 2,
                            borderRadius: 3,
                            barPercentage: 1,
                            categoryPercentage: 0.8,
                            maxBarThickness: 50
                        },
                        {
                            label: 'Pengeluaran',
                            data: payload.rekapHarianBarKeluar,
                            backgroundColor: 'rgba(255, 26, 63, 0.6)',
                            borderColor: 'rgba(239,68,68,1)',
                            borderWidth: 2,
                            borderRadius: 3,
                            barPercentage: 1,
                            categoryPercentage: 0.8,
                            maxBarThickness: 50
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        });

        Livewire.on('update-rekap-harian-pie', (data) => {
            const payload = data[0];
            const ctxPie = document.getElementById('grafikRekapHarianPie');
            if (!ctxPie) return;

            if (dataRekapHarianPie) dataRekapHarianPie.destroy();

            dataRekapHarianPie = new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: ['Pendapatan', 'Pengeluaran'],
                    datasets: [{
                        data: [payload.rekapHarianPieMasuk, payload.rekapHarianPieKeluar],
                        backgroundColor: [
                            'rgba(34,197,94,0.6)',
                            'rgba(239,68,68,0.6)'
                        ],
                        borderColor: [
                            'rgba(34,197,94,1)',
                            'rgba(239,68,68,1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const value = context.raw || 0;
                                    return context.label + ': Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        });
    });
</script>