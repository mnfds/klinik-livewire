<div>
    <div class="mb-4">
        <div class="bg-base-100 p-5 rounded-xl shadow-sm border border-error/40">
            {{-- Header --}}
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 flex items-center justify-center 
                            rounded-lg bg-error/10 text-error text-lg">
                    <i class="fa-solid fa-heart"></i>
                </div>
                <div>
                    <h2 class="text-sm font-semibold uppercase tracking-wide">
                        Best Top 5 Treatment
                    </h2>
                    <p class="text-xs text-gray-500">
                        Treatment dengan performa penjualan tertinggi
                    </p>
                </div>
            </div>
            {{-- List Bundling --}}
            <div class="space-y-3">
                @forelse ($topTreatment as $index => $treatment)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-base-200 hover:bg-base-300 transition">
                        <div class="flex items-center gap-3">
                            {{-- Ranking Number --}}
                            @php
                                $bgClasses = [
                                    0 => 'bg-gradient-to-r from-yellow-400 to-yellow-500 text-white shadow-md',
                                    1 => 'bg-gradient-to-r from-gray-300 to-gray-400 text-white shadow-md',
                                    2 => 'bg-gradient-to-r from-amber-500 to-amber-700 text-white shadow-md',
                                ];
                            @endphp
                            <div class="w-7 h-7 flex items-center justify-center rounded-full text-xs font-bold {{ $bgClasses[$index] ?? 'bg-gray-200 text-gray-700' }}">
                                {{ $index + 1 }}
                            </div>
                            {{-- Nama Bundling --}}
                            <span class="font-medium text-sm">
                                {{ $treatment->nama_treatment }}
                            </span>
                        </div>

                        {{-- Total Penjualan --}}
                        <span class="text-sm font-semibold text-error">
                            {{ number_format($treatment->total_terjual) }} Terjual
                        </span>
                    </div>
                @empty
                    <div class="text-center text-sm text-gray-400 py-6">
                        Belum Ada Data Treatment
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>