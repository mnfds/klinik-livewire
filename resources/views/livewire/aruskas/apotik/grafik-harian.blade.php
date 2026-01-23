<div class="mt-4">
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
                <input x-ref="range" type="text" class="input input-bordered input-primary w-full sm:w-40" placeholder="Pilih rentang tanggal" readonly >
                <button type="button" class="btn btn-error btn-sm flex items-center gap-1" @click="picker.clear(); @this.set('startDate', null); @this.set('endDate', null); @this.call('resetData');">
                    <i class="fa-solid fa-trash-can"></i>
                    Clear
                </button>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4"> 
        <!-- BAR CHART -->
        <div class="card bg-base-100 shadow-md border border-success/50 lg:col-span-3">
            <div class="card-body">
                <h3 class="text-sm font-semibold mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-chart-column text-success"></i>
                    Grafik Apotik Harian
                </h3>
                <canvas id="grafikApotikHarianBar" class="w-full h-[260px] sm:h-[320px]"></canvas>
            </div>
        </div>

        <!-- PIE CHART -->
        <div class="card bg-base-100 shadow-md border border-error/50">
            <div class="card-body flex flex-col items-center justify-center">
                <h3 class="text-sm font-semibold mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-error"></i>
                    Diagram Perbandingan Harian
                </h3>
                <canvas id="grafikApotikHarianPie" class="w-[180px] h-[180px] sm:w-[220px] sm:h-[220px]"></canvas>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctxApotikHarianBar = document.getElementById('grafikApotikHarianBar');
        const dataApotikHarianBar = new Chart(ctxApotikHarianBar, {
            type: 'bar',
            data: {
                // labels: Array.from({ length: 31 }, (_, i) => (i + 1).toString()),
                labels: [],
                datasets: [
                    {
                        label: 'Pendapatan',
                        data: [],  // Array(31).fill(0).map(() => Math.floor(Math.random() * 2000000)),
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
                        data: [], // Array(31).fill(0).map(() => Math.floor(Math.random() * 1500000)),
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
        Livewire.on('update-apotik-harian-bar', (data) => {
            console.log('DATA BAR DARI LIVEWIRE:', data);
            const payload = data[0];
            dataApotikHarianBar.data.labels = payload.labelstanggal;
            dataApotikHarianBar.data.datasets[0].data = payload.apotikHarianBarMasuk;
            dataApotikHarianBar.data.datasets[1].data = payload.apotikHarianBarKeluar;
            dataApotikHarianBar.update();
        });

    });
    
    document.addEventListener('DOMContentLoaded', function () {
        const ctxApotikHarianPie = document.getElementById('grafikApotikHarianPie');
        const dataApotikHarianPie = new Chart(ctxApotikHarianPie, {
            type: 'pie',
            data: {
                labels: ['Pendapatan', 'Pengeluaran'],
                datasets: [{
                    data: [0, 0],
                    backgroundColor: [
                            'rgba(34,197,94,0.6)',  // hijau (pendapatan)
                            'rgba(239,68,68,0.6)'   // merah (pengeluaran)
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
        Livewire.on('update-apotik-harian-pie', data => {
            const payload = data[0];
            dataApotikHarianPie.data.datasets[0].data = [
                payload.apotikHarianPieMasuk,
                payload.apotikHarianPieKeluar
            ];
            dataApotikHarianPie.update();
        });

    });
</script>