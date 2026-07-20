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
            $dimilikiLiburSaya = $kuotaLibur[$authId] ?? 4;
            $sisaCarryLiburSaya = $kuotaSisa[$authId] ?? 0;
            $totalLiburSaya = $dimilikiLiburSaya + $sisaCarryLiburSaya;
            $akhirLiburSaya = $totalLiburSaya - ($kuotaTerpakai[$authId] ?? 0);

            $jatahCutiSaya = $kuotaCuti[$authId] ?? 0;
            $sisaCutiSaya = $jatahCutiSaya - ($kuotaCutiTerpakai[$authId] ?? 0);
        @endphp

        <div class="my-2">
            <p class="text-sm font-bold">Kuota Cuti Anda:<span class="{{ $sisaCutiSaya <= 0 ? 'text-error' : 'text-success' }}">{{ $sisaCutiSaya }}/{{ $jatahCutiSaya }}</span></p>
            <p class="text-sm font-bold">Kuota Libur Anda:<span class="{{ $akhirLiburSaya <= 0 ? 'text-error' : 'text-success' }}">{{ $akhirLiburSaya }}/{{ $dimilikiLiburSaya }}(+{{ $sisaCarryLiburSaya }})</span></p>
            <p class="text-xs text-gray-400">(Setiap Sisa Kuota Libur Akan Di Jumlahkan Ke Kuota Libur Bulan Berikutnya)</p>
        </div>
        <div class="mb-4 text-center text-xl font-bold">
            {{ \Carbon\Carbon::parse($this->tanggal)->translatedFormat('F Y') }}
        </div>
        <div class="schedule-scroll overflow-x-auto">
            <table class="jadwal w-full text-sm table">
                <thead class="bg-primary text-base-primary text-xs uppercase">
                    <tr>
                        <th class="sticky top-0 left-0 z-50 bg-primary px-1.5 py-2 sm:px-3 sm:py-3 border border-base-200 text-left min-w-[100px] sm:min-w-[160px] whitespace-nowrap text-[11px] sm:text-xs">
                            Nama
                        </th>
                        @for ($day = 1; $day <= $tanggal->daysInMonth; $day++)
                            @php
                                $currentDate = $tanggal->copy()->day($day);
                                $isWeekend = $currentDate->isWeekend();
                            @endphp
                            <th class="sticky top-0 z-30 {{ $isWeekend ? 'bg-primary/70' : 'bg-primary' }} px-1 py-2 sm:px-2 sm:py-3 border border-base-200 text-center min-w-[48px] sm:min-w-[78px] whitespace-nowrap">
                                <span class="text-[11px] sm:text-base">{{ $day }}</span><br>
                                <span class="normal-case text-[9px] sm:text-[10px]">{{ $currentDate->locale('id')->isoFormat('ddd') }}</span>
                            </th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        @if ($loop->first || $user->role_id !== $users[$loop->index - 1]->role_id)
                            <tr>
                                <td colspan="{{ $tanggal->daysInMonth + 1 }}" class="bg-primary border border-base-200 p-0">
                                    <div class="sticky left-0 z-20 w-fit px-3 py-2 text-xs font-bold uppercase">
                                        {{ $user->role->nama_role ?? '-' }}
                                    </div>
                                </td>
                            </tr>
                        @endif
                        @if (Gate::allows('akses','Jadwal Tabel'))
                        <tr class="hover:bg-base-200">
                            <td class="sticky left-0 z-20 bg-primary px-1.5 py-2 sm:px-2 sm:py-3 border border-base-200 font-medium whitespace-nowrap text-[11px] sm:text-sm">
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
                                    $sisaCarryLibur = $kuotaSisa[$user->id] ?? 0;
                                    $totalLibur = $jatahLibur + $sisaCarryLibur;
                                    $terpakaiLibur = $kuotaTerpakai[$user->id] ?? 0;
                                    $sisaLibur = $totalLibur - $terpakaiLibur;

                                    $jatahCuti = $kuotaCuti[$user->id] ?? 12;
                                    $terpakaiCuti = $kuotaCutiTerpakai[$user->id] ?? 0;
                                    $sisaCuti = $jatahCuti - $terpakaiCuti;
                                @endphp
                                <br>
                                <span class="text-xs {{ $sisaCuti <= 0 ? 'text-error' : 'text-base-content' }}">
                                    Cuti: {{ $sisaCuti }}/{{ $jatahCuti }}
                                </span>
                                <br>
                                <span class="text-xs {{ $sisaLibur <= 0 ? 'text-error' : 'text-base-content' }}">
                                    Libur: {{ $sisaLibur }}/{{ $jatahLibur }}+{{ $sisaCarryLibur }}
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

                                    $terkunci = $this->isShiftTerkunci($tglCell, $user->id);

                                    $bgClass = match (true) {
                                        $tipeShift === 'libur' => 'bg-error text-error-content',
                                        $tipeShift === 'cuti' => 'bg-error text-error-content',
                                        $jamMasuk !== null => 'bg-success text-success-content',
                                        default => 'bg-neutral/50',
                                    };
                                @endphp
                                <td
                                    wire:click="editShift({{ $user->id }}, '{{ $tglCell }}', '{{ $user->role->id }}')"
                                    class="editable text-center border border-base-200 px-1 py-2 sm:px-2 sm:py-3 cursor-pointer transition hover:brightness-95 hover:outline hover:outline-2 hover:outline-primary hover:-outline-offset-2 {{ $bgClass }}"
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

                                    <div class="flex flex-col items-center gap-0.5 sm:gap-1">
                                        <span class="font-bold text-xs sm:text-md">{{ $namaShift ?? '-' }}</span>

                                        <div class="flex items-center justify-center gap-1 md:gap-2">
                                            @if ($terlambat || $pulangCepat)
                                                <span class="tooltip tooltip-left text-xs text-yellow-300" data-tip="{{ $tooltipText }}">
                                                    <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                                                </span>
                                            @endif
                                            @if ($terkunci)
                                                <span class="tooltip tooltip-left text-xs text-neutral" data-tip="Jadwal Telah Terkunci">
                                                    <i class="fa-solid fa-lock text-xs"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            @endfor
                        </tr>
                        @else
                        <tr>
                            <td colspan="{{ $tanggal->daysInMonth + 1 }}" class="py-6 bg-error/50 text-base-content">
                                Anda Tidak Memiliki Akses
                            </td>
                        </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="{{ $tanggal->daysInMonth + 1 }}" class="py-6 text-gray-400">
                                Belum ada staff dengan posisi ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>