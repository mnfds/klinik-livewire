<div class="pt-1 pb-12"
    x-data="{ 
        totalBundling: 0, 
        totalTreatment: 0, 
        totalProduk: 0 
    }"
    x-init="
        window.addEventListener('total-bundling-updated', e => totalBundling = e.detail);
        window.addEventListener('total-treatment-updated', e => totalTreatment = e.detail);
        window.addEventListener('total-produk-updated', e => totalProduk = e.detail);
    "
>
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
                        <a href="{{ route('pasien.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Pasien
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pasien.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Riwayat Kunjungan
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pasien.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Rekam Medis
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pasien.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Isi Rekam Medis
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-lg font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Rekam Medis Pasien
            </h1>
        </div>

        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                <!-- Kolom Kiri -->
                <div class="lg:col-span-3 space-y-6">

                    <!-- Biodata -->
                    <div class="bg-base-100 text-base-content shadow rounded-box p-6 space-y-4">
                        <h2 class="text-lg font-semibold border-b pb-2">Biodata Pasien</h2>
                        <div class="space-y-2 text-sm mt-2">
                            <!-- Baris 1 -->
                            <div class="grid grid-cols-2 gap-x-6">
                                <div class="flex">
                                    <div class="w-32 font-bold">Nama</div>
                                    <div>: {{ $pasienTerdaftar->pasien->nama }}</div>
                                </div>
                                <div class="flex">
                                    <div class="w-32 font-bold">No. RM / No. IHS</div>
                                    <div>: {{ $pasienTerdaftar->pasien->no_register }} / {{ $pasienTerdaftar->pasien->no_ihs }}</div>
                                </div>
                            </div>

                            <!-- Baris 2 -->
                            <div class="grid grid-cols-2 gap-x-6">
                                <div class="flex">
                                    <div class="w-32 font-bold">Jenis Kelamin</div>
                                    <div>: {{ $pasienTerdaftar->pasien->jenis_kelamin }}</div>
                                </div>
                                <div class="flex">
                                    <div class="w-32 font-bold">Tanggal Lahir</div>
                                    <div>: {{ \Carbon\Carbon::parse($pasienTerdaftar->pasien->tanggal_lahir)->translatedFormat('d F Y') }}</div>
                                </div>
                                {{-- <div class="flex">
                                    <div class="w-32 font-bold">No. IHS</div>
                                    <div>: {{ $pasienTerdaftar->pasien->no_ihs }}</div>
                                </div> --}}
                            </div>

                            <!-- Baris 3 -->
                            {{-- <div class="grid grid-cols-2 gap-x-6">
                                <div class="flex">
                                    <div class="w-32 font-bold">Jenis Kelamin</div>
                                    <div>: {{ $pasienTerdaftar->pasien->jenis_kelamin }}</div>
                                </div>
                                <div class="flex">
                                    <div class="w-32 font-bold">NIK</div>
                                    <div>: {{ $pasienTerdaftar->pasien->nik }}</div>
                                </div>
                            </div> --}}

                        </div>
                    </div>

                    <!-- Informasi Kajian dll -->
                    <div class="bg-base-100 text-base-content shadow rounded-box p-6 space-y-4">
                        <div class="tabs tabs-lift">
                            <!-- A: Hasil Kajian Awal -->
                            <label class="tab gap-2 cursor-pointer">
                                <input type="radio" name="my_tabs_3" class="hidden" checked />
                                <i class="fa-solid fa-clipboard-list"></i>
                                Hasil Kajian
                            </label>
                            <div class="tab-content bg-base-100 border-base-300 p-6 text-base-content">
                                <h2 class="text-lg font-semibold border-b pb-2">Hasil Kajian Awal (Anamnesa)</h2>
                                <div class="space-y-2 text-sm">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">

                                        {{-- Pemeriksaan Fisik --}}
                                        @if ($kajian?->pemeriksaanFisik)
                                            <div>
                                                <div class="font-semibold mb-1">Pemeriksaan Fisik</div>
                                                <div class="grid grid-cols-[auto_1fr] gap-x-2 gap-y-1">
                                                    <div>Tinggi Badan</div><div>: {{ $kajian->pemeriksaanFisik->tinggi_badan }} cm</div>
                                                    <div>Berat Badan</div><div>: {{ $kajian->pemeriksaanFisik->berat_badan }} kg</div>
                                                    <div>IMT</div><div>: {{ $kajian->pemeriksaanFisik->imt }}</div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Tanda Vital --}}
                                        @if ($kajian?->tandaVital)
                                            <div>
                                                <div class="font-semibold mb-1">Tanda Vital</div>
                                                <div class="grid grid-cols-[auto_1fr] gap-x-2 gap-y-1">
                                                    <div>Suhu Tubuh</div><div>: {{ $kajian->tandaVital->suhu_tubuh }} °C</div>
                                                    <div>Nadi</div><div>: {{ $kajian->tandaVital->nadi }} bpm</div>
                                                    <div>Tekanan Darah</div><div>: {{ $kajian->tandaVital->sistole }}/{{ $kajian->tandaVital->diastole }} mmHg</div>
                                                    <div>Frekuensi Napas</div><div>: {{ $kajian->tandaVital->frekuensi_pernapasan }} /menit</div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Data Kesehatan --}}
                                        @if ($kajian?->dataKesehatan)
                                            @php
                                                $riwayat_penyakit = json_decode($kajian->dataKesehatan->riwayat_penyakit ?? '[]', true);
                                                $riwayat_alergi_obat = json_decode($kajian->dataKesehatan->riwayat_alergi_obat ?? '[]', true);
                                                $obat = json_decode($kajian->dataKesehatan->obat_sedang_dikonsumsi ?? '[]', true);
                                                $alergi_lain = json_decode($kajian->dataKesehatan->riwayat_alergi_lainnya ?? '[]', true);
                                            @endphp

                                            <div class="md:col-span-2">
                                                <div class="font-semibold mb-1">Data Kesehatan</div>
                                                <div class="grid grid-cols-[auto_1fr] gap-x-2 gap-y-1">
                                                    <div>Keluhan Utama</div><div>: {{ $kajian->dataKesehatan->keluhan_utama ?? '-' }}</div>
                                                    <div>Status Perokok</div><div>: {{ $kajian->dataKesehatan->status_perokok ?? '-' }}</div>

                                                    <div>Riwayat Penyakit</div>
                                                    <div>:
                                                        @if(empty($riwayat_penyakit))
                                                            -
                                                        @else
                                                            <ul class="ml-4 list-disc">
                                                                @foreach ($riwayat_penyakit as $r)
                                                                    <li>{{ $r }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>

                                                    <div>Riwayat Alergi Obat</div>
                                                    <div>:
                                                        @if(empty($riwayat_alergi_obat))
                                                            -
                                                        @else
                                                            <ul class="ml-4 list-disc">
                                                                @foreach ($riwayat_alergi_obat as $r)
                                                                    <li>{{ $r }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>

                                                    <div>Obat Dikonsumsi</div>
                                                    <div>:
                                                        @if(empty($obat))
                                                            -
                                                        @else
                                                            <ul class="ml-4 list-disc">
                                                                @foreach ($obat as $r)
                                                                    <li>{{ $r }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>

                                                    <div>Alergi Lainnya</div>
                                                    <div>:
                                                        @if(empty($alergi_lain))
                                                            -
                                                        @else
                                                            <ul class="ml-4 list-disc">
                                                                @foreach ($alergi_lain as $r)
                                                                    <li>{{ $r }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Data Estetika --}}
                                        @if ($kajian?->dataEstetika)
                                            @php
                                                $decode = fn($v) => $v ? (is_array(json_decode($v, true)) ? json_decode($v, true) : [$v]) : [];
                                                $problem = $decode($kajian->dataEstetika->problem_dihadapi);
                                                $tindakan = $decode($kajian->dataEstetika->tindakan_sebelumnya);
                                                $metode_kb = $decode($kajian->dataEstetika->metode_kb);
                                            @endphp

                                            <div class="md:col-span-2">
                                                <div class="font-semibold mb-1">Data Estetika</div>
                                                <div class="grid grid-cols-[auto_1fr] gap-x-2 gap-y-1">
                                                    <div>Problem Dihadapi</div>
                                                    <div>:
                                                        @if(empty($problem))
                                                            -
                                                        @else
                                                            <ul class="ml-4 list-disc">
                                                                @foreach ($problem as $r)
                                                                    <li>{{ $r }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>

                                                    <div>Lama Problem</div><div>: {{ $kajian->dataEstetika->lama_problem ?? '-' }}</div>

                                                    <div>Tindakan Sebelumnya</div>
                                                    <div>:
                                                        @if(empty($tindakan))
                                                            -
                                                        @else
                                                            <ul class="ml-4 list-disc">
                                                                @foreach ($tindakan as $r)
                                                                    <li>{{ $r }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>

                                                    <div>Penyakit Dialami</div><div>: {{ $kajian->dataEstetika->penyakit_dialami ?? '-' }}</div>
                                                    <div>Alergi Kosmetik</div><div>: {{ $kajian->dataEstetika->alergi_kosmetik ?? '-' }}</div>
                                                    <div>Sedang Hamil</div><div>: {{ ucfirst($kajian->dataEstetika->sedang_hamil) ?? '-' }}</div>
                                                    <div>Usia Kehamilan</div><div>: {{ $kajian->dataEstetika->usia_kehamilan ? $kajian->dataEstetika->usia_kehamilan . ' bln' : '-' }}</div>

                                                    <div>Metode KB</div>
                                                    <div>:
                                                        @if(empty($metode_kb))
                                                            -
                                                        @else
                                                            <ul class="ml-4 list-disc">
                                                                @foreach ($metode_kb as $r)
                                                                    <li>{{ $r }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>

                                                    <div>Pengobatan Saat Ini</div><div>: {{ $kajian->dataEstetika->pengobatan_saat_ini ?? '-' }}</div>
                                                    <div>Produk Kosmetik</div><div>: {{ $kajian->dataEstetika->produk_kosmetik ?? '-' }}</div>
                                                </div>
                                            </div>
                                        @endif

                                        @if (!$kajian)
                                            <p class="mt-2">Tidak tersedia data kajian awal.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!-- A: Layanan Tersisa -->
                            @php
                                $totalBundlingItems = 0;

                                foreach ($bundlingPasien['treatments'] ?? [] as $t) {
                                    if (($t->jumlah_awal - $t->jumlah_terpakai) > 0) $totalBundlingItems++;
                                }

                                foreach ($bundlingPasien['pelayanans'] ?? [] as $p) {
                                    if (($p->jumlah_awal - $p->jumlah_terpakai) > 0) $totalBundlingItems++;
                                }

                                foreach ($bundlingPasien['produks'] ?? [] as $pr) {
                                    if (($pr->jumlah_awal - $pr->jumlah_terpakai) > 0) $totalBundlingItems++;
                                }
                            @endphp
                            <label class="tab bg-transparent text-base-content gap-2 cursor-pointer">
                                <input type="radio" name="my_tabs_3" class="hidden" />
                                <span class="flex items-center gap-2">
                                    <i class="fa-solid fa-hand-holding-heart"></i>
                                    Layanan Tersisa
                                    <div class="badge badge-sm badge-primary text-base-primary">{{ $totalBundlingItems }}</div>
                                </span>
                            </label>

                            <div class="tab-content bg-base-100 border-base-300 p-6 text-base-content">
                                <h3 class="font-semibold mb-3">Layanan / Tindakan Tersisa</h3>

                                @php
                                    // Gabungkan semua data ke dalam array per bundling_id
                                    $grouped = [];

                                    foreach ($bundlingPasien['treatments'] as $t) {
                                        $grouped[$t->bundling_id]['nama'] = $t->bundling->nama;
                                        $grouped[$t->bundling_id]['treatments'][] = $t;
                                    }

                                    foreach ($bundlingPasien['pelayanans'] as $p) {
                                        $grouped[$p->bundling_id]['nama'] = $p->bundling->nama;
                                        $grouped[$p->bundling_id]['pelayanans'][] = $p;
                                    }

                                    foreach ($bundlingPasien['produks'] as $pr) {
                                        $grouped[$pr->bundling_id]['nama'] = $pr->bundling->nama;
                                        $grouped[$pr->bundling_id]['produks'][] = $pr;
                                    }
                                @endphp

                                @if(count($grouped))
                                    <ul class="space-y-4">
                                        @foreach($grouped as $bundling)
                                            <li class="border rounded-lg p-3">
                                                <p class="font-semibold">{{ $bundling['nama'] }}</p>

                                                {{-- Treatments --}}
                                                @if(!empty($bundling['treatments']))
                                                    <p class="text-sm font-medium mt-2">Treatments</p>
                                                    <ul class="list-disc list-inside text-sm">
                                                        @foreach($bundling['treatments'] as $t)
                                                            @php
                                                                $sisatreatment = $t->jumlah_awal - $t->jumlah_terpakai;
                                                                $sudahDipilihTreatment = false;

                                                                if(isset($layananTerpilih[$t->bundling->nama])) {
                                                                    foreach($layananTerpilih[$t->bundling->nama] as $item) {
                                                                        if($item['id'] == $t->id && $item['tipe'] == 'treatment') {
                                                                            $sudahDipilihTreatment = true;
                                                                            break;
                                                                        }
                                                                    }
                                                                }
                                                            @endphp
                                                            <li class="pb-1">
                                                                {{ $t->treatment->nama_treatment }}
                                                                <span class="badge border-1 badge-sm ml-2 {{ $sisatreatment > 0 ? 'badge-accent' : 'badge-error' }}">
                                                                    Sisa: {{ $sisatreatment }}
                                                                </span>
                                                                @if ($sisatreatment > 0)
                                                                    <button
                                                                        class="btn btn-xs btn-square btn-success {{ $sudahDipilihTreatment && $sisatreatment == 1 ? 'btn-disabled opacity-50 cursor-not-allowed' : '' }}"
                                                                        wire:click="tambahLayananBundling(
                                                                            {{ $t->id }},
                                                                            'treatment',
                                                                            '{{ addslashes($t->treatment->nama_treatment) }}',
                                                                            {{ $sisatreatment }},
                                                                            '{{ addslashes($t->bundling->nama) }}'
                                                                        )"
                                                                        @if($sudahDipilihTreatment && $sisatreatment == 1) disabled @endif
                                                                    >
                                                                        <i class="fa-solid fa-plus"></i>
                                                                    </button>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif

                                                {{-- Pelayanans --}}
                                                @if(!empty($bundling['pelayanans']))
                                                    <p class="text-sm font-medium mt-2">Pelayanan</p>
                                                    <ul class="list-disc list-inside text-sm">
                                                        @foreach($bundling['pelayanans'] as $p)
                                                            @php
                                                                $sisapelayanan = $p->jumlah_awal - $p->jumlah_terpakai;
                                                                $sudahDipilihPelayanan = false;

                                                                if(isset($layananTerpilih[$p->bundling->nama])) {
                                                                    foreach($layananTerpilih[$p->bundling->nama] as $item) {
                                                                        if($item['id'] == $p->id && $item['tipe'] == 'pelayanan') {
                                                                            $sudahDipilihPelayanan = true;
                                                                            break;
                                                                        }
                                                                    }
                                                                }
                                                            @endphp
                                                            <li>
                                                                {{ $p->pelayanan->nama_pelayanan }}
                                                                <span class="badge border-1 badge-sm ml-2 {{ $sisapelayanan > 0 ? 'badge-accent' : 'badge-error' }}">
                                                                    Sisa: {{ $sisapelayanan }}
                                                                </span>
                                                                @if ($sisapelayanan > 0)
                                                                <span>
                                                                    <button
                                                                        class="btn btn-xs btn-square btn-success {{ $sudahDipilihPelayanan && $sisapelayanan == 1 ? 'btn-disabled opacity-50 cursor-not-allowed' : '' }}"
                                                                        wire:click="tambahLayananBundling(
                                                                        {{ $p->id }},
                                                                        'pelayanan',
                                                                        '{{ addslashes($p->pelayanan->nama_pelayanan) }}',
                                                                        {{ $sisapelayanan }},
                                                                        '{{ addslashes($p->bundling->nama) }}'
                                                                        )"
                                                                        @if($sudahDipilihPelayanan && $sisapelayanan == 1) disabled @endif
                                                                    >
                                                                        <i class="fa-solid fa-plus"></i>
                                                                    </button>
                                                                </span>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif

                                                {{-- Produks --}}
                                                @if(!empty($bundling['produks']))
                                                    <p class="text-sm font-medium mt-2">Produk / Obat</p>
                                                    <ul class="list-disc list-inside text-sm">
                                                        @foreach($bundling['produks'] as $pr)
                                                            @php 
                                                                $sisaproduk = $pr->jumlah_awal - $pr->jumlah_terpakai;
                                                                $sudahDipilihProduk = false;
                                                                if(isset($layananTerpilih[$pr->bundling->nama])) {
                                                                    foreach($layananTerpilih[$pr->bundling->nama] as $item) {
                                                                        if($item['id'] == $pr->id && $item['tipe'] == 'produk') {
                                                                            $sudahDipilihProduk = true;
                                                                            break;
                                                                        }
                                                                    }
                                                                }
                                                            @endphp
                                                            <li>
                                                                {{ $pr->produk->nama_dagang }}
                                                                <span class="badge border-1 badge-sm ml-2 {{ $sisaproduk > 0 ? 'badge-accent' : 'badge-error' }} ">
                                                                    Sisa:{{ $sisaproduk }}
                                                                </span>
                                                                @if ($sisaproduk > 0)
                                                                <span>
                                                                    <button
                                                                        class="btn btn-xs btn-square btn-success {{ $sudahDipilihProduk && $sisaproduk == 1 ? 'btn-disabled opacity-50 cursor-not-allowed' : '' }}"
                                                                        wire:click="tambahLayananBundling(
                                                                        {{ $pr->id }}, 'produk',
                                                                        '{{ addslashes($pr->produk->nama_dagang) }}',
                                                                        {{ $sisaproduk }},
                                                                        '{{ addslashes($pr->bundling->nama) }}'
                                                                        )"
                                                                        @if($sudahDipilihProduk && $sisaproduk == 1) disabled @endif
                                                                    >
                                                                        <i class="fa-solid fa-plus"></i>
                                                                    </button>
                                                                </span>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-sm text-gray-500">Tidak ada layanan tersisa</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Form -->
                    <div class="bg-base-100 shadow rounded-box p-6 space-y-6">
                        <h2 class="text-lg font-semibold border-b pb-2">Form Rekam Medis</h2>
                        <form wire:submit.prevent="create" class="space-y-6">
                            <input type="hidden" wire:model.defer='nama_dokter'>
                            <input type="hidden" wire:model.defer='pasien_terdaftar_id'>
                            {{-- NAVIGATION
                            <div class="bg-base-100 text-base-content shadow rounded-box py-4 px-8 flex gap-6 justify-between sticky top-0 z-50">
                                <a href="#subjective" class="font-semibold hover:text-primary">SUBJECTIVE</a>
                                <a href="#objective" class="font-semibold hover:text-primary">OBJECTIVE</a>
                                <a href="#assessment" class="font-semibold hover:text-primary">ASSESSMENT</a>
                                <a href="#plan" class="font-semibold hover:text-primary">PLAN</a>
                            </div> --}}

                            {{-- TABS SOAP --}}
                            <div class="tabs tabs-lift">

                                {{-- TABS SUBJECTIVE --}}
                                <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="SUBJECTIVE" style="background-image: none;" checked/>
                                <div class="tab-content bg-base-100 border-base-300 p-2">
                                    {{-- SUBJECTIVE --}}
                                    <div id="subjective" class="bg-base-200 shadow rounded-lg py-6 px-3 scroll-mt-16"
                                        x-data="formChoicesSubjective()" 
                                        x-init="initChoicesSubjective()" 
                                        x-effect="$wire.selected_forms_subjective = selectedFormsSubjective">

                                        <h2 class="text-lg font-semibold mb-4 border-b pb-2">SUBJECTIVE</h2>
                                        <!-- Select Multiple with Choices.js -->
                                        <div wire:ignore>
                                            <label class="label font-semibold">Pilih Form yang Ingin Ditampilkan:</label>
                                            <select id="formSelect" multiple class="w-full hidden select" x-ref="formSelectSubjective">
                                                @if ($pasienTerdaftar->poliklinik->nama_poli == 'Poli Kecantikan')
                                                    <option value="data-estetika" @selected($pasienTerdaftar->poliklinik->nama_poli == 'Poli Kecantikan')>Data Estetika</option>
                                                @elseif($pasienTerdaftar->poliklinik->nama_poli == 'Poli Umum')
                                                    <option value="data-kesehatan" @selected($pasienTerdaftar->poliklinik->nama_poli == 'Poli Umum')>Data Kesehatan</option>
                                                @elseif($pasienTerdaftar->poliklinik->nama_poli == 'Poli Gigi')
                                                    <option value="data-kesehatan" @selected($pasienTerdaftar->poliklinik->nama_poli == 'Poli Gigi')>Data Kesehatan</option>
                                                @endif
                                            </select>
                                        </div>
                                        <!-- Keluhan Utama -->
                                        <div class="form-control md:col-span-2">
                                            <label class="label">Keluhan Pasien</label>
                                            <input type="text" value="{{ $kajian->dataKesehatan->keluhan_utama ?? null }}" wire:model="keluhan_utama" placeholder="Keluhan Utama" class="input input-bordered w-full" />
                                        </div>
                                        <!-- DATA KESEHATAN -->
                                        <div x-show="selectedFormsSubjective.includes('data-kesehatan')" style="display: none">
                                            <x-rekammedis.datakesehatan :dataKesehatan="$data_kesehatan" wire:model="data_kesehatan" />
                                        </div>
                                        <!-- DATA ESTETIKA -->
                                        <div x-show="selectedFormsSubjective.includes('data-estetika')" style="display: none">
                                            <x-rekammedis.dataestetika :dataEstetika="$data_estetika" wire:model="data_estetika" />
                                        </div>
                                    </div>
                                </div>
                                {{-- TABS OBJECTIVE --}}
                                <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="OBJECTIVE" style="background-image: none;"/>
                                <div class="tab-content bg-base-100 border-base-300 p-2">
                                    {{-- OBJECTIVE --}}
                                    <div id="objective" class="bg-base-200 shadow rounded-lg py-6 px-3 scroll-mt-16"
                                        x-data="formChoicesObjective()"
                                        x-init="initChoicesObjective()"
                                        x-effect="$wire.selected_forms_objective = selectedFormsObjective">
                                            
                                        <h2 class="text-lg font-semibold mb-4 border-b pb-2">OBJECTIVE</h2>
                                        <!-- Select Multiple with Choices.js -->
                                        <div wire:ignore>
                                            <label class="label font-semibold">Pilih Form yang Ingin Ditampilkan:</label>
                                            <select id="formSelect" multiple class="w-full hidden select" x-ref="formSelectObjective">
                                            @if ($pasienTerdaftar->poliklinik->nama_poli == 'Poli Kecantikan')
                                                <option value="pemeriksaan-estetika" @selected($pasienTerdaftar->poliklinik->nama_poli == 'Poli Kecantikan')>Pemeriksaan Kulit & Estetika</option>
                                            @elseif($pasienTerdaftar->poliklinik->nama_poli == 'Poli Umum')
                                                <option value="tanda-vital" @selected($pasienTerdaftar->poliklinik->nama_poli == 'Poli Umum')>Tanda Vital Pasien</option>
                                                <option value="pemeriksaan-fisik" @selected($pasienTerdaftar->poliklinik->nama_poli == 'Poli Umum')>Pemeriksaan Fisik</option>
                                            @elseif($pasienTerdaftar->poliklinik->nama_poli == 'Poli Gigi')
                                                <option value="tanda-vital" @selected($pasienTerdaftar->poliklinik->nama_poli == 'Poli Gigi')>Tanda Vital Pasien</option>
                                                <option value="pemeriksaan-fisik" @selected($pasienTerdaftar->poliklinik->nama_poli == 'Poli Gigi')>Pemeriksaan Fisik</option>
                                            @endif
                                            </select>
                                        </div>
                                        <!-- Tingkat Kesadaran -->
                                        <div class="form-control">
                                            <label class="label">Tingkat Kesadaran</label>
                                            <select wire:model="tingkat_kesadaran" class="select select-bordered w-full">
                                                <option value="">Pilih Tingkatan</option>
                                                <option value="Sadar Baik/Alert">Sadar Baik/Alert</option>
                                                <option value="Berespon dengan kata-kata">Berespon dengan kata-kata</option>
                                                <option value="Hanya berespons jika dirangsang nyeri"> Hanya berespons jika dirangsang nyeri</option>
                                                <option value="Pasien tidak sadar">Pasien tidak sadar</option>
                                                <option value="Gelisah atau bingung">Gelisah atau bingung</option>
                                                <option value="Acute Confusional States">Acute Confusional States</option>
                                            </select>
                                        </div>
                                        <!-- Tanda Vital -->
                                        <div x-show="selectedFormsObjective.includes('tanda-vital')" style="display: none">
                                            <x-rekammedis.tandavital :tandaVital="$tanda_vital" wire:model="tanda_vital" />
                                        </div>
                                        <!-- Data Fisik -->
                                        <div x-show="selectedFormsObjective.includes('pemeriksaan-fisik')" style="display: none">
                                            <x-rekammedis.pemeriksaanfisik :pemeriksaanFisik="$pemeriksaan_fisik" wire:model="pemeriksaan_fisik" />
                                        </div>
                                        <!-- Data Kulit Dan Estetika -->
                                        <div x-show="selectedFormsObjective.includes('pemeriksaan-estetika')" style="display: none">
                                            <x-rekammedis.pemeriksaanestetika :pemeriksaanEstetika="$pemeriksaan_estetika" wire:model="pemeriksaan_estetika" />
                                        </div>
                                    </div>
                                </div>
                                {{-- TABS ASSESSMENT --}}
                                <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="ASSESSMENT" style="background-image: none;"/>
                                <div class="tab-content bg-base-100 border-base-300 p-2">
                                    {{-- ASSESSMENT --}}
                                    <div id="assessment" class="bg-base-200 shadow rounded-lg py-6 px-3 scroll-mt-16"
                                        x-data="formChoicesAssessment()"
                                        x-init="initChoicesAssessment()"
                                        x-effect="$wire.selected_forms_assessment = selectedFormsAssessment">
                                            
                                        <h2 class="text-lg font-semibold mb-4 border-b pb-2">ASSESSMENT</h2>
                                        <!-- Select Multiple with Choices.js -->
                                        {{-- <div wire:ignore>
                                            <label class="label font-semibold">Pilih Form yang Ingin Ditampilkan:</label>
                                            <select id="formSelect" multiple class="w-full hidden select" x-ref="formSelectAssessment">
                                                <option value="diagnosa">Diagnosa</option>
                                                <option value="icd_10">ICD 10</option>
                                            </select>
                                        </div> --}}
                                            
                                        <!-- ICD 10 -->
                                        {{-- <div x-show="selectedFormsAssessment.includes('icd_10')" style="display: none"> --}}
                                            <div class="form-control" x-data="multiSelectIcd10()" x-init="init()">
                                                <label class="label">ICD 10</label>

                                                <!-- Input Area -->
                                                <div class="relative" @click="setTimeout(() => open = true, 10)">
                                                    <div class="w-full border border-gray-300 bg-base-100 rounded-2xl p-1 flex flex-wrap items-center gap-2 min-h-[2.5rem] focus-within:ring-2 focus-within:ring-black transition"
                                                        :class="{ 'ring-2 ring-black': open }">

                                                        <!-- Selected tags -->
                                                        <template x-for="(tag, index) in selected" :key="tag.code">
                                                            <span class="bg-primary text-sm rounded-full px-3 py-1 flex items-center gap-1">
                                                                <span x-text="`${tag.code} - ${tag.name_id}`"></span>
                                                                <button type="button" @click.stop="remove(tag.code)">×</button>
                                                            </span>
                                                        </template>

                                                        <!-- Input search -->
                                                        <input type="text"
                                                            class="flex-grow min-w-[8ch] text-sm border-none rounded-xl bg-base-100"
                                                            placeholder="Ketik disini untuk cari Diagnosa ICD 10..."
                                                            x-model="search"
                                                            @focus="open = true"
                                                            @input.debounce.300ms="fetchOptions(); open = true" />
                                                    </div>

                                                    <!-- Dropdown Menu -->
                                                    <div x-show="open" @click.outside="open = false"
                                                        class="absolute z-10 mt-1 w-full bg-base-200 border border-gray-500 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                                                        <template x-if="filteredOptions.length > 0">
                                                            <template x-for="item in filteredOptions" :key="item.code">
                                                                <div @click="toggle(item)"
                                                                    class="px-3 py-2 hover:bg-primary/50 rounded-2xl cursor-pointer text-sm m-1"
                                                                    :class="selected.some(s => s.code === item.code) ? 'bg-primary rounded-2xl font-semibold' : ''">
                                                                    <span x-text="`${item.code} - ${item.name_id}`"></span>
                                                                </div>
                                                            </template>
                                                        </template>

                                                        <div x-show="filteredOptions.length === 0"
                                                            class="px-3 py-2 text-sm text-base-content">
                                                            Tidak ada hasil.
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Binding ke Livewire: kirim array full object -->
                                                <input type="hidden" wire:model="icd10" :value="JSON.stringify(selected)">

                                                <span class="text-xs text-gray-400 mt-1">* Klik untuk pilih, klik ulang untuk hapus</span>
                                            </div>
                                        {{-- </div> --}}

                                        <!-- Diagnosa -->
                                        {{-- <div x-show="selectedFormsAssessment.includes('diagnosa')" style="display: none"> --}}
                                            <div class="form-control">
                                                <label class="label block mb-1">
                                                    <span class="label-text">Diagnosa</span>
                                                </label>
                                                <textarea wire:model="diagnosa" class="textarea textarea-bordered w-full"
                                                    placeholder="Tuliskan diagnosis utama pasien, misal: Demam tifoid, hipertensi tahap 2">
                                                </textarea>
                                            </div>
                                        {{-- </div> --}}
                                    </div>  
                                </div>
                                {{-- TABS PLAN --}}
                                <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="PLAN" style="background-image: none;"/>
                                <div class="tab-content bg-base-100 border-base-300 p-2">
                                    {{-- PLAN --}}
                                    <div id="plan" class="bg-base-200 shadow rounded-lg py-6 px-3 scroll-mt-16"
                                        x-data="formChoicesPlan()"
                                        x-init="initChoicesPlan()"
                                        x-effect="$wire.selected_forms_plan = selectedFormsPlan">

                                        <h2 class="text-lg font-semibold mb-4 border-b pb-2">PLAN</h2>
                                        <!-- Select Multiple dengan Choices.js -->
                                        <div wire:ignore>
                                            <label class="label font-semibold">Pilih Form yang Ingin Ditampilkan:</label>
                                            <select id="formSelect" multiple class="w-full hidden select" x-ref="formSelect">
                                                @if ($pasienTerdaftar->poliklinik->nama_poli == 'Poli Kecantikan')
                                                    <option value="rencana-estetika" @selected($pasienTerdaftar->poliklinik->nama_poli == 'Poli Kecantikan')>Rencana Tindakan Estetika</option>
                                                    <option value="rencana-bundling" @selected($pasienTerdaftar->poliklinik->nama_poli == 'Poli Kecantikan')>Paket bundling</option>
                                                    <option value="obat-estetika" @selected($pasienTerdaftar->poliklinik->nama_poli == 'Poli Kecantikan')>Produk Estetika</option>
                                                @elseif($pasienTerdaftar->poliklinik->nama_poli == 'Poli Umum')
                                                    <option value="obat-non-racikan" @selected($pasienTerdaftar->poliklinik->nama_poli == 'Poli Umum')>Obat Non Racikan</option>
                                                    <option value="obat-racikan" @selected($pasienTerdaftar->poliklinik->nama_poli == 'Poli Umum')>Obat Racikan</option>
                                                    <option value="rencana-layanan" @selected($pasienTerdaftar->poliklinik->nama_poli == 'Poli Umum')>Rencana Tindakan Medis</option>
                                                @elseif($pasienTerdaftar->poliklinik->nama_poli == 'Poli Gigi')
                                                    <option value="rencana-layanan" @selected($pasienTerdaftar->poliklinik->nama_poli == 'Poli Gigi')>Rencana Tindakan Medis</option>
                                                    <option value="obat-non-racikan" @selected($pasienTerdaftar->poliklinik->nama_poli == 'Poli Gigi')>Obat Non Racikan</option>
                                                    <option value="obat-racikan" @selected($pasienTerdaftar->poliklinik->nama_poli == 'Poli Gigi')>Obat Racikan</option>
                                                @endif
                                            </select>
                                        </div>

                                        <!-- RENCANA TINDAKAN ESTETIKA -->
                                        <div x-show="selectedFormsPlan.includes('rencana-estetika')" style="display: none" wire:ignore >
                                            <x-rekammedis.rencanaestetika 
                                                :layanandanbundling="$layanandanbundling" 
                                                :rencanaEstetika="$rencana_estetika"
                                            />
                                        </div>

                                        <!-- RENCANA LAYANAN/TINDAKAN -->
                                        <div x-show="selectedFormsPlan.includes('rencana-layanan')" style="display: none" wire:ignore >
                                            <x-rekammedis.rencanalayanan 
                                                :layanandanbundling="$layanandanbundling" 
                                                :rencanaLayanan="$rencana_layanan"
                                            />
                                        </div>

                                        <!-- RENCANA BUNDLING -->
                                        <div x-show="selectedFormsPlan.includes('rencana-bundling')" style="display: none" wire:ignore >
                                            <x-rekammedis.rencanabundling 
                                                :layanandanbundling="$layanandanbundling" 
                                                :rencanaBundling="$rencana_bundling"
                                            />
                                        </div>
                                        
                                        <!--  PENGGUNAAN LAYANAN TERSISA -->
                                        <div class="bg-base-200 p-4 rounded border border-base-200">
                                            <div class="divider">Penggunaan Layanan Tersisa</div>

                                            <div class="mt-4 p-4 border rounded-lg bg-base-100 space-y-3">
                                                <div class="form-control">
                                                    @if(count($layananTerpilih))
                                                        <ul class="space-y-4">
                                                            @foreach($layananTerpilih as $bundlingName => $items)
                                                                <li class="border border-base-300 rounded-lg p-3 bg-base-100">
                                                                    <p class="font-semibold text-primary mb-2">{{ $bundlingName }}</p>
                                                                    <ul class="space-y-2">
                                                                        @foreach($items as $index => $item)
                                                                            <li class="p-3 bg-base-200 border rounded flex justify-between items-center">
                                                                                <div>
                                                                                    <p class="font-medium">{{ $item['nama'] }}</p>
                                                                                    <p class="text-xs text-gray-500 capitalize">{{ $item['tipe'] }}</p>
                                                                                </div>

                                                                                <div class="flex items-center gap-2">
                                                                                    <span class="text-sm font-medium">
                                                                                        {{ $item['dipakai'] }} / {{ $item['sisa'] }}
                                                                                    </span>

                                                                                    <div class="flex items-center gap-1">
                                                                                        <button
                                                                                            class="btn btn-xs btn-error"
                                                                                            type="button"
                                                                                            wire:click="kurangiLayanan('{{ $bundlingName }}', {{ $index }})"
                                                                                            @disabled($item['dipakai'] <= 1)
                                                                                        >
                                                                                            <i class="fa-solid fa-minus"></i>
                                                                                        </button>

                                                                                        <button
                                                                                            class="btn btn-xs btn-success"
                                                                                            type="button"
                                                                                            wire:click="tambahLayanan('{{ $bundlingName }}', {{ $index }})"
                                                                                            @disabled($item['dipakai'] >= $item['sisa'])
                                                                                        >
                                                                                            <i class="fa-solid fa-plus"></i>
                                                                                        </button>
                                                                                    </div>

                                                                                    <button
                                                                                        class="btn btn-xs btn-error"
                                                                                        type="button"
                                                                                        wire:click="hapusLayanan('{{ $bundlingName }}', {{ $index }})"
                                                                                    >
                                                                                        <i class="fa-solid fa-trash"></i>
                                                                                    </button>
                                                                                </div>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <p class="text-sm text-gray-500">Belum ada layanan yang dipilih</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- PRODUK DAN OBAT ESTETIKA -->
                                        <div x-show="selectedFormsPlan.includes('obat-estetika')" style="display: none" wire:ignore >
                                            <x-rekammedis.obatestetika 
                                                :layanandanbundling="$layanandanbundling"
                                                :obatEstetika="$obat_estetika"
                                            />
                                        </div>

                                        <!-- OBAT NON RACIK -->
                                        <div x-show="selectedFormsPlan.includes('obat-non-racikan')" style="display: none" wire:ignore >
                                            <x-rekammedis.obatnonracikan 
                                                :obatNonRacikan="$obat_non_racikan"
                                            />
                                        </div>

                                        <!-- OBAT RACIKAN -->
                                        <div x-show="selectedFormsPlan.includes('obat-racikan')" style="display: none" wire:ignore >
                                            <x-rekammedis.obatracikan 
                                                :racikanItems="$racikanItems"
                                            />
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </form>
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="lg:col-span-1">
                    <div class="sticky top-10 space-y-6">
                        <!-- B: Button -->
                        <div class="bg-base-100 shadow rounded-box p-4 pb-7">
                            <h3 class="font-semibold mb-4">Aksi</h3>
                            <button wire:click.prevent="create" class="btn btn-primary w-full mb-1" wire:loading.attr="disabled">
                                <span wire:loading.remove><i class="fa-solid fa-plus"></i> Simpan</span>
                                <span wire:loading.inline>Loading...</span>
                            </button>
                            <a wire:navigate href="{{ route('rekam-medis-pasien.data', ['pasien_id' => $pasienTerdaftar->pasien->id]) }}"
                                class="btn btn-info mb-1 w-full" >
                                <i class="fa-solid fa-book-medical"></i> Riwayat Rekam Medis
                            </a>
                            @if ($pasienTerdaftar->poliklinik->nama_poli == 'Poli Kecantikan')
                                <div class="btn w-full btn-success mb-1 ">
                                    <i class="fa-solid fa-money-bill-1-wave"></i>
                                    Grand Total: 
                                    <span 
                                        x-text="(totalBundling + totalTreatment + totalProduk)
                                            .toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 })">
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
            </div>
        </div>

    </div>
</div>
<script>
    function formChoicesSubjective() {
        return {
            selectedFormsSubjective: [],
            choicesSubjective: null,

            initChoicesSubjective() {
                this.choicesSubjective = new Choices(this.$refs.formSelectSubjective, {
                    removeItemButton: true,
                    placeholderValue: 'Pilih form...',
                    shouldSort: false,
                });

                this.selectedFormsSubjective = Array.from(this.$refs.formSelectSubjective.selectedOptions).map(opt => opt.value);

                this.$refs.formSelectSubjective.addEventListener('change', (event) => {
                    this.selectedFormsSubjective = Array.from(event.target.selectedOptions).map(opt => opt.value);
                });
            }
        }
    }

    function formChoicesObjective() {
        return {
            selectedFormsObjective: [],
            choicesObjective: null,

            initChoicesObjective() {
                this.choicesObjective = new Choices(this.$refs.formSelectObjective, {
                    removeItemButton: true,
                    placeholderValue: 'Pilih form...',
                    shouldSort: false,
                });

                this.selectedFormsObjective = Array.from(this.$refs.formSelectObjective.selectedOptions).map(opt => opt.value);

                this.$refs.formSelectObjective.addEventListener('change', (event) => {
                    this.selectedFormsObjective = Array.from(event.target.selectedOptions).map(opt => opt.value);
                });
            }
        }
    }

    function formChoicesAssessment() {
        return {
            selectedFormsAssessment: [],
            choicesAssessment: null,

            initChoicesAssessment() {
                this.choicesAssessment = new Choices(this.$refs.formSelectAssessment, {
                    removeItemButton: true,
                    placeholderValue: 'Pilih form...',
                    shouldSort: false,
                });

                this.selectedFormsAssessment = Array.from(this.$refs.formSelectAssessment.selectedOptions).map(opt => opt.value);

                this.$refs.formSelectAssessment.addEventListener('change', (event) => {
                    this.selectedFormsAssessment = Array.from(event.target.selectedOptions).map(opt => opt.value);
                });
            }
        }
    }

    function formChoicesPlan() {
        return {
            selectedFormsPlan: [],
            choicesPlan: null,

            initChoicesPlan() {
                // Init Choices.js hanya sekali
                this.choicesPlan = new Choices(this.$refs.formSelect, {
                    removeItemButton: true,
                    placeholderValue: 'Pilih form...',
                    shouldSort: false,
                });

                // Set value awal
                this.selectedFormsPlan = Array.from(this.$refs.formSelect.selectedOptions).map(opt => opt.value);

                // Update Alpine state jika pilihan berubah
                this.$refs.formSelect.addEventListener('change', (event) => {
                    this.selectedFormsPlan = Array.from(event.target.selectedOptions).map(opt => opt.value);
                });
            }
        }
    }
</script>

{{-- Script ICD --}}
<script>
    function multiSelectIcd10() {
        return {
            open: false,
            selected: @entangle('icd10'),
            search: '',
            filteredOptions: [],

            init() {
                if (!Array.isArray(this.selected)) {
                    this.selected = [];
                }
            },

            fetchOptions() {
                if (this.search.trim() === '') {
                    this.filteredOptions = [];
                    return;
                }

                fetch(`/ajax/icd_10?q=${encodeURIComponent(this.search)}`)
                    .then(res => res.json())
                    .then(data => {
                        this.filteredOptions = data; // sudah {code, name_id, name_en}
                    });
            },

            toggle(item) {
                const exists = this.selected.some(s => s.code === item.code);
                if (!exists) {
                    this.selected.push({
                        code: item.code,
                        name_id: item.name_id,
                        name_en: item.name_en,
                    });
                } else {
                    this.selected = this.selected.filter(s => s.code !== item.code);
                }
                this.search = '';
                this.filteredOptions = [];
            },

            remove(code) {
                this.selected = this.selected.filter(s => s.code !== code);
            }
        }
    }
</script>
