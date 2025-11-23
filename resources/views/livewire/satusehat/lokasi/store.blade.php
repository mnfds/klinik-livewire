<div>
    <form wire:submit.prevent="store" class="space-y-6">

        {{-- NAMA + DESKRIPSI --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
                <label class="label font-semibold">Nama Lokasi</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="name">
            </div>

            <div class="form-control">
                <label class="label font-semibold">Deskripsi</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="description">
            </div>
        </div>

        {{-- NO TELP + EMAIL --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
                <label class="label font-semibold">Nomor Telepon</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="no_telp">
            </div>

            <div class="form-control">
                <label class="label font-semibold">Email</label>
                <input type="email" class="input input-bordered w-full" wire:model.lazy="email">
            </div>
        </div>

        {{-- WEBSITE --}}
        <div class="form-control">
            <label class="label font-semibold">Website</label>
            <input type="text" class="input input-bordered w-full" wire:model.lazy="web">
        </div>

        {{-- ALAMAT --}}
        <div class="form-control">
            <label class="label font-semibold">Alamat</label>
            <textarea class="textarea textarea-bordered w-full" rows="2" wire:model.lazy="alamat"></textarea>
        </div>

        {{-- KODE POS --}}
        <div class="form-control">
            <label class="label font-semibold">Kode Pos</label>
            <input type="text" class="input input-bordered w-full" wire:model.lazy="kode_pos">
        </div>

        {{-- PROVINSI - KABUPATEN --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div class="form-control">
                <label class="label font-semibold">Provinsi</label>
                <select id="provinsi" class="select select-bordered w-full" wire:ignore wire:model="province_code">
                    <option value="">-- Pilih Provinsi --</option>
                    @foreach ($provinsi as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-control">
                <label class="label font-semibold">Kabupaten / Kota</label>
                <select id="kabupaten" class="select select-bordered w-full"  wire:ignore wire:model="city_code">
                    <option value="">-- Pilih Kabupaten --</option>
                </select>
            </div>
        </div>

        {{-- KECAMATAN - KELURAHAN --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div class="form-control">
                <label class="label font-semibold">Kecamatan</label>
                <select id="kecamatan" class="select select-bordered w-full"  wire:ignore wire:model="district_code">
                    <option value="">-- Pilih Kecamatan --</option>
                </select>
            </div>

            <div class="form-control">
                <label class="label font-semibold">Kelurahan</label>
                <select id="kelurahan" class="select select-bordered w-full"  wire:ignore wire:model="village_code">
                    <option value="">-- Pilih Kelurahan --</option>
                </select>
            </div>
        </div>

        {{-- RT RW --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
                <label class="label font-semibold">RT</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="rt">
            </div>

            <div class="form-control">
                <label class="label font-semibold">RW</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="rw">
            </div>
        </div>

        {{-- KOORDINAT --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="form-control">
                <label class="label font-semibold">Longitude</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="longitude">
            </div>

            <div class="form-control">
                <label class="label font-semibold">Latitude</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="latitude">
            </div>

            <div class="form-control">
                <label class="label font-semibold">Altitude</label>
                <input type="text" class="input input-bordered w-full" wire:model.lazy="altitude">
            </div>
        </div>

        {{-- TOMBOL --}}
        <div class="flex justify-end pt-4">
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>

    </form>
</div>
<script>
    document.getElementById('provinsi').addEventListener('change', function () {
        let id = this.value;

        // kirim ke Livewire
        @this.set('province_code', id);

        fetch(`/wilayah/kabupaten/${id}`)
            .then(res => res.json())
            .then(data => {
                let kab = document.getElementById('kabupaten');
                kab.innerHTML = '<option value="">-- Pilih Kabupaten --</option>';

                data.forEach(k => {
                    kab.innerHTML += `<option value="${k.id}">${k.name}</option>`;
                });
            });
    });

    document.getElementById('kabupaten').addEventListener('change', function () {
        let id = this.value;

        @this.set('city_code', id);

        fetch(`/wilayah/kecamatan/${id}`)
            .then(res => res.json())
            .then(data => {
                let kec = document.getElementById('kecamatan');
                kec.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';

                data.forEach(k => {
                    kec.innerHTML += `<option value="${k.id}">${k.name}</option>`;
                });
            });
    });

    document.getElementById('kecamatan').addEventListener('change', function () {
        let id = this.value;

        @this.set('district_code', id);

        fetch(`/wilayah/kelurahan/${id}`)
            .then(res => res.json())
            .then(data => {
                let kel = document.getElementById('kelurahan');
                kel.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';

                data.forEach(v => {
                    kel.innerHTML += `<option value="${v.id}">${v.name}</option>`;
                });
            });
    });

    document.getElementById('kelurahan').addEventListener('change', function () {
        @this.set('village_code', this.value);
    });
</script>
