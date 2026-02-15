<?php

namespace App\Livewire\Inventaris;

use App\Models\Inventaris;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class InventarisTable extends PowerGridComponent
{
    public string $tableName = 'inventaris-table-rvtaqc-table';

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
        return Inventaris::query()->latest();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('nama_barang')
            ->add('jumlah')
            ->add('kode_inventaris')
            ->add('lokasi')
            ->add('tanggal_perolehan')
            ->add('kondisi')
            ->add('keterangan');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),

            Column::make('Nama', 'nama_barang')->searchable(),
            Column::make('Jumlah', 'jumlah')->searchable(),
            Column::make('Kode', 'kode_inventaris')->hidden(),
            Column::make('lokasi', 'lokasi')->searchable(),
            Column::make('Tanggal Perolehan', 'tanggal_perolehan')->searchable(),
            Column::make('kondisi', 'kondisi')->searchable(),
            Column::make('Keterangan', 'keterangan')->searchable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(Inventaris $row): array
    {
        $inventarisButton = [];

        Gate::allows('akses', 'Inventaris Edit') && $inventarisButton[] =
        Button::add('updateinventaris')  
            ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
            ->attributes([
                'onclick' => 'modaleditinventaris.showModal()',
                'class' => 'btn btn-primary btn-sm'
            ])
        ->dispatchTo('inventaris.update', 'getupdateinventaris', ['rowId' => $row->id]);

        Gate::allows('akses', 'Inventaris Hapus') && $inventarisButton[] =
        Button::add('deleteinventaris')
            ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
            ->class('btn btn-error btn-sm')
        ->dispatch('modaldeleteinventaris', ['rowId' => $row->id]);

        return $inventarisButton;
    }

    #[\Livewire\Attributes\On('modaldeleteinventaris')]
    public function modaldeleteinventaris($rowId): void
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
                    Livewire.dispatch('konfirmasideleteinventaris', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasideleteinventaris')]
    public function konfirmasideleteinventaris($rowId): void
    {
        if (! Gate::allows('akses', 'Inventaris Hapus')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        Inventaris::findOrFail($rowId)->delete();

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
