<div class="p-4">
    {{-- Alert Notification --}}
    <div
        x-data="{ show: false, message: '', type: 'info' }"
        x-on:toast.window="show = true; message = $event.detail[0]?.message ?? ''; type = $event.detail[0]?.type ?? 'info'; setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition
        class="fixed top-4 right-4 z-50"
        >
        <div role="alert" :class="`alert alert-${type} alert-soft w-md py-5`">
            <i :class="type === 'success' ? 'fa-regular fa-circle-check' : 'fa-regular fa-circle-xmark'"></i>
            <span x-text="message"></span>
        </div>
    </div>
    <div class="card bg-base-100 rounded-box mb-6 shadow-md">
        <div class="card-body items-center text-center">
            <h2 class="text-2xl font-bold text-base-content">
                Pilih Poli untuk Mengambil Nomor Antrian
            </h2>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" wire:poll.visible.5s='updateNomor'>
        @foreach ($poli as $p)
            <div class="card bg-base-100 text-base-content shadow-md hover:shadow-lg transition duration-300">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <h1 class="card-title text-lg font-bold tracking-wide">{{ Str::title($p->nama_poli) }}</h1>
                        {{-- Set poliId THEN open the modal --}}
                        <button
                            wire:click="setPoliId({{ $p->id }})"
                            x-on:poli-id-set.window="document.getElementById('showNameInput').showModal()"
                            class="btn btn-sm btn-primary btn-circle">
                            <i class="fa-solid fa-person"></i>
                        </button>
                    </div>
                    <p class="text-sm font-semibold uppercase text-gray-700">
                        Nomor Antrian Terakhir : {{ $p->nomor_terakhir }}
                    </p>
                    <p class="text-sm text-base-content/70">
                        Silakan klik tombol di bawah untuk mengambil nomor antrian.
                    </p>
                    <div class="card-actions justify-end mt-4">
                        <button wire:click="addNomorAntrian({{ $p->id }})"
                            class="btn btn-primary btn-sm gap-2 w-full">
                            <i class="fa-solid fa-ticket"></i>
                            Ambil Nomor
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Modal is outside the loop — single instance, no id clash --}}
    <dialog id="showNameInput" class="modal" wire:ignore.self
        x-data
        x-init="$wire.on('closeshowNameInput', () => document.getElementById('showNameInput')?.close())">
        <div class="modal-box w-full max-w-lg">
            <form wire:submit.prevent="addAntrianByName">
                <h3 class="text-xl font-semibold text-center mb-5">
                    Masukkan Nama Lengkap
                </h3>
                <div class="flex flex-col items-center p-1">
                    <input class="input" type="text" wire:model.defer="namaPengantri" placeholder="Nama lengkap">
                </div>
                <div class="flex justify-end mt-5 gap-1">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="button" class="btn btn-error"
                        onclick="document.getElementById('showNameInput').close()">Tutup</button>
                </div>
            </form>
        </div>
    </dialog>
</div>
