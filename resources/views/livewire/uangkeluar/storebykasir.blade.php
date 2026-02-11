<dialog id="storeModalUangKeluarKasir" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closestoreModalUangKeluarKasir', () => {
        document.getElementById('storeModalUangKeluarKasir')?.close()
    })
">
    <div class="modal-box w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Pengeluaran</h3>

        <form wire:submit.prevent="store" class="space-y-4">
            <div>
                <label class="label font-medium">Karyawan Yang Mengajukan<span class="text-error">*</span></label>
                @php
                    $users = \App\Models\User::with(['biodata', 'role'])->get();
                @endphp
                <select class="select select-bordered w-full @error('user_id') input-error @enderror" wire:model.lazy="user_id">
                    <option value="">Pilih Karyawan</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}">
                            {{ $u->biodata->nama_lengkap ?? $u->name }}
                            ({{ $u->role->nama_role ?? '-' }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <span class="text-error text-sm">Mohon Memilih Karyawan Dengan Benar</span>
                @enderror
            </div>

            <div x-data="rupiahInput()">
                <label class="label font-medium">Jumlah Uang<span class="text-error">*</span></label>
                <input type="text" x-model="display" @input="onInput" inputmode="numeric" class="input input-bordered w-full @error('jumlah_uang') input-error @enderror">
                @error('jumlah_uang')
                    <span class="text-error text-sm">Mohon Mengisi Jumlah Uang Yang Keluar Dengan Benar</span>
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
                    <option value="Lainnya">Lainnya</option>
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