<!DOCTYPE html>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script>
    const savedTheme = localStorage.getItem('theme') || 'acid';
    document.documentElement.setAttribute('data-theme', savedTheme);
</script>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data
      x-init="
        const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'night' : 'acid');
        document.documentElement.setAttribute('data-theme', theme);
      "
>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Fontawesome Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- select 2 -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>

    <body class="font-sans antialiased bg-base-300 text-base-content">
        <div x-data="sidebar()" x-init="init()" class="min-h-screen">
            <livewire:layout.navigation />
            <livewire:layout.sidebar />

            <!-- Page Content -->
            <main :class="sidebarOpen ? 'sm:ml-48' : 'sm:ml-0'" class="transition-all duration-300 p-4">
                <div class="max-w-screen-xl mx-auto">
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
                                            <a href="{{ route('pendaftaran.search') }}" class="inline-flex items-center gap-1">
                                                <i class="fa-regular fa-folder-open"></i>
                                                Daftarkan Pasien
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <!-- Page Title -->
                            <div class="max-w-full mx-auto sm:px-6 lg:px-8">
                                <h1 class="text-2xl font-bold text-base-content">
                                    <i class="fa-solid fa-layer-group"></i>
                                    Cari Pasien
                                </h1>
                            </div>

                            <!-- Main Content -->
                            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                                <div class="bg-base-100 shadow rounded-box">
                                    <div class="p-6 space-y-6">
                                        @if ($antrian)
                                        <ul class="menu bg-base-200 rounded-box w-56">
                                            <li>Nomor Antrian : {{ $antrian->kode}}-{{ $antrian->nomor_antrian }}</li>
                                            <li>Poli Dituju   : {{ $antrian->poli->nama_poli }}</li>
                                        </ul>
                                        @endif
                                        <!-- Form Group -->
                                        <form id="pasien-form" method="GET" action="{{ route('pendaftaran.create') }}" class="form-control w-full max-w-4xl mx-auto space-y-4">
                                            <!-- Label dan Select -->
                                            @if ($antrian)
                                                <input type="hidden" name="antrian_id" value="{{ $antrian->id }}">
                                            @endif
                                            <label for="pasien-select" class="label">
                                                <span class="label-text text-base font-semibold">Cari Pasien</span>
                                            </label>
                                            <select id="pasien-select" name="pasien_id" class="select select-bordered w-full" required>
                                                <option value="">-- Pilih Pasien --</option>
                                            </select>

                                            <!-- Tombol Aksi -->
                                            <div class="flex gap-4 pt-2">
                                                <button type="submit" class="btn btn-primary">Daftarkan Pasien</button>
                                                <a href="{{ route('pasien.create') }}" class="btn btn-success">
                                                    Tambah Pasien
                                                </a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>                  
                        </div>
                    </div>
                </div>
            </main>
        </div>

        @livewireScripts

        <!-- SIDEBAR -->
        <script>
            function sidebar() {
                return {
                    sidebarOpen: false,
                    isMobile: window.innerWidth < 640,

                    init() {
                        this.isMobile = window.innerWidth < 640;
                        this.sidebarOpen = localStorage.getItem('sidebarOpen') === 'true';

                        // Optional: Update isMobile on resize
                        window.addEventListener('resize', () => {
                            this.isMobile = window.innerWidth < 640;
                        });
                    },

                    toggleSidebar() {
                        this.sidebarOpen = !this.sidebarOpen;
                        localStorage.setItem('sidebarOpen', this.sidebarOpen);
                    },

                    closeSidebar() {
                        this.sidebarOpen = false;
                        localStorage.setItem('sidebarOpen', 'false');
                    }
                }
            }
        </script>
        
        <!-- Toast -->
        <script>
            Livewire.on('toast', (data) => {
                const toast = Array.isArray(data) ? data[0] : data;
                // console.log("Toast fixed:", toast);

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: toast.type ?? 'info',
                    title: toast.message ?? '(Pesan tidak tersedia)',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            });
        </script>
        @if (session('toast'))
            <script>
                window.addEventListener('DOMContentLoaded', () => {
                    Livewire.dispatch('toast', @json(session('toast')));
                });
            </script>
        @endif

        <!-- Ajax Pasien -->
        <script>
            function formatPasien(pasien) {
                if (!pasien.id) return pasien.text;

                let foto = pasien.foto || '{{ asset("default.png") }}';
                let noReg = pasien.no_register || '-';

                return $(`
                    <div class="flex items-center gap-2">
                        <div class="font-medium">${pasien.text}</div>
                        <div class="text-xs text-gray-500">No. Reg: ${noReg}</div>
                    </div>
                `);
            }

            $(document).ready(function () {
                $('#pasien-select').select2({
                    placeholder: 'Cari berdasarkan nama / no register...',
                    allowClear: true,
                    ajax: {
                        url: '{{ route("api.pasien.search") }}',
                        dataType: 'json',
                        delay: 250,
                        data: params => ({ q: params.term }),
                        processResults: data => ({ results: data }),
                        cache: true
                    },
                    templateResult: formatPasien,
                    templateSelection: formatPasien,
                    escapeMarkup: m => m
                });

                $('#pasien-select').on('change', function () {
                    Livewire.emit('setPasien', $(this).val());
                });
            });
        </script>

    </body>
</html>
