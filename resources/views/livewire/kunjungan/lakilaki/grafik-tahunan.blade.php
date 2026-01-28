<div class="mt-4">
    <div class="grid grid-cols-1 gap-4">
        <div class="card bg-base-100 shadow-md border border-warning/50">
            <div class="card-body">
                <h3 class="text-sm font-semibold mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-chart-column text-warning"></i>
                    Grafik Kunjungan Pasien Pria Tahunan
                </h3>
                <div class="relative w-full h-[120px] sm:h-[160px]">
                    <canvas class="w-full" id="grafikKunjunganLakiTahunanBar"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctxKunjunganLakiTahunanBar = document.getElementById('grafikKunjunganLakiTahunanBar');

        const dataKunjunganLakiTahunanBar = new Chart(ctxKunjunganLakiTahunanBar, {
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

        Livewire.on('update-kunjungan-laki-tahunan-bar', data => {
            const payload = data[0];

            dataKunjunganLakiTahunanBar.data.labels = payload.labelsTahunan;
            dataKunjunganLakiTahunanBar.data.datasets = payload.datasets;

            dataKunjunganLakiTahunanBar.update();
        });
    });
</script>