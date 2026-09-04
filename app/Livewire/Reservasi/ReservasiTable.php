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
            ->add('pasien.no_register', fn ($row) => $row->pasien->no_register ?? '-') // No 
            ->add('nama_dan_register', function($row){
                return strtoupper($row->pasien->nama) . '<br><span class="text-sm text-gray-500">' . $row->pasien->no_register . '</span>';
            })
            ->add('poliklinik.nama_poli', fn ($row) => $row->poliklinik->nama_poli ?? '-') // Nama Poli
            ->add('dokter.nama_dokter', fn ($row) => $row->dokter->nama_dokter ?? '-') // Dokter yang menangani
            ->add('dokter_dan_poli', function($row){
                return strtoupper($row->poliklinik->nama_poli) . '<br><span class="text-sm text-gray-500">' . $row->dokter->nama_dokter . '</span>';
            })
            ->add('no_telp', fn ($row) => $row->pasien->no_telp ?? '-')
            ->add('status', fn ($row) =>
                match ($row->status) {
                    'belum datang' => '<span class="badge badge-primary px-2 whitespace-nowrap">Belum Datang</span>',
                    'selesai' => '<span class="badge badge-success px-2 whitespace-nowrap">Selesai</span>',
                    'batal' => '<span class="badge badge-error px-2 whitespace-nowrap">Batal</span>',
                    default => '<span class="badge">-</span>',
                }
            )
            ->add('catatan')
            ->add('tanggal_reservasi');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),

            Column::make('Tanggal Reservasi', 'tanggal_reservasi')
                ->sortable(),

            Column::make('Nama Pasien', 'pasien.nama')
                ->searchable()
                ->hidden(),

            Column::make('No. Register', 'pasien.no_register')
                ->searchable()
                ->hidden(),

            Column::make('Pasien', 'nama_dan_register')
                ->bodyAttribute('whitespace-nowrap'),

            Column::make('Poli Tujuan', 'poliklinik.nama_poli')
                ->searchable()
                ->hidden(),

            Column::make('Dokter', 'dokter.nama_dokter')
                ->searchable()
                ->hidden(),
            
            Column::make('Poli dan Dokter', 'dokter_dan_poli')
                ->bodyAttribute('whitespace-nowrap'),

            Column::make('Catatan', 'catatan')
                ->searchable(),

            Column::make('No Telpon', 'no_telp')
                ->searchable(),

            Column::make('status', 'status')
                ->searchable(),

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
