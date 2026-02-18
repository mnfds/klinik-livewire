<div class="mt-4">
    <div class="grid grid-cols-1 gap-4">
        <div class="card bg-base-100 shadow-md border border-neutral/50">
            <div class="card-body">
                <div class="mb-4">
                    <div
                        class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                        wire:ignore
                        x-data="{ picker: null }"
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
                            })
                        ">
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-semibold mb-2 flex items-center gap-2">
                                <i class="fa-solid fa-arrow-right-arrow-left text-neutral"></i>
                                Rekapitulasi Pendapatan Dan Pengeluaran
                            </h3>
                        </div>
            
                        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                            <input x-ref="range" type="text" class="input input-bordered input-primary w-full sm:w-40" placeholder="Pilih rentang tanggal" readonly >
                            <button type="button" class="btn btn-error btn-sm flex items-center gap-1" @click="picker.clear(); @this.set('startDate', null); @this.set('endDate', null); @this.call('resetData');">
                                <i class="fa-solid fa-trash-can"></i>
                                Clear
                            </button>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Tanggal</th>
                                    <th class="text-success"><i class="fa-solid fa-angles-up"></i> Uang Masuk (Rp)</th>
                                    <th class="text-error"><i class="fa-solid fa-angles-down"></i> Uang Keluar (Rp)</th>
                                    <th class="text-info">Uang Tersisa (Rp)</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rekapHarian as $row)
                                    <tr>
                                        <th>{{ $row['no'] }}</th>
                                        <td>{{ $row['tanggal'] }}</td>

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
                                                <button class="btn btn-info btn-sm w-full sm:w-auto">
                                                    <i class="fa-solid fa-download"></i>
                                                    <span class="hidden sm:inline">Unduh</span>
                                                </button>
                                                <button 
                                                    wire:click="showDetail('{{ $row['tanggal_raw'] }}')" 
                                                    class="btn btn-primary btn-sm w-full sm:w-auto">
                                                    <i class="fa-solid fa-eye"></i>
                                                    <span class="hidden sm:inline">Detail</span>
                                                </button>
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
    {{-- DETAIL --}}
    <div 
        x-data="{ open: false }"
        x-on:open-detail-modal.window="open = true"
        x-show="open"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

        <div class="bg-base-100 w-11/12 max-w-4xl rounded-box pb-6 px-6 overflow-y-auto max-h-[90vh]">

            <div class="flex justify-between items-center mb-4 sticky top-0 z-10 bg-base-100 py-3 border-b">
                <h2 class="text-xl font-bold">
                    Detail Keuangan {{ \Carbon\Carbon::parse($detailTanggal)->locale('id')->translatedFormat('d F Y') }}
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
                        <div class="mt-4 space-y-4">
                            <div class="ml-4">
                                {{-- HEADER JENIS --}}
                                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                                    Item Tambahan
                                </div>
                                {{-- LIST ITEM --}}
                                @foreach($trx->riwayatTransaksi()->where('jenis_item', 'produk_tambahan')->get() as $produk)
                                    <div class="text-sm py-2 border-b border-dashed border-base-200">
                                        {{-- Baris Utama --}}
                                        <div class="flex justify-between items-start">
                                            <div>
                                                {{ $produk->nama_item ?? 'Item tidak ditemukan' }}<span class="text-gray-400"> x({{ $produk->qty }})</span>
                                            </div>
                                            <div class="text-right font-medium text-success">
                                                Rp {{ number_format($produk->subtotal,0,',','.') }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
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
                            $grouped = $trx->riwayat->groupBy('jenis_item');
                            $labels = [
                                'produk' => 'Produk',
                                'pelayanan' => 'Pelayanan',
                                'treatment' => 'Treatment',
                                'bundling' => 'Bundling',
                                'obat_non_racik' => 'Obat Non Racik',
                                'obat_racik' => 'Obat Racik',
                                'produk_tambahan' => 'Produk Tambahan',
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
                                            $produk        = $detail->produk;
                                            $harga_dasar   = $produk->harga_dasar;
                                            $nama          = $produk->nama_dagang ?? $detail->nama_item ?? 'Item tidak ditemukan';
                                            $sediaan       = $produk->sediaan ?? '';
                                            $qty           = $detail->jumlah_produk ?? $detail->qty ?? 1;
                                            $total         = $harga_dasar * $qty;
                                            $subtotal      = $detail->subtotal ?? 0;
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
            @if($detailKeluar ?? collect()->isEmpty())
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