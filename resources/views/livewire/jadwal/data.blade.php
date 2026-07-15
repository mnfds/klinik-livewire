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
                        <div class="w-full md:w-auto grid grid-cols-2 gap-[2px]">
                            <button onclick="document.getElementById('uploadModal').showModal()" class="btn btn-primary w-full"><i class="fa-solid fa-upload"></i> Upload Jadwal</button>
                            <button onclick="document.getElementById('downloadModal').showModal()" class="btn btn-success w-full"><i class="fa-solid fa-download"></i> Download Jadwal</button>
                        </div>
                        <div class="w-full md:w-auto grid grid-cols-3 gap-[2px] mt-2 md:mt-0">
                            <input type="month" wire:model="thisMonth" class="input input-bordered w-full">
                            <select wire:model="selectedRole" class="select select-bordered w-full">
                                <option value="">Pilih Divisi</option>
                                @foreach ($role as $r)
                                <option value="{{ $r->nama_role }}">{{ $r->nama_role }}</option>
                                @endforeach
                            </select>
                            <button wire:click="$refresh" class="btn btn-info w-full">
                                <i class="fa-solid fa-magnifying-glass"></i> Cari
                            </button>
                        </div>
                    </div>
                    <livewire:jadwal.table :bulan="$thisMonth" :role="$selectedRole" :key="$thisMonth . $selectedRole" />
                    <livewire:jadwal.update />
                </div>
            </div>
        </div>
    </div>
</div>