<div>
    <a wire:navigate href="{{ route('pendaftaran.create', ['pasien_id' => $row->id]) }}" class="btn btn-primary">
        <i class="fa-solid fa-laptop-medical"></i>Registrasi
    </a>
    <div class="dropdown dropdown-left">
        <div tabindex="0" role="button" class="btn m-1 btn-secondary">
            <i class="fa-solid fa-ellipsis"></i>
        </div>
        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-1 p-2 shadow-md border-2 border-solid border-base-300 w-56">
            <li>
                <a wire:navigate href="{{ route('pasien.detail', ['id' => $row->id]) }}">
                    <i class="fa-solid fa-magnifying-glass"></i> Detail Pasien
                </a>
            </li>

            <li>
                <a wire:click="('openRekamMedis', {{ $row->id }})">
                    <i class="fa-solid fa-clipboard-list"></i>
                    Rekam Medis Pasien
                </a>
            </li>

            <li>
                <a wire:navigate href="{{ route('pasien.update', ['id' => $row->id]) }}">
                    <i class="fa-solid fa-pen-clip"></i>
                    Edit Pasien
                </a>
            </li>

            <li>
                <a href="#" onclick="Livewire.dispatch('hapusPasien', { rowId: {{ $row->id }} })"
                class="link link-error" style="text-decoration: none">
                    <i class="fa-solid fa-eraser"></i>
                    Hapus Pasien
                </a>
            </li>
        </ul>
    </div>
</div>