<div class="mt-4">
    <div class="grid grid-cols-1 gap-4">
        <div class="card bg-base-100 shadow-md border border-warning/50">
            <div class="card-body">
                <h3 class="text-sm font-semibold mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-chart-column text-warning"></i>
                    Grafik Apotik Tahunan
                </h3>
                <div class="relative w-full h-[120px] sm:h-[160px]">
                    <canvas class="w-full" id="grafikApotikTahunanBar"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctxApotikTahunanBar = document.getElementById('grafikApotikTahunanBar');

        const dataApotikTahunanBar = new Chart(ctxApotikTahunanBar, {
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
                            label: ctx =>
                                `${ctx.dataset.label}: Rp ${ctx.raw.toLocaleString('id-ID')}`
                        }
                    }
                }
            }
        });

        Livewire.on('update-apotik-tahunan-bar', data => {
            console.log('DATA BAR Tahunan:', data);

            const payload = data[0];

            dataApotikTahunanBar.data.labels = payload.labelsTahunan;
            dataApotikTahunanBar.data.datasets[0].data = payload.apotikTahunanBarMasuk;
            dataApotikTahunanBar.data.datasets[1].data = payload.apotikTahunanBarKeluar;

            dataApotikTahunanBar.update();
        });
    });
</script>