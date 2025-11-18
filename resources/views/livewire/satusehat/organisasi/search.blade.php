<div class="space-y-4">

    {{-- Input Pencarian --}}
    <div class="form-control">
        <label class="label font-semibold">Cari Organization berdasarkan PartOf</label>
        <input type="text" wire:model="partof" class="input input-bordered w-full" placeholder="Masukkan PartOf ID">
    </div>

    <button wire:click="search"
        class="btn btn-primary"
        wire:loading.attr="disabled">
        Cari
    </button>

    {{-- Loading --}}
    <div wire:loading class="text-blue-500">
        Mengambil data...
    </div>

    {{-- Hasil --}}
    @if (!empty($dataOrganisasi))
        <div class="mt-6">
            <h3 class="font-bold text-lg mb-2">Hasil Pencarian:</h3>

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
                            <td>{{ $org['active'] ? 'Ya' : 'Tidak' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>

{{-- Untuk alert simple --}}
<script>
    Livewire.on('alert', data => {
        alert(data.message);
    });
</script>
