<!-- Sidebar Component - Refactored for Clarity and Structure -->
<aside>
    <div
        class="fixed top-0 left-0 z-50 h-screen w-64 shadow-md transition-transform sm:translate-x-0"
        :class="sidebarOpen ? '' : '-translate-x-full'">

        <div class="absolute top-0 left-0 h-full w-full px-3 py-4 overflow-y-auto bg-base-100">
            <ul class="space-y-2 font-medium">
                <!-- Brand -->
                <li class="flex items-end">
                    <a href="#" class="flex items-center p-2 font-semibold text-base-content space-x-3">
                        <!-- Kotak Logo -->
                        <div class="w-12 h-12 bg-base-300 rounded-md flex items-center justify-center">
                            <img src="{{ asset('assets/aset/logo-no-text.png') }}" alt="Logo" class="w-8 h-8 object-contain" />
                        </div>

                        <!-- Tulisan -->
                        <span class="text-2xl font-bold tracking-wide font-kapakana text-red-700">
                            Dokter L
                        </span>
                    </a>
                    <span class="text-xs text-base-content/60 pl-2 block">
                        v2.0.1
                        {{-- v(Major).(Minor).(patch)
                        Major = Ada perubahan besar, tidak kompatibel, Hapus fungsi lama
                        Minor = Tambah fitur, tetap kompatibel
                        patch = Perbaikan bug/typo --}}
                    </span>
                </li>
                <hr class="border-base-300">

                <!-- Dashboard -->
                <li>
                    <x-side-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" wire:navigate>
                        <i class="fa-solid fa-house"></i>
                        <span class="ml-3">Dashboard</span>
                    </x-side-link>
                </li>

                <!-- Master Data Section -->
                <li class="pt-2">
                    <span class="text-sm text-base-content">Master Data</span>
                </li>

                @php
                    $masterLinks = [
                        ['name' => 'Staff', 'icon' => 'fa-solid fa-user-nurse', 'url' => 'users.data'],
                        ['name' => 'Role & Akses','icon' => 'fa-solid fa-unlock-keyhole', 'url' => 'role-akses.data'],
                        ['name' => 'Jam Kerja','icon' => 'fa-solid fa-business-time','url' => 'jamkerja.data'],
                        ['name' => 'Poliklinik','icon' => 'fa-solid fa-house-chimney-medical','url' => 'poliklinik.data'],
                        ['name' => 'Produk & Obat','icon' => 'fa-solid fa-pills','url' => 'produk-obat.data'],
                        ['name' => 'Pelayanan','icon' => 'fa-solid fa-hand-holding-medical', 'url' => 'pelayanan.data'],
                        ['name' => 'Paket Bundling','icon' => 'fa-solid fa-gifts',],
                    ];
                @endphp

                @foreach ($masterLinks as $item)
                    @php
                        $href = isset($item['url']) ? route($item['url']) : '#';
                        $active = isset($item['url']) ? request()->routeIs($item['url']) : false;
                    @endphp
                    <li>
                        <x-side-link 
                            href="{{ $href }}" 
                            :active="$active" 
                            wire:navigate
                        >
                            <i class="{{ $item['icon'] }}"></i>
                            <span class="ml-3">{{ $item['name'] }}</span>
                        </x-side-link>
                    </li>
                @endforeach

                <!-- Manajemen Klinik Section -->
                <li class="pt-2">
                    <span class="text-sm text-base-content">Manajemen Klinik</span>
                </li>
                <li>
                    <x-side-link href="#" :active="request()->routeIs('#')" wire:navigate>
                        <i class="fa-solid fa-calendar-days"></i>
                        <span class="ml-3">Jadwal</span>
                    </x-side-link>
                </li>
                <li>
                    <x-side-link href="#" :active="request()->routeIs('#')" wire:navigate>
                        <i class="fa-solid fa-chart-column"></i>
                        <span class="ml-3">Laporan</span>
                    </x-side-link>
                </li>

                <!-- Dropdown Example (reuseable later for antrian/rawat jalan/etc) -->
                <li x-data="{ open: false }">
                    <x-side-link @click.prevent="open = !open" class="cursor-pointer" :active="request()->routeIs('pengajuan.*')">
                        <i class="fa-solid fa-folder-open"></i>
                        <span class="flex-1 ml-3 text-left">Pengajuan</span>
                        <i class="fa-solid fa-chevron-right transition-transform duration-200" :class="open ? 'rotate-90' : ''"></i>
                    </x-side-link>
                    <ul x-show="open" x-collapse x-cloak class="pl-8 space-y-1 py-2">
                        <li><x-side-link href="#" wire:navigate>Lembur</x-side-link></li>
                        <li><x-side-link href="#" wire:navigate>Izin Keluar</x-side-link></li>
                    </ul>
                </li>

                <li x-data="{ open: false }">
                    <x-side-link @click.prevent="open = !open" class="cursor-pointer" :active="request()->routeIs('pengajuan.*')">
                        <i class="fa-solid fa-boxes-stacked"></i>
                        <span class="flex-1 ml-3 text-left">Persediaan</span>
                        <i class="fa-solid fa-chevron-right transition-transform duration-200" :class="open ? 'rotate-90' : ''"></i>
                    </x-side-link>
                    <ul x-show="open" x-collapse x-cloak class="pl-8 space-y-1 py-2">
                        <li><x-side-link href="#" wire:navigate>Barang</x-side-link></li>
                        <li><x-side-link href="#" wire:navigate>Produk & Obat</x-side-link></li>
                    </ul>
                </li>
                <!-- Pelayanan Klinik Section -->
                <li class="pt-2">
                    <span class="text-sm text-base-content">Pelayanan Klinik</span>
                </li>
                <li>
                    <x-side-link href="#" :active="request()->routeIs('#')" wire:navigate>
                        <i class="fa-solid fa-hospital-user"></i>
                        <span class="ml-3">Pasien</span>
                    </x-side-link>
                </li>
                <li x-data="{ open: false }">
                    <x-side-link @click.prevent="open = !open" class="cursor-pointer" :active="request()->routeIs('pengajuan.*')">
                        <i class="fa-solid fa-users"></i>
                        <span class="flex-1 ml-3 text-left">Antrian</span>
                        <i class="fa-solid fa-chevron-right transition-transform duration-200" :class="open ? 'rotate-90' : ''"></i>
                    </x-side-link>
                    <ul x-show="open" x-collapse x-cloak class="pl-8 space-y-1 py-2">
                        <li><x-side-link href="#" wire:navigate>Antrian Registrasi</x-side-link></li>
                        <li><x-side-link href="#" wire:navigate>Antrian Poli</x-side-link></li>
                    </ul>
                </li>
                <li x-data="{ open: false }">
                    <x-side-link @click.prevent="open = !open" class="cursor-pointer" :active="request()->routeIs('pengajuan.*')">
                        <i class="fa-solid fa-notes-medical"></i>
                        <span class="flex-1 ml-3 text-left">Rawat Jalan</span>
                        <i class="fa-solid fa-chevron-right transition-transform duration-200" :class="open ? 'rotate-90' : ''"></i>
                    </x-side-link>
                    <ul x-show="open" x-collapse x-cloak class="pl-8 space-y-1 py-2">
                        <li><x-side-link href="#" wire:navigate>Pasien Terdaftar</x-side-link></li>
                        <li><x-side-link href="#" wire:navigate>Pasien Tindak Lanjut</x-side-link></li>
                        <li><x-side-link href="#" wire:navigate>Pasien Berjanji Temu</x-side-link></li>
                    </ul>
                </li>
                <li x-data="{ open: false }">
                    <x-side-link @click.prevent="open = !open" class="cursor-pointer" :active="request()->routeIs('pengajuan.*')">
                        <i class="fa-solid fa-cash-register"></i>
                        <span class="flex-1 ml-3 text-left">Transaksi</span>
                        <i class="fa-solid fa-chevron-right transition-transform duration-200" :class="open ? 'rotate-90' : ''"></i>
                    </x-side-link>
                    <ul x-show="open" x-collapse x-cloak class="pl-8 space-y-1 py-2">
                        <li><x-side-link href="#" wire:navigate>Klinik</x-side-link></li>
                        <li><x-side-link href="#" wire:navigate>Apotik</x-side-link></li>
                    </ul>
                </li>

                <!-- Repeat dropdowns for Persediaan, Antrian, Rawat Jalan, Transaksi etc... -->
            </ul>
        </div>
    </div>

    <!-- Overlay -->
    <div x-show="sidebarOpen" class="fixed top-0 left-0 w-screen h-screen bg-black opacity-30 z-40" @click="sidebarOpen = false"></div>
</aside>
