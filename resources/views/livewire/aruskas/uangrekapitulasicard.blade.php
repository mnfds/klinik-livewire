<div>
    <div class="mb-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between bg-base-100 p-4 rounded-lg shadow-sm border border-primary/50">

            <div class="flex items-center gap-2 shrink-0">
                <i class="fa-solid fa-calendar-days text-primary"></i>
                <h2 class="text-sm font-semibold uppercase tracking-wide">
                    Set Tanggal Summary Card Rekapitulasi
                </h2>
            </div>

            <div class="flex flex-col gap-2 w-full lg:w-auto lg:items-end">

                {{-- Baris: input tanggal + tombol filter + tombol clear --}}
                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto" wire:ignore x-data="{ picker: null }"
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
                    <input x-ref="range" type="text" class="input input-bordered input-primary w-full sm:w-40" placeholder="Pilih rentang tanggal" readonly>

                    <button type="button" wire:click="openFilterModal" onclick="document.getElementById('applyFilterModal').showModal()" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>

                    <button type="button" wire:click="resetAll" @click="picker.clear()" class="btn btn-error btn-sm flex items-center gap-1">
                        <i class="fa-solid fa-trash-can"></i>
                        Clear
                    </button>
                </div>

                {{-- Ringkasan filter aktif, sejajar kanan di bawah baris tanggal --}}
                <div class="flex flex-wrap items-center justify-end gap-2 text-sm bg-base-200/60 px-3 py-1.5 rounded-lg">
                    <span class="text-base-content/60 text-xs">Tanggal:</span>
                    @if ($startDate != $endDate)
                        <span class="badge badge-primary badge-sm">
                            {{ $startDate ? \Carbon\Carbon::parse($startDate)->locale('id')->translatedFormat('j M Y') : '-' }}
                                -
                            {{ $endDate ? \Carbon\Carbon::parse($endDate)->locale('id')->translatedFormat('j M Y') : '-' }}
                        </span>
                    @else
                        <span class="badge badge-primary badge-sm">
                            {{ $startDate ? \Carbon\Carbon::parse($startDate)->locale('id')->translatedFormat('j M Y') : '-' }}
                        </span>
                    @endif
                    @if ($tipe || $filterUnitUsaha || $filterMetodePembayaran || $filterJenisPengeluaran)
                        <span class="text-base-content/60 text-xs">Filter aktif:</span>
                        @if ($tipe)
                            <span class="badge badge-primary badge-sm">{{ $tipe === 'masuk' ? 'Uang Masuk' : 'Uang Keluar' }}</span>
                        @endif
                        @if ($filterUnitUsaha)
                            <span class="badge badge-primary badge-sm">{{ $filterUnitUsaha }}</span>
                        @endif
                        @if ($filterMetodePembayaran)
                            <span class="badge badge-primary badge-sm">{{ $filterMetodePembayaran }}</span>
                        @endif
                        @if ($filterJenisPengeluaran)
                            <span class="badge badge-primary badge-sm">{{ $filterJenisPengeluaran }}</span>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
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
        <div class="card bg-base-100 shadow-md border border-info/50">
            <div class="card-body space-y-1.5">
                <p class="text-xs md:text-sm text-base-content/70">
                    Total Uang Tersisa
                </p>
                <div class="flex items-center gap-3">
                    <div class="btn btn-soft btn-info btn-circle pointer-events-none">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                    <div class="leading-tight">
                        <p class="text-[11px] md:text-xs text-base-content/60">
                            Saldo Akhir
                        </p>
                        <p class="text-base md:text-lg lg:text-2xl font-bold text-info">
                            Rp {{ number_format($totalBersih, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL FILTERS --}}
    <dialog id="applyFilterModal" class="modal" wire:ignore.self>
        <div class="modal-box w-full max-w-xl">
            <h3 class="text-xl font-semibold mb-4">Filter Card Rekapitulasi</h3>

            <form x-on:submit.prevent="await $wire.filter(); document.getElementById('applyFilterModal').close();" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Tipe --}}
                <div class="col-span-1 sm:col-span-2">
                    <label class="label font-medium text-sm">Tipe</label>
                    <div class="join w-full">
                        <button type="button" wire:click="$set('draftTipe', '')"
                            class="btn btn-sm join-item flex-1 {{ $draftTipe === '' ? 'btn-primary' : 'btn-ghost' }}">
                            Semua
                        </button>
                        <button type="button" wire:click="$set('draftTipe', 'masuk')"
                            class="btn btn-sm join-item flex-1 {{ $draftTipe === 'masuk' ? 'btn-success' : 'btn-ghost' }}">
                            Uang Masuk
                        </button>
                        <button type="button" wire:click="$set('draftTipe', 'keluar')"
                            class="btn btn-sm join-item flex-1 {{ $draftTipe === 'keluar' ? 'btn-error' : 'btn-ghost' }}">
                            Uang Keluar
                        </button>
                    </div>
                </div>

                {{-- Unit Usaha --}}
                <div>
                    <label class="label font-medium text-sm">Unit Usaha</label>
                    <select wire:model.defer="draftUnitUsaha" class="select select-bordered w-full">
                        <option value="">Semua Unit Usaha</option>
                        <option value="Klinik">Klinik</option>
                        <option value="Apotik">Apotik</option>
                        @if ($draftTipe !== 'keluar')
                            <option value="Sewa Multifunction">Sewa Multifunction</option>
                            <option value="Coffeshop">Coffeshop</option>
                            <option value="Dll">Dll</option>
                        @endif
                    </select>
                </div>

                {{-- Metode Pembayaran --}}
                <div>
                    <label class="label font-medium text-sm">Metode Pembayaran</label>
                    <select wire:model.defer="draftMetodePembayaran" class="select select-bordered w-full">
                        <option value="">Semua Metode Pembayaran</option>
                        <option value="Tunai">Tunai</option>
                        <option value="Qris">Qris</option>
                        <option value="Shopeepay">Shopeepay</option>
                        <option value="Mandiri">Mandiri</option>
                        <option value="BCA">BCA</option>
                        <option value="BRI">BRI</option>
                        <option value="BNI">BNI</option>
                    </select>
                </div>

                {{-- Jenis Pengeluaran --}}
                @if ($draftTipe === 'keluar')
                    <div class="col-span-1 sm:col-span-2">
                        <label class="label font-medium text-sm">Jenis Pengeluaran</label>
                        <select wire:model.defer="draftJenisPengeluaran" class="select select-bordered w-full">
                            <option value="">Semua Jenis Pengeluaran</option>
                            <option value="SDM">SDM</option>
                            <option value="Administrasi">Administrasi</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Operasional">Operasional</option>
                            <option value="Fasilitas Dan Bangunan">Fasilitas Dan Bangunan</option>
                            <option value="Rumah Tangga">Rumah Tangga</option>
                            <option value="Dll">Dll</option>
                        </select>
                    </div>
                @endif

                <div class="col-span-1 sm:col-span-2 flex justify-end gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-error" onclick="document.getElementById('applyFilterModal').close()">Batal</button>
                </div>
            </form>
        </div>
    </dialog>
</div>