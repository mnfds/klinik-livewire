<div class="space-y-4">

    {{-- Input Pencarian --}}
    <div class="form-control">
        <label class="label font-semibold">Cari Organization Pada Satu Sehat</label>
        <input type="text" wire:model="partof" class="input input-bordered w-full" placeholder="Masukkan Organization ID">
    </div>

    <button wire:click="search"
        class="btn btn-primary"
        wire:loading.attr="disabled">
        Cari
    </button>

    {{-- Loading --}}
    <div wire:loading class="text-primary">
        <span class="loading loading-spinner loading-md"></span>
    </div>

    {{-- Hasil --}}
    @if (!empty($dataOrganisasi))
        <div class="mt-6 p-2">
            <h3 class="font-bold text-lg mb-2">Hasil Pencarian:</h3>
            <div class="overflow-x-auto w-full">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Kota</th>
                            <th>Alamat</th>
                            <th>Telepon</th>
                            <th>Email</th>
                            <th>Active</th>
                            <th></th>
                        </tr>
                    </thead>
    
                    <tbody>
                        @foreach ($dataOrganisasi as $org)
                            <tr>
                                <td>{{ $org['id'] }}</td>
                                <td>{{ $org['name'] }}</td>
                                <td>{{ $org['city'] }}</td>
                                <td>{{ $org['line'] }}</td>
                                <td>{{ $org['telecom']['phone'] }}</td>
                                <td>{{ $org['telecom']['email'] }}</td>
                                <td>
                                    @if ($org['active'])
                                        <div class="badge badge-success">
                                            <i class="fa-regular fa-circle-check"></i>
                                        </div>
                                    @else
                                        <div class="badge badge-danger">
                                            <i class="fa-regular fa-circle-xmark"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-success" wire:click="saved('{{ $org['id'] }}')">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

{{-- Untuk alert simple --}}
<script>
    Livewire.on('alert', data => {
        alert(data.message);
    });
</script>
