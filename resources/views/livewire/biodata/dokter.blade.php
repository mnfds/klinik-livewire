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
            <label for="nama_dokter" class="label">
                <span class="label-text">{{ __('Nama Lengkap') }}</span>
            </label>
            <input wire:model.defer="nama_dokter" id="nama_dokter" type="text" class="input input-bordered w-full" />
            <x-input-error :messages="$errors->get('nama_dokter')" class="mt-1 text-error text-sm" />
        </div>

        <!-- Alamat -->
        <div class="form-control">
            <label for="alamat_dokter" class="label">
                <span class="label-text">{{ __('Alamat') }}</span>
            </label>
            <textarea wire:model.defer="alamat_dokter" id="alamat_dokter" class="textarea textarea-bordered w-full" rows="3"></textarea>
            <x-input-error :messages="$errors->get('alamat_dokter')" class="mt-1 text-error text-sm" />
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
            <input wire:model.defer="telepon" id="telepon" type="text" inputmode="numeric" pattern="[0-9]*" class="input input-bordered w-full" />
            <x-input-error :messages="$errors->get('telepon')" class="mt-1 text-error text-sm" />
        </div>

        <!-- Tingkat Pendidikan -->
        <div class="form-control">
            <label for="tingkat_pendidikan" class="label">
                <span class="label-text">{{ __('Tingkat Pendidikan') }}</span>
            </label>
            <select wire:model.defer="tingkat_pendidikan" id="tingkat_pendidikan" class="select select-bordered w-full">
                <option value="">Pilih Pendidikan</option>
                <option value="S1">S1</option>
                <option value="S2">S2</option>
                <option value="S3">S3</option>
            </select>
            <x-input-error :messages="$errors->get('tingkat_pendidikan')" class="mt-1 text-error text-sm" />
        </div>
        
        <!-- Institusi -->
        <div class="form-control">
            <label for="institusi" class="label">
                <span class="label-text">{{ __('Institusi') }}</span>
            </label>
            <input wire:model.defer="institusi" id="institusi" type="text" class="input input-bordered w-full" />
            <x-input-error :messages="$errors->get('institusi')" class="mt-1 text-error text-sm" />
        </div>

        <!-- Tahun Kelulusan -->
        <div class="form-control">
            <label for="tahun_kelulusan" class="label">
                <span class="label-text">{{ __('Tahun Kelulusan') }}</span>
            </label>
            <input wire:model.defer="tahun_kelulusan" id="tahun_kelulusan" type="date" class="input input-bordered w-full" />
            <x-input-error :messages="$errors->get('tahun_kelulusan')" class="mt-1 text-error text-sm" />
        </div>

        <!-- No STR -->
        <div class="form-control">
            <label for="no_str" class="label">
                <span class="label-text">{{ __('NO STR') }}</span>
            </label>
            <input wire:model.defer="no_str" id="no_str" type="text" class="input input-bordered w-full" />
            <x-input-error :messages="$errors->get('no_str')" class="mt-1 text-error text-sm" />
        </div>

        <!-- Surat Izin Pratik -->
        <div class="form-control">
            <label for="surat_izin_pratik" class="label">
                <span class="label-text">{{ __('Surat Izin Praktik') }}</span>
            </label>
            <input wire:model.defer="surat_izin_pratik" id="surat_izin_pratik" type="text" class="input input-bordered w-full" />
            <x-input-error :messages="$errors->get('surat_izin_pratik')" class="mt-1 text-error text-sm" />
        </div>
        
        <!-- Masa Berlaku SIP -->
        <div class="form-control">
            <label for="masa_berlaku_sip" class="label">
                <span class="label-text">{{ __('Masa Berlaku SIP') }}</span>
            </label>
            <input wire:model.defer="masa_berlaku_sip" id="masa_berlaku_sip" type="date" class="input input-bordered w-full" />
            <x-input-error :messages="$errors->get('masa_berlaku_sip')" class="mt-1 text-error text-sm" />
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

        <!-- TTD Digital -->
        <div class="form-control mt-4">
            <label for="ttd_digital" class="label">
                <span class="label-text">{{ __('TTD Digital') }}</span>
            </label>

            <input type="file" id="ttd_digital" wire:model="ttd_digital" class="file-input file-input-bordered w-full" />

            <!-- Loading Indicator -->
            <div class="mt-2 text-sm text-gray-500 flex items-center gap-2" wire:loading wire:target="ttd_digital">
                <span class="loading loading-spinner loading-sm text-info"></span>
                <span>Mengunggah foto...</span>
            </div>

            <!-- Preview Baru -->
            @if ($ttd_digital)
                <img src="{{ $ttd_digital->temporaryUrl() }}" alt="Preview Foto" class="w-32 h-32 mt-2 rounded border object-cover" />
            @elseif ($ttd_digital_preview)
                <img src="{{ asset('storage/' . $ttd_digital_preview) }}" alt="TTD Lama" class="w-32 h-32 mt-2 rounded border object-cover" />
            @endif

            <x-input-error :messages="$errors->get('ttd_digital')" class="mt-1 text-error text-sm" />
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
