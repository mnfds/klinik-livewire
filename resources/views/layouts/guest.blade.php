<!DOCTYPE html>
<script>
    const savedTheme = localStorage.getItem('theme') || 'emerald';
    document.documentElement.setAttribute('data-theme', savedTheme);
</script>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data x-init="const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'night' : 'emerald'); document.documentElement.setAttribute('data-theme', theme);">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Kapakana&display=swap" rel="stylesheet">

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-base-200 text-base-content">
    <div class="min-h-screen flex flex-col sm:justify-center items-center px-3 pt-6 sm:pt-0">
        <!-- Logo -->
        <div>
            <a href="/" wire:navigate>
                <x-application-logo class="w-20 h-auto" />
            </a>
        </div>

        <!-- Card -->
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-base-100 shadow-md rounded-box">
            {{ $slot }}
        </div>
    </div>
    @if (session('toast'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: @json(session('toast.type')),
                    title: @json(session('toast.message')),
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            });
        </script>
    @endif
</body>
</html>
