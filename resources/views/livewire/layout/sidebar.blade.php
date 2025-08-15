<!-- Sidebar Component - Refactored for Clarity and Structure -->
<aside>
    <div class="fixed top-0 left-0 z-50 h-screen w-64 shadow-md transition-transform transform duration-300"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
        <div class="absolute top-0 left-0 h-full w-full px-3 pb-4 pt-1 overflow-y-auto bg-base-100">
            <ul class="space-y-2 font-medium">
                <!-- Brand -->
                <li class="h-[50px] flex items-center justify-between px-3">
                    <a href="#" class="flex items-center gap-2 text-base-content">
                        <!-- Kotak Logo -->
                        <div class="w-10 h-10 bg-transparent rounded-md flex items-center justify-center">
                            <img src="{{ asset('assets/aset/logo-no-text.png') }}" alt="Logo" class="w-10 h-10 object-contain" />
                        </div>

                        <!-- Tulisan -->
                        <span class="text-sm font-bold tracking-wide text-red-700">
                            Dokter L
                        </span>
                    </a>

                    <!-- Versi -->
                    <span class="text-[10px] text-base-content/60">v2.0.1</span>
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
                        ['name' => 'Dokter', 'icon' => 'fa-solid fa-user-doctor', 'url' => 'dokter.data'],
                        ['name' => 'Role & Akses','icon' => 'fa-solid fa-unlock-keyhole', 'url' => 'role-akses.data'],
                        ['name' => 'Jam Kerja','icon' => 'fa-solid fa-business-time','url' => 'jamkerja.data'],
                        ['name' => 'Poliklinik','icon' => 'fa-solid fa-house-chimney-medical','url' => 'poliklinik.data'],
                        ['name' => 'Produk & Obat','icon' => 'fa-solid fa-pills','url' => 'produk-obat.data'],
                        ['name' => 'Pelayanan','icon' => 'fa-solid fa-hand-holding-medical', 'url' => 'pelayanan.data'],
                        ['name' => 'Paket Bundling','icon' => 'fa-solid fa-gifts', 'url' => 'bundling.data'],
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
                <li>
                    <x-side-link href="{{ route('barang.data') }}" :active="request()->routeIs('barang.data') || request()->routeIs('barang.riwayat')" wire:navigate>
                        <i class="fa-solid fa-boxes-stacked"></i>
                        <span class="ml-3">Persediaan</span>
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
                        <li><x-side-link href="#" wire:navigate>Daftar Ajuan Lembur</x-side-link></li>
                        <li><x-side-link href="#" wire:navigate>Lembur</x-side-link></li>
                        <li><x-side-link href="#" wire:navigate>Daftar Ajuan Izin Keluar</x-side-link></li>
                        <li><x-side-link href="#" wire:navigate>Izin Keluar</x-side-link></li>
                    </ul>
                </li>

                <!-- Pelayanan Klinik Section -->
                <li class="pt-2">
                    <span class="text-sm text-base-content">Pelayanan Klinik</span>
                </li>
                <li>
                    <x-side-link href="{{ route('pasien.data') }}" :active="request()->routeIs('pasien.*')" wire:navigate>
                        <i class="fa-solid fa-hospital-user"></i>
                        <span class="ml-3">Pasien</span>
                    </x-side-link>
                </li>

                <li x-data="{ open: {{ request()->routeIs('antrian.*') || request()->routeIs('tv.*') ? 'true' : 'false' }} }">
                    <x-side-link @click.prevent="open = !open" class="cursor-pointer" :active="request()->routeIs('antrian.*') || request()->routeIs('tv.*')">
                        <i class="fa-solid fa-users"></i>
                        <span class="flex-1 ml-3 text-left">Antrian</span>
                        <i class="fa-solid fa-chevron-right transition-transform duration-200" :class="open ? 'rotate-90' : ''"></i>
                    </x-side-link>

                    <ul x-show="open" x-collapse x-cloak class="pl-8 space-y-1 py-2">
                        <li>
                            <x-side-link href="{{ route('antrian.display') }}" :active="request()->routeIs('antrian.display')" wire:navigate>
                                Ambil Nomor Antrian
                            </x-side-link>
                        </li>
                        <li>
                            <x-side-link href="{{ route('antrian.data') }}" :active="request()->routeIs('antrian.data')" wire:navigate>
                                Kelola Antrian
                            </x-side-link>
                        </li>

                        <!-- Nested TV Antrian -->
                        <li x-data="{ open: {{ request()->routeIs('tv.*') ? 'true' : 'false' }} }">
                            <x-side-link @click.prevent="open = !open" class="cursor-pointer" :active="request()->routeIs('tv.*')">
                                <span class="flex-1 ml-3 text-left">TV Antrian</span>
                                <i class="fa-solid fa-chevron-right transition-transform duration-200" :class="open ? 'rotate-90' : ''"></i>
                            </x-side-link>

                            <ul x-show="open" x-collapse x-cloak class="pl-8 space-y-1 py-2">
                                <li>
                                    <x-side-link :active="request()->routeIs('tv.pendaftaran')" wire:navigate>
                                        TV Pendaftaran
                                    </x-side-link>
                                </li>
                                <li>
                                    <x-side-link :active="request()->routeIs('tv.poli')" wire:navigate>
                                        TV Poliklinik
                                    </x-side-link>
                                </li>
                                <li>
                                    <x-side-link :active="request()->routeIs('tv.apotek')" wire:navigate>
                                        TV Apotek
                                    </x-side-link>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <li x-data="{ open: {{ request()->routeIs('pendaftaran.*') || request()->routeIs('kajian.*') || request()->routeIs('rekam-medis-pasien.*') ? 'true' : 'false' }} }">
                    <x-side-link @click.prevent="open = !open" 
                        class="cursor-pointer" 
                        :active="request()->routeIs('pendaftaran.*', 'kajian.*', 'rekam-medis-pasien.*')">
                        <i class="fa-solid fa-notes-medical"></i>
                        <span class="flex-1 ml-3 text-left">Rawat Jalan</span>
                        <i class="fa-solid fa-chevron-right transition-transform duration-200" :class="open ? 'rotate-90' : ''"></i>
                    </x-side-link>
                    <ul x-show="open" x-collapse x-cloak class="pl-8 space-y-1 py-2">
                        <li>
                            <x-side-link href="{{ route('pendaftaran.data') }}" 
                                :active="request()->routeIs('pendaftaran.*', 'kajian.*', 'rekam-medis-pasien.*')"  
                                wire:navigate>
                                Pasien Terdaftar
                            </x-side-link>
                        </li>
                        <li>
                            <x-side-link href="#" wire:navigate>Pasien Tindak Lanjut</x-side-link>
                        </li>
                        <li>
                            <x-side-link href="#" wire:navigate>Pasien Berjanji Temu</x-side-link>
                        </li>
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
                <li class="pt-2">
                    <span class="text-sm text-base-content">Tentang Aplikasi</span>
                </li>
                <li>
                    <x-side-link href="#" :active="request()->routeIs('#')" wire:navigate>
                        <i class="fa-solid fa-circle-info"></i>
                        <span class="ml-3">Update Terbaru</span>
                    </x-side-link>
                </li>
                <li>
                    <x-side-link href="#" :active="request()->routeIs('#')" wire:navigate>
                        <i class="fa-solid fa-book"></i>
                        <span class="ml-3">Panduan Pengguna</span>
                    </x-side-link>
                </li>
                <li>
                    <x-side-link href="#" :active="request()->routeIs('#')" wire:navigate>
                        <i class="fa-solid fa-circle-question"></i>
                        <span class="ml-3">Pertanyaan Umum</span>
                    </x-side-link>
                </li>

                <!-- Repeat dropdowns for Persediaan, Antrian, Rawat Jalan, Transaksi etc... -->
            </ul>
        </div>
    </div>

    <!-- ✅ Tombol close sidebar di sebelah kanan sidebar (desktop) -->
    <button 
        x-show="sidebarOpen"
        class="fixed top-2 left-[260px] z-50 text-base-content hover:text-error transition"
        @click="closeSidebar()"
        >
        <i class="fa-solid fa-xmark text-xl"></i>
    </button>


    <!-- Overlay -->
    <div
        x-show="sidebarOpen && isMobile"
        x-transition
        class="fixed top-2 left-[260px] z-50 text-base-content hover:text-error transition sm:block"
        @click="closeSidebar()">
    </div>
</aside>
