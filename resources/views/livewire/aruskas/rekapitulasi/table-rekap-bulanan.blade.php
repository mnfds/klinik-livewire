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
                                Rekapitulasi Pendapatan Dan Pengeluaran Perbulan
                            </h3>
                        </div>

                        <div class="flex flex-col gap-2 w-full lg:w-auto lg:items-end">

                            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                                <select class="select select-bordered select-primary w-full sm:w-40" wire:model.lazy="tahun" wire:change="tahunDipilih">
                                    @for ($y = now()->year; $y >= now()->year - 10; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>

                                <button type="button" wire:click="openFilterModal" onclick="document.getElementById('applyFilterModalTableBulanan').showModal()" class="btn btn-primary btn-sm">
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
                                    <th>Bulan</th>
                                    <th class="text-success"><i class="fa-solid fa-angles-up"></i> Uang Masuk (Rp)</th>
                                    <th class="text-error"><i class="fa-solid fa-angles-down"></i> Uang Keluar (Rp)</th>
                                    <th class="text-info">Uang Tersisa (Rp)</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rekapBulanan as $row)
                                    <tr>
                                        <th>{{ $row['no'] }}</th>
                                        <td>{{ $row['bulan'] }}</td>

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
                                                <button wire:click="unduh({{ $row['bulan_raw'] }})"
                                                    class="btn btn-info btn-sm w-full sm:w-auto">
                                                    <i class="fa-solid fa-download"></i>
                                                    <span class="hidden sm:inline">Unduh</span>
                                                </button>
                                                @endcan
                                                @can('akses', 'Arus Kas Detail')
                                                <button
                                                    wire:click="showDetail({{ $row['bulan_raw'] }})"
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
    <dialog id="applyFilterModalTableBulanan" class="modal" wire:ignore.self>
        <div class="modal-box w-full max-w-xl">
            <h3 class="text-xl font-semibold mb-4">Filter Tabel Rekapitulasi Bulanan</h3>

            <form x-on:submit.prevent="await $wire.filter(); document.getElementById('applyFilterModalTableBulanan').close();" class="grid grid-cols-1 sm:grid-cols-2 gap-4">

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
                    <button type="button" class="btn btn-neutral" onclick="document.getElementById('applyFilterModalTableBulanan').close()">Batal</button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- MODAL DETAIL --}}
    <div
        x-data="{ open: false }"
        x-on:open-detail-modal-bulanan.window="open = true"
        x-show="open"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

        <div class="bg-base-100 w-11/12 max-w-4xl rounded-box pb-6 px-6 overflow-y-auto max-h-[90vh]">

            <div class="flex justify-between items-center mb-4 sticky top-0 z-10 bg-base-100 py-3 border-b">
                <h2 class="text-xl font-bold">
                    Detail Keuangan {{ $detailLabelBulan }}
                </h2>
                <button class="btn btn-sm" @click="open=false">✕</button>
            </div>

            {{-- ================= MASUK ================= --}}
            <div class="divider divider-success text-success font-semibold mt-6 mb-2">Uang Masuk</div>

            {{-- Klinik --}}
            @if(!empty($detailMasuk['klinik']) && count($detailMasuk['klinik']) > 0)
            <div class="mb-4">
                <h4 class="font-medium mb-2">Transaksi Klinik</h4>
                @foreach($detailMasuk['klinik'] ?? [] as $trx)
                    <div class="border rounded p-3 mb-3 bg-base-100">
                        {{-- HEADER TRANSAKSI --}}
                        <div class="flex justify-between font-semibold text-base">
                            <span>No: {{ $trx->no_transaksi }}</span>
                            <span>
                                Rp {{ number_format($trx->total_tagihan_bersih,0,',','.') }}
                            </span>
                        </div>
                        {{-- RINGKASAN TOTAL TRANSAKSI --}}
                        <div class="mt-2 text-sm space-y-1 ml-4">
                            @if ($trx->diskon > 0 || $trx->potongan > 0)
                                <div class="flex justify-between">
                                    <span>Total Tagihan</span>
                                    <span>
                                        Rp {{ number_format($trx->total_tagihan,0,',','.') }}
                                    </span>
                                </div>
                            @endif
                            {{-- Diskon Transaksi --}}
                            @if($trx->diskon > 0)
                                <div class="flex justify-between text-error">
                                    <span>Diskon</span>
                                    <span>
                                        - {{ number_format($trx->diskon,0,',','.') }}%
                                    </span>
                                </div>
                            @endif
                            {{-- Potongan Transaksi --}}
                            @if($trx->potongan > 0)
                                <div class="flex justify-between text-error">
                                    <span>Potongan</span>
                                    <span>
                                        - Rp {{ number_format($trx->potongan,0,',','.') }}
                                    </span>
                                </div>
                            @endif
                            @if($trx->diskon > 0 || $trx->potongan > 0)
                                <div class="flex justify-between font-semibold">
                                    <span>Total Bersih</span>
                                    <span>
                                        Rp {{ number_format($trx->total_tagihan_bersih,0,',','.') }}
                                    </span>
                                </div>
                                <div class="border-t my-1"></div>
                            @endif
                        </div>
                        {{-- ================= DETAIL ITEM ================= --}}
                        @php
                            $grouped = $trx->riwayatTransaksi->groupBy('jenis_item');
                            $labels = [
                                'produk' => 'Produk',
                                'pelayanan' => 'Pelayanan',
                                'treatment' => 'Treatment',
                                'bundling' => 'Bundling',
                                'obat_non_racik' => 'Obat Non Racik',
                                'obat_racik' => 'Obat Racik',
                                'produk_tambahan' => 'Produk Tambahan',
                                'barang_tambahan' => 'Barang Tambahan',
                                'surat_keterangan' => 'Surat Keterangan',
                            ];
                        @endphp
                        <div class="mt-4 space-y-4">
                            @foreach($grouped as $jenis => $items)
                                <div class="ml-4">
                                    {{-- HEADER JENIS --}}
                                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                                        {{ $labels[$jenis] ?? ucfirst($jenis) }}
                                    </div>
                                    {{-- LIST ITEM --}}
                                    @foreach($items as $detail)
                                        <div class="text-sm py-2 border-b border-dashed border-base-200">
                                            {{-- Baris Utama --}}
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    {{ $detail->nama_item ?? 'Item tidak ditemukan' }}<span class="text-gray-400"> x({{ $detail->qty }})</span>
                                                </div>
                                                <div class="text-right font-medium">
                                                    Rp {{ number_format($detail->harga_jual,0,',','.') }}
                                                </div>
                                            </div>
                                            {{-- Diskon Item --}}
                                            @if(($detail->diskon ?? 0) > 0)
                                                <div class="text-right text-xs text-error mt-1">
                                                    - {{ $detail->diskon }}%
                                                </div>
                                            @endif
                                            {{-- Potongan Item --}}
                                            @if(($detail->potongan ?? 0) > 0)
                                                <div class="text-right text-xs text-error">
                                                    - Rp {{ number_format($detail->potongan,0,',','.') }}
                                                </div>
                                            @endif
                                            {{-- Harga Bersih --}}
                                            @if(($detail->subtotal ?? 0) > 0)
                                                <div class="text-right text-xs text-success">
                                                    Rp {{ number_format($detail->subtotal,0,',','.') }}
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>

                        {{-- ================= SISA BUNDLING ================= --}}
                        @php
                            $usageTreatments = $trx->rekammedis?->treatmentBundlingUsages ?? collect();
                            $usagePelayanans = $trx->rekammedis?->pelayananBundlingUsages ?? collect();
                            $usageProduks = $trx->rekammedis?->produkBundlingUsages ?? collect();
                        @endphp

                        @if($usageTreatments->isNotEmpty() || $usagePelayanans->isNotEmpty() || $usageProduks->isNotEmpty())
                            <div class="mt-4 space-y-4">
                                <div class="ml-4">
                                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                                        Item Sisa Bundling
                                    </div>

                                    {{-- Treatment dari sisa bundling --}}
                                    @foreach($usageTreatments as $usage)
                                        <div class="text-sm py-2 border-b border-dashed border-base-200">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <span class="text-xs text-gray-400 block">
                                                        {{ $usage->bundling?->nama ?? '-' }}
                                                    </span>
                                                    {{ $usage->treatment?->nama_treatment ?? '-' }}
                                                    <span class="text-gray-400">x({{ $usage->jumlah_dipakai }})</span>
                                                </div>
                                                <span class="text-xs text-gray-400 italic">Sisa Bundling</span>
                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- Pelayanan dari sisa bundling --}}
                                    @foreach($usagePelayanans as $usage)
                                        <div class="text-sm py-2 border-b border-dashed border-base-200">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <span class="text-xs text-gray-400 block">
                                                        {{ $usage->bundling?->nama ?? '-' }}
                                                    </span>
                                                    {{ $usage->pelayanan?->nama_pelayanan ?? '-' }}
                                                    <span class="text-gray-400">x({{ $usage->jumlah_dipakai }})</span>
                                                </div>
                                                <span class="text-xs text-gray-400 italic">Sisa Bundling</span>
                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- Produk dari sisa bundling --}}
                                    @foreach($usageProduks as $usage)
                                        <div class="text-sm py-2 border-b border-dashed border-base-200">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <span class="text-xs text-gray-400 block">
                                                        {{ $usage->bundling?->nama ?? '-' }}
                                                    </span>
                                                    {{ $usage->produk?->nama_dagang ?? '-' }}
                                                    <span class="text-gray-400">x({{ $usage->jumlah_dipakai }})</span>
                                                </div>
                                                <span class="text-xs text-gray-400 italic">Sisa Bundling</span>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            @endif

            {{-- Apotik --}}
            @if(!empty($detailMasuk['apotik']) && count($detailMasuk['apotik']) > 0)
            <div class="mb-4">
                <h4 class="font-medium">Transaksi Apotik</h4>
                @foreach($detailMasuk['apotik'] ?? [] as $trx)
                    <div class="border rounded p-2 mb-2">
                        <div class="flex justify-between font-semibold">
                            <span>No: {{ $trx->no_transaksi }}</span>
                            <span>Rp {{ number_format($trx->total_harga,0,',','.') }}</span>
                        </div>
                        {{-- Detail Item --}}
                        @php
                            $items = collect();
                            if($trx->riwayat){
                                $items = $items->merge($trx->riwayat);
                            }
                            if($trx->riwayatBarang){
                                $items = $items->merge($trx->riwayatBarang);
                            }
                            $grouped = $items->groupBy('jenis_item');
                            $labels = [
                                'produk' => 'Produk',
                                'pelayanan' => 'Pelayanan',
                                'treatment' => 'Treatment',
                                'bundling' => 'Bundling',
                                'obat_non_racik' => 'Obat Non Racik',
                                'obat_racik' => 'Obat Racik',
                                'produk_tambahan' => 'Produk Tambahan',
                                'barang' => 'Barang',
                            ];
                        @endphp
                        <div class="mt-3 space-y-3">
                            @foreach($grouped as $jenis => $items)
                                <div class="ml-4">
                                    {{-- Header Jenis --}}
                                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                                        {{ $labels[$jenis] ?? ucfirst($jenis) }}
                                    </div>
                                    @foreach($items as $detail)
                                        @php
                                            $produk = $detail->produk ?? null;
                                            $barang = $detail->barang ?? null;
                                            $harga_dasar = $produk->harga_dasar ?? $barang->harga_dasar ?? 0;
                                            $nama = $produk->nama_dagang ?? $barang->nama ?? $detail->nama_item ?? 'Item tidak ditemukan';
                                            $sediaan = $produk->sediaan ?? $barang->satuan ?? '';
                                            $qty = $detail->jumlah_produk ?? $detail->qty ?? 1;
                                            $total = $harga_dasar * $qty;
                                            $subtotal = $detail->subtotal ?? 0;
                                        @endphp

                                        <div class="text-sm py-2 border-b border-dashed border-base-200">
                                            
                                            {{-- Baris 1 : Nama & Harga --}}
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <span>
                                                        {{ $nama }}
                                                        <span class="text-gray-400">
                                                            (x{{ $qty }} {{ $sediaan }})
                                                        </span>
                                                    </span>
                                                </div>

                                                <div class="text-right font-medium">
                                                    Rp {{ number_format($total,0,',','.') }}
                                                </div>
                                            </div>

                                            {{-- Diskon % --}}
                                            @if(($detail->diskon ?? 0) > 0)
                                                <div class="text-right text-xs text-error mt-1">
                                                    - {{ $detail->diskon }}%
                                                </div>
                                            @endif

                                            {{-- Potongan Nominal --}}
                                            @if(($detail->potongan ?? 0) > 0)
                                                <div class="text-right text-xs text-error">
                                                    - Rp {{ number_format($detail->potongan,0,',','.') }}
                                                </div>
                                            @endif

                                            {{-- Harga Bersih Nominal --}}
                                            @if(($detail->potongan ?? 0) > 0 || ($detail->diskon ?? 0) > 0)
                                                <div class="text-right text-xs text-success">
                                                    Rp {{ number_format($subtotal,0,',','.') }}
                                                </div>
                                            @endif

                                        </div>
                                    @endforeach
                                    {{-- Subtotal per jenis --}}
                                    <div class="flex justify-between text-sm font-semibold mt-1">
                                        <span>Subtotal {{ $labels[$jenis] ?? ucfirst($jenis) }}</span>
                                        <span class="text-primary">
                                            Rp {{ number_format($items->sum('subtotal'),0,',','.') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            @endif

            {{-- Lainnya --}}
            @if(!empty($detailMasuk['lainnya']) && count($detailMasuk['lainnya']) > 0)
            <div class="mb-4">
                <h4 class="font-medium">Lainnya</h4>
                @foreach($detailMasuk['lainnya'] ?? [] as $item)
                    <div class="border rounded p-2 mb-2">
                        <div class="flex justify-between font-semibold">
                            <span>No: {{ $item->no_transaksi }}</span>
                            <span>Rp {{ number_format($item->total_tagihan,0,',','.') }}</span>
                        </div>
                        {{-- Detail --}}
                        <div class="flex justify-between text-sm ml-4">
                            <span>
                                {{ $item->keterangan ?? 'Produk tidak ditemukan' }}
                            </span>
                            <span>
                                Status: {{ ucfirst($item->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif
            {{-- MUNCUL KALAU TIDAK ADA PEMASUKAN --}}
            @if(($detailMasuk['klinik'] ?? collect())->isEmpty() && ($detailMasuk['lainnya'] ?? collect())->isEmpty() && ($detailMasuk['apotik'] ?? collect())->isEmpty())
                <div class="bg-base-100 border border-dashed border-gray-300 rounded-xl p-8 text-center">
                    <h3 class="text-lg font-semibold text-gray-600">
                        Tidak Ada Data
                    </h3>
                    <p class="text-sm text-gray-400 mt-1">
                        Belum ada transaksi yang tercatat.
                    </p>
                </div>
            @endif

            {{-- ================= KELUAR ================= --}}
            <div class="divider divider-error text-error font-semibold mt-6 mb-2">Uang Keluar</div>
            @foreach($detailKeluar as $item)
                <div class="border rounded p-2 mb-2">
                    <div class="flex justify-between font-semibold">
                        <span>Kategori: {{ $item->jenis_pengeluaran }}</span>
                        <span>Rp {{ number_format($item->jumlah_uang,0,',','.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm ml-4">
                        <span>
                            {{ $item->keterangan ?? 'Tanpa Keterangan' }}
                        </span>
                        <span>
                            Unit Usaha: {{ ucfirst($item->unit_usaha) }}
                        </span>
                    </div>
                </div>
            @endforeach
            @if(collect($detailKeluar)->isEmpty())
                <div class="bg-base-100 border border-dashed border-gray-300 rounded-xl p-8 text-center">
                    <h3 class="text-lg font-semibold text-gray-600">
                        Tidak Ada Data
                    </h3>
                    <p class="text-sm text-gray-400 mt-1">
                        Belum ada transaksi yang tercatat.
                    </p>
                </div>
            @endif
            {{-- ================= TOTAL ================= --}}
            <div class="divider"></div>

            <div class="space-y-1 font-semibold">
                <div class="flex justify-between text-success">
                    <span>Total Masuk</span>
                    <span>Rp {{ number_format($detailTotalMasuk,0,',','.') }}</span>
                </div>
                <div class="flex justify-between text-error">
                    <span>Total Keluar</span>
                    <span>Rp {{ number_format($detailTotalKeluar,0,',','.') }}</span>
                </div>
                <div class="flex justify-between text-info text-lg">
                    <span>Sisa</span>
                    <span>Rp {{ number_format($detailSisa,0,',','.') }}</span>
                </div>
            </div>

        </div>
    </div>
</div>