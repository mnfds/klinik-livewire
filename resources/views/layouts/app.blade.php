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

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap Icons -->
        {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"> --}}

        <!-- Fontawesome Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

        <!-- Google Font Kapakana  -->
        <link href="https://fonts.googleapis.com/css2?family=Kapakana&display=swap" rel="stylesheet">

        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Cleave JS -->
        <script src="https://cdn.jsdelivr.net/npm/cleave.js@1/dist/cleave.min.js"></script>

        <!-- Choices CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">

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
                    {{ $slot }}
                </div>
            </main>
        </div>

        <script>
            function sidebar() {
                return {
                    sidebarOpen: false,
                    init() {
                        // Jika kamu ingin sidebar terbuka secara default di mobile, bisa pakai window.innerWidth
                        this.sidebarOpen = false
                    }
                }
            }
        </script>


        @livewireScripts

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

        <!-- Choices JS -->
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

        <!-- Init Cleave JS -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                initCleaveRupiah();
            });

            // Fungsi reusable (bisa dipanggil ulang setelah Livewire re-render)
            function initCleaveRupiah() {
                document.querySelectorAll('.input-rupiah').forEach(function (inputEl) {
                    // Hindari duplikasi init
                    if (inputEl._cleave) return;

                    const hiddenEl = inputEl.parentElement.querySelector('.input-rupiah-hidden');

                    const cleave = new Cleave(inputEl, {
                        numeral: true,
                        numeralThousandsGroupStyle: 'thousand',
                        delimiter: '.',
                        numeralDecimalMark: ',',
                    });

                    inputEl._cleave = cleave;

                    if (hiddenEl) {
                        inputEl.addEventListener('input', function () {
                            const raw = cleave.getRawValue();
                            hiddenEl.value = raw;
                            hiddenEl.dispatchEvent(new Event('input'));
                        });
                    }
                });
            }
        </script>

    @stack('scripts')
    </body>
</html>
