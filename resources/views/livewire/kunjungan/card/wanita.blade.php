<div>
    <div class="mb-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-base-100 p-4 rounded-lg shadow-sm border border-primary/50" wire:ignore x-data="{ picker: null }"
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
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-calendar-days text-primary"></i>
                <h2 class="text-sm font-semibold uppercase tracking-wide">
                    Set Tanggal Summary Card Kunjungan
                </h2>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                <input x-ref="range" type="text" class="input input-bordered input-primary w-full sm:w-40" placeholder="Pilih rentang tanggal">
                <button type="button"class="btn btn-error btn-sm flex items-center gap-1"
                    @click="picker.clear(); @this.set('startDate', null); @this.set('endDate', null); @this.call('resetData');">
                    <i class="fa-solid fa-trash-can ml-1"></i>
                    Clear
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach ($kunjunganWanita as $poli => $total)
            <div class="card bg-base-100 shadow-md border border-success/30">
                <div class="card-body space-y-1.5">
                    <p class="text-xs md:text-sm text-base-content/70">
                        {{ $poli }}
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="btn btn-soft btn-success btn-circle pointer-events-none">
                            <i class="fa-solid fa-person"></i>
                        </div>
                        <div class="leading-tight">
                            <p class="text-[11px] md:text-xs text-base-content/60">
                            Jumlah Kunjungan
                            </p>
                            <p class="text-base md:text-lg lg:text-2xl font-bold text-success">
                                {{ number_format($total, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>