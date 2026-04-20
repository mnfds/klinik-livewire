<div class="space-y-4">

    {{-- Info Antrian --}}
    @if ($antrianTerdaftar)
        <div class="alert alert-info">
            <i class="fa-solid fa-circle-info"></i>
            <div>
                <div class="font-semibold">Nomor Antrian: {{ $antrianTerdaftar->kode }}-{{ $antrianTerdaftar->nomor_antrian }}</div>
                <div class="font-semibold">Nama Didaftarkan: {{ $antrianTerdaftar->nama_pengantri ?? '-'}}</div>
                <div class="text-sm">Poli: {{ $antrianTerdaftar->poli->nama_poli }}</div>
            </div>
        </div>
    @endif

    {{-- Search Input --}}
    <div class="form-control w-full">
        <label class="label">
            <span class="label-text font-semibold">Cari Pasien</span>
        </label>

        <div class="relative">
            <div class="flex items-center input input-bordered w-full pr-10 gap-2">
                <i class="fa-solid fa-magnifying-glass text-base-content/50"></i>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari Berdasarkan Nama / No RM..."
                    class="grow bg-transparent outline-none"
                    autocomplete="off"
                />
                @if ($search)
                    <button type="button" wire:click="clearPasien" class="text-base-content/50 hover:text-error">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                @endif
            </div>

            {{-- Dropdown Hasil Pencarian --}}
            @if (count($hasilPencarian) > 0)
                <div class="absolute z-50 w-full mt-1 bg-base-100 border border-base-300 rounded-box shadow-lg max-h-64 overflow-y-auto">
                    @foreach ($hasilPencarian as $pasien)
                        <button
                            type="button"
                            wire:click="pilihPasien({{ $pasien['id'] }})"
                            class="w-full px-4 py-3 text-left hover:bg-base-200 flex items-center justify-between border-b border-base-200 last:border-0"
                        >
                            <span class="font-medium">{{ $pasien['nama'] }}</span>
                            <span class="text-sm text-base-content/60 badge badge-ghost">{{ $pasien['no_register'] }}</span>
                        </button>
                    @endforeach
                </div>
            @elseif (strlen($search) > 0 && count($hasilPencarian) === 0 && !$pasienDipilih)
                {{-- Hanya tampil jika belum ada pasien yang dipilih --}}
                <div class="absolute z-50 w-full mt-1 bg-base-100 border border-base-300 rounded-box shadow-lg">
                    <div class="px-4 py-3 text-base-content/50 text-sm text-center">
                        <i class="fa-solid fa-face-frown mr-1"></i>
                        Pasien tidak ditemukan
                    </div>
                </div>
            @endif
        </div>
        {{-- Pasien Dipilih --}}
        @if ($pasienDipilih)
            <div class="mt-2 flex items-center gap-2 p-3 bg-success/10 border border-success/30 rounded-box">
                <i class="fa-solid fa-circle-check text-success"></i>
                <div>
                    <div class="font-semibold text-sm">{{ $pasienDipilih['nama'] }}</div>
                    <div class="text-xs text-base-content/60">{{ $pasienDipilih['no_register'] }}</div>
                </div>
            </div>
        @endif
    </div>

    {{-- Tombol Aksi --}}
    <div class="flex flex-col sm:flex-row gap-2 pt-2">
        <button
            type="button"
            wire:click="lanjutkan"
            class="btn btn-primary w-full sm:w-auto"
            @if(!$pasienDipilih) disabled @endif
        >
            <i class="fa-solid fa-arrow-right"></i>
            Lanjutkan Pendaftaran
        </button>
        <a href="{{ route('pasien.create') }}" class="btn btn-success w-full sm:w-auto">
            <i class="fa-solid fa-plus"></i>
            Tambah Pasien
        </a>
    </div>

</div>