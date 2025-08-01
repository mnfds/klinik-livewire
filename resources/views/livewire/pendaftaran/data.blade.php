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
                        <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content/70" aria-label="Antrian Pendaftaran" checked="checked" />
                        <div class="tab-content bg-base-100 border-base-300 p-6">
                            <div class="flex justify-between items-center mb-4">
                                <a wire:navigate href="{{ route('pendaftaran.search') }}" class="btn btn-primary">
                                    <i class="fa-solid fa-laptop-medical"></i>Registrasi
                                </a>
                            </div>
                            <livewire:pendaftaran.pendaftaran-table />
                        </div>
                        
                        <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content/70" aria-label="Antrian Pasien Terdaftar" />
                        <div class="tab-content bg-base-100 border-base-300 p-6">
                            <div class="flex justify-between items-center mb-4">
                            </div>
                            <livewire:pendaftaran.pasiendiperiksa-table />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>