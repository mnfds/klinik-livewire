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
                        <a href="{{ route('pendaftaran.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Pasien Terdaftar
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pendaftaran.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Pendaftaran Pasien
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-lg font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Formulir Pendaftaran
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                {{-- Kolom Kiri: Form --}}
                <div class="lg:col-span-3 space-y-6">

                    <form wire:submit.prevent="submit" class="space-y-6">
                        {{-- SECTION: PENDAFTARAN --}}
                        <div class="bg-base-100 shadow rounded-box p-6">
                            <h2 class="text-lg font-semibold mb-4 border-b pb-2">Pendaftaran</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Tanggal Kunjungan</span>
                                    </label>
                                    <input type="date" wire:model.defer="tanggal_kunjungan" class="input input-bordered" />
                                    @error('tanggal_kunjungan') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Jenis Kunjungan</span>
                                    </label>
                                    <select wire:model.defer="jenis_kunjungan" class="select select-bordered">
                                        <option value="">Pilih Jenis</option>
                                        <option value="sehat">Sehat</option>
                                        <option value="sakit">Sakit</option>
                                    </select>
                                    @error('jenis_kunjungan') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- SECTION: DATA PASIEN --}}
                        <div class="bg-base-100 shadow rounded-box p-6">
                            <h2 class="text-lg font-semibold mb-4 border-b pb-2">Data Pasien</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                {{-- Nama --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Nama Lengkap <span class="text-error">*</span></span></label>
                                    <input type="text" wire:model.defer="nama" class="input input-bordered" />
                                    @error('nama') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                                </div>

                                {{-- No Register --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">No. Register <span class="text-error">*</span></span></label>
                                    <input type="text" wire:model.defer="no_register" class="input input-bordered" readonly />
                                    @error('no_register') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                                </div>

                                {{-- NIK --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">NIK</span></label>
                                    <input type="text" wire:model.defer="nik" class="input input-bordered" />
                                    @error('nik') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                                </div>

                                {{-- No IHS --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">No. IHS</span></label>
                                    <input type="text" wire:model.defer="no_ihs" class="input input-bordered" />
                                    @error('no_ihs') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                                </div>

                                {{-- Alamat --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Alamat</span></label>
                                    <input type="text" wire:model.defer="alamat" class="input input-bordered" />
                                    @error('alamat') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                                </div>

                                {{-- Telepon --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">No. Telepon</span></label>
                                    <input wire:model.defer="no_telp" type="number" class="input input-bordered" />
                                    @error('no_telp') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                                </div>

                                {{-- Jenis Kelamin --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Jenis Kelamin</span></label>
                                    <select wire:model.defer="jenis_kelamin" class="select select-bordered">
                                        <option value="">Pilih</option>
                                        <option value="Laki-laki">Laki-laki</option>
                                        <option value="Wanita">Wanita</option>
                                    </select>
                                    @error('jenis_kelamin') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                                </div>

                                {{-- Agama --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Agama</span></label>
                                    <select wire:model.defer="agama" class="select select-bordered">
                                        <option value="">Pilih Agama</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Kristen">Kristen</option>
                                        <option value="Katolik">Katolik</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Buddha">Buddha</option>
                                        <option value="Konghucu">Konghucu</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                    @error('agama') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                                </div>

                                {{-- Profesi --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Profesi</span></label>
                                    <select wire:model.defer="profesi" class="select select-bordered">
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
                                    @error('profesi') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                                </div>

                                {{-- Tanggal Lahir --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Tanggal Lahir</span></label>
                                    <input type="date" wire:model.defer="tanggal_lahir" class="input input-bordered" />
                                    @error('tanggal_lahir') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                                </div>

                                {{-- Status --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Status</span></label>
                                    <select wire:model.defer="status" class="select select-bordered">
                                        <option value="">Pilih Status</option>
                                        <option value="Belum Menikah">Belum Menikah</option>
                                        <option value="Menikah">Menikah</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                    @error('status') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                                </div>

                                {{-- Foto Pasien --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Foto Pasien</span></label>
                                    <input type="file" wire:model="foto_pasien" class="file-input file-input-bordered" />
                                    <div class="mt-2 text-sm text-gray-500 flex items-center gap-2" wire:loading wire:target="foto_pasien">
                                        <span class="loading loading-spinner loading-sm text-info"></span>
                                        <span>Mengunggah foto...</span>
                                    </div>
                                    @error('foto_pasien') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                                </div>

                                {{-- Deskripsi --}}
                                <div class="form-control md:col-span-2">
                                    <label class="label"><span class="label-text">Deskripsi Tambahan</span></label>
                                    <textarea wire:model.defer="deskripsi" class="textarea textarea-bordered w-full"></textarea>
                                    @error('deskripsi') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                                </div>

                                {{-- Preview Foto --}}
                                <div class="form-control md:col-span-2">
                                    <label class="label"><span class="label-text">Preview Foto</span></label>

                                    @if ($foto_pasien instanceof \Livewire\TemporaryUploadedFile)
                                        {{-- Preview foto baru yang diupload --}}
                                        <img src="{{ $foto_pasien->temporaryUrl() }}" alt="Preview Foto" class="w-32 h-32 mt-2 rounded border object-cover" />
                                    @elseif ($foto_pasien_preview)
                                        {{-- Tampilkan foto lama dari storage --}}
                                        <img src="{{ asset('storage/' . $foto_pasien_preview) }}" alt="Foto Pasien Lama" class="w-32 h-32 mt-2 rounded border object-cover" />
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- SECTION: NAKES & POLI --}}
                        <div class="bg-base-100 shadow rounded-box p-6">
                            <h2 class="text-lg font-semibold mb-4 border-b pb-2">Tenaga Kesehatan & Poli</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                
                                {{-- Poli --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Pilih Poli</span></label>
                                    <select wire:model.defer="poli_id" class="select select-bordered">
                                        <option value="">Pilih Poli</option>
                                        @foreach ($poli as $p)
                                            <option value="{{ $p->id }}">{{ $p->nama_poli }}</option>
                                        @endforeach
                                    </select>
                                    @error('poli_id') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                                </div>

                                {{-- Dokter --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Pilih Dokter</span></label>
                                    <select wire:model.defer="dokter_id" class="select select-bordered">
                                        <option value="">Pilih Dokter</option>
                                        @foreach ($dokter as $d)
                                            <option value="{{ $d->id }}">{{ $d->nama_dokter }}</option>
                                        @endforeach
                                    </select>
                                    @error('dokter_id') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                                </div>

                            </div>
                        </div>
                    </form>

                </div>

                {{-- Kolom Kanan: Tombol Simpan Sticky --}}
                <div class="lg:col-span-1">
                    <div class="sticky top-20">
                        <div class="bg-base-100 shadow rounded-box p-4">
                            <h3 class="font-semibold mb-4">Aksi</h3>
                            <button wire:click="submit" class="btn btn-success w-full" wire:loading.attr="disabled">
                                <span wire:loading.remove><i class="fa-solid fa-plus"></i> Simpan</span>
                                <span wire:loading.inline>Loading...</span>
                            </button>
                            <a wire:navigate href="{{ route('pendaftaran.data') }}" class="btn btn-primary w-full my-1">
                                <i class="fa-solid fa-rotate-left"></i>
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>