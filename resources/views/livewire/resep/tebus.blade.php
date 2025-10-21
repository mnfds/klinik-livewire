<div class="pt-1 pb-12">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Breadcrumbs (hanya muncul di layar lg ke atas) -->
        <div class="hidden lg:flex justify-end px-4">
            <div class="breadcrumbs text-sm">
                <ul>
                    <li>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('resep.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Resep
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('resep.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Detail Resep
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Daftar Obat Dan Produk
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                <!-- KONTEN UTAMA -->
                <div class="lg:col-span-4">
                    <div class="bg-base-100 shadow rounded-box">
                        <div class="p-6 text-base-content space-y-6">     

                            <div class="join join-vertical bg-base-100 w-full">
                                {{-- OBAT NON RACIKAN --}}
                                <div class="collapse collapse-open join-item border-base-300 border">
                                    <div class="collapse-title font-semibold">Obat Non Racik</div>
                                    <div class="collapse-content text-sm">
                                        @forelse ($obatNonRacikanItems as $o)
                                            <div class="py-2 border-b">
                                                <div class="flex justify-between font-semibold">
                                                    <span>{{ $o['nama_obat'] }}</span>
                                                    <span>{{ $o['jumlah_obat'] }} {{ $o['satuan_obat'] }}</span>
                                                </div>
                                                <div class="text-xs text-gray-600 flex justify-between mt-1">
                                                    <span>Dosis: {{ $o['dosis'] }} × {{ $o['hari'] }} hari</span>
                                                    <span>{{ ucfirst($o['aturan_pakai']) }}</span>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-gray-500 italic">Tidak ada obat non racikan.</p>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- OBAT RACIKAN --}}
                                <div class="collapse collapse-open join-item border-base-300 border">
                                    <div class="collapse-title font-semibold">Obat Racikan</div>
                                    <div class="collapse-content text-sm">
                                        @forelse ($obatRacikanItems as $r)
                                            <div class="py-2 border-b">
                                                {{-- Baris utama (nama + jumlah) --}}
                                                <div class="flex justify-between font-semibold">
                                                    <span>{{ $r['nama_racikan'] }}</span>
                                                    <span>{{ $r['jumlah_racikan'] }} {{ $r['satuan_racikan'] }}</span>
                                                </div>

                                                {{-- Baris dosis + metode racikan --}}
                                                <div class="text-xs text-gray-600 flex justify-between mt-1">
                                                    <span>Dosis: {{ $r['dosis'] }} × {{ $r['hari'] }} hari, {{ $r['aturan_pakai'] }}</span>
                                                    @if (!empty($r['metode_racikan']))
                                                        <span>Metode: {{ ucfirst($r['metode_racikan']) }}</span>
                                                    @endif
                                                </div>

                                                {{-- Komposisi racikan --}}
                                                <div class="mt-2">
                                                    <span class="font-medium text-xs">Komposisi Racikan:</span>
                                                    <ul class="ml-4 mt-1 list-disc text-xs">
                                                        @foreach ($r['bahan'] as $b)
                                                            <li>{{ $b['nama_obat'] }} - {{ $b['jumlah_obat'] }} {{ $b['satuan_obat'] }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-gray-500 italic">Tidak ada obat racikan.</p>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- PRODUK DIBELI --}}
                                <div class="collapse collapse-open join-item border-base-300 border">
                                    <div class="collapse-title font-semibold">Produk Dibeli</div>
                                    <div class="collapse-content text-sm">

                                        {{-- Produk reguler --}}
                                        @forelse ($produkRencanaItems as $p)
                                            <div class="flex justify-between py-1 border-b">
                                                <span>{{ $p['nama_produk'] }}</span>
                                                <span>{{ $p['jumlah'] }} {{ $p['satuan'] }}</span>
                                            </div>
                                        @empty
                                            {{-- opsional: bisa dikosongkan jika ingin bundling tetap muncul --}}
                                        @endforelse

                                        {{-- Produk dari Bundling --}}
                                        @forelse ($produkBundlingItems as $b)
                                            <div class="flex justify-between py-1 border-b">
                                                <div>
                                                    {{ $b['nama_produk'] }}
                                                    <span class="text-xs text-gray-500 italic">(Bundling: {{ $b['nama_bundling'] }})</span>
                                                </div>
                                                <span>{{ $b['jumlah'] }} {{ $b['satuan'] }}</span>
                                            </div>
                                        @empty
                                            {{-- biarkan kosong --}}
                                        @endforelse

                                        {{-- Jika kedua array kosong --}}
                                        @if (empty($produkRencanaItems) && empty($produkBundlingItems))
                                            <p class="text-gray-500 italic">Tidak ada produk dibeli.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <button wire:click.prevent="create" class="btn btn-success w-full mb-1" wire:loading.attr="disabled">
                                <span wire:loading.remove>Selesai</span>
                                <span wire:loading.inline>Loading...</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>