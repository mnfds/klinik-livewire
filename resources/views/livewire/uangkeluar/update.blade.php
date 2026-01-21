<div>
    {{-- MODAL EDIT PENDING --}}
    <dialog id="modaleditpending" class="modal" wire:ignore.self x-data x-init="
        Livewire.on('closemodaleditpending', () => {
            document.getElementById('modaleditpending')?.close()
        })
        ">
        <div class="modal-box w-full max-w-md">
            <h3 class="text-xl font-semibold mb-4">Edit Pengajuan</h3>
    
            <form wire:submit.prevent="updatepending" class="space-y-4">
                <div x-data="rupiahInput()" x-init="init()">
                    <label class="label font-medium">Jumlah Uang</label>
                    <input type="text" x-model="display" @input="onInput" inputmode="numeric" class="input input-bordered w-full">
                </div>
    
                <div>
                    <label class="label font-medium">Kategori</label>
                    <select class="select select-bordered w-full" wire:model.defer="jenis_pengeluaran">
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
                    <textarea wire:model.defer="keterangan" class="textarea textarea-bordered w-full" rows="3"></textarea>
                </div>

                <div>
                    <label class="label font-medium">Unit Usaha Pengaju</label>
                    <select class="select select-bordered w-full" wire:model.defer="unit_usaha">
                        <option value="">Pilih Unit</option>
                        <option value="Klinik">Klinik</option>
                        <option value="Apotik">Apotik</option>
                    </select>
                </div>
    
                <div class="modal-action justify-end pt-4">
                    @can('akses', 'Pengajuan Pengeluaran Pending Edit')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    @endcan
                    <button type="button" class="btn btn-error" onclick="document.getElementById('modaleditpending').close()">Batal</button>
                </div>
            </form>
        </div>
    </dialog>
    {{-- MODAL EDIT PENDING --}}

    {{-- MODAL EDIT DISETUJUI --}}
    <dialog id="modaleditditerima" class="modal" wire:ignore.self x-data x-init="
        Livewire.on('closemodaleditditerima', () => {
            document.getElementById('modaleditditerima')?.close()
        })
        ">
        <div class="modal-box w-full max-w-md">
            <h3 class="text-xl font-semibold mb-4">Edit Pengajuan</h3>
    
            <form wire:submit.prevent="updateDiterima" class="space-y-4">
                <div x-data="rupiahInput()" x-init="init()">
                    <label class="label font-medium">Jumlah Uang</label>
                    <input type="text" x-model="display" @input="onInput" inputmode="numeric" class="input input-bordered w-full">
                </div>
    
                <div>
                    <label class="label font-medium">Kategori</label>
                    <select class="select select-bordered w-full" wire:model.defer="jenis_pengeluaran">
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
                    <textarea wire:model.defer="keterangan" class="textarea textarea-bordered w-full" rows="3"></textarea>
                </div>

                <div>
                    <label class="label font-medium">Unit Usaha Pengaju</label>
                    <select class="select select-bordered w-full" wire:model.defer="unit_usaha">
                        <option value="">Pilih Unit</option>
                        <option value="Klinik">Klinik</option>
                        <option value="Apotik">Apotik</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="modal-action justify-end pt-4">
                    @can('akses', 'Pengajuan Pengeluaran Disetujui Edit')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    @endcan
                    <button type="button" class="btn btn-error" onclick="document.getElementById('modaleditditerima').close()">Batal</button>
                </div>
            </form>
        </div>
    </dialog>
    {{-- MODAL EDIT DISETUJUI --}}

    {{-- MODAL EDIT DITOLAK --}}
    <dialog id="modaleditditolak" class="modal" wire:ignore.self x-data x-init="
        Livewire.on('closemodaleditditolak', () => {
            document.getElementById('modaleditditolak')?.close()
        })
        ">
        <div class="modal-box w-full max-w-md">
            <h3 class="text-xl font-semibold mb-4">Edit Pengajuan</h3>
    
            <form wire:submit.prevent="updateDitolak" class="space-y-4">
                <div x-data="rupiahInput()" x-init="init()">
                    <label class="label font-medium">Jumlah Uang</label>
                    <input type="text" x-model="display" @input="onInput" inputmode="numeric" class="input input-bordered w-full">
                </div>
    
                <div>
                    <label class="label font-medium">Kategori</label>
                    <select class="select select-bordered w-full" wire:model.defer="jenis_pengeluaran">
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
                    <textarea wire:model.defer="keterangan" class="textarea textarea-bordered w-full" rows="3"></textarea>
                </div>
                
                <div>
                    <label class="label font-medium">Unit Usaha Pengaju</label>
                    <select class="select select-bordered w-full" wire:model.defer="unit_usaha">
                        <option value="">Pilih Unit</option>
                        <option value="Klinik">Klinik</option>
                        <option value="Apotik">Apotik</option>
                    </select>
                </div>

                <div class="modal-action justify-end pt-4">
                    @can('akses', 'Pengajuan Pengeluaran Ditolak Edit')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    @endcan
                    <button type="button" class="btn btn-error" onclick="document.getElementById('modaleditditolak').close()">Batal</button>
                </div>
            </form>
        </div>
    </dialog>
    {{-- MODAL EDIT DITOLAK --}}
</div>
<script>
    function rupiahInput() {
        return {
            display: '',

            init() {
                // saat modal edit dibuka
                Livewire.on('setJumlahUang', value => {
                    this.display = this.formatRupiah(value)
                })
            },

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