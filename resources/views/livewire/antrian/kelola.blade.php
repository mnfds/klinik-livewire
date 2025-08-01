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
                        <a href="{{ route('antrian.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Antrian
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('antrian.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Kelola Antrian
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Kelola Antrian
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                
                <div class="p-6 text-base-content space-y-4">
                    <!-- name of each tab group should be unique -->
                    <div class="tabs tabs-lift">

                        {{-- Tabel Antrian Masuk Dan Dipanggil --}}
                        <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content/70" aria-label="Antrian Pendaftaran" checked="checked" />
                        <div class="tab-content bg-base-100 border-base-300 p-6">
                            <div class="flex flex-col md:flex-row gap-4">
                                <!-- Tabel Antrian Masuk -->
                                <div class="w-full md:w-1/2" wire:poll.visible.15s="refreshTableMasuk">
                                    <div class="flex items-center justify-between mb-4">
                                        <h1 class="text-lg font-bold text-base-content">Antrian Masuk</h1>
                                    </div>
                                    <livewire:antriantable.masuk />
                                </div>

                                <!-- Tabel Antrian Dipanggil -->
                                <div class="w-full md:w-1/2">
                                    <div class="flex items-center justify-between mb-4" wire:poll.visible.15s="refreshTableDipanggil">
                                        <h1 class="text-lg font-bold text-base-content">
                                            Antrian Dipanggil
                                        </h1>
                                    </div>
                                    <livewire:antriantable.dipanggil />
                                </div>
                            </div>
                        </div>
                        
                        <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content/70" aria-label="Antrian Pasien Terdaftar" />
                        <div class="tab-content bg-base-100 border-base-300 p-6">
                            {{-- Isi Tab Konten Antrian Pasien Yang Sudah Terdaftar --}}
                            <h1 class="text-lg font-bold text-base-content">
                                Antrian Terdaftar
                            </h1>
                            <livewire:pendaftaran.pendaftaran-table />
                        </div>

                        <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content/70" aria-label="Pasien Sedang Diperiksa" />
                        <div class="tab-content bg-base-100 border-base-300 p-6">
                            <h1 class="text-lg font-bold text-base-content">
                                <livewire:pendaftaran.pasiendiperiksa-table />
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>