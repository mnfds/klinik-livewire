<div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
    
    <div class="p-6 text-base-content space-y-4">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 mb-4">
            <!-- Button -->
            <div class="w-full md:w-auto grid grid-cols-2 gap-[2px]">
                @can('akses', 'Pendapatan Tambah')
                <button onclick="document.getElementById('storePendapatan').showModal()" class="btn btn-success w-full">
                    <i class="fa-solid fa-plus"></i> Pendapatan
                </button>
                @endcan
            </div>
        </div>
        <div class="space-y-8">
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="text-lg font-semibold text-success flex items-center gap-2">
                        <i class="fa-solid fa-arrow-trend-up"></i> Pendapatan
                    </h2>
                    <div class="divider my-2"></div>
                    @can('akses', 'Pendapatan')
                    <livewire:Pendapatanlainnya.Pendapatan-table />
                    @endcan
                    <livewire:Pendapatanlainnya.Create />
                    <livewire:Pendapatanlainnya.Update />
                    @if (!Gate::allows('akses','Pendapatan'))
                        <div class="flex items-center justify-center min-h-[300px]">
                            <div class="card bg-base-100 shadow-xl max-w-md w-full">
                                <div class="card-body items-center text-center">
                                    <div class="w-16 h-16 rounded-full bg-error/10 flex items-center justify-center">
                                        <i class="fa-solid fa-triangle-exclamation text-3xl text-error"></i>
                                    </div>
                                    <h2 class="card-title text-error mt-4">
                                        Akses Ditolak
                                    </h2>
                                    <p class="text-base-content/70 text-sm">
                                        Anda tidak memiliki izin untuk akses table Pendapatan.
                                        Silakan hubungi administrator untuk mendapatkan akses.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>