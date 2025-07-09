<dialog id="modaleditprodukdanobat" class="modal" wire:ignore.self x-data x-init="
    Livewire.on('closeModal', () => {
        document.getElementById('modaleditprodukdanobat')?.close()
    })
">
    <div class="modal-box">
        <h3 class="font-bold text-lg mb-2">Edit Produk & Obat</h3>

        <form wire:submit.prevent="update">

            {{-- Nama Dagang --}}
            <div class="form-control">
                <label class="label font-semibold">Nama Dagang</label>
                <input type="text" class="input input-bordered" wire:model="nama_dagang" required>
            </div>

            {{-- Kode Produk --}}
            <div class="form-control">
                <label class="label font-semibold">Kode Produk</label>
                <input type="text" class="input input-bordered" wire:model="kode" required>
            </div>

            {{-- Sediaan --}}
            <div class="form-control">
                <label class="label font-semibold">Satuan</label>
                <select class="select select-bordered" wire:model="sediaan" required>
                    <option>Pilih Sediaan</option>
                    <option value="pcs">Pcs</option>
                    <option value="pot">Pot</option>
                    <option value="tablet">Tablet</option>
                    <option value="botol">Botol</option>
                    <option value="sachet">Sachet</option>
                    <option value="strip">Strip</option>
                    <option value="box">Box</option>
                    <option value="paket">Paket</option>
                    <option value="kapsul">Kapsul</option>
                    <option value="sirup">Sirup</option>
                    <option value="salep">Salep</option>
                    <option value="injeksi">Injeksi</option>
                    <option value="tube">Tube</option>
                    {{-- Tambahkan satuan lain sesuai kebutuhan --}}
                </select>
            </div>

            {{-- Harga Jual --}}
            <div class="form-control">
                <label class="label font-semibold">Harga Jual</label>
                <input type="text" class="input input-bordered" id="harga_dasar_input" oninput="formatRupiahToHidden(this, 'harga_dasar_hidden')" value="{{ number_format($harga_dasar ?? 0, 0, ',', '.') }}" inputmode="numeric">
                <input type="hidden" wire:model="harga_dasar" id="harga_dasar_hidden">
            </div>

            {{-- Diskon (%) --}}
            <div class="form-control mb-2">
                <label class="label">Diskon (%)</label>
                <input type="number" min="0" max="100" class="input input-bordered" wire:model="diskon">
            </div>

            {{-- Harga Bersih --}}
            <div class="form-control mb-2">
                <label class="label">Harga Bersih</label>
                <input type="text" class="input input-bordered bg-base-200" value="Rp {{ number_format($harga_bersih ?? 0, 0, ',', '.') }}" readonly>
            </div>

            {{-- Stok --}}
            <div class="form-control">
                <label class="label font-semibold">Stok</label>
                <input type="number" class="input input-bordered" wire:model="stok" min="0" required>
            </div>

            {{-- Tanggal Expired --}}
            <div class="form-control">
                <label class="label font-semibold">Expired</label>
                <input type="date" class="input input-bordered" wire:model="expired_at">
            </div>

            {{-- Batch --}}
            <div class="form-control">
                <label class="label font-semibold">Batch</label>
                <input type="text" class="input input-bordered" wire:model="batch">
            </div>

            {{-- Lokasi --}}
            <div class="form-control">
                <label class="label font-semibold">Lokasi</label>
                <select class="select select-bordered" wire:model="lokasi" required>
                    <option>Pilih Lokasi Penyimpanan</option>
                    <option value="Gudang Utama">Gudang Utama</option>
                    <option value="Gudang Kecil">Gudang Kecil</option>
                </select>
            </div>

            {{-- Supplier --}}
            <div class="form-control">
                <label class="label font-semibold">Supplier</label>
                <input type="text" class="input input-bordered" wire:model="supplier">
            </div>

            <div class="modal-action">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
    <script>
        function formatRupiahToHidden(inputEl, hiddenId) {
            const raw = inputEl.value.replace(/[^\d]/g, '');
            const formatted = new Intl.NumberFormat('id-ID').format(raw);

            inputEl.value = formatted;

            const hidden = document.getElementById(hiddenId);
            hidden.value = raw;

            hidden.dispatchEvent(new Event('input')); // Trigger Livewire update
        }
    </script>
</dialog>