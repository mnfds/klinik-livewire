<div class="bg-base-200 p-4 rounded border border-base-200">
    <div class="divider">Data Kesehatan</div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Keluhan Utama -->
        <div class="form-control md:col-span-2">
            <label class="label"><span class="label-text">Keluhan Utama</span></label>
            <input type="text" wire:model="keluhan_utama" placeholder="Keluhan Utama" class="input input-bordered w-full" />
        </div>

        <!-- Status Perokok -->
        <div class="form-control">
            <label class="label"><span class="label-text">Status Perokok</span></label>
            <select wire:model="status_perokok" class="select select-bordered w-full">
                <option value="">Pilih Status</option>
                <option value="tidak">Tidak</option>
                <option value="iya">Iya</option>
                <option value="berhenti">Berhenti</option>
            </select>
        </div>

        <!-- Riwayat Penyakit -->
        <div class="form-control" x-data="multiSelect()" x-init="init()">
            <label class="label">
                <span class="label-text font-semibold text-sm text-gray-700">Riwayat Penyakit</span>
            </label>

            <!-- Input Area -->
            <div class="relative">
                <div
                    class="w-full border border-none rounded-2xl p-1 flex flex-wrap items-center gap-2 min-h-[2.5rem] focus-within:ring-2 focus-within:ring-black transition" :class="{ 'ring-2 ring-black': open }" @click="open = true">
                    <!-- Selected tags -->
                    <template x-for="(tag, index) in selected" :key="index">
                        <span class="bg-primary text-base-content text-sm rounded-full px-3 py-1 flex items-center gap-1">
                            <span x-text="tag"></span>
                            <button type="button" class="text-red-500 font-bold text-xs" @click.stop="remove(tag)">×</button>
                        </span>
                    </template>

                    <!-- Input for search -->
                    <input type="text" class="flex-grow min-w-[8ch] text-sm border-black rounded-xl placeholder-gray-400" placeholder="Ketik untuk cari..." x-model="search" @focus="open = true" @input="open = true" />
                </div>

                <!-- Dropdown Menu -->
                <div x-show="open" @click.outside="open = false" class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                    <!-- Jika ada hasil -->
                    <template x-if="filteredOptions.length > 0">
                        <template x-for="(item, index) in filteredOptions" :key="index">
                            <div
                                @click="toggle(item)"
                                class="px-3 py-2 hover:bg-blue-100 cursor-pointer text-sm"
                                :class="selected.includes(item) ? 'bg-blue-100 font-semibold' : ''"
                            >
                                <span x-text="item"></span>
                            </div>
                        </template>
                    </template>

                    <!-- Jika tidak ada hasil -->
                    <div x-show="filteredOptions.length === 0" class="px-3 py-2 text-sm text-gray-400">
                        Tidak ada hasil.
                    </div>
                </div>
            </div>

            <!-- Hidden binding untuk Livewire -->
            <input type="hidden" wire:model="riwayat_penyakit" x-model="selected">

            <span class="text-xs text-gray-400 mt-1">* Klik untuk pilih, klik ulang untuk hapus</span>
        </div>

    </div>
</div>
@push('scripts')
<script>
    function multiSelect() {
        return {
            open: false,
            selected: @entangle('riwayat_penyakit'),
            options: @js($listPenyakit),
            search: '',

            get filteredOptions() {
                if (this.search === '') return this.options;
                return this.options.filter(item =>
                    item.toLowerCase().includes(this.search.toLowerCase())
                );
            },

            init() {
                if (!Array.isArray(this.selected)) {
                    this.selected = [];
                }
            },

            toggle(item) {
                const index = this.selected.indexOf(item);
                if (index === -1) {
                    this.selected.push(item);
                } else {
                    this.selected.splice(index, 1);
                }
                this.search = ''; // reset setelah pilih
            },

            remove(item) {
                const index = this.selected.indexOf(item);
                if (index !== -1) {
                    this.selected.splice(index, 1);
                }
            }
        }
    }
</script>
@endpush