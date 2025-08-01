<div class="mt-6">
    <h2 class="text-lg font-semibold border-b pb-2 mb-4">Data Estetika</h2>
    <div class="space-y-4">

        {{-- Problem yang Dihadapi --}}
        <div>
            <label class="label"><span class="label-text">Problem yang Dihadapi</span></label>
            <div class="flex flex-wrap gap-4">
                @foreach (['Jerawat', 'Bekas Jerawat', 'Kulit Kering', 'Kulit Kasar', 'Kulit Kusam', 'Kulit Berminyak', 'Keriput', 'Pori-pori Besar', 'Flek', 'Stres/Kelelahan'] as $problem)
                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model="problem_dihadapi" value="{{ $problem }}" class="checkbox checkbox-sm" />
                        <span>{{ $problem }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Lama Problem Dialami --}}
        <div>
            <label for="lama_problem" class="label"><span class="label-text">Lama Problem Dialami (misal: 3 bulan)</span></label>
            <input id="lama_problem" type="text" class="input input-bordered w-full" wire:model.defer="lama_problem">
        </div>

        {{-- Langkah-langkah yang Telah Dilakukan --}}
        <div>
            <label class="label"><span class="label-text">Langkah-langkah yang Telah Dilakukan</span></label>
            <div class="flex flex-wrap gap-4">
                @foreach (['Didiamkan', 'Diobati Sendiri', 'Ke Dokter', 'Ke Salon'] as $tindakan_sebelumnya)
                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model="tindakan_sebelumnya" value="{{ $tindakan_sebelumnya }}" class="checkbox checkbox-sm" />
                        <span>{{ $tindakan_sebelumnya }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Penyakit yang Pernah Dialami --}}
        <div>
            <label for="penyakit_dialami" class="label"><span class="label-text">Penyakit yang Pernah Dialami</span></label>
            <input id="penyakit_dialami" type="text" class="input input-bordered w-full" wire:model.defer="penyakit_dialami">
        </div>

        {{-- Alergi terhadap Obat/Kosmetik --}}
        <div>
            <label for="alergi_kosmetik" class="label"><span class="label-text">Kosmetik atau Obat yang Menimbulkan Alergi</span></label>
            <input id="alergi_kosmetik" type="text" class="input input-bordered w-full" wire:model.defer="alergi_kosmetik">
        </div>

        {{-- Sedang Hamil & Usia Kehamilan (dengan JS) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
            <div>
                <label for="sedang_hamil" class="label"><span class="label-text">Sedang Hamil?</span></label>
                <select id="sedang_hamil" class="select select-bordered w-full" name="sedang_hamil" onchange="toggleKehamilanInput(this)" wire:model.defer="sedang_hamil">
                    <option value="">Pilih</option>
                    <option value="1">Ya</option>
                    <option value="0">Tidak</option>
                </select>
            </div>
            <div id="usia_kehamilan_section" style="display: none;">
                <label for="usia_kehamilan" class="label"><span class="label-text">Usia Kehamilan (bulan)</span></label>
                <input id="usia_kehamilan" type="number" min="1" class="input input-bordered w-full" wire:model.defer="usia_kehamilan">
            </div>
        </div>

        {{-- Metode KB --}}
        <div>
            <label class="label"><span class="label-text">Metode KB yang Digunakan</span></label>
            <div class="flex flex-wrap gap-4">
                @foreach (['Pil', 'Suntik', 'Implant', 'IUD', 'Steril', 'Tidak Menggunakan'] as $kb)
                    <label class="flex items-center gap-2">
                        <input type="radio" wire:model="metode_kb" value="{{ $kb }}" class="radio radio-sm" />
                        <span>{{ $kb }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Pengobatan Dijalani --}}
        <div>
            <label for="pengobatan_saat_ini" class="label"><span class="label-text">Pengobatan Dokter yang Sedang Dijalani</span></label>
            <input id="pengobatan_saat_ini" type="text" class="input input-bordered w-full" wire:model.defer="pengobatan_saat_ini">
        </div>

        {{-- Produk Kosmetik --}}
        <div>
            <label for="produk_kosmetik" class="label"><span class="label-text">Produk Kosmetik yang Sedang Dipakai</span></label>
            <input id="produk_kosmetik" type="text" class="input input-bordered w-full" wire:model.defer="produk_kosmetik">
        </div>

    </div>
</div>

{{-- SCRIPT UNTUK KEHAMILAN --}}
<script>
    function toggleKehamilanInput(selectElement) {
        const section = document.getElementById('usia_kehamilan_section');
        if (selectElement.value === '1') {
            section.style.display = 'block';
        } else {
            section.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('sedang_hamil');
        toggleKehamilanInput(select);
    });
</script>
