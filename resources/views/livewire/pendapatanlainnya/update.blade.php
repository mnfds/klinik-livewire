<div>
    <dialog id="modaleditpendapatan" class="modal" wire:ignore.self x-data x-init="
        Livewire.on('closemodaleditpendapatan', () => {
            document.getElementById('modaleditpendapatan')?.close()
        })">
        <div class="modal-box w-full max-w-md">
            <h3 class="text-xl font-semibold mb-4">Edit Pendapatan Lainnya</h3>
       
            <form wire:submit.prevent="updatePendapatan" class="space-y-4">
                <div x-data="rupiahInputPendapatan()" x-init="init()">
                    <label class="label font-medium">Jumlah Uang</label>
                    <input type="text" x-model="display" @input="onInputPendapatan" inputmode="numeric" class="input input-bordered w-full">
                </div>

                <div>
                    <label class="label font-medium">Keterangan</label>
                    <textarea wire:model.defer="keterangan" class="textarea textarea-bordered w-full" rows="3"></textarea>
                </div>

                <div>
                    <label class="label font-medium">Unit Usaha Pengaju</label>
                    <select class="select select-bordered w-full" wire:model.defer="unit_usaha">
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
                    <select class="select select-bordered w-full" wire:model.defer="status">
                        <option value="">Pilih</option>
                        <option value="belum lunas">Belum Lunas</option>
                        <option value="lunas">Lunas</option>
                        <option value="belum bayar">Belum Bayar</option>
                        <option value="batal">Batal</option>
                    </select>
                </div>
                
                <div class="modal-action justify-end pt-4">
                    @can('akses', 'Pengajuan Pengeluaran Ditolak Edit')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    @endcan
                    <button type="button" class="btn btn-error" onclick="document.getElementById('modaleditpendapatan').close()">Batal</button>
                </div>
            </form>
        </div>
    </dialog>
</div>
<script>
    function rupiahInputPendapatan() {
        return {
            display: '',

            init() {
                // saat modal edit dibuka
                Livewire.on('setJumlahPendapatan', value => {
                    this.display = this.formatRupiahUangPendapatan(value)
                })
            },

            onInputPendapatan() {
                let angkaPendapatan = this.display.replace(/[^0-9]/g, '')

                this.$wire.set(
                    'total_tagihan',
                    angkaPendapatan === '' ? null : Number(angkaPendapatan)
                )

                this.display = this.formatRupiahUangPendapatan(angkaPendapatan)
            },

            formatRupiahUangPendapatan(angkaPendapatan) {
                if (!angkaPendapatan) return ''
                return 'Rp ' + angkaPendapatan.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
            }
        }
    }
</script>
