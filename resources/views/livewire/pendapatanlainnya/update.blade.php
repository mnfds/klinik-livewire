<div>
    <dialog id="modaleditpendapatan" class="modal" wire:ignore.self x-data x-init="
        Livewire.on('closemodaleditpendapatan', () => {
            document.getElementById('modaleditpendapatan')?.close()
        })">
        <div class="modal-box w-full max-w-md">
            <h3 class="text-xl font-semibold mb-4">Edit Pendapatan</h3>

            <form wire:submit.prevent="updatePendapatan" class="space-y-4">
                <div x-data="rupiahInputPendapatan('total_tagihan', 'setJumlahPendapatan')" x-init="init()">
                    <label class="label font-medium">Total Tagihan<span class="text-error">*</span></label>
                    <input type="text" x-model="display" @input="onInput" inputmode="numeric"
                        @disabled($isPartOfGroup)
                        class="input input-bordered w-full @error('total_tagihan') input-error @enderror">
                    @if ($isPartOfGroup)
                        <span class="text-xs text-gray-500">Total tagihan tidak bisa diubah karena transaksi ini sudah memiliki riwayat pelunasan.</span>
                    @endif
                    @error('total_tagihan')
                        <span class="text-error text-sm">Mohon Mengisi Total Tagihan Dengan Benar</span>
                    @enderror
                </div>

                <div x-data="rupiahInputPendapatan('total_dibayarkan', 'setJumlahDibayarkanPendapatan')" x-init="init()">
                    <label class="label font-medium">Jumlah Dibayarkan (baris ini)<span class="text-error">*</span></label>
                    <input type="text" x-model="display" @input="onInput" inputmode="numeric" class="input input-bordered w-full @error('total_dibayarkan') input-error @enderror">
                    @error('total_dibayarkan')
                        <span class="text-error text-sm">Mohon Mengisi Jumlah Dibayarkan Dengan Benar</span>
                    @enderror
                </div>

                <div>
                    <label class="label font-medium">Keterangan<span class="text-error">*</span></label>
                    <textarea wire:model.defer="keterangan" class="textarea textarea-bordered w-full @error('keterangan') input-error @enderror" rows="3"></textarea>
                    @error('keterangan')
                        <span class="text-error text-sm">Mohon Mengisi Keterangan Dengan Benar</span>
                    @enderror
                </div>

                <div>
                    <label class="label font-medium">Unit Usaha Pengaju<span class="text-error">*</span></label>
                    <select class="select select-bordered w-full @error('unit_usaha') input-error @enderror" wire:model.defer="unit_usaha">
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
                    <select class="select select-bordered w-full @error('metode_pembayaran') input-error @enderror" wire:model.defer="metode_pembayaran">
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
                    @can('akses', 'Pendapatan Edit')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    @endcan
                    <button type="button" class="btn btn-error" onclick="document.getElementById('modaleditpendapatan').close()">Batal</button>
                </div>
            </form>
        </div>
    </dialog>
</div>
<script>
    function rupiahInputPendapatan(field, eventName) {
        return {
            display: '',
            field: field,
            eventName: eventName,

            init() {
                Livewire.on(this.eventName, value => {
                    this.display = this.formatRupiah(value)
                })
            },

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