<div class="space-y-4">
    {{-- Select Praktisi --}}
    <div class="form-control mb-4">
        <label class="label font-semibold">Pilih Praktisi (User)</label>
        <select wire:model="selectedUser" class="select select-bordered w-full">
            <option value="">-- Pilih Praktisi --</option>

            @foreach ($users as $u)
                <option value="{{ $u->id }}">
                    {{ $u->biodata->nama_lengkap ?? $u->dokter->nama_dokter ?? 'Tanpa Nama' }}
                </option>
            @endforeach
        </select>
    </div>

    <button wire:click="searchpractioner"
        class="btn btn-primary"
        wire:loading.attr="disabled">
        Cari
    </button>

    {{-- Loading --}}
    <div wire:loading class="text-primary">
        <span class="loading loading-spinner loading-md"></span>
    </div>

    {{-- Hasil --}}
    @if (!empty($praktisi))
        <div class="mt-6 p-2">
            <h3 class="font-bold text-lg mb-2">Hasil Pencarian:</h3>
            <div class="overflow-x-auto w-full">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>IHS</th>
                            <th>NIK</th>
                            <th>Nama</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>{{ $praktisi['no_ihs'] }}</td>
                            <td>{{ $nik }}</td>
                            <td>{{ $praktisi['nama'] }}</td>
                            <td>
                                <button class="btn btn-success" wire:click="saveIHS('{{ $selectedUser }}')">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>