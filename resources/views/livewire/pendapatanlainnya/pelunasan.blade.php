<div>
    <dialog id="modalpelunasanpendapatan" class="modal" wire:ignore.self x-data x-init="
        Livewire.on('closemodalpelunasanpendapatan', () => {
            document.getElementById('modalpelunasanpendapatan')?.close()
        })
    ">
        <div class="modal-box w-full max-w-md">
            <h3 class="text-xl font-semibold mb-4">Pelunasan Pendapatan</h3>

            <div class="bg-base-200 rounded-lg p-3 mb-4 space-y-1 text-sm" x-data="infoPelunasan()" x-init="init()">
                <div class="flex justify-between">
                    <span>Total Tagihan</span>
                    <span class="font-medium" x-text="formatRupiah(info.total_tagihan)"></span>
                </div>
                <div class="flex justify-between">
                    <span>Sudah Dibayar</span>
                    <span class="font-medium text-success" x-text="formatRupiah(info.total_dibayarkan_group)"></span>
                </div>
                <div class="flex justify-between border-t pt-1 mt-1">
                    <span>Sisa Tagihan</span>
                    <span class="font-semibold text-error" x-text="formatRupiah(info.sisa_tagihan)"></span>
                </div>
            </div>

            <form wire:submit.prevent="storePelunasan" class="space-y-4">
                <div x-data="rupiahInputPelunasan()">
                    <label class="label font-medium">Nominal Pembayaran<span class="text-error">*</span></label>
                    <input type="text" x-model="display" @input="onInput" inputmode="numeric" class="input input-bordered w-full @error('nominal_pelunasan') input-error @enderror">
                    @error('nominal_pelunasan')
                        <span class="text-error text-sm">{{ $message }}</span>
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

                <div>
                    <label class="label font-medium">Keterangan<span class="text-error">*</span></label>
                    <textarea wire:model.lazy="keterangan" class="textarea textarea-bordered w-full @error('keterangan') input-error @enderror" rows="2" placeholder="Contoh: Pelunasan tahap ke-2"></textarea>
                    @error('keterangan')
                        <span class="text-error text-sm">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="modal-action justify-end pt-4">
                    @can('akses', 'Pendapatan Edit')
                    <button type="submit" class="btn btn-success">Bayar</button>
                    @endcan
                    <button type="button" class="btn btn-error" onclick="document.getElementById('modalpelunasanpendapatan').close()">Batal</button>
                </div>
            </form>
        </div>
    </dialog>
</div>
<script>
    function infoPelunasan() {
        return {
            info: { total_tagihan: 0, total_dibayarkan_group: 0, sisa_tagihan: 0 },

            init() {
                Livewire.on('setInfoPelunasan', (data) => {
                    this.info = data[0] ?? data
                })
            },

            formatRupiah(angka) {
                if (!angka) return 'Rp 0'
                return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
            }
        }
    }

    function rupiahInputPelunasan() {
        return {
            display: '',

            onInput() {
                let angka = this.display.replace(/[^0-9]/g, '')

                this.$wire.set('nominal_pelunasan', angka === '' ? null : Number(angka))

                this.display = this.formatRupiah(angka)
            },

            formatRupiah(angka) {
                if (!angka) return ''
                return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
            }
        }
    }
</script>