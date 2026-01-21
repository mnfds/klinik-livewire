<dialog id="storeModalUangKeluarKasir" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closestoreModalUangKeluarKasir', () => {
        document.getElementById('storeModalUangKeluarKasir')?.close()
    })
">
    <div class="modal-box w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Pengeluaran</h3>

        <form wire:submit.prevent="store" class="space-y-4">
        <div>
            <label class="label font-medium">Staff Yang Mengajukan</label>
            @php
                $users = \App\Models\User::with(['biodata', 'role'])->get();
            @endphp
            <select class="select select-bordered w-full" wire:model.lazy="user_id">
                <option value="">Pilih Staff</option>
                @foreach ($users as $u)
                    <option value="{{ $u->id }}">
                        {{ $u->biodata->nama_lengkap ?? $u->name }}
                        ({{ $u->role->nama_role ?? '-' }})
                    </option>
                @endforeach
            </select>
        </div>

            <div x-data="rupiahInput()">
                <label class="label font-medium">Jumlah Uang</label>
                <input type="text" x-model="display" @input="onInput" inputmode="numeric" class="input input-bordered w-full">
            </div>

            <div>
                <label class="label font-medium">Kategori</label>
                <select class="select select-bordered w-full" wire:model.lazy="jenis_pengeluaran">
                    <option value="">Pilih Kategori</option>
                    <option value="SDM">SDM</option>
                    <option value="Administrasi">Administrasi</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Operasional">Operasional</option>
                    <option value="Rumah Tangga">Rumah Tangga</option>
                    <option value="Dll">Dll</option>
                </select>
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
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <div class="modal-action justify-end pt-4">
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