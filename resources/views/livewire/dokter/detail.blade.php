<div class="pt-1 pb-12">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Breadcrumbs (hanya muncul di layar lg ke atas) -->
        <div class="hidden lg:flex justify-end px-4">
            <div class="breadcrumbs text-sm">
                <ul>
                    <li>
                        <a wire:navigate href="{{ route('dashboard') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a wire:navigate href="{{ route('dokter.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Dokter
                        </a>
                    </li>
                    <li>
                        <a wire:navigate href="{{ route('dokter.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Detail Dokter
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-lg font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Detail Data {{ $dokter->nama_dokter }}
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 text-base-content space-y-4">
                    <h2 class="text-xl font-bold mb-4">Detail Dokter</h2>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <p><strong>Nama:</strong> {{ $dokter->nama_dokter }}</p>
                            <p><strong>Jenis Kelamin:</strong> {{ $dokter->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                            <p><strong>Telepon:</strong> {{ $dokter->telepon ?? '-' }}</p>
                            <p><strong>Alamat:</strong> {{ $dokter->alamat_dokter ?? '-' }}</p>
                        </div>

                        <div>
                            <p><strong>Tingkat Pendidikan:</strong> {{ $dokter->tingkat_pendidikan ?? '-' }}</p>
                            <p><strong>Institusi:</strong> {{ $dokter->institusi ?? '-' }}</p>
                            <p><strong>Tahun Kelulusan:</strong> 
                                {{ \Carbon\Carbon::parse($dokter->tahun_kelulusan)->translatedFormat('Y') ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p><strong>Nomor STR:</strong> {{ $dokter->no_str ?? '-' }}</p>
                            <p><strong>Surat Izin Praktik:</strong> {{ $dokter->surat_izin_pratik ?? '-' }}</p>
                            <p><strong>Masa Berlaku SIP:</strong> 
                                {{ $dokter->masa_berlaku_sip ? \Carbon\Carbon::parse($dokter->masa_berlaku_sip)->translatedFormat('d F Y') : '-' }}
                            </p>
                        </div>

                        {{-- Daftar Poli --}}
                        <div>
                            <p class="font-semibold">Daftar Poli:</p>
                            @if ($poli->count())
                                <ul class="list-disc list-inside text-sm">
                                    @foreach ($poli as $item)
                                        <li>{{ $item->poli->nama_poli ?? '-' }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-gray-500 text-sm">Tidak terdaftar pada poli manapun.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Foto dan TTD --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6">
                        <div>
                            <p class="font-semibold mb-2">Foto Wajah</p>
                            @if ($dokter->foto_wajah)
                                <img src="{{ asset('storage/' . $dokter->foto_wajah) }}" alt="Foto Dokter"
                                    class="w-32 h-32 rounded border object-cover">
                            @else
                                <p class="text-gray-500">Belum ada foto</p>
                            @endif
                        </div>

                        <div>
                            <p class="font-semibold mb-2">Tanda Tangan Digital</p>
                            @if ($dokter->ttd_digital)
                                <img src="{{ asset('storage/' . $dokter->ttd_digital) }}" alt="TTD Digital"
                                    class="w-32 h-32 rounded border object-contain">
                            @else
                                <p class="text-gray-500">Belum ada tanda tangan</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>