<div wire:init="loadGrafik" class="mt-4">

    {{-- Spinner Loading Awal --}}
    <div wire:loading wire:target="loadGrafik" class="flex flex-col items-center justify-center min-h-[200px] gap-3">
        <span class="loading loading-spinner loading-lg text-primary"></span>
        <p class="text-base-content/50 text-sm">Memuat grafik tahunan...</p>
    </div>

    {{-- Konten Utama --}}
    <div wire:loading.remove wire:target="loadGrafik">
        <div class="grid grid-cols-1 gap-4">
            <div class="card bg-base-100 shadow-md border border-warning/50">
                <div class="card-body">
                    <h3 class="text-sm font-semibold mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-chart-column text-warning"></i>
                        Grafik Kunjungan Pasien Pria Tahunan
                    </h3>
                    <div class="relative w-full h-[120px] sm:h-[160px]">
                        <canvas id="grafikKunjunganLakiTahunanBar" class="absolute inset-0 w-full h-full"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let dataKunjunganLakiTahunanBar = null;

        Livewire.on('update-kunjungan-laki-tahunan-bar', data => {
            const payload = data[0];
            const ctxBar = document.getElementById('grafikKunjunganLakiTahunanBar');
            if (!ctxBar) return;

            if (dataKunjunganLakiTahunanBar) dataKunjunganLakiTahunanBar.destroy();

            dataKunjunganLakiTahunanBar = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: payload.labelsTahunan,
                    datasets: payload.datasets
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
                                label: ctx => `${ctx.dataset.label}: ${ctx.raw} kunjungan`
                            }
                        }
                    }
                }
            });
        });
    });
</script>