<dialog id="storeModalUangKeluarKasir" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closestoreModalUangKeluarKasir', () => {
        document.getElementById('storeModalUangKeluarKasir')?.close()
    })
    ">
    <div class="modal-box w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Pengeluaran</h3>

        <form wire:submit.prevent="store" class="space-y-4">
            <div
                x-data="{
                    open: false,
                    search: '',
                    users: {{ \Illuminate\Support\Js::from($users) }},
                    selectedId: @entangle('user_id'),
                    get filtered() {
                        return this.search === ''
                            ? this.users
                            : this.users.filter(u => u.name.toLowerCase().includes(this.search.toLowerCase()))
                    },
                    get selectedLabel() {
                        let u = this.users.find(u => u.id == this.selectedId)
                        return u ? u.name + ' (' + u.role + ')' : ''
                    },
                    choose(user) {
                        this.selectedId = user.id
                        this.search = user.name
                        this.open = false
                    }
                }"
                x-init="search = selectedLabel" @click.outside="open = false; search = selectedLabel" class="relative">
                <label class="label font-medium">Karyawan Yang Mengajukan<span class="text-error">*</span></label>
                <input type="text" x-model="search" @focus="open = true; search = ''" placeholder="Cari nama karyawan..." autocomplete="off" class="input input-bordered w-full @error('user_id') input-error @enderror">

                <ul x-show="open" x-cloak class="absolute z-50 mt-1 w-full max-h-60 overflow-y-auto bg-base-100 border border-base-300 rounded-box shadow">
                    <template x-for="user in filtered" :key="user.id">
                        <li @click="choose(user)"
                            class="px-4 py-2 cursor-pointer hover:bg-base-200"
                            x-text="user.name + ' (' + user.role + ')'">
                        </li>
                    </template>
                    <li x-show="filtered.length === 0" class="px-4 py-2 text-gray-400 text-sm">
                        Tidak ditemukan
                    </li>
                </ul>
                @error('user_id')<span class="text-error text-sm">Mohon Memilih Karyawan Dengan Benar</span>@enderror
            </div>

            <div x-data="rupiahInput()">
                <label class="label font-medium">Jumlah Uang<span class="text-error">*</span></label>
                <input type="text" x-model="display" @input="onInput" inputmode="numeric" class="input input-bordered w-full @error('jumlah_uang') input-error @enderror">
                @error('jumlah_uang')
                    <span class="text-error text-sm">Mohon Mengisi Jumlah Uang Yang Keluar Dengan Benar</span>
                @enderror
            </div>

            <div>
                <label class="label font-medium">Pembayaran<span class="text-error">*</span></label>
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
                <label class="label font-medium">Kategori<span class="text-error">*</span></label>
                <select class="select select-bordered w-full @error('jenis_pengeluaran') input-error @enderror" wire:model.lazy="jenis_pengeluaran">
                    <option value="">Pilih Kategori</option>
                    <option value="SDM">SDM</option>
                    <option value="Administrasi">Administrasi</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Operasional">Operasional</option>
                    <option value="Fasilitas Dan Bangunan">Fasilitas Dan Bangunan</option>
                    <option value="Rumah Tangga">Rumah Tangga</option>
                    <option value="Dll">Dll</option>
                </select>
                @error('jenis_pengeluaran')
                    <span class="text-error text-sm">Mohon Memilih Kategori Dengan Benar</span>
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
                    <option value="Dll">Dll</option>
                </select>
                @error('unit_usaha')
                    <span class="text-error text-sm">Mohon Memilih Unit Usaha Dengan Benar</span>
                @enderror
            </div>

            <div class="modal-action justify-end pt-4">
                @can('akses', 'Pengeluaran')
                <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                @can('akses', 'Pengajuan Pengeluaran Disetujui Tambah')
                <button type="submit" class="btn btn-primary">Ajukan</button>
                @endcan
                <button type="button" class="btn btn-error" onclick="document.getElementById('storeModalUangKeluarKasir').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>
<script>
    function rupiahInput() {
        return {
            display: '',

            onInput() {
                let angka = this.display.replace(/[^0-9]/g, '')

                this.$wire.set(
                    'jumlah_uang',
                    angka === '' ? null : Number(angka)
                )

                this.display = this.formatRupiah(angka)
            },

            formatRupiah(angka) {
                if (!angka) return ''
                return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
            }
        }
    }
</script>