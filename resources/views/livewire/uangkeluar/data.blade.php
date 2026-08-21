<div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">

    <div class="p-6 text-base-content space-y-4">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 mb-4">
            <!-- Button -->
            <div class="w-full md:w-auto grid grid-cols-2 gap-[2px]">
                @can('akses', 'Pengeluaran Tambah')
                    <button onclick="document.getElementById('storeModalUangKeluarKasir').showModal()" class="btn btn-error w-full">
                        <i class="fa-solid fa-plus"></i> Pengeluaran
                    </button>
                @endcan
            </div>
        </div>
        <div class="space-y-8">
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="text-lg font-semibold text-error flex items-center gap-2">
                        <i class="fa-solid fa-arrow-trend-up"></i> Pengeluaran
                    </h2>
                    <div class="divider my-2"></div>
                    @can('akses', 'Pengeluaran')
                    <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 mb-4">
                        <select wire:model.live="filterJenis" class="select select-bordered select-primary w-full sm:w-auto">
                            <option value="">Kategori</option>
                            <option value="SDM">SDM</option>
                            <option value="Administrasi">Administrasi</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Operasional">Operasional</option>
                            <option value="Fasilitas Dan Bangunan">Fasilitas Dan Bangunan</option>
                            <option value="Rumah Tangga">Rumah Tangga</option>
                            <option value="Dll">Dll</option>
                        </select>

                        <select wire:model.live="filterUnitUsaha" class="select select-bordered select-primary w-full sm:w-auto">
                            <option value="">Unit</option>
                            <option value="Klinik">Klinik</option>
                            <option value="Apotik">Apotik</option>
                            <option value="Dll">Dll</option>
                        </select>
                        <select wire:model.live="filterMetodePembayaran" class="select select-bordered select-primary w-full sm:w-auto">
                            <option value="">Pembayaran</option>
                            <option value="Tunai">Tunai</option>
                            <option value="Qris">Qris</option>
                            <option value="ShopeePay">ShopeePay</option>
                            <option value="Mandiri">Mandiri</option>
                            <option value="BCA">BCA</option>
                            <option value="BRI">BRI</option>
                            <option value="BNI">BNI</option>
                        </select>
                        <div wire:ignore x-data="{ picker: null }"
                            class="w-full sm:w-auto"
                            x-init="
                                picker = flatpickr($refs.range, {
                                    mode: 'range',
                                    dateFormat: 'Y-m-d',
                                    onChange(selectedDates, dateStr, instance) {
                                        if (selectedDates.length === 2) {
                                            @this.set('startDate', instance.formatDate(selectedDates[0], 'Y-m-d'))
                                            @this.set('endDate', instance.formatDate(selectedDates[1], 'Y-m-d'))
                                            @this.call('tanggalDipilihUangKeluar')
                                        }
                                    }
                                });

                                $wire.on('reset-flatpickr', () => picker.clear());
                            "
                            >
                            <input x-ref="range" type="text" readonly
                                class="input input-bordered input-primary w-full sm:w-40"
                                placeholder="Pilih rentang tanggal">
                        </div>
                    </div>
                    <div class="flex gap-2 mb-3">
                        @if($filterJenis)
                            <span class="badge badge-primary gap-1">
                                Kategori: {{ $filterJenis }}
                                <button wire:click="clearFilterUangKeluar('filterJenis')">✕</button>
                            </span>
                        @endif

                        @if($filterUnitUsaha)
                            <span class="badge badge-primary gap-1">
                                Unit: {{ $filterUnitUsaha }}
                                <button wire:click="clearFilterUangKeluar('filterUnitUsaha')">✕</button>
                            </span>
                        @endif

                        @if($filterMetodePembayaran)
                            <span class="badge badge-primary gap-1">
                                Pembayaran: {{ $filterMetodePembayaran }}
                                <button wire:click="clearFilterUangKeluar('filterMetodePembayaran')">✕</button>
                            </span>
                        @endif

                        @if($startDate && $endDate)
                            <span class="badge badge-primary gap-1">
                                Tanggal: {{ $startDate }} - {{ $endDate }}
                                <button wire:click="clearTanggalUangKeluar">✕</button>
                            </span>
                        @endif

                        @if($filterJenis || $filterUnitUsaha || $filterMetodePembayaran || $startDate || $endDate)
                            <button wire:click="clearAllFilterUangKeluar" class="btn btn-xs">
                                Clear all
                            </button>
                        @endif
                    </div>
                    <livewire:uangkeluar.Diterima-table />
                    @endcan
                    <livewire:Uangkeluar.Storebykasir />
                    <livewire:Uangkeluar.Update />
                    @if (!Gate::allows('akses','pengeluaran'))
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
                                        Anda tidak memiliki izin untuk akses table Pengeluaran.
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