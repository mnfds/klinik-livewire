<div class="p-4 bg-base-200 rounded-xl border border-base-300">
    <h3 class="font-semibold mb-3">
        Bundling Aktif: {{ $row->nama }}
    </h3>

    @php
        // Gabungkan semua item dari 3 jenis
        $semuaBundlings = collect()
            ->merge($row->pelayananBundlings ?? collect())
            ->merge($row->produkObatBundlings ?? collect())
            ->merge($row->treatmentBundlings ?? collect())
            ->groupBy('group_bundling'); // 🔸 grupkan per group_bundling

        $groupedBundlings = $semuaBundlings->map(function ($group) {
            $isAktif = $group->contains(fn($i) => $i->jumlah_terpakai < $i->jumlah_awal);
            return [
                'status' => $isAktif ? 'aktif' : 'selesai',
                'nama' => $group->first()->bundling->nama ?? 'Tanpa Nama',
                'items' => $group,
            ];
        });
    @endphp

    @forelse ($groupedBundlings as $bundle)
        <div class="bg-white shadow-sm rounded-lg p-3 mb-3 border-l-4 {{ $bundle['status'] === 'aktif' ? 'border-l-blue-500' : 'border-l-green-500' }}">
            <div class="flex justify-between items-center">
                <p class="font-medium text-primary">{{ $bundle['nama'] }}</p>
                <span class="text-xs px-2 py-0.5 rounded-md {{ $bundle['status'] === 'aktif' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                    {{ ucfirst($bundle['status']) }}
                </span>
            </div>

            <ul class="list-disc list-inside text-sm space-y-1 mt-2">
                @foreach ($bundle['items'] as $item)
                    @php
                        $terpakai = $item->jumlah_terpakai ?? 0;
                        $total = $item->jumlah_awal ?? 0;
                        $sisa = max(0, $total - $terpakai);
                    @endphp

                    @if(isset($item->pelayanan))
                        <li>Pelayanan: {{ $item->pelayanan->nama_pelayanan }}
                            <span class="text-xs text-gray-500 ml-2">(Tersisa : {{ $sisa }})</span>
                            {{-- <span class="text-xs text-gray-500 ml-2">({{ $terpakai }} / {{ $total }})</span> --}}
                        </li>
                    @elseif(isset($item->produk))
                        <li>Produk: {{ $item->produk->nama_dagang }}
                            <span class="text-xs text-gray-500 ml-2">(Tersisa : {{ $sisa }})</span>
                            {{-- <span class="text-xs text-gray-500 ml-2">({{ $terpakai }} / {{ $total }})</span> --}}
                        </li>
                    @elseif(isset($item->treatment))
                        <li>Treatment: {{ $item->treatment->nama_treatment }}
                            <span class="text-xs text-gray-500 ml-2">(Tersisa : {{ $sisa }})</span>
                            {{-- <span class="text-xs text-gray-500 ml-2">({{ $terpakai }} / {{ $total }})</span> --}}
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
