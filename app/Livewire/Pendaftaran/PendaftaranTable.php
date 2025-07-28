<?php

namespace App\Livewire\Pendaftaran;

use App\Models\PasienTerdaftar;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class PendaftaranTable extends PowerGridComponent
{
    public string $tableName = 'pendaftaran-table-3tzmte-table';

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
        return PasienTerdaftar::where('status_terdaftar', 'terdaftar')
            ->with(['pasien', 'poliklinik', 'dokter']);
    }

    public function relationSearch(): array
    {
        return [];
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
            ->add('tanggal_kunjungan') // Jika ingin menampilkan tanggal kunjungan juga
            ->add('jenis_kunjungan')  // Jika ingin menampilkan jenis kunjungan juga
            ->add('kunjungan', function($row){
                return strtoupper($row->jenis_kunjungan) . '<br><span class="text-sm text-gray-500">' . $row->tanggal_kunjungan . '</span>';
            });
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),

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

            Column::make('Tanggal Kunjungan', 'tanggal_kunjungan')
                ->hidden()
                ->sortable(),

            Column::make('Jenis Kunjungan', 'jenis_kunjungan')
                ->hidden(),

            Column::make('Kunjungan', 'kunjungan')
                ->bodyAttribute('whitespace-nowrap'),

            Column::action('Action') // untuk tombol edit/delete
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(PasienTerdaftar $row): array
    {
        return [
            Button::add('updatebarang')  
                ->slot('<i class="fa-solid fa-clipboard-list"></i> Kajian Awal')
                ->attributes([
                    'onclick' => 'modaleditbarang.showModal()',
                    'class' => 'btn btn-info'
                ])
                ->dispatchTo('barang.update', 'getupdatebarang', ['rowId' => $row->id]),
            
            Button::add('deletepasienterdaftar')
                ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
                ->class('btn btn-error')
                ->dispatch('modaldeletepasienterdaftar', ['rowId' => $row->id]),
        ];
    }

    #[\Livewire\Attributes\On('modaldeletepasienterdaftar')]
    public function modaldeletepasienterdaftar($rowId): void
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
                    Livewire.dispatch('konfirmasideletepasienterdaftar', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasideletepasienterdaftar')]
    public function konfirmasideletepasienterdaftar($rowId): void
    {
        PasienTerdaftar::findOrFail($rowId)->delete();

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
