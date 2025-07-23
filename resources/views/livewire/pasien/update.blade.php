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
                        <a href="{{ route('pasien.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Pasien
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pasien.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Update Data Pasien
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-lg font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Update Data Pasien {{ $nama }}
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 shadow rounded-box">
                <form wire:submit.prevent="update" class="p-6 space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        {{-- No Register --}}
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text">No Register <span class="text-error">*</span></span>
                            </label>
                            <div class="flex w-full gap-2">
                                <input type="text" wire:model="no_register"
                                    placeholder="Masukkan Huruf Awal dan klik Generate"
                                    class="input input-bordered w-full" maxlength="10" required />
                                <button type="button" wire:click="generateNoRegister" class="btn btn-neutral">
                                    Generate
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('no_register')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- NIK --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">NIK</span></label>
                            <input wire:model.defer="nik" type="text" class="input input-bordered w-full" />
                            <x-input-error :messages="$errors->get('nik')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- No IHS --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">No IHS</span></label>
                            <input wire:model.defer="no_ihs" type="text" class="input input-bordered w-full" />
                            <x-input-error :messages="$errors->get('no_ihs')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Nama --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Nama <span class="text-error">*</span></span>
                            </label>
                            <input wire:model.defer="nama" type="text" class="input input-bordered w-full" required />
                            <x-input-error :messages="$errors->get('nama')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Alamat --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Alamat</span></label>
                            <input wire:model.defer="alamat" type="text" class="input input-bordered w-full" />
                            <x-input-error :messages="$errors->get('alamat')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- No Telepon --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">No Telepon</span></label>
                            <input wire:model.defer="no_telp" type="number" inputmode="numeric" pattern="[0-9]*"
                                class="input input-bordered w-full" />
                            <x-input-error :messages="$errors->get('no_telp')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Jenis Kelamin --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Jenis Kelamin <span class="text-error">*</span></span>
                            </label>
                            <select wire:model.defer="jenis_kelamin" class="select select-bordered w-full" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Wanita">Wanita</option>
                            </select>
                            <x-input-error :messages="$errors->get('jenis_kelamin')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Agama --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Agama</span></label>
                            <select wire:model.defer="agama" class="select select-bordered w-full">
                                <option value="">Pilih Agama</option>
                                <option value="Islam">Islam</option>
                                <option value="Kristen">Kristen</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Buddha">Buddha</option>
                                <option value="Konghucu">Konghucu</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                            <x-input-error :messages="$errors->get('agama')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Profesi --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Profesi</span></label>
                            <select wire:model.defer="profesi" class="select select-bordered w-full">
                                <option value="">Pilih Profesi</option>
                                <option value="Pelajar">Pelajar</option>
                                <option value="Mahasiswa">Mahasiswa</option>
                                <option value="PNS">PNS</option>
                                <option value="Karyawan Swasta">Karyawan Swasta</option>
                                <option value="Wiraswasta">Wiraswasta</option>
                                <option value="Ibu Rumah Tangga">Ibu Rumah Tangga</option>
                                <option value="Tidak Bekerja">Tidak Bekerja</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                            <x-input-error :messages="$errors->get('profesi')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Tanggal Lahir --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Tanggal Lahir</span></label>
                            <input wire:model.defer="tanggal_lahir" type="date" class="input input-bordered w-full" />
                            <x-input-error :messages="$errors->get('tanggal_lahir')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Status --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Status</span></label>
                            <select wire:model.defer="status" class="select select-bordered w-full">
                                <option value="">Pilih Status</option>
                                <option value="Belum Menikah">Belum Menikah</option>
                                <option value="Menikah">Menikah</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Upload Foto --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Unggah Foto Pasien</span></label>
                            <input type="file" wire:model="new_foto_pasien" class="file-input file-input-bordered w-full" />

                            <!-- Loading indicator -->
                            <div class="mt-2 text-sm text-gray-500 flex items-center gap-2" wire:loading wire:target="new_foto_pasien">
                                <span class="loading loading-spinner loading-sm text-info"></span>
                                <span>Mengunggah foto...</span>
                            </div>
                        </div>

                        {{-- Preview Foto --}}
                        <div class="form-control col-span-full">
                            <label class="label"><span class="label-text">Preview Foto</span></label>

                            {{-- Tampilkan preview jika upload baru --}}
                            @if ($new_foto_pasien)
                                <img src="{{ $new_foto_pasien->temporaryUrl() }}" alt="Preview Foto"
                                    class="w-32 h-32 mt-2 rounded border object-cover" />
                            {{-- Kalau tidak, tampilkan dari storage lama --}}
                            @elseif ($foto_pasien_preview)
                                <img src="{{ asset('storage/' . $foto_pasien_preview) }}" alt="Foto Pasien Lama"
                                    class="w-32 h-32 mt-2 rounded border object-cover" />
                            @endif
                        </div>

                        {{-- Deskripsi --}}
                        <div class="form-control col-span-full">
                            <label class="label"><span class="label-text">Deskripsi Tambahan</span></label>
                            <textarea wire:model.defer="deskripsi" class="textarea textarea-bordered w-full" rows="3"></textarea>
                            <x-input-error :messages="$errors->get('deskripsi')" class="text-error text-sm mt-1" />
                        </div>

                    </div>

                    {{-- Tombol Simpan --}}
                    <div class="pt-4 text-right border-t">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i> Simpan Pasien
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>