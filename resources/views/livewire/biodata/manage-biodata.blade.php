<section>
    <header class="mb-4">
        <h2 class="text-xl font-bold text-base-content">
            {{ __('Biodata Pengguna') }}
        </h2>
        <p class="mt-1 text-sm text-base-content/70">
            {{ __('Perbarui informasi pribadi Anda di bawah ini.') }}
        </p>
    </header>

    <form wire:submit.prevent="save" class="space-y-5">
        <!-- Nama Lengkap -->
        <div class="form-control">
            <label for="nama_lengkap" class="label">
                <span class="label-text">{{ __('Nama Lengkap') }}</span>
            </label>
            <input wire:model.defer="nama_lengkap" id="nama_lengkap" type="text" class="input input-bordered w-full" />
            <x-input-error :messages="$errors->get('nama_lengkap')" class="mt-1 text-error text-sm" />
        </div>

        <!-- Alamat -->
        <div class="form-control">
            <label for="alamat" class="label">
                <span class="label-text">{{ __('Alamat') }}</span>
            </label>
            <textarea wire:model.defer="alamat" id="alamat" class="textarea textarea-bordered w-full" rows="3"></textarea>
            <x-input-error :messages="$errors->get('alamat')" class="mt-1 text-error text-sm" />
        </div>

        <!-- Tempat Lahir -->
        <div class="form-control">
            <label for="tempat_lahir" class="label">
                <span class="label-text">{{ __('Tempat Lahir') }}</span>
            </label>
            <input wire:model.defer="tempat_lahir" id="tempat_lahir" type="text" class="input input-bordered w-full" />
            <x-input-error :messages="$errors->get('tempat_lahir')" class="mt-1 text-error text-sm" />
        </div>

        <!-- Tanggal Lahir -->
        <div class="form-control">
            <label for="tanggal_lahir" class="label">
                <span class="label-text">{{ __('Tanggal Lahir') }}</span>
            </label>
            <input wire:model.defer="tanggal_lahir" id="tanggal_lahir" type="date" class="input input-bordered w-full" />
            <x-input-error :messages="$errors->get('tanggal_lahir')" class="mt-1 text-error text-sm" />
        </div>

        <!-- Jenis Kelamin -->
        <div class="form-control">
            <label for="jenis_kelamin" class="label">
                <span class="label-text">{{ __('Jenis Kelamin') }}</span>
            </label>
            <select wire:model.defer="jenis_kelamin" id="jenis_kelamin" class="select select-bordered w-full">
                <option value="">{{ __('Pilih Jenis Kelamin') }}</option>
                <option value="L">{{ __('Laki-laki') }}</option>
                <option value="P">{{ __('Perempuan') }}</option>
            </select>
            <x-input-error :messages="$errors->get('jenis_kelamin')" class="mt-1 text-error text-sm" />
        </div>

        <!-- Telepon -->
        <div class="form-control">
            <label for="telepon" class="label">
                <span class="label-text">{{ __('Telepon') }}</span>
            </label>
            <input wire:model.defer="telepon" id="telepon" type="text" class="input input-bordered w-full" />
            <x-input-error :messages="$errors->get('telepon')" class="mt-1 text-error text-sm" />
        </div>

        <!-- Mulai Bekerja -->
        <div class="form-control">
            <label for="mulai_bekerja" class="label">
                <span class="label-text">{{ __('Mulai Bekerja') }}</span>
            </label>
            <input wire:model.defer="mulai_bekerja" id="mulai_bekerja" type="date" class="input input-bordered w-full" />
            <x-input-error :messages="$errors->get('mulai_bekerja')" class="mt-1 text-error text-sm" />
        </div>

        <!-- Foto Wajah -->
        <div class="form-control mt-4">
            <label for="foto_wajah" class="label">
                <span class="label-text">{{ __('Foto Wajah') }}</span>
            </label>

            <input type="file" id="foto_wajah" wire:model="foto_wajah" class="file-input file-input-bordered w-full" />

            <!-- Loading Indicator -->
            <div class="mt-2 text-sm text-gray-500 flex items-center gap-2" wire:loading wire:target="foto_wajah">
                <span class="loading loading-spinner loading-sm text-info"></span>
                <span>Mengunggah foto...</span>
            </div>

            <!-- Preview Baru -->
            @if ($foto_wajah)
                <img src="{{ $foto_wajah->temporaryUrl() }}" alt="Preview Foto" class="w-32 h-32 mt-2 rounded border object-cover" />
            @elseif ($foto_wajah_preview)
                <img src="{{ asset('storage/' . $foto_wajah_preview) }}" alt="Foto Wajah Lama" class="w-32 h-32 mt-2 rounded border object-cover" />
            @endif

            <x-input-error :messages="$errors->get('foto_wajah')" class="mt-1 text-error text-sm" />
        </div>

        <!-- Tombol -->
        <div class="flex items-center gap-4 mt-2">
            <x-primary-button>
                {{ __('Simpan') }}
            </x-primary-button>

            @if (session()->has('message'))
                <span class="text-success text-sm">
                    {{ session('message') }}
                </span>
            @endif
        </div>
    </form>
</section>
