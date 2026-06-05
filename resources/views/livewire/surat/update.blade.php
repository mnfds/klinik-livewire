<dialog id="modalupdatesurat" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeModal', () => {
        document.getElementById('modalupdatesurat')?.close()
    })
">
    <div class="modal-box w-full max-w-2xl">
        <h3 class="text-xl font-semibold mb-4">Edit Surat Keterangan</h3>

        <form wire:submit.prevent="update" class="space-y-4">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label font-medium">Mulai Berlaku <span class="text-error">*</span></label>
                    <input type="date" class="input input-bordered w-full @error('mulai_berlaku') input-error @enderror"
                        wire:model="mulai_berlaku">
                    @error('mulai_berlaku')
                        <span class="text-error text-sm mt-1">Mohon mengisi tanggal</span>
                    @enderror
                </div>
                <div>
                    <label class="label font-medium">Selesai Berlaku <span class="text-error">*</span></label>
                    <input type="date" class="input input-bordered w-full @error('selesai_berlaku') input-error @enderror"
                        wire:model="selesai_berlaku">
                    @error('selesai_berlaku')
                        <span class="text-error text-sm mt-1">Mohon mengisi tanggal</span>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label font-medium">Jenis Tanda Tangan <span class="text-error">*</span></label>
                    <select class="select select-bordered w-full @error('tipe_ttd') select-error @enderror"
                        wire:model="tipe_ttd">
                        <option value="">Pilih Tanda Tangan</option>
                        <option value="digital">Digital</option>
                        <option value="basah">Basah</option>
                    </select>
                    @error('tipe_ttd')
                        <span class="text-error text-sm mt-1">Mohon memilih jenis tanda tangan</span>
                    @enderror
                </div>
                <div>
                    <label class="label font-medium">Jenis Surat <span class="text-error">*</span></label>
                    <select class="select select-bordered w-full @error('jenis_surat') select-error @enderror"
                        wire:model.live="jenis_surat">
                        <option value="">Pilih Jenis Surat</option>
                        <option value="standar">Surat Sehat Standar</option>
                        <option value="lengkap">Surat Sehat Lengkap</option>
                        <option value="sakit">Surat Sakit</option>
                    </select>
                    @error('jenis_surat')
                        <span class="text-error text-sm mt-1">Mohon memilih jenis surat</span>
                    @enderror
                </div>
            </div>

            @if($jenis_surat === 'sakit')
            <div wire:key="field-sakit-update">
                <label class="label font-medium">Keterangan Sakit <span class="text-error">*</span></label>
                <textarea class="textarea textarea-bordered w-full @error('sakit') textarea-error @enderror"
                    wire:model.lazy="sakit" rows="3"
                    placeholder="Contoh: Demam, batuk, dan pilek selama 3 hari"></textarea>
                @error('sakit')
                    <span class="text-error text-sm mt-1">Mohon mengisi keterangan sakit</span>
                @enderror
            </div>
            @endif

            <div>
                <label class="label font-medium">Harga Surat</label>
                <input type="text" class="input input-bordered w-full" wire:model="harga_surat" placeholder="0"
                    x-data
                    x-on:input="
                        let v = $el.value.replace(/\D/g,'');
                        $el.value = v.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                        $wire.set('harga_surat', v);
                    ">
            </div>

            <div class="modal-action justify-end mt-6">
                {{-- @can('akses', 'Surat Keterangan Edit') --}}
                <button type="submit" class="btn btn-primary">Simpan</button>
                {{-- @endcan --}}
                <button type="button" class="btn btn-neutral"
                    onclick="document.getElementById('modalupdatesurat').close()">Batal</button>
            </div>

        </form>
    </div>
</dialog>