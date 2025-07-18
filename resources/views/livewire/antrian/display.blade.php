<div class="p-4">
    <!-- Heading Card -->
    <div class="card bg-base-100 rounded-box mb-6 shadow-md">
        <div class="card-body items-center text-center">
            <h2 class="text-2xl font-bold text-base-content">
                Pilih Poli untuk Mengambil Nomor Antrian
            </h2>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6">
        @foreach ($poli as $p)
            <div class="card bg-base-100 text-base-content shadow-md hover:shadow-lg transition duration-300">
                <div class="card-body">
                    <h1 class="card-title">{{ $p->nama_poli }}</h1>
                    <p class="text-sm text-base-content/70">
                        Silakan klik tombol di bawah untuk mengambil nomor antrian.
                    </p>
                    <div class="card-actions justify-end mt-4">
                        <button wire:click="addNomorAntrian({{ $p->id }})" class="btn btn-primary btn-sm gap-2 w-full">
                            <i class="fa-solid fa-ticket"></i>
                            Ambil Nomor
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
