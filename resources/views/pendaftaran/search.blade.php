<!DOCTYPE html>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script>
    const savedTheme = localStorage.getItem('theme') || 'emerald';
    document.documentElement.setAttribute('data-theme', savedTheme);
</script>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data
      x-init="
        const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'night' : 'emerald');
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

        <style>
                /* Default (emerald / light theme) */
                /* html[data-theme='emerald'] body {
                    background-color: #e2eefd;
                    background-image: url("data:image/svg+xml,%3Csvg width='16' height='16' viewBox='0 0 16 16' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 0h16v2h-6v6h6v8H8v-6H2v6H0V0zm4 4h2v2H4V4zm8 8h2v2h-2v-2zm-8 0h2v2H4v-2zm8-8h2v2h-2V4z' fill='%23d6dee7' fill-opacity='0.4' fill-rule='evenodd'/%3E%3C/svg%3E");
                } */

                /* Dark mode (night theme) */
                /* html[data-theme='night'] body {
                    background-color: #2d2e30;
                    background-image: url("data:image/svg+xml,%3Csvg width='16' height='16' viewBox='0 0 16 16' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 0h16v2h-6v6h6v8H8v-6H2v6H0V0zm4 4h2v2H4V4zm8 8h2v2h-2v-2zm-8 0h2v2H4v-2zm8-8h2v2h-2V4z' fill='%2331363d' fill-opacity='0.4' fill-rule='evenodd'/%3E%3C/svg%3E");
                } */
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>

    <body class="font-sans antialiased bg-base-200 text-base-content">
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
                                                Lanjutkan Pendaftaran
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
                            {{-- MAIN --}}
                            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                                <div class="bg-base-100 shadow rounded-box">
                                    <div class="p-6">
                                        <livewire:pendaftaran.search :id="request('id')" />
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
            window.$ = window.jQuery;

            function formatPasien(pasien) {
                if (!pasien.id) return pasien.text;
                let noReg = pasien.no_register || '-';
                return $(` 
                    <div class="flex items-center gap-2">
                        <div class="font-medium">${pasien.text}</div>
                        <div class="text-xs text-gray-500">No. Reg: ${noReg}</div>
                    </div>
                `);
            }

            function initSelect2() {
                let $select = $('#pasien-select');
                if ($select.length === 0) return;
                if (typeof $.fn.select2 === 'undefined') return;

                if ($select.hasClass("select2-hidden-accessible")) {
                    $select.select2('destroy');
                }

                $select.select2({
                    width: '100%',
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
            }

            // Langsung init tanpa tunggu Livewire
            $(document).ready(function () {
                initSelect2();
            });
        </script>

    </body>
</html>
