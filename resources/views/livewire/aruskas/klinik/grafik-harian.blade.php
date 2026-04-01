<div wire:init="loadGrafik" class="mt-4">

    {{-- Spinner Loading Awal --}}
    <div wire:loading wire:target="loadGrafik" class="flex flex-col items-center justify-center min-h-[400px] gap-3">
        <span class="loading loading-spinner loading-lg text-primary"></span>
        <p class="text-base-content/50 text-sm">Memuat grafik harian...</p>
    </div>

    {{-- Konten Utama --}}
    <div wire:loading.remove wire:target="loadGrafik">

        {{-- Filter Tanggal --}}
        <div class="mb-4">
            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-base-100 p-4 rounded-lg shadow-sm border border-primary/50"
                wire:ignore
                x-data="{ picker: null }"
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
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days text-primary"></i>
                    <h2 class="text-sm font-semibold uppercase tracking-wide">
                        Set Tanggal Grafik Harian
                    </h2>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                    <input x-ref="range" type="text" class="input input-bordered input-primary w-full sm:w-40" placeholder="Pilih rentang tanggal" readonly>
                    <button type="button" class="btn btn-error btn-sm flex items-center gap-1"
                        @click="picker.clear(); @this.set('startDate', null); @this.set('endDate', null); @this.call('resetData');">
                        <i class="fa-solid fa-trash-can"></i>
                        Clear
                    </button>
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
                        Grafik Klinik Harian
                    </h3>
                    <div wire:loading wire:target="tanggalDipilih,resetData" class="flex items-center justify-center h-[260px] sm:h-[320px]">
                        <span class="loading loading-spinner loading-md text-success"></span>
                    </div>
                    <canvas wire:loading.remove wire:target="tanggalDipilih,resetData"
                        id="grafikKlinikHarianBar"
                        class="w-full h-[260px] sm:h-[320px]">
                    </canvas>
                </div>
            </div>

            {{-- PIE CHART --}}
            <div class="card bg-base-100 shadow-md border border-error/50">
                <div class="card-body flex flex-col items-center justify-center">
                    <h3 class="text-sm font-semibold mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-chart-pie text-error"></i>
                        Diagram Perbandingan Harian
                    </h3>
                    <div wire:loading wire:target="tanggalDipilih,resetData" class="flex items-center justify-center w-[180px] h-[180px]">
                        <span class="loading loading-spinner loading-md text-error"></span>
                    </div>
                    <canvas wire:loading.remove wire:target="tanggalDipilih,resetData"
                        id="grafikKlinikHarianPie"
                        class="w-[180px] h-[180px] sm:w-[220px] sm:h-[220px]">
                    </canvas>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let dataKlinikHarianBar = null;
        let dataKlinikHarianPie = null;

        Livewire.on('update-klinik-harian-bar', (data) => {
            const payload = data[0];
            const ctxBar = document.getElementById('grafikKlinikHarianBar');
            if (!ctxBar) return;

            if (dataKlinikHarianBar) dataKlinikHarianBar.destroy();

            dataKlinikHarianBar = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: payload.labelstanggal,
                    datasets: [
                        {
                            label: 'Pendapatan',
                            data: payload.klinikHarianBarMasuk,
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
                            data: payload.klinikHarianBarKeluar,
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

        Livewire.on('update-klinik-harian-pie', (data) => {
            const payload = data[0];
            const ctxPie = document.getElementById('grafikKlinikHarianPie');
            if (!ctxPie) return;

            if (dataKlinikHarianPie) dataKlinikHarianPie.destroy();

            dataKlinikHarianPie = new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: ['Pendapatan', 'Pengeluaran'],
                    datasets: [{
                        data: [payload.klinikHarianPieMasuk, payload.klinikHarianPieKeluar],
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