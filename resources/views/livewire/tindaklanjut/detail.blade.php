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
                            <i class="fa-regular fa-folder-open"></i>
                            Tindak Lanjut
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Daftar Pasien Dengan Layanan Tersisa
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                <div class="p-6 text-base-content space-y-4">
                    @if ($pasien)
                        <div>
                            <h2 class="font-bold text-lg mb-3">
                                Bundling Belum Selesai — {{ $pasien->nama }}
                            </h2>

                            @forelse ($bundlingAktif as $bundleName => $items)
                                <div class="bg-base-300 shadow-sm rounded-lg p-4 mb-4">
                                    <p class="font-semibold text-primary mb-2">
                                        {{ $bundleName }}
                                    </p>

                                    <table class="table-auto w-full text-sm border-collapse">
                                        <thead>
                                            <tr class="border-b border-base-200">
                                                <th class="py-1 text-left">Tipe</th>
                                                <th class="py-1 text-left">Nama Item</th>
                                                <th class="py-1 text-center">Total</th>
                                                <th class="py-1 text-center">Terpakai</th>
                                                <th class="py-1 text-center">Sisa</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($items as $item)
                                                <tr class="border-b border-base-200">
                                                    <td class="py-1 text-left">{{ $item['tipe'] }}</td>
                                                    <td class="py-1 text-left">{{ $item['nama_item'] }}</td>
                                                    <td class="py-1 text-center">{{ $item['jumlah_awal'] }}</td>
                                                    <td class="py-1 text-center">{{ $item['jumlah_terpakai'] }}</td>
                                                    <td class="py-1 font-medium text-success text-center">{{ $item['sisa'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @empty
                                <p class="italic text-base-200">Tidak ada bundling aktif untuk pasien ini.</p>
                            @endforelse
                        </div>

                        <hr class="my-6 border-base-200">

                        <div>
                            <h2 class="font-bold text-lg mb-3">
                                Bundling Selesai
                            </h2>

                            @forelse ($bundlingSelesai as $bundleName => $items)
                                <div class="bg-white shadow-sm rounded-lg p-4 mb-4 opacity-70">
                                    <p class="font-semibold text-gray-600 mb-2">
                                        {{ $bundleName }}
                                    </p>

                                    <table class="table-auto w-full text-sm border-collapse">
                                        <thead>
                                            <tr class="text-left border-b border-gray-300">
                                                <th class="py-1">Tipe</th>
                                                <th class="py-1">Nama Item</th>
                                                <th class="py-1">Total</th>
                                                <th class="py-1">Terpakai</th>
                                                <th class="py-1">Sisa</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($items as $item)
                                                <tr class="border-b border-gray-200 text-gray-500">
                                                    <td class="py-1">{{ $item['tipe'] }}</td>
                                                    <td class="py-1">{{ $item['nama_item'] }}</td>
                                                    <td class="py-1">{{ $item['jumlah_awal'] }}</td>
                                                    <td class="py-1">{{ $item['jumlah_terpakai'] }}</td>
                                                    <td class="py-1 text-red-600 font-medium">{{ $item['sisa'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @empty
                                <p class="italic text-gray-500">Tidak ada bundling yang sudah selesai.</p>
                            @endforelse
                        </div>
                    @else
                        <p class="text-red-500 font-medium">Pasien tidak ditemukan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>