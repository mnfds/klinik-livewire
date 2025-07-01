<x-app-layout>
    <div class="pt-1 pb-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Breadcrumbs (hanya muncul di layar lg ke atas) -->
            <div class="hidden lg:flex justify-end px-4">
                <div class="breadcrumbs text-sm">
                    <ul>
                        <li>
                            <a class="inline-flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 stroke-current" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a class="inline-flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 stroke-current" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                                Staff
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Page Title -->
            <div class="max-w-full mx-auto sm:px-6 lg:px-8">
                <h1 class="text-2xl font-bold text-base-content">
                    <i class="fa-solid fa-layer-group"></i>
                    Staff
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
                            <input wire:model.debounce.500ms="search" type="text" placeholder="Cari nama/email..." class="input input-bordered w-full max-w-xs" />
                            <a href="{{ route('users.create') }}" class="btn btn-primary">
                                Tambah User
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="table table-zebra w-full">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>No. Telepon</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                    <tr>
                                        <td>{{ $user->biodata->nama_lengkap ?? '-' }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->biodata->telepon ?? '-' }}</td>
                                        <td class="flex gap-2">
                                            <a href="{{ route('users.edit', $user->id) }}"
                                                class="btn btn-sm btn-warning">Edit</a>
                                            <button wire:click="$emit('deleteUser', {{ $user->id }})"
                                                class="btn btn-sm btn-error">Hapus</button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada data</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>