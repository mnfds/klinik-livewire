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
                        <a wire:navigate href="{{ route('dokter.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Update Dokter
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Edit Data {{ $nama_dokter }}
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 shadow rounded-box">
                <form wire:submit.prevent="update" class="p-6 space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Poli --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Poli</span></label>
                            <select wire:model.defer="poli_id" class="select select-bordered w-full">
                                <option value="">Pilih Poli</option>
                                @foreach ($poli as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_poli }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('poli_id')" class="text-error text-sm mt-1" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        {{-- Nama Lengkap --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Nama Lengkap</span></label>
                            <input wire:model.defer="nama_dokter" type="text" class="input input-bordered w-full" />
                            <x-input-error :messages="$errors->get('nama_dokter')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- NIK --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">NIK</span></label>
                            <input wire:model.defer="nik" type="text" class="input input-bordered w-full" />
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
                            <input wire:model.defer="telepon" type="text" class="input input-bordered w-full" />
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
                                <option value="">Pilih Pendidikan</option>
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
                            <input type="file" wire:model="new_foto_wajah" class="file-input file-input-bordered w-full" />
                            <div class="mt-2 text-sm text-gray-500 flex items-center gap-2" wire:loading wire:target="new_foto_wajah">
                                <span class="loading loading-spinner loading-sm text-info"></span>
                                <span>Mengunggah foto...</span>
                            </div>
                            <x-input-error :messages="$errors->get('new_foto_wajah')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Upload TTD --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text">Unggah Tanda Tangan Digital</span></label>
                            <input type="file" wire:model="new_ttd_digital" class="file-input file-input-bordered w-full" />
                            <div class="mt-2 text-sm text-gray-500 flex items-center gap-2" wire:loading wire:target="new_ttd_digital">
                                <span class="loading loading-spinner loading-sm text-info"></span>
                                <span>Mengunggah tanda tangan...</span>
                            </div>
                            <x-input-error :messages="$errors->get('new_ttd_digital')" class="text-error text-sm mt-1" />
                        </div>

                        {{-- Preview Foto --}}
                        <div class="form-control col-span-full">
                            <label class="label"><span class="label-text">Preview Foto</span></label>
                            @if ($new_foto_wajah)
                                <img src="{{ $new_foto_wajah->temporaryUrl() }}" class="w-32 h-32 mt-2 rounded border object-cover" />
                            @elseif ($foto_wajah_preview)
                                <img src="{{ asset('storage/' . $foto_wajah_preview) }}" class="w-32 h-32 mt-2 rounded border object-cover" />
                            @endif
                        </div>

                        {{-- Preview TTD --}}
                        <div class="form-control col-span-full">
                            <label class="label"><span class="label-text">Preview Tanda Tangan</span></label>
                            @if ($new_ttd_digital)
                                <img src="{{ $new_ttd_digital->temporaryUrl() }}" class="w-32 h-32 mt-2 rounded border object-cover" />
                            @elseif ($ttd_digital_preview)
                                <img src="{{ asset('storage/' . $ttd_digital_preview) }}" class="w-32 h-32 mt-2 rounded border object-cover" />
                            @endif
                        </div>
                    </div>

                    {{-- Tombol Simpan --}}
                    <div class="pt-4 text-right border-t">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>