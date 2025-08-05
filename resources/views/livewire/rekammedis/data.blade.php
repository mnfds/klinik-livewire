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
                            <i class="fa-regular fa-folder-open"></i>
                            Rekam Medis Pasien
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Page Title -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-base-content">
                <i class="fa-solid fa-layer-group"></i>
                Riwayat Rekam Medis Pasien
            </h1>
        </div>

        <!-- Main Content -->
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xs rounded-sm sm:rounded-lg">
                <div class="p-6 space-y-6">

                    <!-- Biodata Pasien -->
                    <div class="bg-base-100 border border-base-300 rounded-lg p-4">
                        <h3 class="font-semibold text-lg mb-4">Biodata Pasien</h3>
                        <div class="grid grid-cols-2 gap-y-2 gap-x-4 text-sm">
                            <div><span class="font-medium">Nama</span> : {{ $pasien->nama }}</div>
                            <div><span class="font-medium">No. Register</span> : {{ $pasien->no_register }}</div>
                            <div><span class="font-medium">NIK</span> : {{ $pasien->nik }}</div>
                            <div><span class="font-medium">No. IHS</span> : {{ $pasien->no_ihs }}</div>
                            <div><span class="font-medium">Jenis Kelamin</span> : {{ $pasien->jenis_kelamin }}</div>
                            <div><span class="font-medium">Tanggal Lahir</span> : {{ \Carbon\Carbon::parse($pasien->tanggal_lahir)->translatedFormat('d F Y') }}</div>
                        </div>
                    </div>

                    <!-- Accordion -->
                    <div class="max-h-72 overflow-y-auto space-y-3 py-5 px-3">
                        @foreach ($pasienTerdaftar as $item)
                            <div class="collapse collapse-arrow bg-base-100 border border-base-300">
                                <input type="radio" name="accordion-kunjungan" {{ $loop->first ? 'checked' : '' }} />

                                {{-- === TITLE === --}}
                                <div class="collapse-title font-semibold">
                                    {{ \Carbon\Carbon::parse($item->tanggal_kunjungan)->translatedFormat('d F Y') }}
                                    — {{ ucfirst($item->status_terdaftar) }}
                                </div>

                                {{-- === CONTENT === --}}
                                <div class="collapse-content text-sm space-y-4">
                                    @php $kajian = $item->kajianAwal; @endphp

                                    @if ($kajian)

                                        {{-- Pemeriksaan Fisik --}}
                                        @if ($kajian->pemeriksaanFisik)
                                            <div>
                                                <div class="font-semibold mb-1">Pemeriksaan Fisik</div>
                                                <div class="space-y-1">
                                                    <div>Tinggi Badan : {{ $kajian->pemeriksaanFisik->tinggi_badan }} cm</div>
                                                    <div>Berat Badan : {{ $kajian->pemeriksaanFisik->berat_badan }} kg</div>
                                                    <div>IMT : {{ $kajian->pemeriksaanFisik->imt }}</div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Tanda Vital --}}
                                        @if ($kajian->tandaVital)
                                            <div>
                                                <div class="font-semibold mb-1">Tanda Vital</div>
                                                <div class="space-y-1">
                                                    <div>Suhu Tubuh : {{ $kajian->tandaVital->suhu_tubuh }} °C</div>
                                                    <div>Nadi : {{ $kajian->tandaVital->nadi }} bpm</div>
                                                    <div>Tekanan Darah : {{ $kajian->tandaVital->sistole }}/{{ $kajian->tandaVital->diastole }} mmHg</div>
                                                    <div>Frekuensi Napas : {{ $kajian->tandaVital->frekuensi_pernapasan }} /menit</div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Data Kesehatan --}}
                                        @if ($kajian->dataKesehatan)
                                            <div>
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
                                            <div>
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
                                        <p class="italic text-gray-500">Belum ada kajian awal.</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>