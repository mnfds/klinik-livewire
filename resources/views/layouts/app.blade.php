<!DOCTYPE html>
<script>
    const savedTheme = localStorage.getItem('theme') || 'pastel';
    document.documentElement.setAttribute('data-theme', savedTheme);
</script>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data
      x-init="
        const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'night' : 'pastel');
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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-base-300 text-base-content">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen">
            <livewire:layout.navigation />
            <livewire:layout.sidebar />

            <!-- Page Content -->
            <main class="sm:ml-64 p-2">
                {{ $slot }}
            </main>
        </div>
        @livewireScripts
    </body>
</html>
