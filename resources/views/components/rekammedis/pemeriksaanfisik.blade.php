<div class="bg-base-200 p-4 rounded border border-base-200">
@props([
    'pemeriksaanFisik' => [
        'tinggi_badan' => null,
        'berat_badan' => null,
        'imt' => null,
    ]
])
    <div class="divider">Pemeriksaan Fisik Pasien</div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <label class="input input-bordered flex items-center gap-2">
            <input value="{{ $pemeriksaanFisik['tinggi_badan'] }}" wire:model="pemeriksaan_fisik.tinggi_badan" type="number" id="tinggi_badan" placeholder="Tinggi Badan" class="grow" />
            <span class="label">cm</span>
        </label>

        <label class="input input-bordered flex items-center gap-2">
            <input value="{{ $pemeriksaanFisik['berat_badan'] }}" wire:model="pemeriksaan_fisik.berat_badan" type="number" id="berat_badan" placeholder="Berat Badan" class="grow" />
            <span class="label">kg</span>
        </label>
    </div>
    <div class="mt-4">
        <label class="input input-bordered flex items-center gap-2">
            <input type="text" id="imt_result" readonly class="grow" value="{{ $pemeriksaanFisik['imt'] }}" placeholder="IMT akan muncul di sini" />
            <span class="label">kg/m²</span>
        </label>
        <input type="hidden" wire:model="pemeriksaan_fisik.imt" id="imt" />
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
