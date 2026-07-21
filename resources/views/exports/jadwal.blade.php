@php
    use Carbon\Carbon;
@endphp
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Staff</th>
            <th>Role</th>
            @foreach($days as $day)
                <th>{{ $day->translatedFormat('d M (D)') }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($users as $i => $user)
            @php
                $namaStaff = $user->dokter->nama_dokter
                    ?? $user->biodata->nama_lengkap
                    ?? $user->name
                    ?? '-';

                $jadwalUser = $jadwals->get($user->id, collect());
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $namaStaff }}</td>
                <td>{{ $user->role->nama_role ?? '-' }}</td>
                @foreach($days as $day)
                    @php
                        $jadwalHariIni = $jadwalUser->firstWhere(
                            fn($j) => Carbon::parse($j->tanggal)->isSameDay($day)
                        );
                    @endphp
                    <td>{{ $jadwalHariIni?->jamkerja?->nama_shift ?? '-' }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>