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
                    Grafik Kunjungan Pasien Wanita Harian
                </h3>
                <canvas id="grafikKunjunganWanitaHarianBar" class="w-full h-[260px] sm:h-[320px]"></canvas>
            </div>
        </div>

        <!-- PIE CHART -->
        <div class="card bg-base-100 shadow-md border border-error/50">
            <div class="card-body flex flex-col items-center justify-center">
                <h3 class="text-sm font-semibold mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-error"></i>
                    Diagram Perbandingan Harian
                </h3>
                <canvas id="grafikKunjunganWanitaHarianPie" class="w-[180px] h-[180px] sm:w-[220px] sm:h-[220px]"></canvas>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctxKunjunganWanitaHarianBar = document.getElementById('grafikKunjunganWanitaHarianBar');
        const dataKunjunganWanitaHarianBar = new Chart(ctxKunjunganWanitaHarianBar, {
            type: 'bar',
            data: {
                // labels: Array.from({ length: 31 }, (_, i) => (i + 1).toString()),
                labels: [],
                datasets: []
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
        Livewire.on('update-kunjungan-wanita-harian-bar', (data) => {
            console.log('DATA POLI KUNJUNGAN WANITA BAR:', data);
            const payload = data[0];
            dataKunjunganWanitaHarianBar.data.labels = payload.labelstanggal;
            dataKunjunganWanitaHarianBar.data.datasets = payload.datasets;

            dataKunjunganWanitaHarianBar.update();
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('grafikKunjunganWanitaHarianPie');

        const chartKunjunganWanitaPie = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [],
                    borderColor: [],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const value = context.raw || 0;
                                return `${context.label}: ${value} kunjungan`;
                            }
                        }
                    }
                }
            }
        });

        Livewire.on('update-kunjungan-wanita-harian-pie', payload => {
            const data = payload[0]; // 🔥 INI KUNCINYA

            console.log('DATA POLI KUNJUNGAN WANITA PIE:', data);
            chartKunjunganWanitaPie.data.labels = data.labels;
            chartKunjunganWanitaPie.data.datasets[0].data =
                data.datasets[0].data;
            chartKunjunganWanitaPie.data.datasets[0].backgroundColor =
                data.datasets[0].backgroundColor;
            chartKunjunganWanitaPie.data.datasets[0].borderColor =
                data.datasets[0].borderColor;

            chartKunjunganWanitaPie.update();
        });

    });

</script>