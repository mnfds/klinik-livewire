<dialog id="storeModalCuti" class="modal" wire:ignore.self x-data x-init="Livewire.on('closestoreModalCuti', () => { document.getElementById('storeModalCuti')?.close() })">
    <style>
        #storeModalCuti .flatpickr-wrapper {
            width: 100%;
        }
    </style>
    <div class="modal-box w-full max-w-lg overflow-visible">
        <h3 class="text-xl font-semibold mb-4">Pengajuan Cuti</h3>
        <form wire:submit.prevent="store" class="space-y-4">
            {{-- Tambah Tab --}}
            <div>
                <button type="button" class="btn btn-primary btn-sm" wire:click="addTab">
                    + Tambah Form
                </button>
            </div>

            {{-- Tab Headers --}}
            <div role="tablist" class="tabs tabs-bordered flex-wrap">
                @foreach ($items as $i => $item)
                    <button type="button" role="tab" class="tab gap-1 {{ $activeTab === $i ? 'tab-active border-b-2 border-primary text-primary' : 'border-b-2 border-transparent' }}" wire:click="$set('activeTab', {{ $i }})">
                        <span>
                            Form {{ $i + 1 }}
                            @if ($errors->has("items.$i.user_id") || $errors->has("items.$i.tanggal_mulai") || $errors->has("items.$i.alasan"))
                                <span class="inline-block w-2 h-2 rounded-full bg-error ml-1 align-middle"></span>
                            @endif
                        </span>
                        @if (count($items) > 1)
                            <span role="button" class="ml-1 text-base-content/40 hover:text-error transition-colors" wire:click.stop="removeTab({{ $i }})">
                                <i class="fa-solid fa-circle-xmark"></i>
                            </span>
                        @endif
                    </button>
                @endforeach
            </div>

            {{-- Tab Content --}}
            @foreach ($items as $i => $item)
                <div @class(['hidden' => $activeTab !== $i, 'space-y-4' => true])>
                    {{-- Range Tanggal Cuti --}}
                    <div
                        wire:key="tanggal-range-{{ $i }}"
                        wire:ignore class="form-control relative"
                        x-data="{
                            mulai: @entangle("items.{$i}.tanggal_mulai"),
                            selesai: @entangle("items.{$i}.tanggal_selesai"),
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
                            }
                        }"
                        >
                        <label class="label font-medium block">Tanggal Cuti <span class="text-error">*</span></label>
                        <input type="text" x-ref="input" readonly autocomplete="off"
                            placeholder="Pilih rentang tanggal"
                            class="input input-bordered w-full @error("items.$i.tanggal_mulai") input-error @enderror">
                    </div>
                    @error("items.$i.tanggal_mulai")
                        <span class="text-error text-sm">{{ $message }}</span>
                    @enderror
                    @error("items.$i.tanggal_selesai")
                        <span class="text-error text-sm">{{ $message }}</span>
                    @enderror
                        
                    {{-- Nama Karyawan --}}
                    <div
                        wire:key="user-combobox-{{ $i }}"
                        class="relative"
                        >
                        <label class="label font-medium">Nama Karyawan <span class="text-error">*</span></label>
                        @can('akses', 'Persetujuan Pengajuan Cuti')
                            {{-- Jika memiliki akses: bisa memilih karyawan --}}
                            <div
                                x-data="{
                                    open: false,
                                    search: '',
                                    users: {{ \Illuminate\Support\Js::from($users) }},
                                    selectedId: @entangle("items.{$i}.user_id"),

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
                                <input type="text" x-model="search" @focus="open = true; search = ''" placeholder="Cari nama karyawan..." autocomplete="off" class="input input-bordered w-full @error("items.$i.user_id") input-error @enderror">

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

                            {{-- Jika tidak memiliki akses: hanya bisa menggunakan akun sendiri --}}
                            <input
                                type="text"
                                value="{{ $users[array_search(auth()->id(), array_column($users, 'id'))]['nama'] ?? '-' }}"
                                readonly
                                class="input input-bordered w-full bg-base-200 cursor-not-allowed"
                            >

                        @endcan

                        @error("items.$i.user_id")
                            <span class="text-error text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Alasan --}}
                    <div>
                        <label class="label font-medium">Alasan <span class="text-error">*</span></label>
                        <textarea class="textarea textarea-bordered w-full @error("items.$i.alasan") textarea-error @enderror" rows="2" wire:model.lazy="items.{{ $i }}.alasan" placeholder="Alasan cuti..."></textarea>
                        @error("items.$i.alasan")
                            <span class="text-error text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            @endforeach

            {{-- Actions --}}
            <div class="modal-action justify-end pt-4">
                @can('akses', 'Pengajuan Cuti')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                <button type="button" class="btn btn-error" onclick="document.getElementById('storeModalCuti').close()">
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