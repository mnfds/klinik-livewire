<?php

namespace App\Livewire\Pasien;

use App\Models\Pasien;
use Illuminate\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class PasienTable extends PowerGridComponent
{
    public string $tableName = 'pasien-table-dwogwo-table';

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
        return Pasien::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('nama')
            ->add('no_register')
            ->add('nama_dan_register', function ($pasien) {
                return strtoupper($pasien->nama) . '<br><span class="text-sm text-gray-500">' . $pasien->no_register . '</span>';
            })
            ->add('tanggal_lahir', function ($row) {
                return Carbon::parse($row->created_at)->format('d M Y');
            })
            ->add('nik')
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),
            Column::make('Pasien', 'nama_dan_register')
                ->bodyAttribute('whitespace-nowrap')
                ->sortable()
                ->searchable(['nama', 'no_register']), // agar tetap bisa dicari dari dua kolom
            Column::make('Tanggal Lahir', 'tanggal_lahir')->searchable(),
            Column::make('NIK', 'nik')->searchable(),
            Column::make('No IHS', 'no_ihs')->searchable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(Pasien $row): array
    {
        return [
            // Button::add('edit')
            //     ->slot('Edit: '.$row->id)
            //     ->id()
            //     ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
            //     ->dispatch('edit', ['rowId' => $row->id])
        ];
    }

    public function actionsFromView($row): View
    {
        return view('components.dropdown-aksi-table-pasien', ['row' => $row]);
    }

    #[\Livewire\Attributes\On('hapusPasien')]
    public function hapusPasien($rowId): void
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
                    Livewire.dispatch('konfirmasihapuspasien', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasihapuspasien')]
    public function konfirmasihapuspasien($rowId): void
    {
        Pasien::findOrFail($rowId)->delete();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil dihapus.',
        ]);
        
        $this->dispatch('pg:eventRefresh')->to(self::class); // refresh PowerGrid

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
