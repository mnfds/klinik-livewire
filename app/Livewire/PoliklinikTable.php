<?php

namespace App\Livewire;

use App\Models\PoliKlinik;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class PoliklinikTable extends PowerGridComponent
{
    public string $tableName = 'poliklinik-table-gkzxqe-table';


    public $editingPoli,$nama_poli,$kode_poli;

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
        return PoliKlinik::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('nama_poli')
            ->add('kode')
            ->add('status')
            ->add('status_label', fn ($model) => $model->status === 1 ? 'Aktif' : 'Nonaktif');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),
            Column::make('Nama Poliklinik', 'nama_poli')
                ->sortable()
                ->searchable(),

            Column::make('Kode Poli', 'kode')
                ->sortable()
                ->searchable(),

            Column::make('Status', 'status_label')
                ->hidden(),
            
            Column::make('Status Aktif', 'status')
                ->toggleable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    public function actions(PoliKlinik $row): array
    {
        return [
            // Button::add('edit')
            //     ->slot('Edit: '.$row->id)
            //     ->id()
            //     ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
            //     ->dispatch('edit', ['rowId' => $row->id]),

            Button::add('editpoli')  
                ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
                ->attributes([
                    'onclick' => 'modaleditpoli.showModal()',
                    'class' => 'btn btn-primary'
                ])
                ->dispatchTo('poli.update-poliklinik', 'editPoli', ['rowId' => $row->id]),
            
            Button::add('deletePoli')
                ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
                ->class('btn btn-error')
                ->dispatch('modaldeletepoli', ['rowId' => $row->id]),
        ];
    }

    #[\Livewire\Attributes\On('modaldeletepoli')]
    public function modaldeletepoli($rowId): void
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
                    Livewire.dispatch('konfirmasideletepoli', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasideletepoli')]
    public function konfirmasideletepoli($rowId): void
    {
        PoliKlinik::findOrFail($rowId)->delete();

        $this->dispatch('pg:eventRefresh')->to(self::class); // refresh PowerGrid

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil dihapus.',
        ]);
    }

    public function onUpdatedToggleable(string|int $id, string $field, string $value): void 
    {
        $boolStatus = $value === 'true' || $value === '1';

        PoliKlinik::find($id)->update([
            $field => $boolStatus,
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Status Poli berhasil diperbarui.'
        ]);

        // $this->skipRender();
        $this->dispatch('pg:eventRefresh')->to(self::class);
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
