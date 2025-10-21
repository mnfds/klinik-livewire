<div class="pt-1 pb-12">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Breadcrumbs -->
        <div class="hidden lg:flex justify-end px-4">
            <div class="breadcrumbs text-sm">
                <ul>
                    <li>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('transaksi.kasir') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i> Transaksi
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('transaksi.kasir') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i> Proses Pembayaran
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-lg font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i> Proses Pembayaran
            </h1>
        </div>

        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-6 gap-6">
                
                {{-- Kolom Kiri: Detail Produk --}}
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-base-100 shadow rounded-box p-4">
                        <div class="border-b pb-4 mb-4">
                            <h3 class="text-xl font-bold mb-2">
                                Rincian Pembayaran
                            </h3>
                            <div class="text-sm text-base-content space-y-1">
                                <p><span class="font-semibold">Pasien:</span> {{ $pasien->nama ?? '-' }}</p>
                                <p><span class="font-semibold">No. Rekam Medis:</span> {{ $pasien->no_register ?? '-' }}</p>
                                <p><span class="font-semibold mb-2">Tanggal:</span> {{ $pasienTerdaftar->created_at->translatedFormat('d M Y H:i') }}</p>
                                <p><span class="font-semibold">Kasir:</span> {{ Auth::user()->biodata->nama_lengkap }}</p>
                            </div>
                        </div>

                        {{-- Pelayanan --}}
                        @if($pelayanan->isNotEmpty())
                            <div class="mb-6">
                                <h4 class="font-semibold mb-2 text-base-content">Pelayanan</h4>

                                <div class="space-y-3">
                                    @foreach($pelayanan as $item)
                                        <div class="bg-base-100 border border-base-300 border-t-3 border-t-primary rounded-lg p-3 shadow-sm hover:shadow transition">
                                            {{-- Nama pelayanan --}}
                                            <div class="font-semibold text-base-content mb-1">
                                                {{ ucfirst($item->pelayanan->nama_pelayanan ?? '-') }}
                                            </div>

                                            {{-- Harga --}}
                                            <div class="text-sm text-base-content/70">
                                                {{ $item->jumlah_pelayanan ?? 0 }} x 
                                                Rp {{ number_format($item->pelayanan->harga_pelayanan ?? 0, 0, ',', '.') }}
                                            </div>

                                            {{-- Diskon, Potongan dan Subtotal --}}
                                            <div class="mt-2 text-sm space-y-0.5">
                                                @if ($item->potongan)
                                                <div class="flex justify-between">
                                                    <span class="text-base-content/70">Potongan</span>
                                                    <span class="font-medium text-base-content/70">
                                                        {{ number_format($item->potongan ?? 0, 0, ',', '.') }}
                                                    </span>
                                                </div>
                                                @endif
                                                @if ($item->diskon)
                                                <div class="flex justify-between">
                                                    <span class="text-base-content/70">Diskon</span>
                                                    <span class="font-medium text-base-content">
                                                        {{ $item->diskon ? $item->diskon . '%' : '-' }}
                                                    </span>
                                                </div>
                                                @endif
                                                <div class="flex justify-between border-t border-dashed pt-1">
                                                    <span class="text-base-content">Subtotal</span>
                                                    <span class="font-semibold text-base-content">
                                                        {{-- Rp {{ number_format($item->subtotal ?? 0, 0, ',', '.') }} --}}
                                                        Rp {{ number_format($item->pelayanan->harga_pelayanan ?? 0, 0, ',', '.') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Treatment --}}
                        @if($treatment->isNotEmpty())
                            <div class="mb-6">
                                <h4 class="font-semibold mb-2 text-base-content">Treatment</h4>

                                <div class="space-y-3">
                                    @foreach($treatment as $item)
                                        <div class="bg-base-100 border border-base-300 border-t-3 border-t-error rounded-lg p-3 shadow-sm hover:shadow transition">
                                            {{-- Nama treatment --}}
                                            <div class="font-semibold text-base-content mb-1">
                                                {{ ucfirst($item->treatment->nama_treatment ?? '-') }}
                                            </div>

                                            {{-- Baris harga: jumlah x harga satuan --}}
                                            <div class="text-sm text-base-content/70">
                                                {{ $item->jumlah_treatment ?? 0 }} x 
                                                Rp {{ number_format($item->treatment->harga_treatment ?? 0, 0, ',', '.') }}
                                            </div>

                                            {{-- Diskon, Potongan dan Subtotal --}}
                                            <div class="mt-2 text-sm space-y-0.5">
                                                @if ($item->potongan)
                                                <div class="flex justify-between">
                                                    <span class="text-base-content/70">Potongan</span>
                                                    <span class="font-medium text-base-content/70">
                                                        {{ number_format($item->potongan ?? 0, 0, ',', '.') }}
                                                    </span>
                                                </div>
                                                @endif
                                                @if ($item->diskon)
                                                <div class="flex justify-between">
                                                    <span class="text-base-content/70">Diskon</span>
                                                    <span class="font-medium text-base-content/70">
                                                        {{ $item->diskon ? $item->diskon . '%' : '-' }}
                                                    </span>
                                                </div>
                                                @endif
                                                <div class="flex justify-between border-t border-dashed pt-1">
                                                    <span class="text-base-content">Subtotal</span>
                                                    <span class="font-semibold text-base-content">
                                                        Rp {{ number_format($item->subtotal ?? 0, 0, ',', '.') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Produk --}}
                        @if($produk->isNotEmpty())
                            <div class="mb-6">
                                <h4 class="font-semibold mb-2 text-base-content">Produk</h4>
                                
                                <div class="space-y-3">
                                    @foreach($produk as $item)
                                        <div class="bg-base-100 border border-base-300 border-t-3 border-t-warning rounded-lg p-3 shadow-sm hover:shadow transition">
                                            {{-- Nama produk --}}
                                            <div class="font-semibold text-base-content mb-1">
                                                {{ ucfirst($item->produk->nama_dagang ?? '-') }}
                                            </div>

                                            {{-- Baris harga: jumlah x harga satuan --}}
                                            <div class="text-sm text-base-content/70">
                                                {{ $item->jumlah_produk ?? 1 }} x 
                                                Rp {{ number_format($item->produk->harga_dasar ?? 0, 0, ',', '.') }}
                                            </div>

                                            {{-- Diskon, Potongan dan Subtotal --}}
                                            <div class="mt-2 text-sm space-y-0.5">
                                                @if ($item->potongan)
                                                <div class="flex justify-between">
                                                    <span class="text-base-content/70">Potongan</span>
                                                    <span class="font-medium text-base-content/70">
                                                        Rp {{ number_format($item->potongan ?? 0, 0, ',', '.') }}
                                                    </span>
                                                </div>
                                                @endif

                                                @if ($item->diskon)
                                                <div class="flex justify-between">
                                                    <span class="text-base-content/70">Diskon</span>
                                                    <span class="font-medium text-base-content/70">
                                                        {{ $item->diskon ? $item->diskon . '%' : '-' }}
                                                    </span>
                                                </div>
                                                @endif

                                                <div class="flex justify-between border-t border-dashed pt-1">
                                                    <span class="text-base-content">Subtotal</span>
                                                    <span class="font-semibold text-base-content">
                                                        Rp {{ number_format($item->subtotal ?? 0, 0, ',', '.') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Bundling --}}
                        @if($bundling->isNotEmpty())
                            <div class="mb-6">
                                <h4 class="font-semibold mb-2 text-base-content">Bundling</h4>

                                <div class="space-y-3">
                                    @foreach($bundling as $item)
                                        <div class="bg-base-100 border border-base-300 border-t-3 border-t-success rounded-lg p-3 shadow-sm hover:shadow transition">
                                            
                                            {{-- Nama bundling dan harga --}}
                                            <div class="font-semibold text-base-content mb-1">
                                                {{ ucfirst($item->bundling->nama ?? '-') }}
                                            </div>

                                            {{-- Jumlah, Harga Satuan, Potongan, Diskon --}}
                                            <div class="text-sm text-base-content/70">
                                                <div class="">
                                                    <span class="font-medium text-base-content/70">{{ $item->jumlah_bundling ?? 0 }} x </span>
                                                    <span class="font-medium text-base-content/70">Rp {{ number_format($item->bundling->harga ?? 0, 0, ',', '.') }}</span>
                                                </div>

                                                @if ($item->potongan)
                                                    <div class="flex justify-between">
                                                        <span class="font-medium text-base-content/70">Potongan</span>
                                                        <span class="font-medium text-base-content/70">
                                                            Rp {{ number_format($item->potongan ?? 0, 0, ',', '.') }}
                                                        </span>
                                                    </div>
                                                @endif

                                                @if ($item->diskon)
                                                    <div class="flex justify-between">
                                                        <span class="font-medium text-base-content/70">Diskon</span>
                                                        <span class="font-medium text-base-content/70">{{ $item->diskon }}%</span>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Detail item bundling --}}
                                            <div class="ml-3 text-sm text-base-content/70 mt-3 space-y-2">
                                                @php $hasItems = false; @endphp

                                                {{-- Treatment --}}
                                                @foreach($item->bundling->treatmentBundlingRM ?? [] as $t)
                                                    @php $hasItems = true; @endphp
                                                    <div class="flex justify-between items-center">
                                                        <span>
                                                            {{ $t->treatment->nama_treatment ?? '-' }}
                                                            <span class="text-xs text-base-content/70">(Treatment)</span>
                                                        </span>
                                                        <span class="font-medium text-base-content/70">
                                                            {{ $t->jumlah_terpakai ?? 0 }} dari {{ $t->jumlah_awal ?? 0 }} telah digunakan
                                                        </span>
                                                    </div>
                                                @endforeach

                                                {{-- Pelayanan --}}
                                                @foreach($item->bundling->pelayananBundlingRM ?? [] as $p)
                                                    @php $hasItems = true; @endphp
                                                    <div class="flex justify-between items-center">
                                                        <span>
                                                            {{ $p->pelayanan->nama_pelayanan ?? '-' }}
                                                            <span class="text-xs text-base-content/70">(Pelayanan)</span>
                                                        </span>
                                                        <span class="font-medium text-base-content/70">
                                                            {{ $p->jumlah_terpakai ?? 0 }} dari {{ $p->jumlah_awal ?? 0 }} telah digunakan
                                                        </span>
                                                    </div>
                                                @endforeach

                                                {{-- Produk --}}
                                                @foreach($item->bundling->produkObatBundlingRM ?? [] as $pr)
                                                    @php $hasItems = true; @endphp
                                                    <div class="flex justify-between items-center">
                                                        <span>
                                                            {{ $pr->produk->nama_dagang ?? '-' }}
                                                            <span class="text-xs text-base-content/70">(Produk)</span>
                                                        </span>
                                                        <span class="font-medium text-base-content/70">
                                                            {{ $pr->jumlah_terpakai ?? 0 }} dari {{ $pr->jumlah_awal ?? 0 }} telah diambil
                                                        </span>
                                                    </div>
                                                @endforeach

                                                @unless($hasItems)
                                                    <div class="italic text-base-content/70">Tidak ada item di bundling ini.</div>
                                                @endunless
                                            </div>

                                            {{-- Subtotal --}}
                                            <div class="flex justify-between border-t border-dashed pt-2 mt-3 text-sm">
                                                <span class="text-base-content">Subtotal</span>
                                                <span class="font-semibold text-base-content">
                                                    Rp {{ number_format($item->subtotal ?? ($item->bundling->harga ?? 0), 0, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($obatapoteker->isNotEmpty())
                            <form wire:submit.prevent="create" >
                                {{-- === OBAT NON RACIK (FINAL) === --}}
                                <div class="mb-6">
                                    <h4 class="font-semibold mb-2 text-base-content">Obat Non Racik</h4>
    
                                    <div class="space-y-3">
                                        @foreach($obatapoteker as $final)
                                            @foreach($final->obatNonRacikanFinals ?? [] as $item)
                                                <label class="block cursor-pointer"
                                                        x-data="{
                                                            get checked() {
                                                                return $wire.selectedObat.includes({{ $item->id }});
                                                            },
                                                            updateLivewire(e) {
                                                                let updated = [...$wire.selectedObat];

                                                                if (e.target.checked) {
                                                                    if (!updated.includes({{ $item->id }})) {
                                                                        updated.push({{ $item->id }});
                                                                    }
                                                                } else {
                                                                    updated = updated.filter(i => i !== {{ $item->id }});
                                                                }

                                                                $wire.set('selectedObat', updated);
                                                            }
                                                        }"
                                                    >
                                                    <div class="bg-base-100 border border-base-300 border-t-3 border-t-warning rounded-lg p-3 shadow-sm hover:shadow transition">

                                                        {{-- Nama Obat --}}
                                                        <div class="font-semibold text-base-content mb-1 flex items-center gap-2">
                                                            <input type="checkbox"
                                                                :checked="checked"
                                                                @change="updateLivewire($event)"
                                                                class="checkbox checkbox-primary checkbox-xs"
                                                            />
                                                            {{ ucfirst($item->produk->nama_dagang ?? '-') }}
                                                        </div>

                                                        {{-- Jumlah & Harga --}}
                                                        <div class="text-sm text-base-content/70" :class="checked ? '' : 'blur-[2px]'">
                                                            {{ $item->jumlah_obat ?? 0 }} {{ $item->satuan_obat ?? '' }} ×
                                                            Rp {{ number_format($item->harga_obat ?? 0, 0, ',', '.') }}
                                                        </div>

                                                        {{-- Potongan & Diskon --}}
                                                        <div class="mt-2 text-sm space-y-0.5" :class="checked ? '' : 'blur-[2px]'">
                                                            @if ($item->potongan)
                                                                <div class="flex justify-between">
                                                                    <span class="text-base-content/70">Potongan</span>
                                                                    <span class="font-medium text-base-content/70">
                                                                        Rp {{ number_format($item->potongan ?? 0, 0, ',', '.') }}
                                                                    </span>
                                                                </div>
                                                            @endif

                                                            @if ($item->diskon)
                                                                <div class="flex justify-between">
                                                                    <span class="text-base-content/70">Diskon</span>
                                                                    <span class="font-medium text-base-content">
                                                                        {{ $item->diskon }}%
                                                                    </span>
                                                                </div>
                                                            @endif

                                                            {{-- Subtotal --}}
                                                            <div class="flex justify-between border-t border-dashed pt-1">
                                                                <span class="text-base-content">Subtotal</span>
                                                                <span class="font-semibold text-base-content">
                                                                    Rp {{ number_format($item->total_obat ?? 0, 0, ',', '.') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
    
                                {{-- === OBAT RACIK (FINAL) === --}}
                                <div class="mb-6">
                                    <h4 class="font-semibold mb-2 text-base-content">Obat Racik</h4>
    
                                    <div class="space-y-3">
                                        @foreach($obatapoteker as $final)
                                            @foreach($final->obatRacikanFinals ?? [] as $item)
                                                <label class="block cursor-pointer"
                                                        wire:ignore
                                                        x-data="{
                                                            get checked() {
                                                                return $wire.selectedRacikan.includes({{ $item->id }});
                                                            },
                                                            toggle() {
                                                                let updated = [...$wire.selectedRacikan];
                                                                const id = {{ $item->id }};

                                                                if (updated.includes(id)) {
                                                                    updated = updated.filter(i => i !== id);
                                                                } else {
                                                                    updated.push(id);
                                                                }

                                                                $wire.set('selectedRacikan', updated);
                                                            }
                                                        }"
                                                    >
                                                    <div class="bg-base-100 border border-base-300 border-t-3 border-t-success rounded-lg p-3 shadow-sm hover:shadow transition">

                                                        {{-- Nama Racikan --}}
                                                        <div class="font-semibold text-base-content mb-1 flex items-center gap-2">
                                                            <input type="checkbox"
                                                                :checked="checked"
                                                                @change="toggle()"
                                                                class="checkbox checkbox-primary checkbox-xs"
                                                            />
                                                            {{ ucfirst($item->nama_racikan ?? '-') }}
                                                        </div>

                                                        {{-- Potongan & Diskon --}}
                                                        <div class="mt-2 text-sm space-y-0.5" :class="checked ? '' : 'blur-[2px]'">
                                                            @if ($item->potongan)
                                                                <div class="flex justify-between">
                                                                    <span class="text-base-content/70">Potongan</span>
                                                                    <span class="font-medium text-base-content/70">
                                                                        Rp {{ number_format($item->potongan ?? 0, 0, ',', '.') }}
                                                                    </span>
                                                                </div>
                                                            @endif

                                                            @if ($item->diskon)
                                                                <div class="flex justify-between">
                                                                    <span class="text-base-content/70">Diskon</span>
                                                                    <span class="font-medium text-base-content">
                                                                        {{ $item->diskon }}%
                                                                    </span>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        {{-- Bahan Racikan --}}
                                                        <div class="ml-3 text-sm text-base-content/70 mt-3 space-y-2" :class="checked ? '' : 'blur-[2px]'">
                                                            @php $bahanList = $item->bahanRacikanFinals ?? collect(); @endphp

                                                            @if($bahanList->isNotEmpty())
                                                                @foreach($bahanList as $detail)
                                                                    <div class="flex justify-between items-center">
                                                                        <span>{{ $detail->produk->nama_dagang ?? '-' }}</span>
                                                                        <span class="font-medium text-base-content/70">
                                                                            {{ $detail->jumlah_obat ?? 0 }} {{ $detail->satuan_obat ?? '' }} × Rp {{ number_format($detail->harga_obat ?? 0, 0, ',', '.') }}
                                                                        </span>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <div class="italic text-base-content/70">Tidak ada bahan racikan.</div>
                                                            @endif
                                                        </div>

                                                        {{-- Subtotal --}}
                                                        <div class="flex justify-between border-t border-dashed pt-2 mt-3 text-sm" :class="checked ? '' : 'blur-[2px]'">
                                                            <span class="text-base-content">Subtotal</span>
                                                            <span class="font-semibold text-base-content">
                                                                Rp {{ number_format($item->total_racikan ?? 0, 0, ',', '.') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </label>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            </form>
                        @else
                            {{-- === OBAT NON RACIK (RME Draft) === --}}
                            @if($obatnonracik->isNotEmpty())
                                <div class="mb-6">
                                    <h4 class="font-semibold mb-2 text-base-content">Obat Non Racik</h4>

                                    <div class="space-y-3">
                                        @foreach($obatnonracik as $item)
                                            <div class="bg-base-100 border border-base-300 border-t-3 border-t-warning rounded-lg p-3 shadow-sm hover:shadow transition">
                                                <div class="font-semibold text-base-content mb-1">
                                                    {{ ucfirst($item->nama_obat_non_racikan ?? '-') }}
                                                </div>

                                                <div class="text-sm text-base-content/70">
                                                    {{ $item->jumlah_obat_non_racikan ?? 0 }} {{ $item->satuan_obat_non_racikan ?? '' }}
                                                </div>

                                                {{-- Diskon & Potongan --}}
                                                <div class="mt-2 text-sm space-y-0.5">
                                                    @if ($item->potongan)
                                                        <div class="flex justify-between">
                                                            <span class="text-base-content/70">Potongan</span>
                                                            <span class="font-medium text-base-content/70">
                                                                Rp {{ number_format($item->potongan ?? 0, 0, ',', '.') }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @if ($item->diskon)
                                                        <div class="flex justify-between">
                                                            <span class="text-base-content/70">Diskon</span>
                                                            <span class="font-medium text-base-content/70">{{ $item->diskon }}%</span>
                                                        </div>
                                                    @endif
                                                    <div class="flex justify-between border-t border-dashed pt-1">
                                                        <span class="text-base-content">Subtotal</span>
                                                        <span class="font-semibold text-base-content">Sedang Dalam Proses</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- === OBAT RACIK (RME Draft) === --}}
                            @if($obatracik->isNotEmpty())
                                <div class="mb-6">
                                    <h4 class="font-semibold mb-2 text-base-content">Obat Racik</h4>

                                    <div class="space-y-3">
                                        @foreach($obatracik as $item)
                                            <div class="bg-base-100 border border-base-300 border-t-3 border-t-success rounded-lg p-3 shadow-sm hover:shadow transition">
                                                <div class="font-semibold text-base-content mb-1">
                                                    {{ ucfirst($item->nama_racikan ?? '-') }}
                                                </div>

                                                <div class="ml-3 text-sm text-base-content/70 mt-3 space-y-2">
                                                    @php $bahanList = $item->bahanRacikan ?? collect(); @endphp

                                                    @if($bahanList->isNotEmpty())
                                                        @foreach($bahanList as $b)
                                                            <div class="flex justify-between items-center">
                                                                <span>{{ $b->nama_obat_racikan ?? '-' }}</span>
                                                                <span>{{ $b->jumlah_obat_racikan ?? 0 }} {{ $b->satuan_obat_racikan ?? '' }}</span>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="italic text-base-content/70">Tidak ada bahan racikan.</div>
                                                    @endif
                                                </div>

                                                <div class="flex justify-between border-t border-dashed pt-2 mt-3 text-sm">
                                                    <span class="text-base-content">Subtotal</span>
                                                    <span class="font-semibold text-base-content">Sedang Dalam Proses</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Kolom Kanan: Invoice --}}
                <div class="lg:col-span-2">
                    <div class="sticky top-20 space-y-6">
                        <div class="bg-base-100 border-t-3 border-t-info shadow rounded-box p-4">
                            <h3 class="font-semibold mb-4">Invoice</h3>
                            <div class="space-y-2 text-sm">

                                {{-- === Pelayanan, Treatment, Produk, Bundling === --}}
                                @foreach([$pelayanan, $treatment, $produk, $bundling] as $collection)
                                    @foreach($collection as $item)
                                        @php
                                            $nama = $item->pelayanan->nama_pelayanan
                                                ?? $item->treatment->nama_treatment
                                                ?? $item->produk->nama_dagang
                                                ?? $item->bundling->nama
                                                ?? '-';

                                            $harga = $item->subtotal
                                                ?? $item->pelayanan->harga_pelayanan
                                                ?? $item->treatment->harga_treatment
                                                ?? $item->produk->harga_jual
                                                ?? $item->bundling->harga_bundling
                                                ?? 0;
                                        @endphp

                                        <div class="flex justify-between">
                                            <span>{{ $nama }}</span>
                                            <span>Rp {{ number_format($harga, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                @endforeach

                                {{-- === Obat Non Racikan & Racikan === --}}
                                @foreach($obatapoteker as $obat)

                                    {{-- Non Racikan --}}
                                    @foreach($obat->obatNonRacikanFinals ?? [] as $non)
                                        @if(in_array($non->id, $selectedObat ?? []))
                                            <div class="flex justify-between">
                                                <span>{{ $non->produk->nama_dagang }}</span>
                                                <span>Rp {{ number_format($non->total_obat ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                        @endif
                                    @endforeach

                                    {{-- Racikan --}}
                                    @foreach($obat->obatRacikanFinals ?? [] as $racik)
                                        @if(in_array($racik->id, $selectedRacikan ?? []))
                                            <div class="flex justify-between">
                                                <span>{{ $racik->nama_racikan ?? 'Obat Racikan' }}</span>
                                                <span>Rp {{ number_format($racik->total_racikan ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                        @endif
                                    @endforeach

                                    {{-- Tuslah & Embalase -> khusus racikan --}}
                                    @php
                                        $adaRacikanDipilih = $obat->obatRacikanFinals?->whereIn('id', $selectedRacikan ?? [])->isNotEmpty();
                                    @endphp

                                    @if($adaRacikanDipilih)
                                        @if($obat->tuslah)
                                            <div class="flex justify-between">
                                                <span>Tuslah</span>
                                                <span>Rp {{ number_format($obat->tuslah, 0, ',', '.') }}</span>
                                            </div>
                                        @endif

                                        @if($obat->embalase)
                                            <div class="flex justify-between">
                                                <span>Embalase</span>
                                                <span>Rp {{ number_format($obat->embalase, 0, ',', '.') }}</span>
                                            </div>
                                        @endif
                                    @endif

                                @endforeach

                                <hr class="my-2 border-base-300">

                                {{-- === Hitung Total === --}}
                                @php
                                    $total = collect([$pelayanan, $treatment, $produk, $bundling])
                                        ->flatten()
                                        ->sum(fn($item) => $item->subtotal
                                            ?? $item->pelayanan->harga_pelayanan
                                            ?? $item->treatment->harga_treatment
                                            ?? $item->produk->harga_jual
                                            ?? $item->bundling->harga_bundling
                                            ?? 0
                                        );

                                    foreach ($obatapoteker as $obat) {
                                        // Total obat dipilih
                                        $total +=
                                            ($obat->obatNonRacikanFinals?->whereIn('id', $selectedObat ?? [])->sum('total_obat') ?? 0) +
                                            ($obat->obatRacikanFinals?->whereIn('id', $selectedRacikan ?? [])->sum('total_racikan') ?? 0);

                                        // Tambah tuslah & embalase hanya jika ada racikan terpilih
                                        $adaRacikanDipilih = $obat->obatRacikanFinals?->whereIn('id', $selectedRacikan ?? [])->isNotEmpty();

                                        if($adaRacikanDipilih){
                                            $total += ($obat->tuslah ?? 0) + ($obat->embalase ?? 0);
                                        }
                                    }
                                @endphp

                                <div class="flex justify-between font-bold text-base mt-3">
                                    <span>Total:</span>
                                    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                                </div>
                                
                                @if ($pasienTerdaftar->status_terdaftar == "pembayaran")
                                    <button wire:click.prevent="create" class="btn btn-success btn-sm mt-4 w-full">
                                        <i class="fa-solid fa-plus"></i> Bayar
                                    </button>
                                @else
                                    <button class="btn btn-warning btn-sm mt-4 w-full">
                                        <i class="fa-solid fa-rotate"></i> Diproses
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>

    </div>
</div>