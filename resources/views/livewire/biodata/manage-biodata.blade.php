<section>
<header class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-4">
        <div>
            <h2 class="text-xl font-bold text-base-content">
                {{ __('Biodata Pengguna') }}
            </h2>
            <p class="mt-1 text-sm text-base-content/70">
                {{ __('Perbarui informasi pribadi Anda di bawah ini.') }}
            </p>
        </div>

        <div class="flex flex-col items-center gap-3 p-4 rounded-xl bg-base-100 mx-auto sm:mx-0">
            <div class="w-32 h-32 flex items-center justify-center" id="qr-image">
                {!! $qrUserImage !!}
            </div>
            <p class="text-sm font-mono font-semibold tracking-widest bg-base-200 px-3 py-1 rounded-lg">{{ $user_code_qr }}</p>
            <button onclick="downloadQR('{{ $user_code_qr }}')" class="btn btn-sm btn-info gap-2 w-full">
                <i class="fa-solid fa-download"></i>Download
            </button>
        </div>
    </header>

    <form wire:submit.prevent="save" class="space-y-5">
        <!-- Nama Lengkap -->
        <div class="form-control">
            <label for="nama_lengkap" class="label">
                <span class="label-text">{{ __('Nama Lengkap') }} <span class="text-error">*</span></span>
            </label>
            <input wire:model.defer="nama_lengkap" id="nama_lengkap" type="text" class="input input-bordered w-full @error('nama_lengkap') input-error @enderror" />
            @error('nama_lengkap')
                <span class="text-error text-sm mt-1">
                    Mohon Mengisi Nama Lengkap Anda Dengan Benar
                </span>
            @enderror
            {{-- <x-input-error :messages="$errors->get('nama_lengkap')" class="mt-1 text-error text-sm" /> --}}
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
                <span class="label-text">{{ __('Jenis Kelamin') }} <span class="text-error">*</span></span>
            </label>
            <select wire:model.defer="jenis_kelamin" id="jenis_kelamin" class="select select-bordered w-full @error('jenis_kelamin') input-error @enderror">
                <option value="">{{ __('Pilih Jenis Kelamin') }}</option>
                <option value="L">{{ __('Laki-laki') }}</option>
                <option value="P">{{ __('Perempuan') }}</option>
            </select>
            @error('jenis_kelamin')
                <span>
                    Mohon Memilih Jenis Kelamin Anda Dengan Benar
                </span>   
            @enderror
            {{-- <x-input-error :messages="$errors->get('jenis_kelamin')" class="mt-1 text-error text-sm" /> --}}
        </div>

        <!-- Telepon -->
        <div class="form-control">
            <label for="telepon" class="label">
                <span class="label-text">{{ __('Telepon') }}</span>
            </label>
            <input wire:model.defer="telepon" id="telepon" type="text" inputmode="numeric" pattern="[0-9]*" class="input input-bordered w-full" />
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
<script>
    function downloadQR(filename) {
        const svgEl = document.querySelector('#qr-image svg');
        const svgData = new XMLSerializer().serializeToString(svgEl);
        const svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
        const url = URL.createObjectURL(svgBlob);

        const img = new Image();
        img.onload = function () {
            const canvas = document.createElement('canvas');
            canvas.width = 400;  // resolusi PNG, makin besar makin tajam
            canvas.height = 400;

            const ctx = canvas.getContext('2d');
            ctx.fillStyle = '#ffffff'; // background putih
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

            const pngUrl = canvas.toDataURL('image/png');
            const a = document.createElement('a');
            a.href = pngUrl;
            a.download = `qrcode-${filename}.png`;
            a.click();

            URL.revokeObjectURL(url);
        };

        img.src = url;
    }
</script>
