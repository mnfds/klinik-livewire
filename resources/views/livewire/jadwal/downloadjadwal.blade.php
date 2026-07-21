<dialog id="downloadModal" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closedownloadModal', () => {
        document.getElementById('downloadModal')?.close()
    })
    ">
    <div class="modal-box w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Unduh Jadwal</h3>

        <form wire:submit.prevent="unduh" class="space-y-4">
                        
            <div>
                <label class="label font-medium">Bulan dan tahun</label>
                <input type="month" wire:model='bulanini' class="input input-bordered w-full @error ('bulanini') input-error @enderror">
            </div>
            <div>
                <label class="label font-medium">Divisi User<span class="text-error">*</span></label>
                <select class="select select-bordered w-full @error('divisi') input-error @enderror" wire:model.lazy="divisi">
                    <option value="">Pilih Kondisi</option>
                    <option value="semua">Semua Divisi</option>
                    @foreach ($role as $r)
                        <option value="{{ $r->nama_role }}">{{ $r->nama_role }}</option>
                    @endforeach
                </select>
                @error('divisi')
                    <span class="text-error text-sm">Mohon Memilih Role</span>
                @enderror
            </div>

            <div class="modal-action justify-end pt-4">
                @can('akses', 'Jadwal Download')
                <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                <button type="button" class="btn btn-error" onclick="document.getElementById('downloadModal').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>