<div class="mt-4">
    <div class="mb-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-base-100 p-4 rounded-lg shadow-sm border border-primary/50">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-calendar-days text-primary"></i>
                <h2 class="text-sm font-semibold uppercase tracking-wide">
                    Set Tahun Grafik Perbulan
                </h2>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                <select class="select select-bordered select-primary w-full sm:w-40" wire:model.lazy="tahun" wire:change="tahunDipilih">
                    <option value="">Pilih Tahun</option>
                    @for ($y = now()->year; $y >= now()->year - 10; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>

                <button type="button" class="btn btn-error btn-sm flex items-center gap-1" wire:click="resetData">
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
                    Rekap Perbulan
                </h3>
                <canvas id="grafikRekapBulananBar" class="w-full h-[260px] sm:h-[320px]"></canvas>
            </div>
        </div>

        <!-- PIE CHART -->
        <div class="card bg-base-100 shadow-md border border-error/50">
            <div class="card-body flex flex-col items-center justify-center">
                <h3 class="text-sm font-semibold mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-error"></i>
                    Perbandingan Total Perbulan
                </h3>
                <canvas id="grafikRekapBulananRadar" class="w-[180px] h-[180px] sm:w-[220px] sm:h-[220px]"></canvas>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctxRekapBulananBar = document.getElementById('grafikRekapBulananBar');

        const dataRekapBulananBar = new Chart(ctxRekapBulananBar, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Pendapatan',
                        data: [],
                        backgroundColor: 'rgba(34,197,94,0.6)',
                        borderColor: 'rgba(34,197,94,1)',
                        borderWidth: 2,
                        borderRadius: 3,
                        maxBarThickness: 50
                    },
                    {
                        label: 'Pengeluaran',
                        data: [],
                        backgroundColor: 'rgba(239,68,68,0.6)',
                        borderColor: 'rgba(239,68,68,1)',
                        borderWidth: 2,
                        borderRadius: 3,
                        maxBarThickness: 50
                    }
                ]
            },
            options: {
                responsive: true,
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
                            label: ctx =>
                                `${ctx.dataset.label}: Rp ${ctx.raw.toLocaleString('id-ID')}`
                        }
                    }
                }
            }
        });

        Livewire.on('update-rekap-bulanan-bar', data => {
            console.log('DATA BAR BULANAN:', data);

            const payload = data[0];

            dataRekapBulananBar.data.labels = payload.labelsBulan;
            dataRekapBulananBar.data.datasets[0].data = payload.rekapBulananBarMasuk;
            dataRekapBulananBar.data.datasets[1].data = payload.rekapBulananBarKeluar;

            dataRekapBulananBar.update();
        });
    });
    
    document.addEventListener('DOMContentLoaded', function () {
        const ctxRekapBulananRadar = document.getElementById('grafikRekapBulananRadar');

        const bulanLabels = [
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
        ];

        const dataRekapBulananRadar = new Chart(ctxRekapBulananRadar, {
            type: 'radar',
            data: {
                labels: bulanLabels,
                datasets: [
                    {
                        label: 'Pendapatan',
                        data: Array(12).fill(0),
                        backgroundColor: 'rgba(34,197,94,0.35)',
                        borderColor: 'rgba(34,197,94,1)',
                        pointBackgroundColor: 'rgba(34,197,94,1)',
                        borderWidth: 2
                    },
                    {
                        label: 'Pengeluaran',
                        data: Array(12).fill(0),
                        backgroundColor: 'rgba(239,68,68,0.35)',
                        borderColor: 'rgba(239,68,68,1)',
                        pointBackgroundColor: 'rgba(239,68,68,1)',
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    r: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => 'Rp ' + value.toLocaleString('id-ID')
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const value = context.raw || 0;
                                return `${context.dataset.label}: Rp ${value.toLocaleString('id-ID')}`;
                            }
                        }
                    }
                }
            }
        });

        Livewire.on('update-rekap-bulanan-radar', data => {
            console.log('RADAR DATA:', data);

            const payload = data[0]; // ⬅️ INI KUNCINYA

            dataRekapBulananRadar.data.datasets[0].data = payload.rekapBulananRadarMasuk;
            dataRekapBulananRadar.data.datasets[1].data = payload.rekapBulananRadarKeluar;

            dataRekapBulananRadar.update();
        });
    });
</script>