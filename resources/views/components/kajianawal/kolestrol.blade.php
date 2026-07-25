<div class="bg-base-200 p-4 rounded border border-base-200">
    <div class="divider">Kolestrol</div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="label">Kolestrol Baik (HDL)</label>
            <label class="input input-bordered flex items-center gap-2">
                <input type="number" wire:model="kolestrol_hdl" placeholder="0" class="grow" />
                <span class="label">mg/dL</span>
            </label>
        </div>
        <div>
            <label class="label">Kolestrol Jahat (LDL)</label>
            <label class="input input-bordered flex items-center gap-2">
                <input type="number" wire:model="kolestrol_ldl" placeholder="0" class="grow" />
                <span class="label">mg/dL</span>
            </label>
        </div>
        <div>
            <label class="label">Trigliserida</label>
            <label class="input input-bordered flex items-center gap-2">
                <input type="number" wire:model="trigliserida" placeholder="0" class="grow" />
                <span class="label">mg/dL</span>
            </label>
        </div>
        <div>
            <label class="label">Kolestrol Total</label>
            <label class="input input-bordered flex items-center gap-2">
                <input type="number" wire:model="kolestrol_total" placeholder="0" class="grow" />
                <span class="label">mg/dL</span>
            </label>
        </div>
    </div>
</div>
