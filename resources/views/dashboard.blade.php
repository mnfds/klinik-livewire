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
                <div class="bg-base-200 overflow-hidden rounded-sm sm:rounded-lg">
                    <div class="p-6 text-base-content space-y-4">
                        {{-- <div class="flex flex-wrap gap-2">
                            <button class="btn btn-neutral" onclick="playNotif()">Klik Untuk Bunyi Bell</button>
                            <audio id="notifAudio" src="{{ asset('assets/music/bell.mp3') }}"></audio>
                        </div> --}}
                        @if (Gate::allows('akses','Absen Scan User') || Gate::allows('akses','Absen Button'))
                            <livewire:Absen.Scanning />
                        @endif
                        @can('akses', 'Kinerja Karyawan')
                            <livewire:Tugas.DataPerorangan />
                        @endcan
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
