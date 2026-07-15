<div>
    <style>
        .schedule-scroll::-webkit-scrollbar {
            height: 10px;
            width: 10px;
        }

        .schedule-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }

        table.jadwal td:nth-child(1),
        table.jadwal th:nth-child(1) {
            position: sticky;
            left: 0;
            z-index: 20;
        }
        .tooltip:before {
            white-space: pre-line;
            text-align: left;
        }
    </style>

    <div class="max-w-full mx-auto">
        @php
            $authId = auth()->id();
            $jatahLiburSaya = $kuotaLibur[$authId] ?? 0;
            $sisaLiburSaya = $jatahLiburSaya - ($kuotaTerpakai[$authId] ?? 0);

            $jatahCutiSaya = $kuotaCuti[$authId] ?? 0;
            $sisaCutiSaya = $jatahCutiSaya - ($kuotaCutiTerpakai[$authId] ?? 0);
        @endphp

        <div class="my-2 ">
            <p class="text-sm font-bold">Kuota Libur Anda:<span class="{{ $sisaLiburSaya <= 0 ? 'text-error' : 'text-success' }}"> {{ $sisaLiburSaya }}/{{ $jatahLiburSaya }}</span></p>
            <p class="text-sm font-bold">Kuota Cuti Anda:<span class="{{ $sisaCutiSaya <= 0 ? 'text-error' : 'text-success' }}"> {{ $sisaCutiSaya }}/{{ $jatahCutiSaya }}</span></p>
        </div>
        <div class="mb-4 text-center text-xl font-bold">
            {{ \Carbon\Carbon::parse($this->tanggal)->translatedFormat('F Y') }}
        </div>
        <div class="schedule-scroll overflow-x-auto max-h-[70vh]">
            <table class="jadwal w-full text-sm table">
                <thead class="bg-primary text-base-primary text-xs uppercase">
                    <tr>
                        <th class="sticky top-0 z-40 bg-primary px-3 py-3 border border-base-200 text-left min-w-[160px] whitespace-nowrap">
                            Nama
                        </th>
                        @for ($day = 1; $day <= $tanggal->daysInMonth; $day++)
                            @php
                                $currentDate = $tanggal->copy()->day($day);
                                $isWeekend = $currentDate->isWeekend();
                            @endphp
                            <th class="sticky top-0 z-30 {{ $isWeekend ? 'bg-primary/70' : 'bg-primary' }} px-2 py-3 border border-base-200 text-center min-w-[78px] whitespace-nowrap">
                                {{ $day }}<br>
                                <span class="normal-case text-[10px]">{{ $currentDate->locale('id')->isoFormat('ddd') }}</span>
                            </th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="hover:bg-base-200">
                            <td class="bg-primary px-2 py-3 border border-base-200 font-medium whitespace-nowrap">
                                @if ($user->biodata)
                                    {{ $user->biodata->nama_lengkap ?? '-' }}
                                @endif
                                @if ($user->dokter)
                                    {{ $user->dokter->nama_dokter ?? '-' }}
                                @endif
                                @if ($user->biodata && $user->dokter)
                                    {{ $user->name ?? '-' }}
                                @endif
                                @php
                                    $jatahLibur = $kuotaLibur[$user->id] ?? 4;
                                    $terpakaiLibur = $kuotaTerpakai[$user->id] ?? 0;
                                    $sisaLibur = $jatahLibur - $terpakaiLibur;

                                    $jatahCuti = $kuotaCuti[$user->id] ?? 12;
                                    $terpakaiCuti = $kuotaCutiTerpakai[$user->id] ?? 0;
                                    $sisaCuti = $jatahCuti - $terpakaiCuti;
                                @endphp
                                <br>
                                <span class="text-xs {{ $sisaLibur <= 0 ? 'text-error' : 'text-base-content' }}">
                                    Libur: {{ $sisaLibur }}/{{ $jatahLibur }}
                                </span>
                                <br>
                                <span class="text-xs {{ $sisaCuti <= 0 ? 'text-error' : 'text-base-content' }}">
                                    Cuti: {{ $sisaCuti }}/{{ $jatahCuti }}
                                </span>
                            </td>

                            @for ($day = 1; $day <= $tanggal->daysInMonth; $day++)
                                @php
                                    $tglCell = $tanggal->copy()->day($day)->format('Y-m-d');
                                    $shift = collect($jadwal[$user->id] ?? [])->firstWhere('tanggal', $tglCell);
                                    $namaShift = $shift['jamkerja']['nama_shift'] ?? null;
                                    $tipeShift = $shift['jamkerja']['tipe_shift'] ?? null;
                                    $jamMulai = $shift['jamkerja']['jam_mulai'] ?? null;
                                    $jamSelesai = $shift['jamkerja']['jam_selesai'] ?? null;

                                    $absenHariIni = $absen[$user->id][$tglCell] ?? null;
                                    $jamMasuk = $absenHariIni['jam_masuk'] ?? null;
                                    $jamPulang = $absenHariIni['jam_pulang'] ?? null;

                                    $bgClass = match (true) {
                                        $tipeShift === 'libur' => 'bg-error text-error-content',
                                        $tipeShift === 'cuti' => 'bg-error text-error-content',
                                        $namaShift !== null => 'bg-success text-success-content',
                                        default => 'bg-neutral/50',
                                    };
                                @endphp
                                <td
                                    wire:click="editShift({{ $user->id }}, '{{ $tglCell }}')"
                                    class="editable text-center border border-base-200 px-2 py-3 cursor-pointer transition hover:brightness-95 hover:outline hover:outline-2 hover:outline-primary hover:-outline-offset-2 {{ $bgClass }}"
                                >
                                    @php
                                        $terlambat = false;
                                        $pulangCepat = false;

                                        if ($jamMasuk && $jamMulai) {
                                            $terlambat = \Carbon\Carbon::parse($jamMasuk)->format('H:i:s') > \Carbon\Carbon::parse($jamMulai)->format('H:i:s');
                                        }

                                        if ($jamPulang && $jamSelesai) {
                                            $pulangCepat = \Carbon\Carbon::parse($jamPulang)->format('H:i:s') < \Carbon\Carbon::parse($jamSelesai)->format('H:i:s');
                                        }

                                        $tooltipLines = [];
                                        if ($jamMulai && $jamSelesai) {
                                            $tooltipLines[] = 'Shift: ' . \Carbon\Carbon::parse($jamMulai)->format('H:i') . ' - ' . \Carbon\Carbon::parse($jamSelesai)->format('H:i');
                                        }
                                        if ($jamMasuk || $jamPulang) {
                                            $tooltipLines[] = 'Absen: ' . ($jamMasuk ? \Carbon\Carbon::parse($jamMasuk)->format('H:i') : '-') . ' - ' . ($jamPulang ? \Carbon\Carbon::parse($jamPulang)->format('H:i') : '?');
                                        }
                                        if ($terlambat) {
                                            $tooltipLines[] = 'Terlambat masuk';
                                        }
                                        if ($pulangCepat) {
                                            $tooltipLines[] = 'Pulang lebih awal';
                                        }
                                        $tooltipText = implode("\n", $tooltipLines);
                                    @endphp

                                    <div class="flex flex-col items-center gap-1">
                                        <span class="font-bold text-md">{{ $namaShift ?? '-' }}</span>

                                        <div class="flex items-center justify-center gap-2">
                                            @if ($terlambat || $pulangCepat)
                                                <span class="tooltip tooltip-left text-xs text-yellow-300" data-tip="{{ $tooltipText }}">
                                                    <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                                                </span>
                                            @endif

                                            <span class="tooltip tooltip-left text-xs text-neutral" data-tip="Jadwal Telah Terkunci">
                                                <i class="fa-solid fa-lock text-xs"></i>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            @endfor
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $tanggal->daysInMonth + 1 }}" class="text-center py-6 text-gray-400">
                                Belum ada staff dengan posisi ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>