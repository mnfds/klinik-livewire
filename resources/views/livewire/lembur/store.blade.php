<dialog id="storeModalLembur" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closestoreModalLembur', () => {
        document.getElementById('storeModalLembur')?.close()
    })
    ">
    <div class="modal-box w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Pengajuan Lembur</h3>

        <form wire:submit.prevent="store" class="space-y-4">
            {{-- Nama Karyawan --}}
            <div x-data="{ open: false }" class="relative">
                <label class="label font-medium">Karyawan <span class="text-error">*</span></label>
                @can('akses', 'Persetujuan Ajuan Lembur')
                    <div
                        x-data="{
                            open: false,
                            search: '',
                            users: {{ \Illuminate\Support\Js::from($users) }},
                            selectedId: @entangle('user_id'),

                            get filtered() {
                                return this.search === ''
                                    ? this.users
                                    : this.users.filter(b =>
                                        b.nama.toLowerCase().includes(this.search.toLowerCase())
                                    )
                            },

                            get selectedLabel() {
                                let b = this.users.find(b => b.id == this.selectedId)
                                return b ? b.nama : ''
                            },

                            choose(item) {
                                this.selectedId = item.id
                                this.search = item.nama
                                this.open = false
                            }
                        }"
                        x-init="search = selectedLabel"
                        @click.outside="open = false; search = selectedLabel"
                        >
                        <input type="text" x-model="search" @focus="open = true; search = ''" placeholder="Cari nama karyawan..." autocomplete="off" class="input input-bordered w-full @error('user_id') input-error @enderror">

                        <ul x-show="open" x-cloak class="absolute z-50 mt-1 w-full max-h-60 overflow-y-auto bg-base-100 border border-base-300 rounded-box shadow">
                            <template x-for="item in filtered" :key="item.id">
                                <li @click="choose(item)" class="px-4 py-2 cursor-pointer hover:bg-base-200" x-text="item.nama"></li>
                            </template>
                            <li x-show="filtered.length === 0" class="px-4 py-2 text-gray-400 text-sm">
                                Tidak ditemukan
                            </li>
                        </ul>
                    </div>
                @else
                    <input type="text" value="{{ $users[array_search(auth()->id(), array_column($users, 'id'))]['nama'] ?? '-' }}" readonly class="input input-bordered w-full bg-base-200 cursor-not-allowed">
                @endcan
                @error('user_id')
                    <span class="text-error text-sm">Mohon Memilih Karyawan Dengan Benar</span>
                @enderror
            </div>

            <div class="space-y-2">
                <label class="label font-medium"><span class="label-text">Tanggal & Waktu Lembur<span class="text-error">*</span></span></label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <input type="date" class="input input-bordered w-full @error('tanggal_lembur') input-error @enderror" wire:model.lazy="tanggal_lembur" >
                        <span class="text-xs text-gray-500 ml-1">Tanggal Lembur</span><br>
                        @error('tanggal_lembur')
                            <span class="text-error text-sm">
                                Mohon Mengisi Tanggal Lembur Dengan Benar
                            </span>
                        @enderror
                    </div>
                    <div>
                        <input max="6" min="1" type="number" class="input input-bordered w-full @error('perkiraan_durasi') input-error @enderror" wire:model.lazy="perkiraan_durasi" >
                        <span class="text-xs text-gray-500 ml-1">Durasi Lembur</span><br>
                        @error('perkiraan_durasi')
                            <span class="text-error text-sm">
                                Mohon Mengisi Waktu Lembur Dengan Benar
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div>
                <label class="label font-medium">Keperluan<span class="text-error">*</span></label>
                <textarea wire:model.lazy="keperluan" class="textarea textarea-bordered w-full @error('keperluan') input-error @enderror" rows="3"></textarea>
                @error('keperluan')
                    <span class="text-error text-sm">
                        Mohon Mengisi Keperluan Yang Dilakukan Hingga Lembur
                    </span>
                @enderror
            </div>

            <div class="modal-action justify-end pt-4">
                @can('akses', 'Pengajuan Lembur Tambah')
                <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                <button type="button" class="btn btn-error" onclick="document.getElementById('storeModalLembur').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>