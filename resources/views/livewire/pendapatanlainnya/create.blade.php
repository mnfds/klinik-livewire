<dialog id="storePendapatan" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closestorePendapatan', () => {
        document.getElementById('storePendapatan')?.close()
    })
">
    <div class="modal-box w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Pendapatan</h3>

        <form wire:submit.prevent="storePendapatan" class="space-y-4">
            <div x-data="rupiahInputFormat('total_tagihan')">
                <label class="label font-medium">Total Tagihan<span class="text-error">*</span></label>
                <input type="text" x-model="display" @input="onInput" inputmode="numeric" class="input input-bordered w-full @error('total_tagihan') input-error @enderror">
                @error('total_tagihan')
                    <span class="text-error text-sm">Mohon Mengisi Total Tagihan Dengan Benar</span>
                @enderror
            </div>

            <div x-data="rupiahInputFormat('total_dibayarkan')">
                <label class="label font-medium">Jumlah Dibayarkan<span class="text-error">*</span></label>
                <input type="text" x-model="display" @input="onInput" inputmode="numeric" class="input input-bordered w-full @error('total_dibayarkan') input-error @enderror">
                @error('total_dibayarkan')
                    <span class="text-error text-sm">Mohon Mengisi Jumlah Dibayarkan Dengan Benar (tidak boleh lebih dari total tagihan)</span>
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
                <label class="label font-medium">Metode Pembayaran<span class="text-error">*</span></label>
                <select class="select select-bordered w-full @error('metode_pembayaran') input-error @enderror" wire:model.lazy="metode_pembayaran">
                        <option value="">Pembayaran</option>
                        <option value="Tunai">Tunai</option>
                        <option value="Qris">Qris</option>
                        <option value="ShopeePay">ShopeePay</option>
                        <option value="Mandiri">Mandiri</option>
                        <option value="BCA">BCA</option>
                        <option value="BRI">BRI</option>
                        <option value="BNI">BNI</option>
                </select>
                @error('metode_pembayaran')
                    <span class="text-error text-sm">Mohon Memilih Metode Pembayaran Dengan Benar</span>
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
    function rupiahInputFormat(field) {
        return {
            display: '',
            field: field,

            onInput() {
                let angka = this.display.replace(/[^0-9]/g, '')

                this.$wire.set(this.field, angka === '' ? null : Number(angka))

                this.display = this.formatRupiah(angka)
            },

            formatRupiah(angka) {
                if (!angka) return ''
                return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
            }
        }
    }
</script>