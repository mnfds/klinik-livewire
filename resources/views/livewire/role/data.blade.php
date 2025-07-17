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
    </div>
</div>