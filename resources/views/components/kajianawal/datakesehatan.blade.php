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

            <!-- Input Trigger -->
            <div class="relative">
                <div class="input input-bordered w-full text-sm min-h-[2.5rem] flex flex-wrap items-center gap-1 p-2 cursor-pointer" @click="open = !open">
                    <template x-for="(tag, index) in selected" :key="index">
                        <div class="badge badge-info badge-outline flex items-center gap-1">
                            <span x-text="tag"></span>
                            <button type="button" class="text-white ml-1" @click.stop="remove(tag)">×</button>
                        </div>
                    </template>
                    <input type="text" class="bg-transparent flex-1 outline-none" readonly placeholder="Klik untuk pilih..." />
                </div>

                <!-- Dropdown Menu -->
                <div x-show="open" @click.outside="open = false"
                    class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                    <template x-for="(item, index) in options" :key="index">
                        <div @click="toggle(item)"
                            class="px-3 py-2 hover:bg-blue-100 cursor-pointer text-sm"
                            :class="selected.includes(item) ? 'bg-blue-100 font-semibold' : ''">
                            <span x-text="item"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Hidden Livewire binding -->
            <input type="hidden" id="riwayatPenyakitInput" x-model="selected" x-ref="hidden">

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
            },

            remove(item) {
                const index = this.selected.indexOf(item);
                if (index !== -1) {
                    this.selected.splice(index, 1);
                }
            },
        }
    }
</script>
@endpush