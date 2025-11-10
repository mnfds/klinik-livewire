<div class="p-4 bg-base-200 rounded-xl border border-base-300">
    <h3 class="font-semibold mb-3">
        Bundling Aktif: {{ $row->nama }}
    </h3>

    @php
        $groupedBundlings = collect()
            ->merge($row->pelayananBundlings ?? collect())
            ->merge($row->produkObatBundlings ?? collect())
            ->merge($row->treatmentBundlings ?? collect())
            ->filter(fn($item) => ($item->jumlah_awal ?? 0) > ($item->jumlah_terpakai ?? 0))
            ->groupBy(fn($item) => $item->bundling->nama ?? 'Tanpa Nama');
    @endphp

    @forelse ($groupedBundlings as $bundleName => $items)
        <div class="bg-white shadow-sm rounded-lg p-3 mb-3">
            <p class="font-medium text-primary mb-1">{{ $bundleName }}</p>
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach ($items as $item)
                    @php
                        $sisa = $item->jumlah_terpakai ?? 0;
                        $total = $item->jumlah_awal ?? 0;
                    @endphp

                    @if(isset($item->pelayanan))
                        <li>
                            Pelayanan: {{ $item->pelayanan->nama_pelayanan }}
                            <span class="text-xs text-gray-500 ml-2">({{ $sisa }} / {{ $total }})</span>
                        </li>
                    @elseif(isset($item->produk))
                        <li>
                            Produk: {{ $item->produk->nama_dagang }}
                            <span class="text-xs text-gray-500 ml-2">({{ $sisa }} / {{ $total }})</span>
                        </li>
                    @elseif(isset($item->treatment))
                        <li>
                            Treatment: {{ $item->treatment->nama_treatment }}
                            <span class="text-xs text-gray-500 ml-2">({{ $sisa }} / {{ $total }})</span>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    @empty
        <p class="italic text-sm text-gray-500">Tidak ada bundling dengan sisa.</p>
    @endforelse

    <div class="flex justify-end">
        <button
            wire:click.prevent="toggleDetail('{{ $id }}')"
            class="bg-red-600 text-white rounded-lg py-1 px-3 text-xs">
            Tutup
        </button>
    </div>
</div>
