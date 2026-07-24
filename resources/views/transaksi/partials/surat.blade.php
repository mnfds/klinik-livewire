<div class="mb-6">
    <h4 class="font-semibold mb-2 text-base-content">Surat Keterangan</h4>

    <div class="space-y-3">
        <div
            class="bg-base-100 border border-base-300 border-t-3 border-t-primary rounded-lg p-3 shadow-sm hover:shadow transition">
            {{-- Surat --}}
            <div class="font-semibold text-base-content mb-1">
                Surat Keterangan Sehat/Sakit
                {{-- Surat Keterangan {{ ucfirst($item->suratKeterangan->sakit ?? 'Sehat') }} --}}
            </div>
            {{-- Harga Dan Jenis Surat --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label font-medium">Harga</label>
                    <input type="text" class="input input-bordered w-full" wire:model.live="harga_surat" placeholder="0"
                        x-data x-on:input="
                            let v = $el.value.replace(/\D/g,'');
                            $el.value = v.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                            $wire.set('harga_surat', v);
                        ">
                </div>
                <div>
                    <label class="label font-medium">Jenis Surat <span class="text-error">*</span></label>
                    <select class="select select-bordered w-full" wire:model.live="jenis_surat">
                        <option value="">Pilih Jenis Surat</option>
                        <option value="standar">Surat Sehat Standar</option>
                        <option value="lengkap">Surat Sehat Lengkap</option>
                        <option value="sakit">Surat Sakit</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label font-medium">Tipe TTD <span class="text-error">*</span></label>
                    <select class="select select-bordered w-full" wire:model.live="tipe_ttd">
                        <option value="">Pilih Tanda Tangan</option>
                        <option value="digital">Digital</option>
                        <option value="basah">Basah</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>