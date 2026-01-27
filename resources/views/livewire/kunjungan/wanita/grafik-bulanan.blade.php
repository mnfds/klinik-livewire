<div class="mt-4">
    <div class="mb-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-base-100 p-4 rounded-lg shadow-sm border border-primary/50">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-calendar-days text-primary"></i>
                <h2 class="text-sm font-semibold uppercase tracking-wide">
                    Set Tahun Grafik Bulanan
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

    {{-----  CHART BULANAN BAR  -----}}
    <div class="grid grid-cols-1 gap-4">
        <div class="card bg-base-100 shadow-md border border-info/50">
            <div class="card-body">
                <h3 class="text-sm font-semibold mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-chart-column text-info"></i>
                    Grafik Kunjungan Pasien Wanita Bulanan
                </h3>
                <div class="relative w-full h-[120px] sm:h-[160px]">
                    <canvas wire:ignore class="w-full" id="grafikKunjunganWanitaBulananBar"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctxKunjunganWanitaBulananBar = document.getElementById('grafikKunjunganWanitaBulananBar');

        const dataKunjunganWanitaBulananBar = new Chart(ctxKunjunganWanitaBulananBar, {
            type: 'bar',
            data: {
                labels: [],
                datasets: []
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: ctx =>
                                `${ctx.dataset.label}: ${ctx.raw} kunjungan`
                        }
                    }
                }
            }
        });
        Livewire.on('update-kunjungan-wanita-bulanan-bar', data => {
            const payload = data[0];

            dataKunjunganWanitaBulananBar.data.labels = payload.labelsBulan;
            dataKunjunganWanitaBulananBar.data.datasets.length = 0;
            payload.datasets.forEach(ds => {
                dataKunjunganWanitaBulananBar.data.datasets.push(ds);
            });

            dataKunjunganWanitaBulananBar.update();
        });
    });
</script>