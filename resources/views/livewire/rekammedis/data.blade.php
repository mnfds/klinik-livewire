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
                            <i class="fa-regular fa-folder"></i>
                            Detail Pasien
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pasien.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Rekam Medis Pasien
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Riwayat Rekam Medis Pasien
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                <div class="p-6 space-y-6">

                    <!-- Biodata Pasien -->
                    <div class="bg-base-100 border border-base-300 rounded-lg p-4">
                        <h3 class="font-semibold text-lg mb-4">Biodata Pasien</h3>
                        <div class="grid grid-cols-2 gap-y-2 gap-x-4 text-sm">
                            <div><span class="font-medium">Nama</span> : {{ $pasien->nama }}</div>
                            <div><span class="font-medium">No. Register</span> : {{ $pasien->no_register }}</div>
                            <div><span class="font-medium">NIK</span> : {{ $pasien->nik }}</div>
                            <div><span class="font-medium">No. IHS</span> : {{ $pasien->no_ihs }}</div>
                            <div><span class="font-medium">Jenis Kelamin</span> : {{ $pasien->jenis_kelamin }}</div>
                            <div><span class="font-medium">Tanggal Lahir</span> : {{ \Carbon\Carbon::parse($pasien->tanggal_lahir)->translatedFormat('d F Y') }}</div>
                        </div>
                    </div>
                    
                    <div class="bg-base-100 border border-base-300 rounded-lg p-4">
                        <h3 class="font-semibold text-lg mb-4">Riwayat Kunjungan</h3>
                        <livewire:Rekammedis.RiwayatTable :pasien_id="$pasien_id">
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>