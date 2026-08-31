<dialog id="modaleditpengajuancuti" class="modal" wire:ignore.self x-data x-init="Livewire.on('closemodaleditpengajuancuti', () => { document.getElementById('modaleditpengajuancuti')?.close() })">
    <style>
        #modaleditpengajuancuti .flatpickr-wrapper {
            width: 100%;
        }
    </style>
    <div class="modal-box w-full max-w-lg overflow-visible">
        <h3 class="text-xl font-semibold mb-4">Edit Pengajuan Cuti</h3>
        <form wire:submit.prevent="update" class="space-y-4">

            {{-- Range Tanggal Cuti --}}
            <div
                wire:ignore class="form-control relative"
                x-data="{
                    mulai: @entangle('tanggal_mulai'),
                    selesai: @entangle('tanggal_selesai'),
                    fp: null,
                    init() {
                        this.fp = flatpickr(this.$refs.input, {
                            mode: 'range',
                            dateFormat: 'Y-m-d',
                            static: true,
                            defaultDate: [this.mulai, this.selesai].filter(Boolean),
                            onClose: (selectedDates) => {
                                if (selectedDates.length === 2) {
                                    this.mulai = this.fp.formatDate(selectedDates[0], 'Y-m-d');
                                    this.selesai = this.fp.formatDate(selectedDates[1], 'Y-m-d');
                                }
                            }
                        });

                        // saat modal dibuka utk row lain, mulai/selesai berubah dari server
                        // (via listener getupdatepengajuancuti) -> sinkronkan tampilan flatpickr
                        this.$watch('mulai', () => this.syncPicker());
                        this.$watch('selesai', () => this.syncPicker());
                    },
                    syncPicker() {
                        if (this.mulai && this.selesai) {
                            this.fp.setDate([this.mulai, this.selesai], false);
                        }
                    }
                }"
                >
                <label class="label font-medium block">Tanggal Cuti <span class="text-error">*</span></label>
                <input type="text" x-ref="input" readonly autocomplete="off"
                    placeholder="Pilih rentang tanggal"
                    class="input input-bordered w-full @error('tanggal_mulai') input-error @enderror">
                @error('tanggal_mulai')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
                @error('tanggal_selesai')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- Combobox Nama Karyawan --}}
            <div
                x-data="{
                    open: false,
                    search: '',
                    users: {{ \Illuminate\Support\Js::from($users) }},
                    selectedId: @entangle('user_id'),
                    get filtered() {
                        return this.search === ''
                            ? this.users
                            : this.users.filter(b => b.nama.toLowerCase().includes(this.search.toLowerCase()))
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
                class="relative"
                >
                <label class="label font-medium">
                    Nama Karyawan <span class="text-error">*</span>
                </label>

                <input
                    type="text"
                    x-model="search"
                    @focus="open = true; search = ''"
                    placeholder="Cari nama karyawan..."
                    autocomplete="off"
                    class="input input-bordered w-full @error('user_id') input-error @enderror"
                >

                <ul x-show="open" x-cloak
                    class="absolute z-50 mt-1 w-full max-h-60 overflow-y-auto bg-base-100 border border-base-300 rounded-box shadow">
                    <template x-for="item in filtered" :key="item.id">
                        <li @click="choose(item)"
                            class="px-4 py-2 cursor-pointer hover:bg-base-200"
                            x-text="item.nama">
                        </li>
                    </template>
                    <li x-show="filtered.length === 0" class="px-4 py-2 text-gray-400 text-sm">
                        Tidak ditemukan
                    </li>
                </ul>

                @error('user_id')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- Alasan --}}
            <div>
                <label class="label font-medium">Alasan <span class="text-error">*</span></label>
                <textarea class="textarea textarea-bordered w-full @error('alasan') textarea-error @enderror" rows="2" wire:model.lazy="alasan" placeholder="Alasan cuti..."></textarea>
                @error('alasan')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="modal-action justify-end pt-4">
                @can('akses', 'Pengajuan Cuti')
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                @endcan
                <button type="button" class="btn btn-error" onclick="document.getElementById('modaleditpengajuancuti').close()">
                    Batal
                </button>
            </div>
        </form>
    </div>

    {{-- Backdrop --}}
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>