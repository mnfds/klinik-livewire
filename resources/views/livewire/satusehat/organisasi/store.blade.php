<div>
    <form wire:submit.prevent="store" class="space-y-4">
        {{-- Departemen + Kota --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
                <label class="label font-semibold">Organisasi</label>
                <select wire:model.lazy="departemen" class="select select-bordered">
                    <option value="">Pilih Organisasi</option>
                    @foreach ($poli as $p)
                        <option value="{{ $p->nama_poli }}">{{ $p->nama_poli }}</option>
                    @endforeach
                </select>
                @error('departemen') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>
            <div class="form-control">
                <label class="label font-semibold">Kota</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="kota">
            </div>
        </div>

        {{-- Alamat --}}
        <div class="form-control">
            <label class="label font-semibold">Alamat</label>
            <textarea class="textarea textarea-bordered w-full" rows="2" wire:model.lazy="alamat"></textarea>
        </div>

        {{-- Kode Pos + No Telepon --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
                <label class="label font-semibold">Kode Pos</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="kode_pos">
            </div>

            <div class="form-control">
                <label class="label font-semibold">Nomor Telepon</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="no_telp">
            </div>
        </div>

        {{-- Email + Url Web + Status --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
                <label class="label font-semibold">Email</label>
                <input type="email" class="input input-bordered w-full" wire:model.lazy="email">
            </div>

            <div class="form-control">
                <label class="label font-semibold">Web</label>
                <input type="string" class="input input-bordered w-full" wire:model.lazy="web">
            </div>

            {{-- <div class="form-control">
                <label class="label font-semibold">Status</label>
                <select class="select select-bordered w-full" wire:model.lazy="status">
                    <option value="">-- Pilih Status --</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div> --}}
        </div>

        {{-- Tombol --}}
        <div class="modal-action flex justify-end gap-2 pt-4">
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>