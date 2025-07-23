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
                        <a href="{{ route('pendaftaran.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Pasien Terdaftar
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pendaftaran.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Pendaftaran Pasien
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-lg font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Daftarkan Pasien
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 shadow rounded-box p-6 space-y-6">
                <h2 class="text-xl font-bold">Formulir Pendaftaran</h2>

                {{-- Info jika ada ID --}}
                @if ($id && $pasien)
                    <div class="alert alert-info shadow">
                        Menampilkan data pasien dengan ID: {{ $id }} ({{ $pasien->nama }})
                    </div>
                @endif

                {{-- Form --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label">Nama</label>
                        <input type="text" class="input input-bordered" wire:model.defer="nama" placeholder="Nama Pasien" />
                    </div>

                    <div class="form-control">
                        <label class="label">NIK</label>
                        <input type="text" class="input input-bordered" wire:model.defer="nik" placeholder="Nomor Induk Kependudukan" />
                    </div>

                    <div class="form-control md:col-span-2">
                        <label class="label">Alamat</label>
                        <textarea class="textarea textarea-bordered" wire:model.defer="alamat" placeholder="Alamat Lengkap"></textarea>
                    </div>
                </div>

                {{-- Tombol Submit --}}
                <div class="pt-4">
                    <button class="btn btn-primary" wire:click="submit">Simpan</button>
                </div>
            </div>
        </div>

    </div>
</div>