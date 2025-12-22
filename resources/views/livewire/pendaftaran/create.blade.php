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
                            {{-- Data Antrian --}}
                            @if ($antrian)
                                <div class="bg-gray-100 border border-gray-300 rounded-lg p-4 mb-4">
                                    <h3 class="text-md font-semibold mb-2">Informasi Antrian</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-700">
                                        <div>
                                            <span class="font-medium">Nomor Antrian:</span> {{ $antrian->kode }}-{{ $antrian->nomor_antrian }}
                                        </div>
                                        <div class="md:col-span-2">
                                            <span class="font-medium">Poliklinik:</span> {{ $antrian->poli->nama_poli ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Tanggal Kunjungan<span class="text-error">*</span></span>
                                    </label>
                                    <input type="date" wire:model.defer="tanggal_kunjungan" class="input input-bordered" />
                                    @error('tanggal_kunjungan') <span class="text-sm text-red-500">Harap tentukan tanggal kunjungan terlebih dahulu</span> @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Jenis Kunjungan<span class="text-error">*</span></span>
                                    </label>
                                    <select wire:model.defer="jenis_kunjungan" class="select select-bordered">
                                        <option value="">Pilih Jenis</option>
                                        <option value="sehat">Sehat</option>
                                        <option value="sakit">Sakit</option>
                                    </select>
                                    @error('jenis_kunjungan') <span class="text-sm text-red-500">Harap tentukan jenis kunjungan terlebih dahulu</span> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- SECTION: NAKES & POLI --}}
                        <div class="bg-base-100 shadow rounded-box p-6">
                            <h2 class="text-lg font-semibold mb-4 border-b pb-2">Tenaga Kesehatan & Poli</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                
                                {{-- Poli --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Pilih Poli<span class="text-error">*</span></span></label>
                                    <select wire:model.defer="poli_id" class="select select-bordered" @if($antrian_id) disabled @endif>
                                        <option value="">Pilih Poli</option>
                                        @foreach ($poli as $p)
                                            <option value="{{ $p->id }}">{{ $p->nama_poli }}</option>
                                        @endforeach
                                    </select>
                                    @error('poli_id') <span class="text-sm text-red-500">Harap tentukan poliklinik tujuan terlebih dahulu</span> @enderror
                                </div>

                                {{-- Dokter --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Pilih Dokter<span class="text-error">*</span></span></label>
                                    <select wire:model.defer="dokter_id" class="select select-bordered">
                                        <option value="">Pilih Dokter</option>
                                        @foreach ($dokter as $d)
                                            <option value="{{ $d->id }}">{{ $d->nama_dokter }}</option>
                                        @endforeach
                                    </select>
                                    @error('dokter_id') <span class="text-sm text-red-500">Harap tentukan dokter terlebih dahulu</span> @enderror
                                </div>

                            </div>
                        </div>

                        {{-- SECTION: DATA PASIEN --}}
                        <div class="bg-base-100 shadow rounded-box p-6">
                            <h2 class="text-lg font-semibold mb-4 border-b pb-2">Data Pasien</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <input type="hidden" name="id_user" wire:model.defer="pasien_id">
                                {{-- Nama --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Nama Lengkap</span></label>
                                    <p class="p-2 border rounded bg-gray-100">{{ $nama }}</p>
                                </div>

                                {{-- No Register --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">No. RM</span></label>
                                    <p class="p-2 border rounded bg-gray-100">{{ $no_register }}</p>
                                </div>

                                {{-- NIK --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">NIK</span></label>
                                    <p class="p-2 border rounded bg-gray-100">{{ $nik }}</p>
                                </div>

                                {{-- No IHS --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">No. IHS</span></label>
                                    <p class="p-2 border rounded bg-gray-100">{{ $no_ihs }}</p>
                                </div>

                                {{-- Alamat --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Alamat</span></label>
                                    <p class="p-2 border rounded bg-gray-100">{{ $alamat }}</p>
                                </div>

                                {{-- Telepon --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">No. Telepon</span></label>
                                    <p class="p-2 border rounded bg-gray-100">{{ $no_telp }}</p>
                                </div>

                                {{-- Jenis Kelamin --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Jenis Kelamin</span></label>
                                    <p class="p-2 border rounded bg-gray-100">{{ $jenis_kelamin }}</p>
                                </div>

                                {{-- Agama --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Agama</span></label>
                                    <p class="p-2 border rounded bg-gray-100">{{ $agama }}</p>
                                </div>

                                {{-- Profesi --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Profesi</span></label>
                                    <p class="p-2 border rounded bg-gray-100">{{ $profesi }}</p>
                                </div>

                                {{-- Tanggal Lahir --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Tanggal Lahir</span></label>
                                    <p class="p-2 border rounded bg-gray-100">{{ \Carbon\Carbon::parse($tanggal_lahir)->format('d-m-Y') }}</p>
                                </div>

                                {{-- Status --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Status</span></label>
                                    <p class="p-2 border rounded bg-gray-100">{{ $status }}</p>
                                </div>

                                {{-- Foto Pasien --}}
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Foto Pasien</span></label>
                                    @if ($foto_pasien instanceof \Livewire\TemporaryUploadedFile)
                                        <img src="{{ $foto_pasien->temporaryUrl() }}" alt="Foto Pasien" class="w-32 h-32 mt-2 rounded border object-cover" />
                                    @elseif ($foto_pasien_preview)
                                        <img src="{{ asset('storage/' . $foto_pasien_preview) }}" alt="Foto Pasien" class="w-32 h-32 mt-2 rounded border object-cover" />
                                    @else
                                        <p class="p-2 border rounded bg-gray-100 text-gray-500">Tidak ada foto</p>
                                    @endif
                                </div>

                                {{-- Deskripsi --}}
                                <div class="form-control md:col-span-2">
                                    <label class="label"><span class="label-text">Deskripsi Tambahan</span></label>
                                    <div class="p-2 border rounded bg-gray-100 min-h-[4rem]">{{ $deskripsi }}</div>
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
                            @can('akses', 'Pasien Registrasi')                                
                            <button wire:click="submit" class="btn btn-success w-full" wire:loading.attr="disabled">
                                <span wire:loading.remove><i class="fa-solid fa-plus"></i> Simpan</span>
                                <span wire:loading.inline>Loading...</span>
                            </button>
                            @endcan
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