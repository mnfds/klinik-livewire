<div class="mt-4">
    <div class="grid grid-cols-1 gap-4">
        <div class="card bg-base-100">
            <div class="card-body">

                {{-- Filter --}}
                <div class="mb-4">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">

                        <div class="flex items-center gap-2 shrink-0">
                            <h3 class="text-sm font-semibold flex items-center gap-2">
                                <i class="fa-solid fa-arrow-right-arrow-left text-neutral"></i>
                                Rekapitulasi Pendapatan Dan Pengeluaran Pertahun
                            </h3>
                        </div>

                        <div class="flex flex-col gap-2 w-full lg:w-auto lg:items-end">

                            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                                <button type="button" wire:click="openFilterModal" onclick="document.getElementById('applyFilterModalTableTahunan').showModal()" class="btn btn-primary btn-sm">
                                    <i class="fa-solid fa-filter"></i> Filter
                                </button>

                                <button type="button" wire:click="resetAll" class="btn btn-error btn-sm flex items-center gap-1">
                                    <i class="fa-solid fa-trash-can"></i>
                                    Clear
                                </button>
                            </div>

                            @if ($tipe || $filterUnitUsaha || $filterMetodePembayaran || $filterJenisPengeluaran)
                                <div class="flex flex-wrap items-center justify-end gap-2 text-sm bg-base-200/60 px-3 py-1.5 rounded-lg">
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
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Tabel --}}
                <div>
                    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Tahun</th>
                                    <th class="text-success"><i class="fa-solid fa-angles-up"></i> Uang Masuk (Rp)</th>
                                    <th class="text-error"><i class="fa-solid fa-angles-down"></i> Uang Keluar (Rp)</th>
                                    <th class="text-info">Uang Tersisa (Rp)</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rekapTahunan as $row)
                                    <tr>
                                        <th>{{ $row['no'] }}</th>
                                        <td>{{ $row['tahun'] }}</td>

                                        <td class="text-success font-bold">
                                            + Rp. {{ number_format($row['masuk'], 0, ',', '.') }}
                                        </td>

                                        <td class="text-error font-bold">
                                            - Rp. {{ number_format($row['keluar'], 0, ',', '.') }}
                                        </td>

                                        <td class="text-info font-bold">
                                            Rp. {{ number_format($row['sisa'], 0, ',', '.') }}
                                        </td>

                                        <td>
                                            <div class="flex flex-col sm:flex-row gap-2">
                                                @can('akses', 'Arus Kas Unduh')
                                                <button wire:click="unduhTahun({{ $row['tahun_raw'] }})"
                                                    class="btn btn-info btn-sm w-full sm:w-auto">
                                                    <i class="fa-solid fa-download"></i>
                                                    <span class="hidden sm:inline">Unduh</span>
                                                </button>
                                                @endcan
                                                @can('akses', 'Arus Kas Detail')
                                                <button
                                                    wire:click="showDetailTahun({{ $row['tahun_raw'] }})"
                                                    class="btn btn-primary btn-sm w-full sm:w-auto">
                                                    <i class="fa-solid fa-eye"></i>
                                                    <span class="hidden sm:inline">Detail</span>
                                                </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 bg-base-100 p-4 space-y-2 max-w-sm">
                        <div class="flex justify-between">
                            <span class="font-medium">Total Uang Masuk</span>
                            <span class="font-bold text-success">
                                + Rp. {{ number_format($totalMasuk, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="font-medium">Total Uang Keluar</span>
                            <span class="font-bold text-error">
                                - Rp. {{ number_format($totalKeluar, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="divider my-1"></div>

                        <div class="flex justify-between text-lg">
                            <span class="font-semibold">Total Uang Tersisa</span>
                            <span class="font-bold text-info">
                                Rp. {{ number_format($totalSisa, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL FILTER --}}
    <dialog id="applyFilterModalTableTahunan" class="modal" wire:ignore.self>
        <div class="modal-box w-full max-w-xl">
            <h3 class="text-xl font-semibold mb-4">Filter Tabel Rekapitulasi Tahunan</h3>

            <form x-on:submit.prevent="await $wire.filter(); document.getElementById('applyFilterModalTableTahunan').close();" class="grid grid-cols-1 sm:grid-cols-2 gap-4">

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
                    <button type="button" class="btn btn-neutral" onclick="document.getElementById('applyFilterModalTableTahunan').close()">Batal</button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- MODAL DETAIL --}}
    <div
        x-data="{ open: false }"
        x-on:open-detail-modal-tahunan.window="open = true"
        x-show="open"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

        <div class="bg-base-100 w-11/12 max-w-3xl rounded-box pb-6 px-6 overflow-y-auto max-h-[90vh]">

            <div class="flex justify-between items-center mb-4 sticky top-0 z-10 bg-base-100 py-3 border-b">
                <h2 class="text-xl font-bold">
                    Detail Bulanan — {{ $detailLabelTahun }}
                </h2>
                <button class="btn btn-sm" @click="open=false">✕</button>
            </div>

            <div class="overflow-x-auto rounded-box border border-base-content/5">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Tanggal</th>
                            <th class="text-success">Uang Masuk (Rp)</th>
                            <th class="text-error">Uang Keluar (Rp)</th>
                            <th class="text-info">Uang Tersisa (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($detailPerBulan as $row)
                            <tr>
                                <th>{{ $row['no'] }}</th>
                                <td>{{ $row['bulan'] }}</td>

                                <td class="text-success font-semibold">
                                    + Rp {{ number_format($row['masuk'],0,',','.') }}
                                </td>

                                <td class="text-error font-semibold">
                                    - Rp {{ number_format($row['keluar'],0,',','.') }}
                                </td>

                                <td class="text-info font-semibold">
                                    Rp {{ number_format($row['sisa'],0,',','.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-6">
                                    Tidak ada data pada tahun ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 bg-base-100 p-4 space-y-2 max-w-sm ml-auto">
                <div class="flex justify-between">
                    <span class="font-medium">Total Uang Masuk</span>
                    <span class="font-bold text-success">
                        + Rp {{ number_format($detailTotalMasuk, 0, ',', '.') }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium">Total Uang Keluar</span>
                    <span class="font-bold text-error">
                        - Rp {{ number_format($detailTotalKeluar, 0, ',', '.') }}
                    </span>
                </div>
                <div class="divider my-1"></div>
                <div class="flex justify-between text-lg">
                    <span class="font-semibold">Total Uang Tersisa</span>
                    <span class="font-bold text-info">
                        Rp {{ number_format($detailSisa, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>