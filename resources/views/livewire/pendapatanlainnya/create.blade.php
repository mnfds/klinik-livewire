<dialog id="storePendapatan" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closestorePendapatan', () => {
        document.getElementById('storePendapatan')?.close()
    })
">
    <div class="modal-box w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Pendapatan</h3>

        <form wire:submit.prevent="storePendapatan" class="space-y-4">
            <div x-data="rupiahInputFormat()">
                <label class="label font-medium">Jumlah Uang<span class="text-error">*</span></label>
                <input type="text" x-model="display" @input="onInputUang" inputmode="numeric" class="input input-bordered w-full @error('total_tagihan') input-error @enderror">
                @error('total_tagihan')
                    <span class="text-error text-sm">Mohon Mengisi Jumlah Uang Yang Didapat Dengan Benar</span>
                @enderror
            </div>

            <div>
                <label class="label font-medium">Keterangan<span class="text-error">*</span></label>
                <textarea wire:model.lazy="keterangan" class="textarea textarea-bordered w-full @error('keterangan') input-error @enderror" rows="3"></textarea>
                @error('keterangan')
                    <span class="text-error text-sm">Mohon Mengisi Keterangan Dengan Benar</span>
                @enderror
            </div>

            <div>
                <label class="label font-medium">Unit Usaha Pengaju<span class="text-error">*</span></label>
                <select class="select select-bordered w-full @error('unit_usaha') input-error @enderror" wire:model.lazy="unit_usaha">
                    <option value="">Pilih Unit</option>
                    <option value="Klinik">Klinik</option>
                    <option value="Apotik">Apotik</option>
                    <option value="Sewa Multifunction">Sewa Multifunction</option>
                    <option value="Coffeshop">Coffeshop</option>
                    <option value="Dll">Dll</option>
                </select>
                @error('unit_usaha')
                    <span class="text-error text-sm">Mohon Memilih Unit Usaha Dengan Benar</span>
                @enderror
            </div>

            <div>
                <label class="label font-medium">Status<span class="text-error">*</span></label>
                <select class="select select-bordered w-full @error('status') input-error @enderror" wire:model.lazy="status">
                    <option value="">Pilih</option>
                    <option value="belum lunas">Belum Lunas</option>
                    <option value="lunas">Lunas</option>
                    <option value="belum bayar">Belum Bayar</option>
                    {{-- <option value="batal">Batal</option> --}}
                </select>
                @error('status')
                    <span class="text-error text-sm"> Mohon Mengisi Status Dengan Benar</span>
                @enderror
            </div>

            <div class="modal-action justify-end pt-4">
                @can('akses', 'Pendapatan Tambah')
                <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                <button type="button" class="btn btn-error" onclick="document.getElementById('storePendapatan').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>
<script>
    function rupiahInputFormat() {
        return {
            display: '',

            onInputUang() {
                let angkaUang = this.display.replace(/[^0-9]/g, '')

                this.$wire.set(
                    'total_tagihan',
                    angkaUang === '' ? null : Number(angkaUang)
                )

                this.display = this.formatRupiahUang(angkaUang)
            },

            formatRupiahUang(angkaUang) {
                if (!angkaUang) return ''
                return 'Rp ' + angkaUang.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
            }
        }
    }
</script>