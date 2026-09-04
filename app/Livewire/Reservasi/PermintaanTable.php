<?php

namespace App\Livewire\Reservasi;

use App\Models\PermintaanReservasi;
use App\Models\PoliKlinik;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class PermintaanTable extends PowerGridComponent
{
    public string $tableName = 'permintaan-table-ztlmdy-table';

    public function setUp(): array
    {
        // $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return PermintaanReservasi::query()
            ->with(['poliklinik', 'dokter'])
            ->orderByRaw("
                CASE status
                    WHEN 'menunggu' THEN 1
                    WHEN 'disetujui' THEN 2
                    WHEN 'ditolak' THEN 3
                END
            ")
            ->orderBy('tanggal_reservasi');
    }

    public function relationSearch(): array
    {
        return [
            'poliklinik' => ['nama_poli'],
            'dokter' => ['nama_dokter'],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('#')
            ->add('nama_register', function ($row) {
                $nama = strtoupper($row->nama ?? '-');
                $noRegister = $row->no_register ?? '-';
                return $nama .
                    '<br><span class="text-sm text-gray-500">' . $noRegister . '</span>';
            })

            ->add('nik', fn ($row) => $row->nik ?? '-')
            ->add('no_telp', fn ($row) => $row->no_telp ?? '-')
            ->add('telp_nik', function ($row) {
                $telp = strtoupper($row->no_telp ?? '-');
                $nik = $row->nik ?? '-';
                return $telp .
                    '<br><span class="text-sm text-gray-500">NIK: ' . $nik . '</span>';
            })

            ->add('tanggal_jam', function ($row) {
                $tanggal = $row->tanggal_reservasi
                    ? \Carbon\Carbon::parse($row->tanggal_reservasi)->format('d M Y')
                    : '-';
                $jam = $row->jam_reservasi
                    ? \Carbon\Carbon::parse($row->jam_reservasi)->format('H:i') . ' WITA'
                    : '-';
                return strtoupper($tanggal) .
                    '<br><span class="text-sm text-gray-500">' . $jam . '</span>';
            })

            ->add('dokter_poli', function ($row) {
                $dokter = $row->dokter?->nama_dokter ?? 'Tanpa Dokter Spesifik';
                $poli = $row->poliklinik?->nama_poli ?? '-';
                return strtoupper($dokter) .
                    '<br><span class="text-sm text-gray-500">' . $poli . '</span>';
            })

            ->add('tipe_status', function ($row) {
                $tipe = $row->pasien_baru ? 'Pasien Baru' : 'Pasien Lama';
                $status = $row->status ?? '-';
                $badge = match ($status) {
                    'menunggu' => '<span class="badge badge-warning badge-sm">Menunggu</span>',
                    'disetujui' => '<span class="badge badge-success badge-sm">Disetujui</span>',
                    'ditolak' => '<span class="badge badge-error badge-sm">Ditolak</span>',
                    default => '<span class="badge badge-sm">' . ucfirst($status) . '</span>',
                };
                return strtoupper($tipe) . '<br>' . $badge;
            })

            ->add('catatan', function ($row) {
                $catatan = $row->catatan ?? '-';
                $escaped = e($catatan);

                return '<span
                            x-data="{ expanded: false }"
                            x-on:click="expanded = !expanded"
                            x-bind:class="expanded ? \'whitespace-normal\' : \'max-w-[220px] truncate\'"
                            class="block cursor-pointer hover:text-primary"
                            title="Klik untuk lihat selengkapnya"
                        >' . $escaped . '</span>';
            });
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),

            Column::make('Tanggal Reservasi', 'tanggal_jam')->bodyAttribute('whitespace-nowrap'),
            Column::make('Tanggal', 'tanggal_reservasi')->sortable()->hidden(),
            Column::make('Jam', 'jam_reservasi')->sortable()->hidden(),

            Column::make('Nama', 'nama')->sortable()->searchable()->hidden(),
            Column::make('No. Telp', 'no_telp')->sortable()->searchable()->hidden(),
            Column::make('Pasien', 'nama_register')->bodyAttribute('whitespace-nowrap'),

            Column::make('NIK', 'pasien.nik')->searchable()->hidden(),
            Column::make('No Telpon', 'pasien.no_telp')->searchable()->hidden(),
            Column::make('Telp & NIK', 'telp_nik')->bodyAttribute('whitespace-nowrap'),

            Column::make('Poliklinik', 'poli_nama')->hidden(),
            Column::make('Dokter', 'dokter_nama')->hidden(),
            Column::make('Dokter & Poli', 'dokter_poli')->bodyAttribute('whitespace-nowrap'),

            Column::make('Keluhan', 'catatan'),
            
            Column::make('Tipe', 'pasien_baru_label')->sortable()->hidden(),
            Column::make('Status', 'status')->sortable()->hidden(),
            Column::make('Tipe & Status', 'tipe_status')->bodyAttribute('whitespace-nowrap'),

            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('status', 'status')
                ->dataSource([
                    ['value' => 'menunggu', 'label' => 'Menunggu'],
                    ['value' => 'disetujui', 'label' => 'Disetujui'],
                    ['value' => 'ditolak', 'label' => 'Ditolak'],
                ])
                ->optionValue('value')
                ->optionLabel('label'),
        ];
    }

    public function actions(PermintaanReservasi $row): array
    {
        $permintaanReservasi = [];
        Gate::allows('akses', 'Persetujuan Ajuan Lembur') && $permintaanReservasi[] =
        Button::add('setujui')
            ->slot('<i class="fa-solid fa-circle-check"></i> Setujui')
            ->attributes([
                'onclick' => 'modalsetujuireservasi.showModal()',
                'class' => 'btn btn-success'
            ])
        ->dispatchTo('reservasi.approval', 'getapprove', ['rowId' => $row->id]);

        Gate::allows('akses', 'Persetujuan Ajuan Lembur') && $permintaanReservasi[] =
        Button::add('tolak')  
            ->slot('<i class="fa-solid fa-circle-xmark"></i> Tolak')
            ->attributes([
                'class' => 'btn btn-warning'
            ])
        ->dispatch('tolak', ['rowId' => $row->id]);

        Gate::allows('akses', 'Pengajuan Lembur Hapus') && $permintaanReservasi[] =
        Button::add('deletePermintaan')
            ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
            ->class('btn btn-error')
        ->dispatch('modalDeletePermintaan', ['rowId' => $row->id]);

        return $permintaanReservasi;
    }

    #[\Livewire\Attributes\On('tolak')]
    public function tolak($rowId)
    {
        if (! Gate::allows('akses', 'Persetujuan Ajuan Lembur')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        PermintaanReservasi::where('id', $rowId)->update([
            'status' => 'ditolak',
        ]);
        $this->dispatch('pg:eventRefresh');
        $this->dispatch('refresh-PermintaanTable');
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Reservasi Telah Berhasil Ditolak',
        ]);
    }

    #[\Livewire\Attributes\On('modalDeletePermintaan')]
    public function modalDeletePermintaan($rowId): void
    {
        $this->js(<<<JS
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Data ini tidak bisa dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('konfirmasiDeletePermintaan', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasiDeletePermintaan')]
    public function konfirmasiDeletePermintaan($rowId): void
    {
        if (! Gate::allows('akses', 'Pengajuan Lembur Hapus')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        PermintaanReservasi::findOrFail($rowId)->delete();

        $this->dispatch('pg:eventRefresh')->to(self::class); // refresh PowerGrid

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil dihapus.',
        ]);
    }

    #[\Livewire\Attributes\On('refresh-PermintaanTable')]
    public function refreshPermintaan()
    {
        $this->dispatch('pg:eventRefresh');
    }

    public function actionRules($row): array
    {
        return [
            Rule::button('deletePermintaan')
                ->when(fn ($row) => $row->status === 'menunggu')
                ->hide(),

            Rule::button('setujui')
                ->when(fn ($row) => $row->status !== 'menunggu')
                ->hide(),

            Rule::button('tolak')
                ->when(fn ($row) => $row->status !== 'menunggu')
                ->hide(),
        ];
    }
}
