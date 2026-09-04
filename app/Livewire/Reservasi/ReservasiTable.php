<?php

namespace App\Livewire\Reservasi;

use App\Models\Reservasi;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class ReservasiTable extends PowerGridComponent
{
    public string $tableName = 'reservasi-table-yckzvi-table';

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
        return Reservasi::with(['pasien', 'poliklinik', 'dokter'])
            ->orderByRaw("CASE WHEN status = 'belum datang' THEN 0 ELSE 1 END")
            ->orderBy('tanggal_reservasi', 'asc')
            ->orderBy('jam_reservasi', 'asc');
    }

    public function relationSearch(): array
    {
        return [
            'pasien' => ['nama', 'no_register'],
            'poliklinik' => ['nama_poli'],
            'dokter' => ['nama_dokter'],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('#') // untuk nomor urut
            ->add('pasien.nama', fn ($row) => $row->pasien->nama ?? '-') // Nama Pasien
            ->add('pasien.no_register', fn ($row) => $row->pasien->no_register ?? '-') // No RM
            ->add('nama_dan_register', function ($row) {
                $nama = strtoupper($row->pasien?->nama ?? '-');
                $noRegister = $row->pasien?->no_register ?? '-';
                return $nama .
                    '<br><span class="text-sm text-gray-500">' . $noRegister . '</span>';
            })

            ->add('poliklinik.nama_poli', fn ($row) => $row->poliklinik->nama_poli ?? '-') // Nama Poli
            ->add('dokter.nama_dokter', fn ($row) => $row->dokter->nama_dokter ?? '-') // Dokter yang menangani
            ->add('dokter_dan_poli', function ($row) {
                $dokter = strtoupper($row->dokter?->nama_dokter ?? '-');
                $poli = $row->poliklinik?->nama_poli ?? '-';
                return $dokter . '<br><span class="text-sm text-gray-500">' . $poli . '</span>';
            })

            ->add('pasien.nik', fn ($row) => $row->pasien->nik ?? '-')
            ->add('no_telp', fn ($row) => $row->pasien->no_telp ?? '-')
            ->add('telp_nik', function ($row) {
                $telp = strtoupper($row->pasien?->no_telp ?? '-');
                $nik = $row->pasien?->nik ?? '-';
                return $telp .
                    '<br><span class="text-sm text-gray-500">NIK: ' . $nik . '</span>';
            })

            ->add('status', fn ($row) =>
                match ($row->status ?? null) {
                    'belum datang' => '<span class="badge badge-primary px-2 whitespace-nowrap">Belum Datang</span>',
                    'selesai' => '<span class="badge badge-success px-2 whitespace-nowrap">Selesai</span>',
                    'batal' => '<span class="badge badge-error px-2 whitespace-nowrap">Batal</span>',
                    default => '<span class="badge">-</span>',
                }
            )

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
            });
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),

            Column::make('Tanggal Reservasi', 'tanggal_jam')->sortable(),
            Column::make('Tanggal', 'tanggal_reservasi')->sortable()->hidden(),
            Column::make('Jam', 'jam_reservasi')->sortable()->hidden(),

            Column::make('Nama Pasien', 'pasien.nama')->searchable()->hidden(),
            Column::make('No. Register', 'pasien.no_register')->searchable()->hidden(),
            Column::make('Pasien', 'nama_dan_register')->bodyAttribute('whitespace-nowrap'),

            Column::make('NIK', 'pasien.nik')->searchable()->hidden(),
            Column::make('No Telpon', 'pasien.no_telp')->searchable()->hidden(),
            Column::make('Telp & NIK', 'telp_nik')->bodyAttribute('whitespace-nowrap'),

            Column::make('Poli Tujuan', 'poliklinik.nama_poli')->searchable()->hidden(),
            Column::make('Dokter', 'dokter.nama_dokter')->searchable()->hidden(),
            Column::make('Dokter & Poli', 'dokter_dan_poli')->bodyAttribute('whitespace-nowrap'),

            Column::make('Keluhan', 'catatan'),

            Column::make('status', 'status')->searchable(),

            Column::action('Action') // untuk tombol edit/delete
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(Reservasi $row): array
    {
        $noHp = preg_replace('/[^0-9]/', '', $row->pasien->no_telp ?? '');
        if (str_starts_with($noHp, '0')) {
            $noHp = '62' . substr($noHp, 1);
        }

        $pesanWa = "Halo {$row->pasien->nama}, mengingatkan reservasi Anda di klinik pada "
            . \Carbon\Carbon::parse($row->tanggal_reservasi)->translatedFormat('d F Y')
            . ($row->jam_reservasi ? ' pukul ' . \Carbon\Carbon::parse($row->jam_reservasi)->format('H:i') : '')
            . ". Terima kasih.";

        $waUrl = 'https://wa.me/' . $noHp . '?text=' . urlencode($pesanWa);

        return [
            Button::add('pendaftaranButton')
                ->slot('<i class="fa-solid fa-notes-medical"></i> Daftar')
                ->tag('button')
                ->attributes([
                    'title' => 'Pendaftaran Pasien',
                    'onclick' => "Livewire.navigate('".route('pendaftaran.create', ['pasien_id' => $row->pasien->id, 'poli_id' => $row->poliklinik->id, 'dokter_id' => $row->dokter->id, 'tanggal_reservasi' => $row->tanggal_reservasi, 'reservasi_id' => $row->id,] )."')",
                    'class' => 'btn btn-secondary'
                ]),

            // Button::add('waReservasi')
            //     ->slot('<i class="fa-brands fa-whatsapp"></i> WA')
            //     ->tag('a')
            //     ->attributes([
            //         'href' => $waUrl,
            //         'target' => '_blank',
            //         'title' => 'Hubungi via WhatsApp',
            //         'class' => 'btn btn-success' . ($noHp === '' ? ' btn-disabled' : ''),
            //     ]),

            Button::add('editReservasi')  
                ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
                ->attributes([
                    'onclick' => 'modaleditreservasi.showModal()',
                    'class' => 'btn btn-primary'
                ])
                ->dispatchTo('reservasi.update', 'editreservasi', ['rowId' => $row->id]),
            
            Button::add('deleteReservasi')
                ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
                ->class('btn btn-error')
                ->dispatch('deleteModalReservasi', ['rowId' => $row->id]),
        ];
    }

    #[\Livewire\Attributes\On('deleteModalReservasi')]
    public function deleteModalReservasi($rowId): void
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
                    Livewire.dispatch('konfirmasideletereservasi', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasideletereservasi')]
    public function konfirmasideletereservasi($rowId): void
    {
        Reservasi::findOrFail($rowId)->delete();

        $this->dispatch('pg:eventRefresh')->to(self::class); // refresh PowerGrid

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil dihapus.',
        ]);
    }

    #[\Livewire\Attributes\On('refresh-ReservasiTable')]
    public function refreshReservasi()
    {
        $this->dispatch('pg:eventRefresh');
    }

    public function actionRules($row): array
    {
        return [
            Rule::button('pendaftaranButton')
                ->when(fn($row) => $row->status !== 'belum datang')
                ->hide(),
            Rule::button('waReservasi')
                ->when(fn($row) => $row->status !== 'belum datang')
                ->hide(),
        ];
    }

}
