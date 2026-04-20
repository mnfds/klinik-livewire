<div class="pt-1 pb-12">
    <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Breadcrumbs (hanya muncul di layar lg ke atas) -->
        <div class="hidden lg:flex justify-end px-4">
            <div class="breadcrumbs text-sm">
                <ul>
                    <li>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('role-akses.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Role
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Role & Akses
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
             <div class="tabs tabs-lift">
                <input type="radio" name="my_tabs_1" class="tab bg-transparent text-base-content" aria-label="Role Group" style="background-image: none;" checked/>
                <div class="tab-content bg-base-100 border-base-300 p-6">
                    <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                        {{-- <div class="p-6 text-base-content space-y-4">
                            konten
                        </div> --}}
                        <div class="p-6 text-base-content space-y-4">
                            <div class="flex justify-between items-center mb-4">
                                <button onclick="document.getElementById('storeModalRole').showModal()" class="btn btn-success"><i class="fa-solid fa-plus"></i> Role</button>
                            </div>
                            <livewire:role-table/>
                            <script>
                                window.addEventListener('show-delete-confirmation', event => {
                                        if (confirm('Yakin ingin menghapus user ini?')) {
                                            Livewire.call('confirmDelete', event.detail.rowId);
                                        }
                                    });
                            </script>
                        </div>
                    </div>
                </div>

                <input type="radio" name="my_tabs_1" class="tab bg-transparent text-base-content" aria-label="Individu" style="background-image: none;"/>
                <div class="tab-content bg-base-100 border-base-300 p-6">
                    <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                        <div class="p-6 text-base-content space-y-4">

                            {{-- Select User --}}
                            <div class="max-w-sm">
                                <label class="label font-medium mb-1">Pilih User</label>
                                <select class="select select-bordered w-full" wire:model.live="selectedUserId">
                                    <option value="">-- Pilih User --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }}
                                            @if($user->role) ({{ $user->role->nama_role }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Tabel Akses --}}
                            @if($selectedUser && count($aksesGrouped))
                                <div class="mt-4 space-y-4">
                                    @foreach($aksesGrouped as $group => $aksesItems)
                                        <div class="border border-base-200 rounded-lg overflow-hidden">
                                            <div class="bg-base-200 px-4 py-2 font-semibold text-sm">
                                                {{ $aksesItems[0]['nama_akses'] ?? $group }}
                                            </div>
                                            <div class="divide-y divide-base-200">
                                                @foreach($aksesItems as $akses)
                                                    @php
                                                        $dariRole = in_array($akses['id'], $roleAkses);
                                                        $dariIndividu = in_array($akses['id'], $individu);
                                                        $checked = $dariRole || $dariIndividu;
                                                    @endphp

                                                    <div class="flex items-center justify-between px-4 py-2 hover:bg-base-50">
                                                        <span class="text-sm">{{ $akses['nama_akses'] }}</span>
                                                        <div class="flex items-center gap-2">
                                                            @if($dariRole)
                                                                <span class="badge badge-info badge-sm">Role/Group</span>
                                                            @elseif($dariIndividu)
                                                                <span class="badge badge-success badge-sm">Individu</span>
                                                            @endif

                                                            <input
                                                                type="checkbox"
                                                                class="checkbox checkbox-sm {{ $dariRole ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                                {{ $checked ? 'checked' : '' }}
                                                                {{ $dariRole ? 'disabled' : '' }}
                                                                @if(!$dariRole)
                                                                    wire:click="toggleAkses({{ $akses['id'] }})"
                                                                @endif
                                                            />
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @elseif($selectedUserId && !count($aksesGrouped))
                                    <div class="text-center text-base-content/50 py-8">
                                        Tidak ada data akses.
                                    </div>
                                @else
                                    <div class="text-center text-base-content/50 py-8">
                                        Pilih user untuk melihat dan mengatur aksesnya.
                                    </div>
                            @endif

                        </div>
                    </div>
                </div>
             </div>
        </div>
    </div>
</div>