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
                        <a href="{{ route('jadwal.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Jadwal kerja
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Jadwal Kerja
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                <div class="p-6 text-base-content space-y-4">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 mb-4">
                        <!-- KIRI: Tambah Barang & Riwayat -->
                        <div class="w-full md:w-auto flex gap-[2px]">
                            @can('akses', 'Jadwal Download')
                            <button onclick="document.getElementById('downloadModal').showModal()" class="btn btn-success w-full"><i class="fa-solid fa-download"></i> Unduh Jadwal</button>
                            @endcan
                        </div>

                        <!-- KANAN: Filter Bulan, Role, Cari -->
                        <div class="w-full md:w-auto flex flex-col md:grid md:grid-cols-3 gap-2 md:gap-[2px] mt-2 md:mt-0">
                            <input type="month" wire:model="thisMonth" wire:loading.attr="disabled" wire:target="previousMonth,nextMonth,$refresh,thisMonth,selectedRole" class="input input-bordered w-full">
                            <select wire:model="selectedRole" wire:loading.attr="disabled" wire:target="previousMonth,nextMonth,$refresh,thisMonth,selectedRole" class="select select-bordered w-full">
                                <option value="">Pilih Divisi</option>
                                <option value="semua">Semua Role</option>
                                @foreach ($role as $r)
                                <option value="{{ $r->nama_role }}">{{ $r->nama_role }}</option>
                                @endforeach
                            </select>
                            <button wire:click="$refresh" wire:loading.attr="disabled" wire:target="previousMonth,nextMonth,$refresh,thisMonth,selectedRole" class="btn btn-info w-full">
                                <span wire:loading.remove wire:target="$refresh"><i class="fa-solid fa-magnifying-glass"></i> Cari</span>
                                <span wire:loading wire:target="$refresh" class="loading loading-spinner loading-xs"></span>
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-row justify-between items-center gap-3 mb-4 text-xl font-bold">
                        <button wire:click="previousMonth" wire:loading.attr="disabled" wire:target="previousMonth,nextMonth,$refresh,thisMonth,selectedRole" class="btn btn-circle btn-primary btn-sm sm:btn-md" aria-label="Bulan sebelumnya">
                            <span wire:loading.remove wire:target="previousMonth"><i class="fa-solid fa-chevron-left"></i></span>
                            <span wire:loading wire:target="previousMonth" class="loading loading-spinner loading-xs"></span>
                        </button>
                        <div class="text-center text-base sm:text-xl">
                            {{ \Carbon\Carbon::parse($this->thisMonth)->locale('id')->translatedFormat('F Y') }}
                        </div>
                        <button wire:click="nextMonth" wire:loading.attr="disabled" wire:target="previousMonth,nextMonth,$refresh,thisMonth,selectedRole" class="btn btn-circle btn-primary btn-sm sm:btn-md" aria-label="Bulan berikutnya">
                            <span wire:loading.remove wire:target="nextMonth"><i class="fa-solid fa-chevron-right"></i></span>
                            <span wire:loading wire:target="nextMonth" class="loading loading-spinner loading-xs"></span>
                        </button>
                    </div>

                    <!-- Wrapper untuk overlay loading di atas tabel jadwal -->
                    <div class="relative">
                        <div
                            wire:loading.delay
                            wire:target="previousMonth,nextMonth,$refresh,thisMonth,selectedRole"
                            class="absolute inset-0 bg-base-100/70 backdrop-blur-[1px] flex items-center justify-center z-[60] rounded-lg"
                        >
                            <div class="flex flex-col items-center gap-2">
                                <span class="loading loading-spinner loading-lg text-primary"></span>
                                <span class="text-sm text-base-content">Memuat jadwal...</span>
                            </div>
                        </div>

                        <livewire:jadwal.table :bulan="$thisMonth" :role="$selectedRole" :key="$thisMonth . $selectedRole" />
                    </div>

                    <livewire:jadwal.update />
                </div>
            </div>
        </div>
    </div>
</div>