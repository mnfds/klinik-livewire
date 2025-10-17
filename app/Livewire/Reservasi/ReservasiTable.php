<?php

namespace App\Livewire\Reservasi;

use App\Models\Reservasi;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
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
        return Reservasi::with(['pasien', 'poliklinik', 'dokter']);
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
            ->add('status', fn ($row) =>
                match ($row->status) {
                    'belum bayar' => '<span class="badge badge-accent px-2 whitespace-nowrap">Belum Bayar</span>',
                    'belum lunas' => '<span class="badge badge-secondary px-2 whitespace-nowrap">Belum Lunas</span>',
                    'lunas' => '<span class="badge badge-success px-2 whitespace-nowrap">Lunas</span>',
                    'selesai' => '<span class="badge badge-primary px-2 whitespace-nowrap">Selesai</span>',
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
        return [
            Button::add('pendaftaranButton')
                ->slot('<i class="fa-solid fa-notes-medical"></i> Daftar')
                ->tag('button')
                ->attributes([
                    'title' => 'Pendaftaran Pasien',
                    'onclick' => "Livewire.navigate('".route('pendaftaran.create', ['pasien_id' => $row->pasien->id, 'poli_id' => $row->poliklinik->id, 'dokter_id' => $row->dokter->id,] )."')",
                    'class' => 'btn btn-secondary'
                ]),

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
    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
