<dialog id="storeModal" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeStoreModal', () => {
        document.getElementById('storeModal')?.close()
    })
">
    <div class="modal-box w-full max-w-2xl">
        <h3 class="text-xl font-semibold mb-4">Tambah Surat Keterangan</h3>

        <form wire:submit.prevent="store" class="space-y-4">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- PASIEN: Autocomplete --}}
                <div class="relative">
                    <label class="label font-medium">Nama Pasien <span class="text-error">*</span></label>
                    <input type="text" class="input input-bordered w-full @error('pasien_id') input-error @enderror" wire:model.live.debounce.400ms="search_pasien" placeholder="Cari nama atau no. RM..." autocomplete="off">
                    {{-- Dropdown hasil pencarian --}}
                    @if($show_dropdown && count($hasil_pasien))
                        <ul class="absolute z-50 w-full bg-base-100 border border-base-300 rounded-box shadow-lg mt-1 max-h-52 overflow-y-auto">
                            @foreach($hasil_pasien as $pasien)
                            <li wire:click="pilihPasien({{ $pasien['id'] }}, '{{ $pasien['nama'] }}')" class="px-4 py-2 hover:bg-base-200 cursor-pointer flex justify-between items-center">
                                <span>{{ $pasien['nama'] }}</span>
                                <span class="text-xs text-base-content/50">{{ $pasien['no_register'] }}</span>
                            </li>
                            @endforeach
                        </ul>
                    @endif
                    {{-- Hidden: validasi pasien_id --}}
                    <input type="hidden" wire:model="pasien_id">
                    @error('pasien_id')
                        <span class="text-error text-sm mt-1">Mohon memilih pasien</span>
                    @enderror
                </div>
                {{-- DOKTER: Select biasa --}}
                <div>
                    <label class="label font-medium">Dokter <span class="text-error">*</span></label>
                    <select class="select select-bordered w-full @error('dokter_id') select-error @enderror" wire:model.lazy="dokter_id">
                        <option value="">Pilih Dokter</option>
                        @foreach($daftar_dokter as $dokter)
                        <option value="{{ $dokter['id'] }}">{{ $dokter['nama_dokter'] }}</option>
                        @endforeach
                    </select>
                    @error('dokter_id')
                        <span class="text-error text-sm mt-1">Mohon memilih dokter</span>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label font-medium">Mulai Berlaku Pada <span class="text-error">*</span></label>
                    <input type="date" class="input input-bordered w-full @error('mulai_berlaku') input-error @enderror" wire:model.lazy="mulai_berlaku">
                    @error('mulai_berlaku')
                        <span class="text-error text-sm mt-1">
                            Mohon Mengisi Tanggal
                        </span>
                    @enderror
                </div>
                <div>
                    <label class="label font-medium">Berlaku Selama (Hari) <span class="text-error">*</span></label>
                    <input type="number" class="input input-bordered w-full @error('selesai_berlaku') input-error @enderror" wire:model.lazy="selesai_berlaku">
                    @error('selesai_berlaku')
                        <span class="text-error text-sm mt-1">
                            Mohon mengisi jumlah hari
                        </span>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label font-medium">Jenis Tanda Tangan <span class="text-error">*</span></label>
                    <select class="select select-bordered w-full @error('tipe_ttd') input-error @enderror" wire:model.live="tipe_ttd">
                        <option value="">Pilih Tanda Tangan</option>
                        <option value="digital">Digital</option>
                        <option value="basah">Basah</option>
                    </select>
                    @error('tipe_ttd')
                        <span class="text-error text-sm mt-1">
                            Mohon Memilih Jenis Tanda Tangan
                        </span>
                    @enderror
                </div>
                <div>
                    <label class="label font-medium">Jenis Surat <span class="text-error">*</span></label>
                    <select class="select select-bordered w-full @error('jenis_surat') input-error @enderror" wire:model.live="jenis_surat">
                        {{-- wire:model.live agar langsung reaktif tanpa lazy --}}
                        <option value="">Pilih Jenis Surat</option>
                        <option value="standar">Surat Sehat Standar</option>
                        <option value="lengkap">Surat Sehat Lengkap</option>
                        <option value="sakit">Surat Sakit</option>
                    </select>
                    @error('jenis_surat')
                        <span class="text-error text-sm mt-1">
                            Mohon Memilih Jenis Surat Keterangan
                        </span>
                    @enderror
                </div>
            </div>

            {{-- Muncul hanya jika jenis_surat === 'sakit' --}}
            @if($jenis_surat === 'sakit')
            <div wire:key="field-sakit">
                <label class="label font-medium">Keterangan Sakit <span class="text-error">*</span></label>
                <textarea
                    class="textarea textarea-bordered w-full @error('sakit') textarea-error @enderror"
                    wire:model.lazy="sakit"
                    rows="3"
                    placeholder="Contoh: Demam, batuk, dan pilek selama 3 hari"
                ></textarea>
                @error('sakit')
                    <span class="text-error text-sm mt-1">Mohon mengisi keterangan sakit</span>
                @enderror
            </div>
            @endif

            @if ($jenis_surat === 'standar' || $jenis_surat === 'lengkap')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if ($jenis_surat === 'lengkap')
                <div>
                    <label class="label font-medium">Suhu Tubuh</label>
                    <label class="input input-bordered w-full">
                        <input type="number" step="0.1" wire:model.lazy="suhu_tubuh" />
                        <span class="label">°C</span>
                    </label>
                </div>
                @endif
                @if ($jenis_surat === 'lengkap')
                <div>
                    <label class="label font-medium">Nadi</label>
                    <label class="input input-bordered w-full">
                        <input type="number" wire:model.lazy="nadi"/>
                        <span class="label">kali/menit</span>
                    </label>
                </div>
                @endif
                <div>
                    <label class="label font-medium">Tekanan Sistole</label>
                    <label class="input input-bordered w-full">
                        <input type="number" wire:model.lazy="sistole"/>
                        <span class="label">mmHg</span>
                    </label>
                </div>
                <div>
                    <label class="label font-medium">Tekanan Diastole</label>
                    <label class="input input-bordered w-full">
                        <input type="number" wire:model.lazy="diastole"/>
                        <span class="label">mmHg</span>
                    </label>
                </div>
                @if ($jenis_surat === 'lengkap')
                    <div>
                        <label class="label font-medium">Frekuensi Pernapasan</label>
                        <label class="input input-bordered w-full">
                            <input type="number" wire:model.lazy="frekuensi_pernapasan"/>
                            <span class="label">kali/menit</span>
                        </label>
                    </div>
                @endif
                <div>
                    <label class="label font-medium">Tinggi Badan</label>
                    <label class="input input-bordered w-full">
                        <input type="number" wire:model.lazy="tinggi_badan"/>
                        <span class="label">Cm</span>
                    </label>
                </div>
                <div>
                    <label class="label font-medium">Berat Badan</label>
                    <label class="input input-bordered w-full">
                        <input type="number" wire:model.lazy="berat_badan"/>
                        <span class="label">Kg</span>
                    </label>
                </div>
                @if ($jenis_surat === 'lengkap')
                <div>
                    <label class="label font-medium">Kolesterol Baik (HDL)</label>
                    <label class="input input-bordered w-full">
                        <input type="number" wire:model.lazy="hdl"/>
                        <span class="label">mg/dL</span>
                    </label>
                </div>
                @endif
                @if ($jenis_surat === 'lengkap')
                <div>
                    <label class="label font-medium">Kolesterol Jahat (LDL)</label>
                    <label class="input input-bordered w-full">
                        <input type="number" wire:model.lazy="ldl"/>
                        <span class="label">mg/dL</span>
                    </label>
                </div>
                @endif
                @if ($jenis_surat === 'lengkap')
                <div>
                    <label class="label font-medium">Trigliserida</label>
                    <label class="input input-bordered w-full">
                        <input type="number" wire:model.lazy="trigliserida"/>
                        <span class="label">mg/dL</span>
                    </label>
                </div>
                @endif
                @if ($jenis_surat === 'lengkap')
                <div>
                    <label class="label font-medium">Kolesterol Total</label>
                    <label class="input input-bordered w-full">
                        <input type="number" wire:model.lazy="kolestrol_total"/>
                        <span class="label">mg/dL</span>
                    </label>
                </div>
                @endif
            </div>
            @endif

            <div>
                <label class="label font-medium">Harga Surat <span class="text-error">*</span></label>
                <input type="text" class="input input-bordered w-full" wire:model.live="harga_surat" placeholder="0"
                    x-data
                    x-on:input="
                        let v = $el.value.replace(/\D/g,'');
                        $el.value = v.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                        $wire.set('harga_surat', v);
                    "
                >
            </div>

            <div class="modal-action justify-end mt-6">
                @can('akses', 'Surat Keterangan Tambah')
                <button type="submit" class="btn btn-primary">Simpan</button>
                @endcan
                <button type="button" class="btn btn-neutral" onclick="document.getElementById('storeModal').close()">Batal</button>
            </div>
        </form>
    </div>
</dialog>
<script>
    const tinggiInput = document.getElementById('tinggi_badan');
    const beratInput = document.getElementById('berat_badan');
    const imtResult = document.getElementById('imt_result');
    const imtHidden = document.getElementById('imt');

    function hitungIMT() {
        const tinggi = parseFloat(tinggiInput.value);
        const berat = parseFloat(beratInput.value);

        if (tinggi > 0 && berat > 0) {
            const tinggiMeter = tinggi / 100;
            const imt = berat / (tinggiMeter * tinggiMeter);
            imtResult.value = imt.toFixed(2);
            imtHidden.value = imt.toFixed(2);
            imtHidden.dispatchEvent(new Event('input')); // trigger Livewire update
        } else {
            imtResult.value = '';
            imtHidden.value = '';
            imtHidden.dispatchEvent(new Event('input'));
        }
    }

    tinggiInput.addEventListener('input', hitungIMT);
    beratInput.addEventListener('input', hitungIMT);
</script>