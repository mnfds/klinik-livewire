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
                        <a href="{{ route('pasien.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Pasien
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pasien.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder"></i>
                            Detail Pasien
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pasien.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-folder"></i>
                            Rekam Medis Pasien
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pasien.data') }}" class="inline-flex items-center gap-1">
                            <i class="fa-regular fa-folder-open"></i>
                            Detail Rekam Medis Pasien
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Detail Rekam Medis Pasien
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="p-6 space-y-6">

                <div class="bg-base-100 border border-base-300 rounded-lg p-4">
                    <div class="collapse-title font-semibold">
                        {{ \Carbon\Carbon::parse($pasienTerdaftar->tanggal_kunjungan)->translatedFormat('d F Y') }}
                            — {{ ucfirst($pasienTerdaftar->status_terdaftar) }}
                    </div>
                    <div class="tabs tabs-lift">
                        
                        {{-- TAB INFORMASI KUNJUNGAN --}}
                        <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Informasi Kunjungan" checked="checked" style="background-image: none;" />
                        <div class="tab-content bg-base-100 border-base-300 p-6">
                            <div class="divider">Data Kunjungan Pasien</div>
                            
                            <div class="grid grid-cols-2 gap-y-2 gap-x-4 text-sm">
                                <div><span class="font-medium">Nama</span> : {{ $pasien->nama }}</div>
                                <div><span class="font-medium">No. RM</span> : {{ $pasien->no_register }}</div>
                                <div><span class="font-medium">NIK</span> : {{ $pasien->nik }}</div>
                                <div><span class="font-medium">No. IHS</span> : {{ $pasien->no_ihs }}</div>
                                <div><span class="font-medium">Jenis Kelamin</span> : {{ $pasien->jenis_kelamin }}</div>
                                <div><span class="font-medium">Tanggal Lahir</span> : {{ \Carbon\Carbon::parse($pasien->tanggal_lahir)->translatedFormat('d F Y') }}</div>
                                <div><span class="font-medium">Poliklinik</span> : {{ $pasienTerdaftar->poliklinik->nama_poli }}</div>
                            </div>
                        </div>

                        @if ($pasienTerdaftar->poliklinik->kode !== 'KCT')
                            {{-- TAB KAJIAN AWAL --}}
                            <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Kajian Awal (Anamnesa)" style="background-image: none;" />
                            <div class="tab-content bg-base-100 border-base-300 p-6">
                                <div class="mt-2">
                                    <div class="grid grid-cols-[200px_1fr] gap-y-1">
                                        <div>Nakes Pengkaji</div>
                                        <div>: {{ $kajian->nama_pengkaji ?? '-' }}</div>
    
                                        <div>Keluhan Utama</div>
                                        <div>: {{ $rekammedis->keluhan_utama ?? '-' }}</div>
    
                                        <div>Tingkat Kesadaran</div>
                                        <div>: {{ $rekammedis->tingkat_kesadaran ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="divider">Hasil Kajian Awal (Anamnesa)</div>
                                            
                                @if ($kajian)
    
                                    {{-- Pemeriksaan Fisik --}}
                                    @if ($kajian->pemeriksaanFisik)
                                        <div class="mt-1">
                                            <div class="font-semibold mb-1">Pemeriksaan Fisik</div>
                                            <div class="space-y-1">
                                                <div>Tinggi Badan : {{ $kajian->pemeriksaanFisik->tinggi_badan ?? '-'}} cm</div>
                                                <div>Berat Badan : {{ $kajian->pemeriksaanFisik->berat_badan ?? '-'}} kg</div>
                                                <div>IMT : {{ $kajian->pemeriksaanFisik->imt ?? '-'}}</div>
                                            </div>
                                        </div>
                                    @endif
    
                                    {{-- Tanda Vital --}}
                                    @if ($kajian->tandaVital)
                                        <div class="mt-1">
                                            <div class="font-semibold mb-1">Tanda Vital</div>
                                            <div class="space-y-1">
                                                <div>Suhu Tubuh : {{ $kajian->tandaVital->suhu_tubuh ?? '-'}} °C</div>
                                                <div>Nadi : {{ $kajian->tandaVital->nadi ?? '-'}} bpm</div>
                                                <div>Tekanan Darah : {{ $kajian->tandaVital->sistole ?? '-'}}/{{ $kajian->tandaVital->diastole ?? '-'}} mmHg</div>
                                                <div>Frekuensi Napas : {{ $kajian->tandaVital->frekuensi_pernapasan ?? '-'}} /menit</div>
                                            </div>
                                        </div>
                                    @endif
    
                                    {{-- Data Kesehatan --}}
                                    @if ($kajian->dataKesehatan)
                                        <div class="mt-1">
                                            <div class="font-semibold mb-1">Data Kesehatan</div>
                                            <div class="space-y-1">
                                                <div>Keluhan Utama : {{ $kajian->dataKesehatan->keluhan_utama ?? '-' }}</div>
                                                <div>Status Perokok : {{ $kajian->dataKesehatan->status_perokok ?? '-' }}</div>
    
                                                @php
                                                    $riwayat_penyakit = $kajian->dataKesehatan->riwayat_penyakit ? json_decode($kajian->dataKesehatan->riwayat_penyakit,true) : [];
                                                    $riwayat_alergi_obat = $kajian->dataKesehatan->riwayat_alergi_obat ? json_decode($kajian->dataKesehatan->riwayat_alergi_obat,true) : [];
                                                    $obat = $kajian->dataKesehatan->obat_sedang_dikonsumsi ? json_decode($kajian->dataKesehatan->obat_sedang_dikonsumsi,true) : [];
                                                    $alergi_lain = $kajian->dataKesehatan->riwayat_alergi_lainnya ? json_decode($kajian->dataKesehatan->riwayat_alergi_lainnya,true) : [];
                                                @endphp
    
                                                <div>
                                                    Riwayat Penyakit :
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
    
                                                <div>
                                                    Riwayat Alergi Obat :
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
    
                                                <div>
                                                    Obat Dikonsumsi :
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
    
                                                <div>
                                                    Alergi Lainnya :
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
                                    @if ($kajian->dataEstetika)
                                        <div class="mt-1">
                                            <div class="font-semibold mb-1">Data Estetika</div>
                                            @php
                                                $decode = fn($v) => $v ? (is_array(json_decode($v, true)) ? json_decode($v, true) : [$v]) : [];
                                                $problem = $decode($kajian->dataEstetika->problem_dihadapi);
                                                $tindakan = $decode($kajian->dataEstetika->tindakan_sebelumnya);
                                                $metode_kb = $decode($kajian->dataEstetika->metode_kb);
                                            @endphp
                                            <div class="space-y-1">
                                                <div>
                                                    Problem Dihadapi :
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
    
                                                <div>Lama Problem : {{ $kajian->dataEstetika->lama_problem ?? '-' }}</div>
    
                                                <div>
                                                    Tindakan Sebelumnya :
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
    
                                                <div>Penyakit Dialami : {{ $kajian->dataEstetika->penyakit_dialami ?? '-' }}</div>
                                                <div>Alergi Kosmetik : {{ $kajian->dataEstetika->alergi_kosmetik ?? '-' }}</div>
    
                                                <div>Sedang Hamil : {{ ucfirst($kajian->dataEstetika->sedang_hamil) ?? '-' }}</div>
                                                <div>Usia Kehamilan : {{ $kajian->dataEstetika->usia_kehamilan ? $kajian->dataEstetika->usia_kehamilan.' bln' : '-' }}</div>
    
                                                <div>
                                                    Metode KB :
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
    
                                                <div>Pengobatan Saat Ini : {{ $kajian->dataEstetika->pengobatan_saat_ini ?? '-' }}</div>
                                                <div>Produk Kosmetik : {{ $kajian->dataEstetika->produk_kosmetik ?? '-' }}</div>
                                            </div>
                                        </div>
                                    @endif
    
                                @else
                                    <p class="italic text-gray-500 mt-2">Tidak tersedia data kajian awal (anamnesa)</p>
                                @endif
                            </div>
                        @endif

                        {{-- TAB SUBJECTIVE --}}
                        <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Subjective" style="background-image: none;" />
                        <div class="tab-content bg-base-100 border-base-300 p-6">
                            <div class="mt-2">
                                <div class="grid grid-cols-[200px_1fr] gap-y-1">
                                    <div>Nakes Pemeriksa</div>
                                    <div>: {{ $rekammedis->nama_dokter ?? '-' }}</div>

                                    <div>Keluhan Utama</div>
                                    <div>: {{ $rekammedis->keluhan_utama ?? '-' }}</div>

                                    <div>Tingkat Kesadaran</div>
                                    <div>: {{ $rekammedis->tingkat_kesadaran ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="divider">Hasil Pemeriksaan Subjective</div>
                            @if ($rekammedis?->dataKesehatanRM || $rekammedis?->dataEstetikaRM)
                                @if ($rekammedis->dataKesehatanRM)
                                    <div class="mt-1">
                                        <div class="font-semibold mb-1">Data Kesehatan</div>
                                        <div class="space-y-1">
                                            <div>Status Perokok : {{ $rekammedis->dataKesehatanRM->status_perokok ?? '-' }}</div>

                                            @php
                                                $riwayat_penyakit = $rekammedis->dataKesehatanRM->riwayat_penyakit ? json_decode($rekammedis->dataKesehatanRM->riwayat_penyakit,true) : [];
                                                $riwayat_alergi_obat = $rekammedis->dataKesehatanRM->riwayat_alergi_obat ? json_decode($rekammedis->dataKesehatanRM->riwayat_alergi_obat,true) : [];
                                                $obat = $rekammedis->dataKesehatanRM->obat_sedang_dikonsumsi ? json_decode($rekammedis->dataKesehatanRM->obat_sedang_dikonsumsi,true) : [];
                                                $alergi_lain = $rekammedis->dataKesehatanRM->riwayat_alergi_lainnya ? json_decode($rekammedis->dataKesehatanRM->riwayat_alergi_lainnya,true) : [];
                                            @endphp

                                            <div>
                                                Riwayat Penyakit :
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

                                            <div>
                                                Riwayat Alergi Obat :
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

                                            <div>
                                                Obat Dikonsumsi :
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

                                            <div>
                                                Alergi Lainnya :
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

                                @if ($rekammedis->dataEstetikaRM)
                                    <div class="mt-1">
                                        <div class="font-semibold mb-1">Data Estetika</div>
                                        @php
                                            $decode = fn($v) => $v ? (is_array(json_decode($v, true)) ? json_decode($v, true) : [$v]) : [];
                                            $problem = $decode($rekammedis->dataEstetikaRM->problem_dihadapi);
                                            $tindakan = $decode($rekammedis->dataEstetikaRM->tindakan_sebelumnya);
                                            $metode_kb = $decode($rekammedis->dataEstetikaRM->metode_kb);
                                        @endphp
                                        <div class="space-y-1">
                                            <div>
                                                Problem Dihadapi :
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

                                            <div>Lama Problem : {{ $rekammedis->dataEstetikaRM->lama_problem ?? '-' }}</div>

                                            <div>
                                                Tindakan Sebelumnya :
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

                                            <div>Penyakit Dialami : {{ $rekammedis->dataEstetikaRM->penyakit_dialami ?? '-' }}</div>
                                            <div>Alergi Kosmetik : {{ $rekammedis->dataEstetikaRM->alergi_kosmetik ?? '-' }}</div>

                                            <div>Sedang Hamil : {{ ucfirst($rekammedis->dataEstetikaRM->sedang_hamil) ?? '-' }}</div>
                                            <div>Usia Kehamilan : {{ $rekammedis->dataEstetikaRM->usia_kehamilan ? $rekammedis->dataEstetikaRM->usia_kehamilan.' bln' : '-' }}</div>

                                            <div>
                                                Metode KB :
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

                                            <div>Pengobatan Saat Ini : {{ $rekammedis->dataEstetikaRM->pengobatan_saat_ini ?? '-' }}</div>
                                            <div>Produk Kosmetik : {{ $rekammedis->dataEstetikaRM->produk_kosmetik ?? '-' }}</div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <p class="italic text-gray-500">Tidak Tersedia Data Subjective</p>
                            @endif
                        </div>

                        {{-- TAB OBJECTIVE --}}
                        <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Objective" style="background-image: none;" />
                        <div class="tab-content bg-base-100 border-base-300 p-6">
                            <div class="mt-2">
                                <div class="grid grid-cols-[200px_1fr] gap-y-1">
                                    <div>Nakes Pemeriksa</div>
                                    <div>: {{ $rekammedis->nama_dokter ?? '-' }}</div>

                                    <div>Keluhan Utama</div>
                                    <div>: {{ $rekammedis->keluhan_utama ?? '-' }}</div>

                                    <div>Tingkat Kesadaran</div>
                                    <div>: {{ $rekammedis->tingkat_kesadaran ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="divider">Hasil Pemeriksaan Objective</div>
                            @if ($rekammedis?->tandaVitalRM || $rekammedis?->pemeriksaanFisikRM || $rekammedis?->pemeriksaanKulitRM)
                                @if ($rekammedis->tandaVitalRM)
                                    <div class="mt-2">
                                        <div class="font-semibold mb-1">Tanda Vital</div>
                                        <div class="space-y-1">
                                            <div>Suhu Tubuh : {{ $rekammedis->tandaVitalRM->suhu_tubuh ?? '-' }} °C</div>
                                            <div>Nadi : {{ $rekammedis->tandaVitalRM->nadi ?? '-'}} bpm</div>
                                            <div>Tekanan Darah : {{ $rekammedis->tandaVitalRM->sistole ?? '-'}}/{{ $kajian->tandaVitalRM->diastole ?? '-'}} mmHg</div>
                                            <div>Frekuensi Napas : {{ $rekammedis->tandaVitalRM->frekuensi_pernapasan ?? '-'}} /menit</div>
                                        </div>
                                    </div>
                                @endif

                                @if ($rekammedis->pemeriksaanFisikRM)
                                    <div class="mt-2">
                                        <div class="font-semibold mb-1">Pemeriksaan Fisik</div>
                                        <div class="space-y-1">
                                            <div>Tinggi Badan : {{ $rekammedis->pemeriksaanFisikRM->tinggi_badan ?? '-'}} cm</div>
                                            <div>Berat Badan : {{ $rekammedis->pemeriksaanFisikRM->berat_badan ?? '-'}} kg</div>
                                            <div>IMT : {{ $rekammedis->pemeriksaanFisikRM->imt ?? '-'}}</div>
                                        </div>
                                    </div>
                                @endif

                                @if ($rekammedis->pemeriksaanKulitRM)
                                    <div class="mt-1">
                                        <div class="font-semibold mb-1">Pemeriksaan Kulit</div>
                                        @php
                                            $decode = fn($v) => $v ? (is_array(json_decode($v, true)) ? json_decode($v, true) : [$v]) : [];
                                            $acne = $decode($rekammedis->pemeriksaanKulitRM->acne);
                                            $lesions = $decode($rekammedis->pemeriksaanKulitRM->lesions);
                                        @endphp
                                        <div class="space-y-1">

                                            <div>Warna Kulit : {{ $rekammedis->pemeriksaanKulitRM->warna_kulit ?? '-' }}</div>
                                            <div>Ketebalan Kulit : {{ $rekammedis->pemeriksaanKulitRM->ketebalan_kulit ?? '-' }}</div>
                                            <div>Kadar Minyak : {{ $rekammedis->pemeriksaanKulitRM->kadar_minyak ?? '-' }}</div>
                                            <div>Kerapuhan Kulit : {{ $rekammedis->pemeriksaanKulitRM->kerapuhan_kulit ?? '-' }}</div>
                                            <div>Kekencangan Kulit : {{ $rekammedis->pemeriksaanKulitRM->kekencangan_kulit ?? '-' }}</div>
                                            <div>Melasma : {{ $rekammedis->pemeriksaanKulitRM->melasma ?? '-' }}</div>
                                            <div>
                                                acne :
                                                @if(empty($acne))
                                                    -
                                                @else
                                                    <ul class="ml-4 list-disc">
                                                        @foreach ($acne as $a)
                                                            <li>{{ $a }}</li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                            <div>
                                                lesions :
                                                @if(empty($lesions))
                                                    -
                                                @else
                                                    <ul class="ml-4 list-disc">
                                                        @foreach ($lesions as $l)
                                                            <li>{{ $l }}</li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <p class="italic text-gray-500">Tidak Tersedia Data Objective</p>
                            @endif
                        </div>

                        {{-- TAB ASSESSMENT --}}
                        <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Assessment" style="background-image: none;" />
                        <div class="tab-content bg-base-100 border-base-300 p-6">
                            <div class="mt-2">
                                <div class="grid grid-cols-[200px_1fr] gap-y-1">
                                    <div>Nakes Pemeriksa</div>
                                    <div>: {{ $rekammedis->nama_dokter ?? '-' }}</div>

                                    <div>Keluhan Utama</div>
                                    <div>: {{ $rekammedis->keluhan_utama ?? '-' }}</div>

                                    <div>Tingkat Kesadaran</div>
                                    <div>: {{ $rekammedis->tingkat_kesadaran ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="divider">Hasil Pemeriksaan Assessment</div>
                            @if ($rekammedis?->diagnosaRM || $rekammedis?->icdRM)
                                @if ($rekammedis->diagnosaRM)
                                    <div class="mt-2">
                                        <div class="font-semibold mb-1">Diagnosa</div>
                                        <div class="space-y-1">
                                            <div>Catatan Diagnosa : {{ $rekammedis->diagnosaRM->diagnosa ?? '-' }}</div>
                                        </div>
                                    </div>
                                @endif

                                @if ($rekammedis->icdRM->IsNotEmpty())
                                    <div class="mt-1">
                                        <div class="font-semibold mb-1">ICD 10</div>

                                        @foreach ($rekammedis->icdRM as $icd)
                                            @php
                                                $decode = function ($v) {
                                                    $arr = json_decode($v, true);
                                                    return $arr && is_array($arr) ? $arr : ($v ? [$v] : []);
                                                };
                                                $codes = $decode($icd->code ?? null);
                                                $names = $decode($icd->name_id ?? null);
                                            @endphp

                                            <div class="mb-2">
                                                @if(empty($codes))
                                                    -
                                                @else
                                                    <ul class="ml-4 list-disc">
                                                        @foreach ($codes as $index => $c)
                                                            <li>{{ $c }} - {{ $names[$index] ?? '-' }}</li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <p class="italic text-gray-500">Tidak Tersedia Data Asessment</p>
                            @endif
                        </div>

                        {{-- TAB PLAN --}}
                        <input type="radio" name="my_tabs_3" class="tab bg-transparent text-base-content" aria-label="Plan" style="background-image: none;" />
                        <div class="tab-content bg-base-100 border-base-300 p-6">
                            <div class="mt-2">
                                <div class="grid grid-cols-[200px_1fr] gap-y-1">
                                    <div>Nakes Pemeriksa</div>
                                    <div>: {{ $rekammedis->nama_dokter ?? '-' }}</div>

                                    <div>Keluhan Utama</div>
                                    <div>: {{ $rekammedis->keluhan_utama ?? '-' }}</div>

                                    <div>Tingkat Kesadaran</div>
                                    <div>: {{ $rekammedis->tingkat_kesadaran ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="divider">Hasil Pemeriksaan Plan</div>
                            @if ($rekammedis?->rencanaLayananRM || $rekammedis?->rencanaTreatmentRM || $rekammedis?->rencanaProdukRM || $rekammedis?->rencanaBundlingRM || $rekammedis?->obatNonRacikanRM || $rekammedis?->obatRacikanRM)
                            
                                @if ($rekammedis->rencanaLayananRM)
                                    <div class="mt-1">
                                        <div class="font-semibold">Layanan Medis</div>

                                        @foreach ($rekammedis->rencanaLayananRM as $layanans)
                                            @php
                                                $decode = fn($v) => $v ? (is_array(json_decode($v, true)) ? json_decode($v, true) : [$v]) : [];
                                                $layanan = $decode($layanans->pelayanan->nama_pelayanan ?? null);
                                                $jumlah = $decode($layanans->jumlah_pelayanan ?? null);
                                            @endphp
                                            <div class="mb-2">
                                                @if(empty($layanan))
                                                    -
                                                @else
                                                    <ul class="ml-4 list-disc">
                                                        @foreach ($layanan as $index => $c)
                                                            <li>{{ $c }}  {{ $jumlah[$index] ?? '0' }}x</li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if ($rekammedis->rencanaTreatmentRM)
                                    <div class="mt-1">
                                        <div class="font-semibold">Layanan Estetika</div>

                                        @foreach ($rekammedis->rencanaTreatmentRM as $treatments)
                                            @php
                                                $decode = fn($v) => $v ? (is_array(json_decode($v, true)) ? json_decode($v, true) : [$v]) : [];
                                                $treatment = $decode($treatments->treatment->nama_treatment ?? null);
                                                $jumlah = $decode($treatments->jumlah_treatment ?? null);
                                            @endphp
                                            <div class="mb-2">
                                                @if(empty($treatment))
                                                    -
                                                @else
                                                    <ul class="ml-4 list-disc">
                                                        @foreach ($treatment as $index => $c)
                                                            <li>{{ $c }}  {{ $jumlah[$index] ?? '0' }}x</li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if ($rekammedis->rencanaProdukRM)
                                    <div class="mt-1">
                                        <div class="font-semibold">Produk Estetika</div>

                                        @foreach ($rekammedis->rencanaProdukRM as $produkEstetika)
                                            @php
                                                $decode = fn($v) => $v ? (is_array(json_decode($v, true)) ? json_decode($v, true) : [$v]) : [];
                                                $produkobat = $decode($produkEstetika->produk->nama_dagang ?? null);
                                                $jumlah = $decode($produkEstetika->jumlah_produk ?? null);
                                            @endphp
                                            <div class="mb-2">
                                                @if(empty($produkobat))
                                                    -
                                                @else
                                                    <ul class="ml-4 list-disc">
                                                        @foreach ($produkobat as $index => $c)
                                                            <li>{{ $c }}  {{ $jumlah[$index] ?? '0' }}x</li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if ($rekammedis->rencanaBundlingRM)
                                    <div class="mt-1">
                                        <div class="font-semibold">Bundling</div>

                                        @foreach ($rekammedis->rencanaBundlingRM as $bundlings)
                                            @php
                                                $decode = fn($v) => $v ? (is_array(json_decode($v, true)) ? json_decode($v, true) : [$v]) : [];
                                                $bundling = $decode($bundlings->bundling->nama ?? null);
                                                $jumlah_bundling = $decode($bundlings->jumlah_bundling ?? null);
                                            @endphp
                                            <div class="mb-2">
                                                @if(empty($bundling))
                                                    -
                                                @else
                                                    <ul class="ml-4 list-disc">
                                                        @foreach ($bundling as $index => $bund)
                                                            <li>{{ $bund }}  {{ $jumlah_bundling[$index] ?? '0' }}x</li>
                                                            <ul class="list-disc list-inside space-y-2">
                                                                {{-- Treatments --}}
                                                                <li>
                                                                    <span class="font-semibold text-sm">Treatment</span>
                                                                    <ul class="ml-4 list-disc list-inside text-sm space-y-1">
                                                                        @foreach ($bundlings->bundling->treatmentBundlingRM as $tb)
                                                                            <li>
                                                                                {{ $tb->treatment->nama_treatment ?? '-' }}
                                                                                <span class="text-gray-500">
                                                                                    (Tersedia: {{ $tb->jumlah_awal }}, Digunakan: {{ $tb->jumlah_terpakai }}, Tersisa: {{ $tb->jumlah_awal - $tb->jumlah_terpakai }})
                                                                                </span>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </li>

                                                                {{-- Pelayanan --}}
                                                                <li>
                                                                    <span class="font-semibold text-sm">Pelayanan</span>
                                                                    <ul class="ml-4 list-disc list-inside text-sm space-y-1">
                                                                        @foreach ($bundlings->bundling->pelayananBundlingRM as $pb)
                                                                            <li>
                                                                                {{ $pb->pelayanan->nama_pelayanan ?? '-' }}
                                                                                <span class="text-gray-500">
                                                                                    (Tersedia: {{ $pb->jumlah_awal }}, Digunakan: {{ $pb->jumlah_terpakai }}, Tersisa: {{ $pb->jumlah_awal - $pb->jumlah_terpakai }})
                                                                                </span>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </li>

                                                                {{-- Produk & Obat --}}
                                                                <li>
                                                                    <span class="font-semibold text-sm">Produk & Obat</span>
                                                                    <ul class="ml-4 list-disc list-inside text-sm space-y-1">
                                                                        @foreach ($bundlings->bundling->produkObatBundlingRM as $pob)
                                                                            <li>
                                                                                {{ $pob->produk->nama_dagang ?? '-' }}
                                                                                <span class="text-gray-500">
                                                                                    (Tersedia: {{ $pob->jumlah_awal }}, Digunakan: {{ $pob->jumlah_terpakai }}, Tersisa: {{ $pob->jumlah_awal - $pob->jumlah_terpakai }})
                                                                                </span>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </li>
                                                            </ul>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if ($rekammedis->obatNonRacikanRM)
                                    <div class="mt-1">
                                        <div class="font-semibold mb-1">Obat Non Racik</div>

                                        @foreach ($rekammedis->obatNonRacikanRM as $obat)
                                            @php
                                                $decode = function ($v) {
                                                    $arr = json_decode($v, true);
                                                    return $arr && is_array($arr) ? $arr : ($v ? [$v] : []);
                                                };

                                                $nama_obat   = $decode($obat->nama_obat_non_racikan ?? null);
                                                $jumlah_obat = $decode($obat->jumlah_obat_non_racikan ?? null);
                                                $satuan_obat = $decode($obat->satuan_obat_non_racikan ?? null);
                                                $dosis_obat  = $decode($obat->dosis_obat_non_racikan ?? null);
                                                $hari_obat   = $decode($obat->hari_obat_non_racikan ?? null);
                                                $aturan_obat = $decode($obat->aturan_pakai_obat_non_racikan ?? null);
                                            @endphp

                                            <div class="mb-2">
                                                @if(empty($nama_obat))
                                                    <p>-</p>
                                                @else
                                                    <ul class="ml-4 list-disc space-y-1">
                                                        @foreach ($nama_obat as $index => $nama)
                                                            <li>
                                                                <span class="font-semibold">{{ $nama ?? '-' }}</span><br>
                                                                Jumlah: {{ $jumlah_obat[$index] ?? '-' }} {{ $satuan_obat[$index] ?? '-' }}<br>
                                                                Dosis: {{ $dosis_obat[$index] ?? '-' }}<br>
                                                                Hari: {{ $hari_obat[$index] ?? '-' }}<br>
                                                                Aturan Pakai: {{ $aturan_obat[$index] ?? '-' }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        @endforeach

                                    </div>
                                @endif

                                @if ($rekammedis->obatRacikanRM)
                                    <div class="mt-1">
                                        <div class="font-semibold mb-1">Obat Racikan</div>

                                        @foreach ($rekammedis->obatRacikanRM as $racik)
                                            @php
                                                $decode = function ($v) {
                                                    $arr = json_decode($v, true);
                                                    return $arr && is_array($arr) ? $arr : ($v ? [$v] : []);
                                                };

                                                // Data racikan utama
                                                $nama_racikan   = $decode($racik->nama_racikan ?? null);
                                                $jumlah_racikan = $decode($racik->jumlah_racikan ?? null);
                                                $satuan_racikan = $decode($racik->satuan_racikan ?? null);
                                                $dosis_racikan  = $decode($racik->dosis_obat_racikan ?? null);
                                                $hari_racikan   = $decode($racik->hari_obat_racikan ?? null);
                                                $aturan_racikan = $decode($racik->aturan_pakai_racikan ?? null);
                                                $metode_racikan = $decode($racik->metode_racikan ?? null);
                                            @endphp

                                            <div class="mb-2">
                                                {{-- Racikan Utama --}}
                                                @if (!empty($nama_racikan))
                                                    <ul class="ml-4 list-disc space-y-1">
                                                        @foreach ($nama_racikan as $i => $nama)
                                                            <li>
                                                                <span class="font-semibold">{{ $nama ?? '-' }}</span><br>
                                                                Jumlah: {{ $jumlah_racikan[$i] ?? '-' }} {{ $satuan_racikan[$i] ?? '-' }}<br>
                                                                Dosis: {{ $dosis_racikan[$i] ?? '-' }}<br>
                                                                Hari: {{ $hari_racikan[$i] ?? '-' }}<br>
                                                                Aturan Pakai: {{ $aturan_racikan[$i] ?? '-' }}<br>
                                                                Instruksi Racikan: {{ $metode_racikan[$i] ?? '-' }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <p>-</p>
                                                @endif

                                                {{-- Bahan Racikan --}}
                                                @if ($racik->bahanRacikan && $racik->bahanRacikan->isNotEmpty())
                                                    <div class="mt-3 ml-6">
                                                        <div class="font-semibold">Bahan Racikan:</div>
                                                        <ul class="ml-4 list-disc space-y-1">
                                                            @foreach ($racik->bahanRacikan as $bahan)
                                                                <li>
                                                                    {{ $bahan->nama_obat_racikan ?? '-' }}
                                                                    ({{ $bahan->jumlah_obat_racikan ?? '-' }} {{ $bahan->satuan_obat_racikan ?? '-' }})
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                            @else
                                <p class="italic text-gray-500">Tidak Tersedia Data Plan </p>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>