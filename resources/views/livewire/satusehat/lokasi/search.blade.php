<div class="space-y-4">

    {{-- Input Pencarian --}}
    <div class="form-control">
        <label class="label font-semibold">Cari Location Pada Satu Sehat</label>
        <input type="text" wire:model="org_id" class="input input-bordered w-full" placeholder="Masukkan Organization ID">
    </div>

    <button wire:click="searchloc"
        class="btn btn-primary"
        wire:loading.attr="disabled">
        Cari
    </button>

    {{-- Loading --}}
    <div wire:loading class="text-primary">
        <span class="loading loading-spinner loading-md"></span>
    </div>

    {{-- Hasil --}}
    @if (!empty($dataLocation))
        <div class="mt-6 p-2">
            <h3 class="font-bold text-lg mb-2">Hasil Pencarian:</h3>
            <div class="overflow-x-auto w-full">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>ID</th>
                            {{-- <th>Identifier</th> --}}
                            <th>Nama</th>
                            <th>Deskripsi</th>
                            {{-- <th>Tipe</th> --}}
                            {{-- <th>Organization</th> --}}
                            <th>Alamat Lengkap</th>
                            {{-- <th>Kota</th>
                            <th>Alamat</th>
                            <th>Prov</th>
                            <th>Kec</th>
                            <th>Kel</th>
                            <th>RT</th>
                            <th>RW</th>
                            <th>Kode Pos</th> --}}
                            <th>Kontak</th>
                            {{-- <th>Telepon</th>
                            <th>Email</th>
                            <th>Web</th> --}}

                            <th>Koordinat</th>
                            {{-- <th>Latitude</th>
                            <th>Longitude</th>
                            <th>Altitude</th> --}}
                            {{-- <th>Status</th> --}}
                            {{-- <th>LastUpdated</th> --}}
                            {{-- <th>Version</th> --}}
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($dataLocation as $loc)
                            @php
                                // helper extraction dengan fallback
                                $res = $loc['raw'] ?? $loc; // jika kamu menyimpan seluruh resource di key 'raw'
                                // id & basic
                                $id = $res['id'] ?? ($loc['id'] ?? '-');
                                // identifier (ambil pertama jika ada)
                                $identifierSystem = $res['identifier'][0]['system'] ?? ($loc['identifier.system'] ?? null);
                                $identifierValue = $res['identifier'][0]['value'] ?? ($loc['kode_lokasi'] ?? '-');

                                // name, description, type
                                $name = $res['name'] ?? ($loc['name'] ?? '-');
                                $description = $res['description'] ?? ($loc['description'] ?? '-');
                                // physicalType text atau coding.display
                                $type = $res['physicalType']['text'] 
                                        ?? ($res['physicalType']['coding'][0]['display'] ?? ($loc['type'] ?? '-'));
                                // organization reference (format Organization/<uuid>)
                                $orgRef = $res['managingOrganization']['reference'] ?? ($loc['managingOrganization'] ?? '-');

                                // address
                                $addressLine = $res['address']['line'][0] ?? ($loc['address'] ?? '-');
                                $city = $res['address']['city'] ?? ($loc['city'] ?? '-');
                                $postal = $res['address']['postalCode'] ?? ($loc['postalCode'] ?? '-');

                                // extract extension values (province, district, village, rt, rw)
                                $prov = $kec = $kel = $rt = $rw = null;
                                if (!empty($res['address']['extension'])) {
                                    foreach ($res['address']['extension'] as $ext) {
                                        if (!empty($ext['extension']) && is_array($ext['extension'])) {
                                            foreach ($ext['extension'] as $sub) {
                                                $url = $sub['url'] ?? null;
                                                $val = $sub['valueCode'] ?? ($sub['valueString'] ?? null);
                                                if ($url && $val) {
                                                    if ($url === 'province') $prov = $val;
                                                    if ($url === 'district') $kec = $val;
                                                    if ($url === 'village') $kel = $val;
                                                    if ($url === 'rt') $rt = $val;
                                                    if ($url === 'rw') $rw = $val;
                                                    if ($url === 'city') $city = $val; // sometimes city in extension
                                                }
                                            }
                                        }
                                    }
                                }

                                // telecoms
                                $phone = $email = $url = null;
                                if (!empty($res['telecom']) && is_array($res['telecom'])) {
                                    foreach ($res['telecom'] as $t) {
                                        if (($t['system'] ?? null) === 'phone' && empty($phone)) $phone = $t['value'] ?? null;
                                        if (($t['system'] ?? null) === 'email' && empty($email)) $email = $t['value'] ?? null;
                                        if (($t['system'] ?? null) === 'url' && empty($url)) $url = $t['value'] ?? null;
                                    }
                                } else {
                                    $phone = $loc['telecom']['phone'] ?? null;
                                    $email = $loc['telecom']['email'] ?? null;
                                    $url = $loc['telecom']['url'] ?? null;
                                }

                                // posisi
                                $latitude = $res['position']['latitude'] ?? ($loc['latitude'] ?? null);
                                $longitude = $res['position']['longitude'] ?? ($loc['longitude'] ?? null);
                                $altitude = $res['position']['altitude'] ?? ($loc['altitude'] ?? null);

                                // meta
                                $lastUpdated = $res['meta']['lastUpdated'] ?? ($loc['meta.lastUpdated'] ?? '-');
                                $versionId = $res['meta']['versionId'] ?? ($loc['meta.versionId'] ?? '-');

                                // status
                                $status = $res['status'] ?? ($loc['status'] ?? '-');
                            @endphp

                            <tr>
                                <td>{{ $id }}</td>
                                {{-- <td>{{ ($identifierSystem ? $identifierSystem . ' | ' : '') . ($identifierValue ?? '-') }}</td> --}}
                                <td>{{ $name }}</td>
                                <td>{{ $description }}</td>
                                {{-- <td>{{ $type }}</td> --}}
                                {{-- <td>{{ $orgRef ?? '-' }}</td> --}}
                                <td>
                                    {{
                                        collect([
                                            $addressLine ?? null,
                                            $kel ? 'Kel. ' . $kel : null,
                                            $kec ? 'Kec. ' . $kec : null,
                                            $city ?? null,
                                            $prov ?? null,
                                            ($rt || $rw) ? 'RT ' . ($rt ?? '-') . ' / RW ' . ($rw ?? '-') : null,
                                            $postal ?? null,
                                        ])->filter()->join(', ') ?: '-'
                                    }}
                                </td>
                                
                                {{-- <td>{{ $city ?? '-' }}</td>
                                <td>{{ $addressLine ?? '-' }}</td>
                                <td>{{ $prov ?? '-' }}</td>
                                <td>{{ $kec ?? '-' }}</td>
                                <td>{{ $kel ?? '-' }}</td>
                                <td>{{ $rt ?? '-' }}</td>
                                <td>{{ $rw ?? '-' }}</td>
                                <td>{{ $postal ?? '-' }}</td> --}}
                                {{-- <td>{{ $phone ?? '-' }}</td>
                                <td>{{ $email ?? '-' }}</td>
                                <td>
                                    @if($url)
                                        <a href="{{ $url }}" target="_blank" class="link link-primary">{{ $url }}</a>
                                    @else
                                        -
                                    @endif
                                </td> --}}
                                <td>
                                    {!! collect([
                                        $phone ? '<i class="fa-solid fa-phone"></i> ' . e($phone) : null,
                                        $email ? '<i class="fa-solid fa-envelope"></i> ' . e($email) : null,
                                        $url ? '<i class="fa-solid fa-globe"></i> <a href="'. e($url) .'" target="_blank" class="link link-primary">'. e($url) .'</a>' : null,
                                    ])->filter()->join('<br>') ?: '-' !!}
                                </td>

                                {{-- <td>{{ $latitude ?? '-' }}</td>
                                <td>{{ $longitude ?? '-' }}</td>
                                <td>{{ $altitude ?? '-' }}</td> --}}
                                <td>
                                    {{
                                        collect([
                                            $latitude ? 'Lat: ' . $latitude : null,
                                            $longitude ? 'Long: ' . $longitude : null,
                                            $altitude ? 'Alt: ' . $altitude : null,
                                        ])->filter()->join(', ') ?: '-'
                                    }}
                                </td>

                                {{-- <td>
                                    @if (($status ?? null) === 'active')
                                        <div class="badge badge-success"><i class="fa-regular fa-circle-check"></i></div>
                                    @elseif (($status ?? null) === 'suspended' || ($status ?? null) === 'inactive')
                                        <div class="badge badge-warning"><i class="fa-regular fa-circle-exclamation"></i></div>
                                    @else
                                        <div class="badge badge-neutral">{{ $status ?? '-' }}</div>
                                    @endif
                                </td> --}}
                                {{-- <td>{{ $lastUpdated }}</td> --}}
                                {{-- <td>{{ $versionId }}</td> --}}
                                <td>
                                    <button class="btn btn-success" wire:click="saveLocation('{{ $id }}')">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

{{-- Untuk alert simple --}}
<script>
    Livewire.on('alert', data => {
        alert(data.message);
    });
</script>
