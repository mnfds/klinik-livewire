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

        <!-- Fontawesome Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-base-300 text-base-content">
        <div class="min-h-screen">
            <!-- NAVBAR  -->
            <div class="navbar bg-primary text-primary-content shadow-sm">
                <div class="navbar-start flex items-center gap-2">
                    <!-- LOGO -->
                    <div class="w-10 h-10 bg-base-300 rounded-md flex items-center justify-center">
                        <img src="{{ asset('assets/aset/logo-no-text.png') }}" alt="Logo" class="w-10 h-10 object-contain" />
                    </div>
                    <span class="font-bold text-lg">Klinik Dokter L</span>
                </div>
                <div class="navbar-center text-center">
                    <div class="font-mono text-md leading-tight" id="liveClock">
                        <div id="clockTime" class="text-2xl countdown">
                            <span style="--value:00;">00</span>:
                            <span style="--value:00;">00</span>:
                            <span style="--value:00;">00</span>
                        </div>
                        <div id="clockDate" class="text-sm text-primary-content/70">Memuat tanggal...</div>
                    </div>
                </div>
                <div class="navbar-end">
                    <label class="swap swap-rotate">
                    <!-- this hidden checkbox controls the state -->
                    <input type="checkbox" class="theme-controller hidden" value="night" />

                    <!-- sun icon -->
                    <svg
                        class="swap-off h-10 w-10 fill-current"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24">
                        <path
                        d="M5.64,17l-.71.71a1,1,0,0,0,0,1.41,1,1,0,0,0,1.41,0l.71-.71A1,1,0,0,0,5.64,17ZM5,12a1,1,0,0,0-1-1H3a1,1,0,0,0,0,2H4A1,1,0,0,0,5,12Zm7-7a1,1,0,0,0,1-1V3a1,1,0,0,0-2,0V4A1,1,0,0,0,12,5ZM5.64,7.05a1,1,0,0,0,.7.29,1,1,0,0,0,.71-.29,1,1,0,0,0,0-1.41l-.71-.71A1,1,0,0,0,4.93,6.34Zm12,.29a1,1,0,0,0,.7-.29l.71-.71a1,1,0,1,0-1.41-1.41L17,5.64a1,1,0,0,0,0,1.41A1,1,0,0,0,17.66,7.34ZM21,11H20a1,1,0,0,0,0,2h1a1,1,0,0,0,0-2Zm-9,8a1,1,0,0,0-1,1v1a1,1,0,0,0,2,0V20A1,1,0,0,0,12,19ZM18.36,17A1,1,0,0,0,17,18.36l.71.71a1,1,0,0,0,1.41,0,1,1,0,0,0,0-1.41ZM12,6.5A5.5,5.5,0,1,0,17.5,12,5.51,5.51,0,0,0,12,6.5Zm0,9A3.5,3.5,0,1,1,15.5,12,3.5,3.5,0,0,1,12,15.5Z" />
                    </svg>

                    <!-- moon icon -->
                    <svg
                        class="swap-on h-10 w-10 fill-current"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24">
                        <path
                        d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05,1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z" />
                    </svg>
                    </label>
                </div>
            </div>

            <!-- Page Content -->
            <main :class="sidebarOpen ? 'sm:ml-48' : 'sm:ml-0'" class="transition-all duration-300 p-4">
                <div class="max-w-screen-xl mx-auto">
                    <livewire:antrian.display />
                </div>
            </main>

        </div>
        @livewireScripts

        <script>
            function updateClock() {
                const now = new Date();

                // Waktu Indonesia Barat (WIB) = UTC+7
                // Waktu Indonesia Barat (WITA) = UTC+8
                const wibOffset = 8 * 60;
                const localOffset = now.getTimezoneOffset(); // dalam menit
                const wibTime = new Date(now.getTime() + (wibOffset + localOffset) * 60000);

                const hours = wibTime.getHours().toString().padStart(2, '0');
                const minutes = wibTime.getMinutes().toString().padStart(2, '0');
                const seconds = wibTime.getSeconds().toString().padStart(2, '0');

                const timeSpans = document.querySelectorAll('#clockTime span');
                timeSpans[0].setAttribute('style', `--value:${hours}`);
                timeSpans[0].setAttribute('aria-label', hours);
                timeSpans[0].textContent = hours;

                timeSpans[1].setAttribute('style', `--value:${minutes}`);
                timeSpans[1].setAttribute('aria-label', minutes);
                timeSpans[1].textContent = minutes;

                timeSpans[2].setAttribute('style', `--value:${seconds}`);
                timeSpans[2].setAttribute('aria-label', seconds);
                timeSpans[2].textContent = seconds;

                // Format tanggal: Kamis, 18 Juli 2025
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                const formattedDate = wibTime.toLocaleDateString('id-ID', options);

                document.getElementById('clockDate').textContent = formattedDate;
            }

            updateClock();
            setInterval(updateClock, 1000);
        </script>

        
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
    </body>
</html>