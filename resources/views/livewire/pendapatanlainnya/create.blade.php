<dialog id="storePendapatan" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closestorePendapatan', () => {
        document.getElementById('storePendapatan')?.close()
    })
">
    <div class="modal-box w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Tambah Pendapatan Lainnya</h3>

        <form wire:submit.prevent="storePendapatan" class="space-y-4">
            <div x-data="rupiahInputFormat()">
                <label class="label font-medium">Jumlah Uang</label>
                <input type="text" x-model="display" @input="onInputUang" inputmode="numeric" class="input input-bordered w-full">
            </div>

            <div>
                <label class="label font-medium">Keterangan</label>
                <textarea wire:model.lazy="keterangan" class="textarea textarea-bordered w-full" rows="3"></textarea>
            </div>

            <div>
                <label class="label font-medium">Unit Usaha Pengaju</label>
                <select class="select select-bordered w-full" wire:model.lazy="unit_usaha">
                    <option value="">Pilih Unit</option>
                    <option value="Klinik">Klinik</option>
                    <option value="Apotik">Apotik</option>
                    <option value="Sewa Multifunction">Sewa Multifunction</option>
                    <option value="Coffeshop">Coffeshop</option>
                    <option value="Dll">Dll</option>
                </select>
            </div>

            <div>
                <label class="label font-medium">Status</label>
                <select class="select select-bordered w-full" wire:model.lazy="status">
                    <option value="">Pilih</option>
                    <option value="belum lunas">Belum Lunas</option>
                    <option value="lunas">Lunas</option>
                    <option value="belum bayar">Belum Bayar</option>
                    <option value="batal">Batal</option>
                </select>
            </div>

            <div class="modal-action justify-end pt-4">
                @can('akses', 'Pengajuan Pengeluaran Tambah')
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