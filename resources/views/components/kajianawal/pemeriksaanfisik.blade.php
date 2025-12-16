<div class="bg-base-200 p-4 rounded border border-base-200">
    <div class="divider">Pemeriksaan Fisik</div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="form-control">
            <label class="label">Tinggi Badan (Cm)<span class="text-error">*</span></label>
            <input type="number" wire:model="tinggi_badan" id="tinggi_badan" placeholder="Tinggi Badan Pasien" class="input input-bordered w-full grow" />
            @error('tinggi_badan') <span class="text-sm text-red-500">Mohon isi tinggi badan pasien untuk melanjutkan pemeriksaan.</span> @enderror
        </div>
        <div class="form-control">
            <label class="label">Berat Badan (Kg)<span class="text-error">*</span></label>
            <input type="number" wire:model="berat_badan" id="berat_badan" placeholder="Berat Badan Pasien" class="input input-bordered w-full grow" />
            @error('berat_badan') <span class="text-sm text-red-500">Mohon isi berat badan pasien untuk melanjutkan pemeriksaan.</span> @enderror
        </div>
    </div>
    <div class="mt-4">
        <label class="label"> Kalkulasi IMT</label>
        <label class="input input-bordered flex items-center gap-2">
            <input type="text" id="imt_result" readonly class="grow" placeholder="IMT akan muncul di sini" />
            <span class="label">kg/m²</span>
        </label>
        <input type="hidden" wire:model="imt" id="imt" />
    </div>
</div>
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
