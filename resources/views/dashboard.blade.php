<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="pt-1 pb-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Breadcrumbs (hanya muncul di layar lg ke atas) -->
            <div class="hidden lg:flex justify-end px-4">
                <div class="breadcrumbs text-sm">
                    <ul>
                        <li>
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1">
                                <i class="fa-regular fa-folder-open"></i>
                                Dashboard
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Page Title -->
            <div class="max-w-full mx-auto sm:px-6 lg:px-8">
                <h1 class="text-2xl font-bold text-base-content">
                    <i class="fa-solid fa-layer-group"></i>
                    Dashboard
                </h1>
            </div>

            <!-- Main Content -->
            <div class="max-w-full mx-auto sm:px-6 lg:px-8">
                <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                    <div class="p-6 text-base-content space-y-4">
                        <p>{{ __("You're logged in!") }}</p>
                        @can('akses', 'create A')
                        <p>A</p>
                        <p>HAK AKSES BEKERJA DENGAN BAIK</p>
                        @endcan
                        @can('akses', 'create B')
                        <p>B</p>
                        <p>AKSES SUKSES</p>
                        @endcan
                        <div class="flex flex-wrap gap-2">
                            <button class="btn btn-neutral">Neutral</button>
                            <button class="btn btn-primary">Primary</button>
                            <button class="btn btn-secondary">Secondary</button>
                            <button class="btn btn-accent">Accent</button>
                            <button class="btn btn-info">Info</button>
                            <button class="btn btn-success">Success</button>
                            <button class="btn btn-warning">Warning</button>
                            <button class="btn btn-error">Error</button>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button class="btn btn-neutral" onclick="playNotif()">Klik Untuk Bunyi Bell</button>
                            <audio id="notifAudio" src="{{ asset('assets/music/bell.mp3') }}"></audio>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    function playNotif() {
        const audio = document.getElementById('notifAudio');
        audio.currentTime = 0; // reset biar bisa dipencet berkali-kali
        audio.play();
    }
</script>

</x-app-layout>
