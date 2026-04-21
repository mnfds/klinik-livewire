<dialog id="takeModalBahanbaku" class="modal" wire:ignore.self x-data x-init="Livewire.on('closetakeModalBahanbaku', () => {document.getElementById('takeModalBahanbaku')?.close()})">
    <div class="modal-box w-full max-w-lg">
        <h3 class="text-xl font-semibold mb-4">Bahan Baku Keluar</h3>
        <form wire:submit.prevent="store" class="space-y-4">
            {{-- Tambah Tab --}}
            <div>
                <button type="button" class="btn btn-primary btn-sm" wire:click="addTab">
                    + Tambah Form
                </button>
            </div>
            {{-- Tab Headers --}}
            <div role="tablist" class="tabs tabs-bordered flex-wrap">
                @foreach ($items as $i => $item)
                    <button type="button" role="tab" class="tab gap-1 {{ $activeTab === $i ? 'tab-active border-b-2 border-primary text-primary' : 'border-b-2 border-transparent' }}" wire:click="$set('activeTab', {{ $i }})">
                        <span>
                            Form {{ $i + 1 }}
                            @if ($errors->has("items.$i.bahan_baku_id") || $errors->has("items.$i.jumlah"))
                                <span class="inline-block w-2 h-2 rounded-full bg-error ml-1 align-middle"></span>
                            @endif
                        </span>
                        @if (count($items) > 1)
                            <span role="button" class="ml-1 text-base-content/40 hover:text-error transition-colors" wire:click.stop="removeTab({{ $i }})" >
                                <i class="fa-solid fa-circle-xmark"></i>
                            </span>
                        @endif
                    </button>
                @endforeach
            </div>
            {{-- Tab Content --}}
            @foreach ($items as $i => $item)
                <div @class(['hidden' => $activeTab !== $i, 'space-y-4' => true])>
                    {{-- Nama Bahan Baku --}}
                    <div>
                        <label class="label font-medium">
                            Nama Bahan Baku <span class="text-error">*</span>
                        </label>
                        <select class="select select-bordered w-full @error("items.$i.bahan_baku_id") select-error @enderror" wire:model.lazy="items.{{ $i }}.bahan_baku_id">
                            <option value="">Pilih Bahan Baku</option>
                            @foreach ($bahan as $b)
                                <option value="{{ $b->id }}">{{ $b->nama }}</option>
                            @endforeach
                        </select>
                        @error("items.$i.bahan_baku_id")
                            <span class="text-error text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    {{-- Jumlah --}}
                    <div>
                        <label class="label font-medium">
                            Jumlah Keluar <span class="text-error">*</span>
                        </label>
                        <input type="number" min="1" class="input input-bordered w-full @error("items.$i.jumlah") input-error @enderror" wire:model.lazy="items.{{ $i }}.jumlah" placeholder="0" >
                        @error("items.$i.jumlah")
                            <span class="text-error text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    {{-- Catatan --}}
                    <div>
                        <label class="label font-medium">Catatan</label>
                        <input type="text" class="input input-bordered w-full" wire:model.lazy="items.{{ $i }}.catatan" placeholder="Opsional...">
                    </div>
                </div>
            @endforeach

            {{-- Actions --}}
            <div class="modal-action justify-end pt-4">
                @can('akses', 'Persediaan Bahan Baku Keluar')
                    <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                <button type="button" class="btn btn-error" onclick="document.getElementById('takeModalBahanbaku').close()">
                    Batal
                </button>
            </div>
        </form>
    </div>

    {{-- Backdrop --}}
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>