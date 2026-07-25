<div class="bg-base-200 p-4 rounded border border-base-200">
@props([
    'kolestrol' => [
        'kolestrol_hdl' => null,
        'kolestrol_ldl' => null,
        'trigliserida' => null,
        'kolestrol_total' => null,
    ]
])
    <div class="divider">Kolesterol</div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="form-control">
            <label class="label">Kolesterol Baik (HDL)</label>
            <label class="input input-bordered flex items-center gap-2">
                <input value="{{ $kolestrol['kolestrol_hdl'] }}" type="number" wire:model="kolestrol.kolestrol_hdl" class="grow" />
                <span class="label">mm/dL</span>
            </label>
        </div>
        <div class="form-control">
            <label class="label">Kolesterol Jahat (LDL)</label>
            <label class="input input-bordered flex items-center gap-2">
                <input value="{{ $kolestrol['kolestrol_ldl'] }}" type="number" wire:model="kolestrol.kolestrol_ldl" class="grow" />
                <span class="label">mm/dL</span>
            </label>
        </div>
        <div class="form-control">
            <label class="label">Trigliserida</label>
            <label class="input input-bordered flex items-center gap-2">
                <input value="{{ $kolestrol['trigliserida'] }}" type="number" wire:model="kolestrol.trigliserida" class="grow" />
                <span class="label">mm/dL</span>
            </label>
        </div>
        <div class="form-control">
            <label class="label">Kolesterol Total</label>
            <label class="input input-bordered flex items-center gap-2">
                <input value="{{ $kolestrol['kolestrol_total'] }}" type="number" wire:model="kolestrol.kolestrol_total" class="grow" />
                <span class="label">mm/dL</span>
            </label>
        </div>
    </div>
</div>