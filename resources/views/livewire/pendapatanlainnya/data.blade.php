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
                    <div class="flex gap-3 mb-4">
                        <select wire:model.live="filterStatus" class="select select-bordered">
                            <option value="">Status</option>
                            <option value="belum lunas">Belum Lunas</option>
                            <option value="lunas">Lunas</option>
                        </select>

                        <select wire:model.live="filterUnitUsaha" class="select select-bordered">
                            <option value="">Unit</option>
                            <option value="Klinik">Klinik</option>
                            <option value="Apotik">Apotik</option>
                            <option value="Sewa Multifunction">Sewa Multifunction</option>
                            <option value="Coffeshop">Coffeshop</option>
                            <option value="Dll">Dll</option>
                        </select>
                    </div>
                    <div class="flex gap-2 mb-3">
                        @if($filterStatus)
                            <span class="badge badge-primary gap-1">
                                status: {{ $filterStatus }}
                                <button wire:click="$set('filterStatus', '')">✕</button>
                            </span>
                        @endif

                        @if($filterUnitUsaha)
                            <span class="badge badge-primary gap-1">
                                Unit: {{ $filterUnitUsaha }}
                                <button wire:click="$set('filterUnitUsaha', '')">✕</button>
                            </span>
                        @endif

                        @if($filterStatus || $filterUnitUsaha)
                            <button wire:click="$set('filterStatus', ''); $set('filterUnitUsaha', '')" class="btn btn-xs">
                                Clear all
                            </button>
                        @endif
                    </div>
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