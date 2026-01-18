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
                @if (
                        Gate::allows('akses', 'Staff Data') ||
                        Gate::allows('akses', 'Dokter Data') ||
                        Gate::allows('akses', 'Jam Kerja Data') ||
                        Gate::allows('akses', 'Poliklinik Data') ||
                        Gate::allows('akses', 'Pelayanan Data') ||
                        Gate::allows('akses', 'Paket Bundling Data') ||
                        Gate::allows('akses', 'Produk & Obat Data')
                    )
                    <li class="pt-2">
                        <span class="text-sm text-base-content">Master Data</span>
                    </li>
                @endif

                @php
                    use App\Models\ProdukDanObat;
                    use Illuminate\Support\Carbon;
                    use Illuminate\Support\Facades\Gate;

                    $now = Carbon::today();

                    $produkAdaReminder = ProdukDanObat::query()
                        ->whereNotNull('expired_at')
                        ->whereNotNull('reminder')
                        ->get()
                        ->contains(function ($row) use ($now) {
                            $expired      = Carbon::parse($row->expired_at)->startOfMonth();
                            $reminderDate = Carbon::parse($row->expired_at)
                                ->subMonths($row->reminder)
                                ->startOfMonth();

                            return $now->greaterThanOrEqualTo($reminderDate);
                        });
                    $isSuperAdmin = auth()->user()->role()->where('id', 1)->exists();

                    $masterLinks = [
                        [
                            'name' => 'Staff',
                            'icon' => 'fa-solid fa-user-nurse',
                            'url'  => 'users.data',
                            'hak_akses' => 'Staff Data',
                        ],
                        [
                            'name' => 'Dokter',
                            'icon' => 'fa-solid fa-user-doctor',
                            'url'  => 'dokter.data',
                            'hak_akses' => 'Dokter Data',
                        ],
                        [
                            'name' => 'Role & Akses',
                            'icon' => 'fa-solid fa-unlock-keyhole',
                            'url'  => 'role-akses.data',
                            'always' => $isSuperAdmin,
                        ],
                        [
                            'name' => 'Jam Kerja',
                            'icon' => 'fa-solid fa-business-time',
                            'url'  => 'jamkerja.data',
                            'hak_akses' => 'Jam Kerja Data',
                        ],
                        [
                            'name' => 'Poliklinik',
                            'icon' => 'fa-solid fa-house-chimney-medical',
                            'url'  => 'poliklinik.data',
                            'hak_akses' => 'Poliklinik Data',
                        ],
                        [
                            'name' => 'Produk & Obat',
                            'icon' => 'fa-solid fa-pills',
                            'url'  => 'produk-obat.data',
                            'warning' => $produkAdaReminder,
                            'hak_akses' => 'Produk & Obat Data',
                        ],
                        [
                            'name' => 'Pelayanan',
                            'icon' => 'fa-solid fa-hand-holding-medical',
                            'url'  => 'pelayanan.data',
                            'hak_akses' => 'Pelayanan Data',
                        ],
                        [
                            'name' => 'Paket Bundling',
                            'icon' => 'fa-solid fa-gifts',
                            'url'  => 'bundling.data',
                            'hak_akses' => 'Paket Bundling Data',
                        ],
                    ];
                @endphp

                @foreach ($masterLinks as $item)
                    @php
                        $href   = isset($item['url']) ? route($item['url']) : '#';
                        $active = isset($item['url']) ? request()->routeIs($item['url']) : false;

                        $always = $item['always'] ?? false;
                        $akses  = $item['hak_akses'] ?? null;

                        // LOGIC VISIBILITAS
                        $bolehTampil = $always || ($akses && Gate::allows('akses', $akses));
                    @endphp

                    @if ($bolehTampil)
                        <li>
                            <x-side-link href="{{ $href }}" :active="$active" wire:navigate>
                                <i class="{{ $item['icon'] }}"></i>
                                <span class="ml-3">{{ $item['name'] }}</span>

                                {{-- ikon warning --}}
                                @if (!empty($item['warning']))
                                    <i class="fa-solid fa-triangle-exclamation ml-auto rounded-full
                                    text-error p-1 bg-accent-content"></i>
                                @endif
                            </x-side-link>
                        </li>
                    @endif
                @endforeach

                <!-- Manajemen Klinik Section -->
                    
                @if (
                    Gate::allows('akses', 'Jadwal') ||
                    Gate::allows('akses', 'Laporan') ||
                    Gate::allows('akses', 'Persediaan') ||
                    Gate::allows('akses', 'Pengajuan')
                )
                <li class="pt-2">
                    <span class="text-sm text-base-content">Manajemen Klinik</span>
                </li>
                @endif
                @can('akses', 'Jadwal')                    
                <li>
                    <x-side-link href="#" :active="request()->routeIs('#')" wire:navigate>
                        <i class="fa-solid fa-calendar-days"></i>
                        <span class="ml-3">Jadwal</span>
                    </x-side-link>
                </li>
                @endcan

                @if (Gate::allows('akses','Laporan') || Gate::allows('akses','Laporan'))
                    <li x-data="{ open: {{ request()->routeIs('aruskas.*') || request()->routeIs('kunjungan.*') || request()->routeIs('kinerja.*') ? 'true' : 'false' }} }">
                        <x-side-link @click.prevent="open = !open" class="cursor-pointer" :active="request()->routeIs('aruskas.*', 'kunjungan.*', 'kinerja.*')">
                            <i class="fa-solid fa-chart-column"></i>
                            <span class="flex-1 ml-3 text-left">Laporan</span>
                            <i class="fa-solid fa-chevron-right transition-transform duration-200" :class="open ? 'rotate-90' : ''"></i>
                        </x-side-link>
                        <ul x-show="open" x-collapse x-cloak class="pl-8 space-y-1 py-2">
                            @can('akses', 'Laporan')
                            <li>
                                <x-side-link href="{{ route('aruskas.data') }}" :active="request()->routeIs('aruskas.*')" wire:navigate>Arus Kas</x-side-link>
                            </li>
                            @endcan
                            @can('akses', 'Laporan')
                            <li>
                                <x-side-link href="{{ route('kunjungan.data') }}" :active="request()->routeIs('kunjungan.*')" wire:navigate>Kunjungan Pasien</x-side-link>
                            </li>
                            @endcan
                            @can('akses', 'Laporan')
                            <li>
                                <x-side-link href="{{ route('kinerja.data') }}" :active="request()->routeIs('kinerja.*')" wire:navigate>Kinerja Utama</x-side-link>
                            </li>
                            @endcan
                        </ul>
                    </li>
                @endif 

                @php
                    use App\Models\BahanBaku;

                    $today = Carbon::today();

                    // Cek bahan baku yang sudah masuk periode reminder
                    $bahanHampirExpired = BahanBaku::query()
                        ->whereNotNull('expired_at')
                        ->whereNotNull('reminder')
                        ->get()
                        ->filter(function ($row) use ($today) {
                            $reminderDate = Carbon::parse($row->expired_at)->subMonths($row->reminder)->startOfMonth();
                            return $today->greaterThanOrEqualTo($reminderDate);
                        });

                    $jumlahReminder = $bahanHampirExpired->count();
                @endphp

                @if (
                        Gate::allows('akses','Persediaan Barang') ||
                        Gate::allows('akses','Persediaan Bahan Baku')
                    )
                    <li 
                        x-data="{ open: {{ request()->routeIs('barang.*') || request()->routeIs('bahanbaku.*') ? 'true' : 'false' }} }"
                        >
                        <x-side-link 
                            @click.prevent="open = !open" 
                            class="cursor-pointer" 
                            :active="request()->routeIs('barang.*', 'bahanbaku.*')"
                            >
                            <i class="fa-solid fa-boxes-stacked"></i>
                            <span class="flex-1 ml-3 text-left">Persediaan</span>

                            {{-- ✅ Badge Reminder: tampil di menu utama hanya jika belum dibuka --}}
                            <template x-if="!open && {{ $jumlahReminder }} > 0">
                                <span class="bg-accent-content text-warning p-1 py-0.5 rounded-full flex items-center gap-1">
                                    <i class="fa-solid fa-bell"></i>
                                </span>
                            </template>

                            <i class="fa-solid fa-chevron-right transition-transform duration-200" 
                            :class="open ? 'rotate-90' : ''"></i>
                        </x-side-link>

                        <ul x-show="open" x-collapse x-cloak class="pl-8 space-y-1 py-2">
                            @can('akses', 'Persediaan Barang')
                            <li>
                                <x-side-link href="{{ route('barang.data') }}" 
                                    :active="request()->routeIs('barang.*')"  
                                    wire:navigate>
                                    Barang
                                </x-side-link>
                            </li>
                            @endcan
                            @can('akses', 'Persediaan Bahan Baku')
                            <li>
                                <x-side-link href="{{ route('bahanbaku.data') }}" 
                                    :active="request()->routeIs('bahanbaku.*')"  
                                    wire:navigate>
                                    Bahan Baku

                                    @if($jumlahReminder > 0)
                                        <span class="ml-auto rounded-full text-warning bg-accent-content">
                                            <i class="fa-solid fa-bell ml-auto rounded-full text-warning p-1 bg-accent-content"></i>
                                        </span>
                                    @endif

                                </x-side-link>
                            </li>
                            @endcan
                        </ul>
                    </li>
                @endif
                 
                @if (Gate::allows('akses','Pengajuan Pengeluaran') || Gate::allows('akses','Pengajuan Pengeluaran') || Gate::allows('akses','Pengajuan Pengeluaran'))   
                <li x-data="{ open: {{ request()->routeIs('uangkeluar.*') || request()->routeIs('izinkeluar.*') || request()->routeIs('uangkeluar.*') ? 'true' : 'false' }} }">
                    <x-side-link @click.prevent="open = !open" class="cursor-pointer" :active="request()->routeIs('pengajuan.*') || request()->routeIs('uangkeluar.*')">
                        <i class="fa-solid fa-folder-open"></i>
                        <span class="flex-1 ml-3 text-left">Pengajuan</span>
                        <i class="fa-solid fa-chevron-right transition-transform duration-200" :class="open ? 'rotate-90' : ''"></i>
                    </x-side-link>
                    <ul x-show="open" x-collapse x-cloak class="pl-8 space-y-1 py-2">
                        <li><x-side-link href="#" wire:navigate>Lembur</x-side-link></li>
                        @can('akses', 'Pengajuan Pengeluaran')
                        <li>
                            <x-side-link href="{{ route('izinkeluar.data') }}" :active="request()->routeIs('izinkeluar.*')" wire:navigate>Izin keluar</x-side-link>
                        </li>
                        @endcan
                        @can('akses', 'Pengajuan Pengeluaran')
                        <li>
                            <x-side-link href="{{ route('uangkeluar.data') }}" :active="request()->routeIs('uangkeluar.*')" wire:navigate>Pengeluaran</x-side-link>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endif

                <!-- Pelayanan Klinik Section -->
                @if (
                    Gate::allows('akses', 'Pasien') ||
                    Gate::allows('akses', 'Antrian') ||
                    Gate::allows('akses', 'Rawat Jalan') ||
                    Gate::allows('akses', 'Transaksi') ||
                    Gate::allows('akses', 'Resep Obat')
                )
                <li class="pt-2">
                    <span class="text-sm text-base-content">Pelayanan Klinik</span>
                </li>
                @endif
                @can('akses', 'Pasien')
                <li>
                    <x-side-link href="{{ route('pasien.data') }}" :active="request()->routeIs('pasien.*')" wire:navigate>
                        <i class="fa-solid fa-hospital-user"></i>
                        <span class="ml-3">Pasien</span>
                    </x-side-link>
                </li>
                @endcan
                @if (
                    Gate::allows('akses','Ambil Nomor') ||
                    Gate::allows('akses','Kelola Nomor')
                    )
                    <li x-data="{ open: {{ request()->routeIs('antrian.*') || request()->routeIs('tv.*') ? 'true' : 'false' }} }">
                        <x-side-link @click.prevent="open = !open" class="cursor-pointer" :active="request()->routeIs('antrian.*') || request()->routeIs('tv.*')">
                            <i class="fa-solid fa-users"></i>
                            <span class="flex-1 ml-3 text-left">Antrian</span>
                            <i class="fa-solid fa-chevron-right transition-transform duration-200" :class="open ? 'rotate-90' : ''"></i>
                        </x-side-link>
    
                        <ul x-show="open" x-collapse x-cloak class="pl-8 space-y-1 py-2">
                            @can('akses', 'Ambil Nomor')                            
                            <li>
                                <x-side-link href="{{ route('antrian.display') }}" :active="request()->routeIs('antrian.display')" wire:navigate>
                                    Ambil Nomor Antrian
                                </x-side-link>
                            </li>
                            @endcan
                            @can('akses', 'Kelola Antrian')                            
                            <li>
                                <x-side-link href="{{ route('antrian.data') }}" :active="request()->routeIs('antrian.data')" wire:navigate>
                                    Kelola Antrian
                                </x-side-link>
                            </li>
                            @endcan
                            @if (
                                    Gate::allows('akses','Display Registrasi') ||
                                    Gate::allows('akses','Display Poliklinik') ||
                                    Gate::allows('akses','Display Apotek')
                                )
                            <!-- Nested TV Antrian -->
                            <li x-data="{ open: {{ request()->routeIs('tv.*') ? 'true' : 'false' }} }">
                                <x-side-link @click.prevent="open = !open" class="cursor-pointer" :active="request()->routeIs('tv.*')">
                                    <span class="flex-1 ml-3 text-left">TV Antrian</span>
                                    <i class="fa-solid fa-chevron-right transition-transform duration-200" :class="open ? 'rotate-90' : ''"></i>
                                </x-side-link>
    
                                <ul x-show="open" x-collapse x-cloak class="pl-8 space-y-1 py-2">
                                    @can('akses', 'Display Registrasi')
                                    <li>
                                        <x-side-link :active="request()->routeIs('tv.pendaftaran')" wire:navigate>
                                            TV Pendaftaran
                                        </x-side-link>
                                    </li>
                                    @endcan
                                    @can('akses', 'Display Poliklinik')
                                    <li>
                                        <x-side-link :active="request()->routeIs('tv.poli')" wire:navigate>
                                            TV Poliklinik
                                        </x-side-link>
                                    </li>
                                    @endcan
                                    @can('akses', 'Display Apotek')
                                    <li>
                                        <x-side-link :active="request()->routeIs('tv.apotek')" wire:navigate>
                                            TV Apotek
                                        </x-side-link>
                                    </li>
                                    @endcan
                                </ul>
                            </li>
                            @endif
                        </ul>
                    </li>
                @endif

                @php
                    use App\Models\Reservasi;
                    // use Illuminate\Support\Carbon;

                    $hariIni = Carbon::today();
                    $duaHariKedepan = Carbon::today()->addDays(1);

                    $reservasiCount = Reservasi::whereDate('tanggal_reservasi', '<=', $duaHariKedepan)
                        ->whereIn('status', ['belum bayar', 'belum lunas', 'selesai', 'batal'])
                        ->count();
                @endphp

                @if (
                    Gate::allows('akses','Pendaftaran') ||
                    Gate::allows('akses','Reservasi') ||
                    Gate::allows('akses','Tindak Lanjut')
                    )                    
                    <li x-data="{ open: {{ request()->routeIs('pendaftaran.*') || request()->routeIs('kajian.*') || request()->routeIs('rekam-medis-pasien.*') || request()->routeIs('reservasi.*') ? 'true' : 'false' }} }">
                        <x-side-link 
                            @click.prevent="open = !open" 
                            class="cursor-pointer relative" 
                            :active="request()->routeIs('pendaftaran.*', 'kajian.*', 'rekam-medis-pasien.*', 'reservasi.*')"
                            >
                            <i class="fa-solid fa-notes-medical"></i>
                            <span class="flex-1 ml-3 text-left">Rawat Jalan</span>
    
                            <template x-if="!open && {{ $reservasiCount }} > 0">
                                <span class="bg-accent-content text-warning p-1 py-0.5 rounded-full flex items-center gap-1">
                                    <i class="fa-solid fa-bell"></i>
                                </span>
                            </template>
    
                            <i class="fa-solid fa-chevron-right transition-transform duration-200" :class="open ? 'rotate-90' : ''"></i>
                        </x-side-link>
    
                        {{-- Submenu --}}
                        <ul x-show="open" x-collapse x-cloak class="pl-8 space-y-1 py-2">
                            @can('akses', 'Pendaftaran')                            
                            <li>
                                <x-side-link href="{{ route('pendaftaran.data') }}" 
                                    :active="request()->routeIs('pendaftaran.*', 'kajian.*', 'rekam-medis-pasien.*')"  
                                    wire:navigate>
                                    Pendaftaran
                                </x-side-link>
                            </li>
                            @endcan
                            @can('akses', 'Reservasi')                            
                            <li>
                                <x-side-link href="{{ route('reservasi.data') }}" 
                                    :active="request()->routeIs('reservasi.*')"  
                                    wire:navigate>
                                    Reservasi
                                    @if ($reservasiCount > 0)
                                        <span class="ml-auto rounded-full text-warning bg-accent-content">
                                            <i class="fa-solid fa-bell ml-auto rounded-full text-warning p-1 bg-accent-content"></i>
                                        </span>
                                    @endif
                                </x-side-link>
                            @endcan
                            </li>
                            @can('akses', 'Tindak Lanjut')                            
                            <li>
                                <x-side-link href="{{ route('tindaklanjut.data') }}" 
                                    :active="request()->routeIs('tindaklanjut*')"  
                                    wire:navigate>
                                    Tindak Lanjut
                                </x-side-link>
                            </li>
                            @endcan
                        </ul>
                    </li>
                @endif                
                @if (
                    Gate::allows('akses','Transaksi Klinik') ||
                    Gate::allows('akses','Transaksi Apotik')
                    )
                <li x-data="{ open: {{ request()->routeIs('apotik.*') || request()->routeIs('transaksi.*') ? 'true' : 'false' }} }">
                    <x-side-link @click.prevent="open = !open" class="cursor-pointer" :active="request()->routeIs('apotik.*', 'transaksi.*')">
                        <i class="fa-solid fa-cash-register"></i>
                        <span class="flex-1 ml-3 text-left">Transaksi</span>
                        <i class="fa-solid fa-chevron-right transition-transform duration-200" :class="open ? 'rotate-90' : ''"></i>
                    </x-side-link>
                    <ul x-show="open" x-collapse x-cloak class="pl-8 space-y-1 py-2">
                        @can('akses', 'Transaksi Klinik')
                        <li>
                            <x-side-link href="{{ route('transaksi.kasir') }}" :active="request()->routeIs('transaksi.kasir*')" wire:navigate>Klinik</x-side-link>
                        </li>
                        @endcan
                        @can('akses', 'Transaksi Apotik')
                        <li>
                            <x-side-link href="{{ route('apotik.kasir') }}" :active="request()->routeIs('apotik.*')" wire:navigate>Apotik</x-side-link>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endif                    
                @can('akses', 'Resep Obat')
                <li>
                    <x-side-link href="{{ route('resep.data') }}" :active="request()->routeIs('resep.*')" wire:navigate>
                        <i class="fa-solid fa-prescription"></i>
                        <span class="ml-3">Resep Obat</span>
                    </x-side-link>
                </li>
                @endcan


                <li class="pt-2">
                    <span class="text-sm text-base-content">Tentang Aplikasi</span>
                </li>
                @if (
                    Gate::allows('akses','Praktisi Satu Sehat') ||
                    Gate::allows('akses','Lokasi Satu Sehat') ||
                    Gate::allows('akses','Organisasi Satu Sehat')
                    )                    
                <li x-data="{ open: {{ request()->routeIs(
                        'satusehat.praktisi*',
                        'satusehat.lokasi*',
                        'satusehat.organisasi*'
                    ) ? 'true' : 'false' }} }">

                    <x-side-link 
                        @click.prevent="open = !open" 
                        class="cursor-pointer"
                        :active="request()->routeIs(
                            'satusehat.praktisi*',
                            'satusehat.lokasi*',
                            'satusehat.organisasi*'
                        )">

                        <i class="fa-solid fa-book-medical"></i>
                        <span class="flex-1 ml-3 text-left">Satu Sehat</span>
                        <i class="fa-solid fa-chevron-right transition-transform duration-200" 
                            :class="open ? 'rotate-90' : ''">
                        </i>
                    </x-side-link>

                    <ul x-show="open" x-collapse x-cloak class="pl-8 space-y-1 py-2">
                        @can('akses', 'Praktisi Satu Sehat')
                        <li>
                            <x-side-link href="{{ route('satusehat.praktisi.data') }}"
                                :active="request()->routeIs('satusehat.praktisi*')" 
                                wire:navigate>
                                Praktisi
                            </x-side-link>
                        </li>
                        @endcan
                        @can('akses', 'Lokasi Satu Sehat')
                        <li>
                            <x-side-link href="{{ route('satusehat.lokasi.data') }}"
                                :active="request()->routeIs('satusehat.lokasi*')" 
                                wire:navigate>
                                Lokasi
                            </x-side-link>
                        </li>
                        @endcan
                        @can('akses', 'Organisasi Satu Sehat')
                        <li>
                            <x-side-link href="{{ route('satusehat.organisasi.data') }}"
                                :active="request()->routeIs('satusehat.organisasi*')" 
                                wire:navigate>
                                Organisasi
                            </x-side-link>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endif            
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
