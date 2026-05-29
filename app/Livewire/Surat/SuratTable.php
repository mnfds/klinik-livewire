<?php

namespace App\Livewire\Surat;

use App\Models\SuratKeterangan;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class SuratTable extends PowerGridComponent
{
    public string $tableName = 'surat-table-mzx2gq-table';

    public function setUp(): array
    {
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
        return SuratKeterangan::query()
            ->join('pasien_terdaftars', 'surat_keterangans.pasien_terdaftar_id', '=', 'pasien_terdaftars.id')
            ->join('pasiens', 'pasien_terdaftars.pasien_id', '=', 'pasiens.id')
            ->join('dokters', 'pasien_terdaftars.dokter_id', '=', 'dokters.id')
            ->select([
                'surat_keterangans.*',
                'pasiens.nama          as nama_pasien',
                'pasiens.no_register   as no_register',
                'dokters.nama_dokter   as nama_dokter',
            ]);
    }

    public function relationSearch(): array
    {
        return [
            'pasienTerdaftar.pasien' => ['nama', 'no_register'],
            'pasienTerdaftar.dokter' => ['nama_dokter'],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('#')
            ->add('no_surat')
            ->add('pasien.nama', fn ($row) => $row->pasienTerdaftar->pasien->nama ?? '-') // Nama Pasien
            ->add('pasien.no_register', fn ($row) => $row->pasienTerdaftar->pasien->no_register ?? '-') // No 
            ->add('nama_dan_register', function($row){
                return strtoupper($row->pasienTerdaftar->pasien->nama) . '<br><span class="text-sm text-gray-500">' . $row->pasienTerdaftar->pasien->no_register . '</span>';
                })
            ->add('nama_dokter')
            ->add('kondisi', fn ($row) => $row->sakit ?? '-');
    }

    public function columns(): array
    {
        return [
            Column::make('No. Surat', 'no_surat'),
            Column::make('Nama Pasien', 'pasien.nama')
                ->searchable()
                ->hidden(),
            Column::make('No. Register', 'pasien.no_register')
                ->searchable()
                ->hidden(),
            Column::make('Pasien', 'nama_dan_register')
                ->bodyAttribute('whitespace-nowrap'),
            Column::make('kondisi', 'sakit'),
            Column::action('Action') // untuk tombol edit/delete
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(SuratKeterangan $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: '.$row->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id])
        ];
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
