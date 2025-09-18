<?php

namespace App\Livewire;

use App\Models\Pelayanan;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class PelayananTable extends PowerGridComponent
{
    public string $tableName = 'pelayanan-table-mouyxx-table';

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
        return Pelayanan::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('nama_pelayanan')
            ->add('harga_pelayanan', fn ($pelayanan) => number_format($pelayanan->harga_pelayanan, 0, ',', '.'))
            ->add('diskon', fn ($row) => $row->diskon ? $row->diskon . '%' : '0%')
            ->add('harga_bersih', fn ($pelayanan) => number_format($pelayanan->harga_bersih, 0, ',', '.'))
            ->add('deskripsi');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),

            Column::make('Nama Pelayanan', 'nama_pelayanan')
                ->sortable()
                ->searchable(),

            Column::make('Harga Dasar', 'harga_pelayanan')
                ->sortable(),

            Column::make('Diskon', 'diskon')
                ->sortable(),

            Column::make('Harga Bersih', 'harga_bersih')
                ->sortable(),

            Column::make('Deskripsi', 'deskripsi'),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(Pelayanan $row): array
    {
        return [
            Button::add('editpelayanan')  
                ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
                ->attributes([
                    'onclick' => 'modaleditpelayanan.showModal()',
                    'class' => 'btn btn-primary'
                ])
                ->dispatchTo('pelayanan.update-pelayanan', 'editPelayanan', ['rowId' => $row->id]),
            
            Button::add('deletePelayanan')
                ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
                ->class('btn btn-error')
                ->dispatch('modaldeletepelayanan', ['rowId' => $row->id]),
        ];
    }

    #[\Livewire\Attributes\On('modaldeletepelayanan')]
    public function modaldeletepelayanan($rowId): void
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
                    Livewire.dispatch('konfirmasideletepelayanan', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasideletepelayanan')]
    public function konfirmasideletepelayanan($rowId): void
    {
        Pelayanan::findOrFail($rowId)->delete();

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
