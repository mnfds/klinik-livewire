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
                    </div>
                </div>

                <!-- Tombol Aksi (Bagian Kanan) -->
                <div>
                    <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                        <div class="p-6 text-base-content space-y-4">
                            <a wire:navigate href="{{ route('pasien.update', ['id' => $pasien->id]) }}" class="btn btn-secondary w-full">
                                <i class="fa-solid fa-pen-clip mr-2"></i>Edit Data Pasien
                            </a>
                            <button class="btn btn-info w-full">
                                <i class="fa-solid fa-clipboard-list mr-2"></i>Riwayat Rekam Medis Pasien
                            </button>
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