<div class="m-8">
    @can('akses', 'Pasien Registrasi')
    <a wire:navigate href="{{ route('pendaftaran.create', ['pasien_id' => $row->id]) }}" class="btn btn-primary">
        <i class="fa-solid fa-laptop-medical"></i>Registrasi
    </a>
    @endcan
    @if (
        Gate::allows('akses', 'Pasien Detail') ||
        Gate::allows('akses', 'Pasien Riwayat Rekam Medis') ||
        Gate::allows('akses', 'Pasien Edit') ||
        Gate::allows('akses', 'Pasien Hapus')
    )        
        <div class="dropdown dropdown-left dropdown-center">
            <div tabindex="0" role="button" class="btn m-1 btn-secondary">
                <i class="fa-solid fa-ellipsis"></i>
            </div>
            <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-1 p-2 shadow-md border-2 border-solid border-base-300 w-56">
                @can('akses', 'Pasien Detail')                
                <li>
                    <a wire:navigate href="{{ route('pasien.detail', ['id' => $row->id]) }}">
                        <i class="fa-solid fa-magnifying-glass"></i> Detail Pasien
                    </a>
                </li>
                @endcan
                @can('akses', 'Pasien Riwayat Rekam Medis')
                <li>
                    <a wire:navigate href="{{ route('rekam-medis-pasien.data', ['pasien_id'=> $row->id ]) }}">
                        <i class="fa-solid fa-clipboard-list"></i>
                        Rekam Medis Pasien
                    </a>
                </li>
                @endcan
                @can('akses', 'Pasien Edit')                
                <li>
                    <a wire:navigate href="{{ route('pasien.update', ['id' => $row->id]) }}">
                        <i class="fa-solid fa-pen-clip"></i>
                        Edit Pasien
                    </a>
                </li>
                @endcan
                @can('akses', 'Pasien Hapus')                
                <li>
                    <a href="#" onclick="Livewire.dispatch('hapusPasien', { rowId: {{ $row->id }} })"
                    class="link link-error" style="text-decoration: none">
                        <i class="fa-solid fa-eraser"></i>
                        Hapus Pasien
                    </a>
                </li>
                @endcan
            </ul>
        </div>
    @endif
</div>