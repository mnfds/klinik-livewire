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
                        <a href="{{ route('pasien.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Pasien
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pasien.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Detail Pasien
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-lg font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Detail Data Pasien {{ $pasien->nama }}
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Biodata Pasien (Bagian Kiri) -->
                <div class="lg:col-span-2">
                    <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                        <div class="p-6 text-base-content space-y-4">
                            <!-- name of each tab group should be unique -->
                            <div class="tabs tabs-lift">
                                <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" style="background-image: none;" aria-label="Biodata" checked="checked" />
                                <div class="tab-content bg-base-100 border-base-300 p-6">
                                    <div class="text-xl font-semibold border-b pb-2">Biodata:</div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="font-semibold">Nama:</p>
                                            <p>{{ $pasien->nama }}</p>
                                        </div>
                                        <div>
                                            <p class="font-semibold">Nomor Register:</p>
                                            <p>{{ $pasien->no_register }}</p>
                                        </div>
                                        <div>
                                            <p class="font-semibold">NIK:</p>
                                            <p>{{ $pasien->nik ?: '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="font-semibold">No. IHS:</p>
                                            <p>{{ $pasien->no_ihs ?: '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="font-semibold">Jenis Kelamin:</p>
                                            <p>{{ $pasien->jenis_kelamin }}</p>
                                        </div>
                                        <div>
                                            <p class="font-semibold">Tanggal Lahir:</p>
                                            <p>{{ \Carbon\Carbon::parse($pasien->tanggal_lahir)->format('d M Y') }}</p>
                                        </div>
                                        <div>
                                            <p class="font-semibold">Agama:</p>
                                            <p>{{ $pasien->agama ?: '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="font-semibold">Profesi:</p>
                                            <p>{{ $pasien->profesi ?: '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="font-semibold">Status:</p>
                                            <p>{{ $pasien->status ?: '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="font-semibold">No. Telp:</p>
                                            <p>{{ $pasien->no_telp ?: '-' }}</p>
                                        </div>
                                        <div class="md:col-span-2">
                                            <p class="font-semibold">Alamat:</p>
                                            <p>{{ $pasien->alamat ?: '-' }}</p>
                                        </div>
                                        <div class="md:col-span-2">
                                            <p class="font-semibold">Deskripsi:</p>
                                            <p>{{ $pasien->deskripsi ?: '-' }}</p>
                                        </div>
                                        @if ($pasien->foto_pasien)
                                            <div class="md:col-span-2">
                                                <p class="font-semibold">Foto Pasien:</p>
                                                <img src="{{ asset('storage/' . $pasien->foto_pasien) }}"
                                                    alt="Foto Pasien"
                                                    class="w-40 h-40 object-cover rounded border">
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" style="background-image: none;" aria-label="Layanan Tersisa" />
                                <div class="tab-content bg-base-100 border-base-300 p-6">
                                    <div class="text-lg font-bold border-b pb-2">Layanan / Tindakan Tersisa</div>

                                    @php
                                        $grouped = [];
                                        foreach ($bundlingPasien['treatments'] as $t) {
                                            $grouped[$t->bundling_id]['nama'] = $t->bundling->nama;
                                            $grouped[$t->bundling_id]['treatments'][] = $t;
                                        }
                                        foreach ($bundlingPasien['pelayanans'] as $p) {
                                            $grouped[$p->bundling_id]['nama'] = $p->bundling->nama;
                                            $grouped[$p->bundling_id]['pelayanans'][] = $p;
                                        }
                                        foreach ($bundlingPasien['produks'] as $pr) {
                                            $grouped[$pr->bundling_id]['nama'] = $pr->bundling->nama;
                                            $grouped[$pr->bundling_id]['produks'][] = $pr;
                                        }
                                    @endphp

                                    @if(count($grouped))
                                        <div class="space-y-6">
                                            @foreach($grouped as $bundling)
                                                <div>
                                                    <p class="font-semibold text-sm mb-3">{{ $bundling['nama'] }}</p>

                                                    {{-- Treatments --}}
                                                    @if(!empty($bundling['treatments']))
                                                        <div class="mb-2">
                                                            <p class="font-medium text-xs text-gray-500 mb-1">Treatment</p>
                                                            <ul class="list-disc list-inside text-sm space-y-1 ml-3">
                                                                @foreach($bundling['treatments'] as $t)
                                                                    <li class="flex items-center justify-between border-b border-base-300 pb-1">
                                                                        <span>{{ optional($t->treatment)->nama_treatment ?? '-' }}</span>
                                                                        <span class="btn btn-xs text-xs btn-circle btn-primary">
                                                                            {{ $t->sisa ?? ($t->jumlah_awal - $t->jumlah_terpakai) }}x
                                                                        </span>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif

                                                    {{-- Pelayanan --}}
                                                    @if(!empty($bundling['pelayanans']))
                                                        <div class="mb-2">
                                                            <p class="font-medium text-xs text-gray-500 mb-1">Pelayanan</p>
                                                            <ul class="list-disc list-inside text-sm space-y-1 ml-3">
                                                                @foreach($bundling['pelayanans'] as $p)
                                                                    <li class="flex items-center justify-between border-b border-base-300 pb-1">
                                                                        <span>{{ optional($p->pelayanan)->nama_pelayanan ?? '-' }}</span>
                                                                        <span class="btn btn-xs text-xs btn-circle btn-primary">
                                                                            {{ $p->jumlah_awal - $p->jumlah_terpakai }}x
                                                                        </span>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif

                                                    {{-- Produk --}}
                                                    @if(!empty($bundling['produks']))
                                                        <div>
                                                            <p class="font-medium text-xs text-gray-500 mb-1">Produk Obat</p>
                                                            <ul class="list-disc list-inside text-sm space-y-1 ml-3">
                                                                @foreach($bundling['produks'] as $pr)
                                                                    <li class="flex items-center justify-between border-b border-base-300 pb-1">
                                                                        <span>{{ optional($pr->produk)->nama_dagang ?? '-' }}</span>
                                                                        <span class="btn btn-xs text-xs btn-circle btn-primary">
                                                                            {{ $pr->jumlah_awal - $pr->jumlah_terpakai }}x
                                                                        </span>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500">Tidak ada layanan tersisa</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi (Bagian Kanan) -->
                <div>
                    <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                        <div class="p-6 text-base-content space-y-4">
                            <a wire:navigate href="{{ route('pasien.update', ['id' => $pasien->id]) }}" class="btn btn-secondary w-full">
                                <i class="fa-solid fa-pen-clip mr-2"></i>Edit Data Pasien
                            </a>
                            <a wire:navigate href="{{ route('rekam-medis-pasien.data', ['pasien_id' => $pasien->id]) }}" class="btn btn-info w-full">
                                <i class="fa-solid fa-clipboard-list mr-2"></i>Riwayat Rekam Medis Pasien
                            </a>
                            <a wire:navigate href="{{ route('pendaftaran.create', ['pasien_id' => $pasien->id]) }}" class="btn btn-primary w-full">
                                <i class="fa-solid fa-laptop-medical"></i>Registrasi Pasien
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>