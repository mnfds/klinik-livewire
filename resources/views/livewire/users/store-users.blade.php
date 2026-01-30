<div class="pt-1 pb-12">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Breadcrumbs (hanya muncul di layar lg ke atas) -->
        <div class="hidden lg:flex justify-end px-4">
            <div class="breadcrumbs text-sm">
                <ul>
                    <li>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('users.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Staff
                        </a>
                    </li>
                    <li>
                        <a class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Tambah Staff
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Tambah Staff
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 shadow rounded-box">
                <form wire:submit.prevent="store" class="p-6 space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Username --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Username<span class="text-error">*</span></span></label>
                            <input wire:model.defer="name" id="name" name="name" type="text"
                                autofocus class="input input-bordered w-full @error('name') input-error @enderror" />
                                @error('name')
                                    <span class="text-error text-sm mt-1">
                                        Mohon Isi Username Dengan Benar
                                    </span>
                                @enderror
                        </div>

                        {{-- Email --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Email<span class="text-error">*</span></span></label>
                            <input wire:model.defer="email" type="email" class="input input-bordered w-full @error('email') input-error @enderror" />
                            @error('email')
                                <span class="text-error text-sm mt-1">
                                    Mohon Isi Email Dengan Benar
                                </span>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Password<span class="text-error">*</span></span></label>
                            <input wire:model.defer="password" type="password" 
                                class="input input-bordered w-full @error('password') input-error @enderror" />
                            @error('password')
                                <span class="text-error text-sm mt-1">
                                    Mohon Isi Password Dengan Benar
                                </span>
                            @enderror
                        </div>

                        {{-- Role --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Role<span class="text-error">*</span></span></label>
                            <select wire:model.defer="role_id" class="select select-bordered w-full @error('role_id') input-error @enderror">
                                <option value="">Pilih Role</option>
                                @foreach ($roles as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <span class="text-error text-sm mt-1">
                                    Mohon Pilih Role Akses Yang Akan diberikan
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        {{-- Nama Lengkap --}}
                        <div class="form-control">
                            <label for="nama_lengkap" class="label"><span class="label-text">Nama Lengkap<span class="text-error">*</span></span></label>
                            <input wire:model="nama_lengkap" type="text" id="nama_lengkap" name="nama_lengkap"
                                class="input input-bordered w-full @error('nama_lengkap') input-error @enderror" />
                            @error('nama_lengkap')
                                <span class="text-error text-sm mt-1">
                                    Mohon Mengisi Nama Lengkap
                                </span>
                            @enderror
                        </div>

                        {{-- NIK --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">NIK</span></label>
                            <input wire:model.defer="nik" type="text" inputmode="numeric" pattern="[0-9]*" class="input input-bordered w-full" />
                            <x-input-error :messages="$errors->get('nik')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- IHS --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">IHS</span></label>
                            <input wire:model.defer="ihs" type="text" class="input input-bordered w-full" />
                            <x-input-error :messages="$errors->get('ihs')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Alamat --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Alamat</span></label>
                            <input wire:model.defer="alamat" type="text" class="input input-bordered w-full" />
                            <x-input-error :messages="$errors->get('alamat')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Telepon --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Telepon</span></label>
                            <input wire:model.defer="telepon" type="text" inputmode="numeric" pattern="[0-9]*" class="input input-bordered w-full" />
                            <x-input-error :messages="$errors->get('telepon')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Jenis Kelamin --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Jenis Kelamin<span class="text-error">*</span></span></label>
                            <select wire:model.defer="jenis_kelamin" class="select select-bordered w-full @error('jenis_kelamin') input-error @enderror">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            @error('jenis_kelamin')
                                <span class="text-error text-sm mt-1">
                                    Mohon Memilih Jenis Kelamin
                                </span>
                            @enderror
                        </div>

                        {{-- Tempat Lahir --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Tempat Lahir</span></label>
                            <input wire:model.defer="tempat_lahir" type="text" class="input input-bordered w-full" />
                            <x-input-error :messages="$errors->get('tempat_lahir')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Tanggal Lahir --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Tanggal Lahir</span></label>
                            <input wire:model.defer="tanggal_lahir" type="date" class="input input-bordered w-full" />
                            <x-input-error :messages="$errors->get('tanggal_lahir')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Mulai Bekerja --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Mulai Bekerja</span></label>
                            <input wire:model.defer="mulai_bekerja" type="date" class="input input-bordered w-full" />
                            <x-input-error :messages="$errors->get('mulai_bekerja')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Upload Foto --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Unggah Foto</span></label>
                            <input type="file" id="foto_wajah" wire:model="foto_wajah" class="file-input file-input-bordered w-full" />
                            <div class="mt-2 text-sm text-gray-500 flex items-center gap-2" wire:loading wire:target="foto_wajah">
                                <span class="loading loading-spinner loading-sm text-info"></span>
                                <span>Mengunggah foto...</span>
                            </div>
                            <x-input-error :messages="$errors->get('foto_wajah')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Preview Foto --}}
                        <div class="form-control col-span-full">
                            <label class="label"><span class="label-text">Preview Foto</span></label>
                            @if ($foto_wajah)
                                <img src="{{ $foto_wajah->temporaryUrl() }}" alt="Preview Foto" class="w-32 h-32 mt-2 rounded border object-cover" />
                            @elseif ($foto_wajah_preview)
                                <img src="{{ asset('storage/' . $foto_wajah_preview) }}" alt="Foto Wajah Lama" class="w-32 h-32 mt-2 rounded border object-cover" />
                            @endif
                        </div>
                    </div>

                    {{-- Tombol Simpan --}}
                    <div class="pt-4 text-right border-t">
                        @can('akses', 'Staff Tambah')
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i> Simpan Pengguna
                        </button>
                        @endcan
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>