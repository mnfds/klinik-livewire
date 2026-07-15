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
    </style>

    <div class="max-w-full mx-auto">
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
                            <td class="bg-base-100 px-2 py-3 border border-base-200 font-medium whitespace-nowrap">
                                @if ($user->biodata)
                                    {{ $user->biodata->nama_lengkap ?? '-' }}
                                @endif
                                @if ($user->dokter)
                                    {{ $user->dokter->nama_dokter ?? '-' }}
                                @endif
                                @if ($user->biodata && $user->dokter)
                                    {{ $user->name ?? '-' }}
                                @endif
                            </td>

                            @for ($day = 1; $day <= $tanggal->daysInMonth; $day++)
                                @php
                                    $tglCell = $tanggal->copy()->day($day)->format('Y-m-d');
                                    $shift = collect($jadwal[$user->id] ?? [])->firstWhere('tanggal', $tglCell);
                                    $namaShift = $shift['jamkerja']['nama_shift'] ?? null;
                                @endphp
                                <td
                                    wire:click="editShift({{ $user->id }}, '{{ $tglCell }}')"
                                    class="editable text-center text-md border border-base-200 px-2 py-3 cursor-pointer transition hover:brightness-95 hover:outline hover:outline-2 hover:outline-primary hover:-outline-offset-2 {{ $namaShift ? 'bg-success/40 text-success-content' : 'bg-base-200' }}"
                                >
                                    {{ $namaShift ?? '-' }}
                                    <span class="tooltip tooltip-left text-xs text-error" data-tip="08:10 - 16:55">
                                        <i class="fa-solid fa-x"></i>
                                    </span>
                                    <span class="tooltip tooltip-left text-xs text-neutral" data-tip="Jadwal Telah Terkunci">
                                        <i class="fa-solid fa-lock text-xs"></i>
                                    </span>
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