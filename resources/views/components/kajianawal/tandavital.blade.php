<div class="bg-base-200 p-4 rounded border border-base-200">
    <div class="divider">Tanda Tanda Vital</div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <label class="input input-bordered flex items-center gap-2">
            <input type="number" step="0.1" wire:model="suhu_tubuh" placeholder="Suhu Tubuh" class="grow" />
            <span class="label">°C</span>
        </label>

        <label class="input input-bordered flex items-center gap-2">
            <input type="number" wire:model="nadi" placeholder="Nadi" class="grow" />
            <span class="label">kali/menit</span>
        </label>

        <label class="input input-bordered flex items-center gap-2">
            <input type="number" wire:model="sistole" placeholder="Tekanan Sistole" class="grow" />
            <span class="label">mmHg</span>
        </label>

        <label class="input input-bordered flex items-center gap-2">
            <input type="number" wire:model="diastole" placeholder="Tekanan Diastole" class="grow" />
            <span class="label">mmHg</span>
        </label>

        <label class="input input-bordered flex items-center gap-2 md:col-span-2">
            <input type="number" wire:model="frekuensi_pernapasan" placeholder="Frekuensi Pernapasan" class="grow" />
            <span class="label">kali/menit</span>
        </label>
    </div>
</div>
