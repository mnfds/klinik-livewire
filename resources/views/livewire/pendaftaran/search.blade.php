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
                        <a href="{{ route('pendaftaran.search') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Daftarkan Pasien
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Cari Pasien
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                  <div class="p-6 text-base-content space-y-4">
                    <div wire:ignore>
                        <label for="pasien-select" class="label">
                            <span class="label-text">Cari Pasien</span>
                        </label>
                        <select id="pasien-select" class="form-select w-full">
                            <option value="">-- Pilih Pasien --</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    function formatPasien(pasien) {
        if (!pasien.id) return pasien.text;

        let foto = pasien.foto || '{{ asset("default.png") }}';
        let noReg = pasien.no_register || '-';

        return $(`
            <div class="flex items-center gap-2">
                <div class="font-medium">${pasien.text}</div>
                <div class="text-xs text-gray-500">No. Reg: ${noReg}</div>
            </div>
        `);
    }

    $(document).ready(function () {
        $('#pasien-select').select2({
            placeholder: 'Cari berdasarkan nama / no register...',
            allowClear: true,
            ajax: {
                url: '{{ route("api.pasien.search") }}',
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: data => ({ results: data }),
                cache: true
            },
            templateResult: formatPasien,
            templateSelection: formatPasien,
            escapeMarkup: m => m
        });

        $('#pasien-select').on('change', function () {
            Livewire.emit('setPasien', $(this).val());
        });
    });
</script>
@endpush
