<div class="mt-4">
    <div class="grid grid-cols-1 gap-4">
        <div class="card bg-base-100 shadow-md border border-warning/50">
            <div class="card-body">
                <h3 class="text-sm font-semibold mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-chart-column text-warning"></i>
                    Grafik Kunjungan Pasien Wanita Tahunan
                </h3>
                <div class="relative w-full h-[120px] sm:h-[160px]">
                    <canvas class="w-full" id="grafikKunjunganWanitaTahunanBar"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctxKunjunganWanitaTahunanBar = document.getElementById('grafikKunjunganWanitaTahunanBar');

        const dataKunjunganWanitaTahunanBar = new Chart(ctxKunjunganWanitaTahunanBar, {
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

        Livewire.on('update-kunjungan-wanita-tahunan-bar', data => {
            const payload = data[0];

            dataKunjunganWanitaTahunanBar.data.labels = payload.labelsTahunan;
            dataKunjunganWanitaTahunanBar.data.datasets = payload.datasets;

            dataKunjunganWanitaTahunanBar.update();
        });
    });
</script>