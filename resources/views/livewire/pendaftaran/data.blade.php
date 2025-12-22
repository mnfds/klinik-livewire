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
                            <i class="fa-regular fa-folder-open"></i>
                            Pasien Terdaftar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Pasien Terdaftar
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                
                <div class="p-6 text-base-content space-y-4">
                    <div class="tabs tabs-lift">
                        {{-- Tabel Antrian Masuk Dan Dipanggil --}}
                        <label class="tab bg-transparent text-base-content gap-2 cursor-pointer">
                            <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Pasien Terdaftar" checked="checked" style="background-image: none;" />
                            <span class="flex items-center gap-2">
                                Pasien Terdaftar
                                <div class="badge badge-sm badge-primary text-base-primary">{{ $jumlahPasienTerdaftar }}</div>
                            </span>
                        </label>
                        <div class="tab-content bg-base-100 border-base-300 p-6">
                            <div class="flex justify-between items-center mb-4">
                                @can('akses', 'Pasien Registrasi')
                                <a wire:navigate href="{{ route('pendaftaran.search') }}" class="btn btn-primary">
                                    <i class="fa-solid fa-laptop-medical"></i>Registrasi
                                </a>
                                @endcan
                            </div>
                            @if (Gate::allows('akses', 'Pasien Terdaftar Data'))
                                <livewire:pendaftaran.pendaftaran-table />
                            @else
                                <div class="alert alert-error">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                    Anda tidak memiliki akses untuk melihat data ini.
                                </div>
                            @endif
                        </div>
                        
                        <label class="tab bg-transparent text-base-content gap-2 cursor-pointer">
                            <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Pasien Menunggu Diperiksa" style="background-image: none;" />
                            <span class="flex items-center gap-2">
                                Pasien Menunggu Diperiksa
                                <div class="badge badge-sm badge-primary text-base-primary">{{ $jumlahPasienDiperiksa }}</div>
                            </span>
                        </label>
                        <div class="tab-content bg-base-100 border-base-300 p-6">
                            <div class="flex justify-between items-center mb-4">
                            </div>
                            @if (Gate::allows('akses', 'Pasien Diperiksa Data'))
                                <livewire:pendaftaran.pasiendiperiksa-table />
                            @else
                                <div class="alert alert-error">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                    Anda tidak memiliki akses untuk melihat data ini.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>