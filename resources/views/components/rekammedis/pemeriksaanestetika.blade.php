<div class="bg-base-200 p-4 rounded border border-base-200">
@props([
    'pemeriksaanEstetika' => [
        'warna_kulit' => null,
        'ketebalan_kulit' => null,
        'kadar_minyak' => null,
        'kerapuhan_kulit' => null,
        'kekencangan_kulit' => null,
        'melasma' => null,
        'acne' => [],
        'lesions' => [],
    ]
])
    <div class="divider">Pemeriksaan Kulit & Estetika</div>

    {{-- Grid untuk semua select --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Warna Kulit --}}
        <div>
            <label class="label"><span class="label-text">Warna Kulit</span></label>
            <select wire:model="pemeriksaan_estetika.warna_kulit" class="select select-bordered w-full">
                <option value="">-- Pilih Warna Kulit --</option>
                @foreach (['Kuning (Yellow)', 'Terang (Light)', 'Sedang (Medium)', 'Gelap (Dark)', 'Lainnya'] as $warna_kulit)
                    <option value="{{ $warna_kulit }}">{{ ucfirst($warna_kulit) }}</option>
                @endforeach
            </select>
        </div>

        {{-- Ketebalan Kulit --}}
        <div>
            <label class="label"><span class="label-text">Ketebalan Kulit</span></label>
            <select wire:model="pemeriksaan_estetika.ketebalan_kulit" class="select select-bordered w-full">
                <option value="">-- Pilih Ketebalan Kulit --</option>
                @foreach (['Tipis (Thin)', 'Sedang (Medium)', 'Tebal (Thick)'] as $ketebalan_kulit)
                    <option value="{{ $ketebalan_kulit }}">{{ ucfirst($ketebalan_kulit) }}</option>
                @endforeach
            </select>
        </div>

        {{-- Kadar Minyak Kulit Wajah --}}
        <div>
            <label class="label"><span class="label-text">Kadar Minyak Kulit Wajah</span></label>
            <select wire:model="pemeriksaan_estetika.kadar_minyak" class="select select-bordered w-full">
                <option value="">-- Pilih Kadar Minyak --</option>
                @foreach (['Berminyak (Oily)', 'Normal (Normal)', 'Kering (Dry)', 'Normal ke Kering (Normal to Dry)', 'Kombinasi ke berminyak (Combination to Oily)'] as $kadar_minyak)
                    <option value="{{ $kadar_minyak }}">{{ ucfirst($kadar_minyak) }}</option>
                @endforeach
            </select>
        </div>

        {{-- Kerapuhan Kulit --}}
        <div>
            <label class="label"><span class="label-text">Kerapuhan Kulit</span></label>
            <select wire:model="pemeriksaan_estetika.kerapuhan_kulit" class="select select-bordered w-full">
                <option value="">-- Pilih Kerapuhan Kulit --</option>
                @foreach (['Rapuh (Fragile)', 'Normal (Normal)'] as $kerapuhan_kulit)
                    <option value="{{ $kerapuhan_kulit }}">{{ ucfirst($kerapuhan_kulit) }}</option>
                @endforeach
            </select>
        </div>

        {{-- Kekencangan Kulit --}}
        <div>
            <label class="label"><span class="label-text">Kekencangan Kulit</span></label>
            <select wire:model="pemeriksaan_estetika.kekencangan_kulit" class="select select-bordered w-full">
                <option value="">-- Pilih Kekencangan Kulit --</option>
                @foreach (['Kendur (Lax)', 'Kencang (Firm)'] as $kekencangan_kulit)
                    <option value="{{ $kekencangan_kulit }}">{{ ucfirst($kekencangan_kulit) }}</option>
                @endforeach
            </select>
        </div>

        {{-- Melasma --}}
        <div>
            <label class="label"><span class="label-text">Melasma</span></label>
            <select wire:model="pemeriksaan_estetika.melasma" class="select select-bordered w-full">
                <option value="">-- Pilih Jenis Melasma --</option>
                @foreach (['Superfisial (Superficial)', 'Dalam (Deep)'] as $melasma)
                    <option value="{{ $melasma }}">{{ ucfirst($melasma) }}</option>
                @endforeach
            </select>
        </div>

    </div>

    {{-- Acne (Checkbox) --}}
    <div class="mt-4">
        <label class="label"><span class="label-text">Acne (Jerawat)</span></label>
        <div class="flex flex-wrap gap-4">
            @foreach ([
                        'Inflamasi (Inflamed)', 'Non-inflamasi (Non inflamed)', 'Komedo hitam (Black Heads)',
                        'Komedo putih (White Heads)','Papula (Papules)', 'Nodul (Nodules)',
                        'Kista (Cysts)', 'Bekas jerawat (Scars)', 'Pori-pori besar (Large Pores)', 'Pustula (Pustulae)'
                    ] as $acne)
                <label class="flex items-center gap-2">
                    <input type="checkbox" wire:model="pemeriksaan_estetika.acne" value="{{ $acne }}" class="checkbox checkbox-sm" />
                    <span>{{ ucfirst($acne) }}</span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- Lesions (Checkbox) --}}
    <div class="mt-4">
        <label class="label"><span class="label-text">Lesi Kulit Lainnya</span></label>
        <div class="flex flex-wrap gap-4">
            @foreach ([
                    'Skin Tags','Actinic/Seborrheic Keratoses', 'Sebaceous Gland Hyperplasia', 'Telangiectasia',
                    'Fine Lines/Wringkles/Dark Cirlce', 'Lentigo Solaris/Freckles', 'PIH'
                ] as $lesion)
                <label class="flex items-center gap-2">
                    <input type="checkbox" wire:model="pemeriksaan_estetika.lesions" value="{{ $lesion }}" class="checkbox checkbox-sm" />
                    <span>{{ ucfirst($lesion) }}</span>
                </label>
            @endforeach
        </div>
    </div>
</div>
