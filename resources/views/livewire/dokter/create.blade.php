<div class="pt-1 pb-12">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Breadcrumbs (hanya muncul di layar lg ke atas) -->
        <div class="hidden lg:flex justify-end px-4">
            <div class="breadcrumbs text-sm">
                <ul>
                    <li>
                        <a wire:navigate href="{{ route('dashboard') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a wire:navigate href="{{ route('dokter.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Dokter
                        </a>
                    </li>
                    <li>
                        <a wire:navigate href="{{ route('dokter.create') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Tambah Dokter
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Tambah Dokter
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 shadow rounded-box">
                <form wire:submit.prevent="store" class="p-6 space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Username --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Username</span></label>
                            <input wire:model.defer="name" id="name" name="name" type="text"
                                required autofocus placeholder="username" class="input input-bordered w-full" />
                            <x-input-error :messages="$errors->get('name')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Email --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Email</span></label>
                            <input wire:model.defer="email" type="email" class="input input-bordered w-full" />
                            <x-input-error :messages="$errors->get('email')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Password --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Password</span></label>
                            <input wire:model.defer="password" type="password" class="input input-bordered w-full"
                                placeholder="Isi password pengguna" />
                            <x-input-error :messages="$errors->get('password')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Role --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Poli</span></label>
                            <select wire:model.defer="id_poli" class="select select-bordered w-full">
                                <option value="">Pilih Role</option>
                                @foreach ($poli as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('id_poli')" class="text-error text-sm mt-1" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        {{-- Nama Lengkap --}}
                        <div class="form-control">
                            <label for="nama_dokter" class="label"><span class="label-text">Nama Lengkap</span></label>
                            <input wire:model="nama_dokter" type="text" id="nama_dokter" name="nama_dokter"
                                class="input input-bordered w-full" />
                            <x-input-error :messages="$errors->get('nama_dokter')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Alamat --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Alamat</span></label>
                            <input wire:model.defer="alamat_dokter" type="text" class="input input-bordered w-full" />
                            <x-input-error :messages="$errors->get('alamat_dokter')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Jenis Kelamin --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Jenis Kelamin</span></label>
                            <select wire:model.defer="jenis_kelamin" class="select select-bordered w-full">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            <x-input-error :messages="$errors->get('jenis_kelamin')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Telepon --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Telepon</span></label>
                            <input wire:model.defer="telepon" type="text" inputmode="numeric" pattern="[0-9]*" class="input input-bordered w-full" />
                            <x-input-error :messages="$errors->get('telepon')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Institusi --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Institusi</span></label>
                            <input wire:model.defer="institusi" type="text" class="input input-bordered w-full" />
                            <x-input-error :messages="$errors->get('institusi')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Tahun Kelulusan --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Tahun Kelulusan</span></label>
                            <input wire:model.defer="tahun_kelulusan" type="date" class="input input-bordered w-full" />
                            <x-input-error :messages="$errors->get('tahun_kelulusan')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Tingkat Pendidikan --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Tingkat Pendidikan</span></label>
                            <select wire:model.defer="tingkat_pendidikan" class="select select-bordered w-full">
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                                <option value="S3">S3</option>
                            </select>
                            <x-input-error :messages="$errors->get('tingkat_pendidikan')" class="text-error text-sm mt-1" />
                        </div>
                    
                        {{-- No STR --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Nomor STR</span></label>
                            <input wire:model.defer="no_str" type="text" class="input input-bordered w-full" />
                            <x-input-error :messages="$errors->get('no_str')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Surat Izin Praktik --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Surat Izin Praktik</span></label>
                            <input wire:model.defer="surat_izin_pratik" type="text" class="input input-bordered w-full" />
                            <x-input-error :messages="$errors->get('surat_izin_pratik')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Masa Berlaku SIP --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Masa Berlaku SIP</span></label>
                            <input wire:model.defer="masa_berlaku_sip" type="date" class="input input-bordered w-full" />
                            <x-input-error :messages="$errors->get('masa_berlaku_sip')" class="text-error text-sm mt-1" />
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

                        {{-- Upload TTD --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Unggah Tanda Tangan Digital</span></label>
                            <input type="file" id="ttd_digital" wire:model="ttd_digital" class="file-input file-input-bordered w-full" />
                            <div class="mt-2 text-sm text-gray-500 flex items-center gap-2" wire:loading wire:target="ttd_digital">
                                <span class="loading loading-spinner loading-sm text-info"></span>
                                <span>Mengunggah Tanda Tangan...</span>
                            </div>
                            <x-input-error :messages="$errors->get('ttd_digital')" class="text-error text-sm mt-1" />
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

                        {{-- Preview TTD --}}
                        <div class="form-control col-span-full">
                            <label class="label"><span class="label-text">Preview Tanda Tangan</span></label>
                            @if ($ttd_digital)
                                <img src="{{ $ttd_digital->temporaryUrl() }}" alt="Preview Foto" class="w-32 h-32 mt-2 rounded border object-cover" />
                            @elseif ($ttd_digital_preview)
                                <img src="{{ asset('storage/' . $ttd_digital_preview) }}" alt="Foto Wajah Lama" class="w-32 h-32 mt-2 rounded border object-cover" />
                            @endif
                        </div>
                    </div>

                    {{-- Tombol Simpan --}}
                    <div class="pt-4 text-right border-t">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i> Simpan Pengguna
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>