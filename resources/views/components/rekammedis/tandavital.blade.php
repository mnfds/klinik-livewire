<div class="bg-base-200 p-4 rounded border border-base-200">
@props([
    'tandaVital' => [
        'suhu_tubuh' => null,
        'nadi' => null,
        'sistole' => null,
        'diastole' => null,
        'frekuensi_pernapasan' => null,
    ]
])

    <div class="divider">Tanda Tanda Vital</div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <label class="input input-bordered flex items-center gap-2">
            <input type="number" step="0.1" value="{{ $tandaVital['suhu_tubuh'] }}" wire:model="tanda_vital.suhu_tubuh" placeholder="Suhu Tubuh" class="grow" />
            <span class="label">°C</span>
        </label>

        <label class="input input-bordered flex items-center gap-2">
            <input type="number" value="{{ $tandaVital['nadi'] }}" wire:model="tanda_vital.nadi" placeholder="Nadi" class="grow" />
            <span class="label">kali/menit</span>
        </label>

        <label class="input input-bordered flex items-center gap-2">
            <input type="number" value="{{ $tandaVital['sistole'] }}" wire:model="tanda_vital.sistole" placeholder="Tekanan Sistole" class="grow" />
            <span class="label">mmHg</span>
        </label>

        <label class="input input-bordered flex items-center gap-2">
            <input type="number" value="{{ $tandaVital['diastole'] }}" wire:model="tanda_vital.diastole" placeholder="Tekanan Diastole" class="grow" />
            <span class="label">mmHg</span>
        </label>

        <label class="input input-bordered flex items-center gap-2 md:col-span-2">
            <input type="number" value="{{ $tandaVital['frekuensi_pernapasan'] }}" wire:model="tanda_vital.frekuensi_pernapasan" placeholder="Frekuensi Pernapasan" class="grow" />
            <span class="label">kali/menit</span>
        </label>
    </div>
</div>
