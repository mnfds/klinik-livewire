<div>
    <div class="py-2 flex gap-2 items-center" wire:ignore x-data="{ picker: null }"
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
            })"
        >
        <input x-ref="range" type="text" class="input input-bordered w-full" placeholder="Pilih rentang tanggal">

        <button type="button"class="btn btn-error btn-sm"
            @click="
                picker.clear()

                @this.set('startDate', null)
                @this.set('endDate', null)
                @this.call('resetData')
            ">
            Clear
            <i class="fa-solid fa-trash-can ml-1"></i>
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- CARD UANG MASUK --}}
        <div class="card bg-base-100 shadow-md border border-success/30">
            <div class="card-body space-y-1.5">
                <p class="text-xs md:text-sm text-base-content/70">
                    Total Uang Masuk
                </p>
                <div class="flex items-center gap-3">
                    <div class="btn btn-soft btn-success btn-circle pointer-events-none">
                        <i class="fa-solid fa-arrow-trend-up"></i>
                    </div>
                    <div class="leading-tight">
                        <p class="text-[11px] md:text-xs text-base-content/60">
                            Pemasukan
                        </p>
                        <p class="text-base md:text-lg lg:text-2xl font-bold text-success">
                            Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    
        {{-- CARD UANG KELUAR --}}
        <div class="card bg-base-100 shadow-md border border-error/30">
            <div class="card-body space-y-1.5">
                <p class="text-xs md:text-sm text-base-content/70">
                    Total Uang Keluar
                </p>
                <div class="flex items-center gap-3">
                    <div class="btn btn-soft btn-error btn-circle pointer-events-none">
                        <i class="fa-solid fa-arrow-trend-down"></i>
                    </div>
                    <div class="leading-tight">
                        <p class="text-[11px] md:text-xs text-base-content/60">
                            Pengeluaran
                        </p>
                        <p class="text-base md:text-lg lg:text-2xl font-bold text-error">
                            Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    
        {{-- CARD UANG TERSISA --}}
        <div class="card bg-base-100 shadow-md border border-primary/30">
            <div class="card-body space-y-1.5">
                <p class="text-xs md:text-sm text-base-content/70">
                    Total Uang Tersisa
                </p>
                <div class="flex items-center gap-3">
                    <div class="btn btn-soft btn-primary btn-circle pointer-events-none">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                    <div class="leading-tight">
                        <p class="text-[11px] md:text-xs text-base-content/60">
                            Saldo Akhir
                        </p>
                        <p class="text-base md:text-lg lg:text-2xl font-bold text-primary">
                            Rp {{ number_format($totalBersih, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>