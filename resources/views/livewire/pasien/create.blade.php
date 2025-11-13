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
                        <a href="{{ route('pasien.create') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Tambah Pasien
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Tambah Pasien Baru
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                
                <div class="p-6 text-base-content space-y-4">
                    <!-- name of each tab group should be unique -->
                    <div class="tabs tabs-lift">

                        {{-- Tab Create SatuSehat (NIK) --}}
                        <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content/70" aria-label="satuSehat (NIK)" checked="checked" />
                        <div class="tab-content bg-base-100 border-base-300 p-6">
                            <h1 class="text-md font-bold text-base-content">
                                Tambah pasien dengan NIK
                            </h1>
                            <livewire:pasien.create-by-nik />
                        </div>
                        
                        {{-- Tab Create SatuSehat (NAMA, JENIS KELAMIN, TANGGAL LAHIR) --}}
                        <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content/70" aria-label="SatuSehat (Nama, Jenis Kelamin, Tanggal Lahir)" />
                        <div class="tab-content bg-base-100 border-base-300 p-6">
                            <h1 class="text-md font-bold text-base-content">
                                Tambah pasien dengan nama, jenis kelamin, dan tanggal lahir
                            </h1>
                            <livewire:pasien.create-by-nama />
                        </div>

                        {{-- Tab Create Non Satu Sehat --}}
                        <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content/70" aria-label="Non SatuSehat" />
                        <div class="tab-content bg-base-100 border-base-300 p-6">
                            <h1 class="text-md font-bold text-base-content">
                                Tambah pasien tanpa SatuSehat
                            </h1>
                            <livewire:pasien.create-default />
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>