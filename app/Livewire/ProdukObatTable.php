<?php

namespace App\Livewire;

use App\Models\ProdukDanObat;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class ProdukObatTable extends PowerGridComponent
{
    public string $tableName = 'produk-obat-table-cjy7oz-table';

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
        return ProdukDanObat::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('nama_dagang')
            ->add('kode')
            ->add('sediaan')
            ->add('harga_dasar', fn ($produkdanobat) => number_format($produkdanobat->harga_dasar, 0, ',', '.'))
            ->add('diskon', fn ($row) => $row->diskon ? $row->diskon . '%' : '0')
            ->add('harga_bersih', fn ($produkdanobat) => number_format($produkdanobat->harga_bersih, 0, ',', '.'))
            ->add('stok', fn ($produkdanobat) => number_format($produkdanobat->stok, 0, ',', '.'))
            ->add('expired_at')
            ->add('batch')
            ->add('lokasi')
            ->add('supplier');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),
            Column::make('Nama Produk/Obat', 'nama_dagang')
                ->searchable(),

            Column::make('Kode', 'kode')
                ->searchable(),

            Column::make('Satuan', 'sediaan')
                ->searchable(),

            Column::make('Harga Jual', 'harga_dasar')
                ->sortable(),

            Column::make('Diskon', 'diskon')
                ->sortable(),

            Column::make('Harga Bersih', 'harga_bersih')
                ->sortable(),

            Column::make('Stok Tersisa', 'stok')
                ->sortable(),
            
            Column::make('Kadaluarsa', 'expired_at')
                ->sortable()
                ->searchable(),

            Column::make('Batch', 'batch')
                ->searchable(),

            Column::make('Lokasi', 'lokasi')
                ->searchable(),

            Column::make('Supplier', 'supplier')
                ->searchable(),

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

    public function actions(ProdukDanObat $row): array
    {
        return [
            Button::add('editprodukdanobat')  
                ->slot('<i class="fas fa-edit mr-1"></i> Edit')
                ->attributes([
                    'onclick' => 'modaleditprodukdanobat.showModal()',
                    'class' => 'btn btn-primary'
                ])
                ->dispatchTo('produkdanobat.update-produkdanobat', 'editProdukDanObat', ['rowId' => $row->id]),
            
            Button::add('deleteprodukdanobat')
                ->slot('<i class="fas fa-trash-alt mr-1"></i> Hapus')
                ->class('btn btn-error')
                ->dispatch('modaldeleteprodukdanobat', ['rowId' => $row->id]),
        ];
    }

    #[\Livewire\Attributes\On('modaldeleteprodukdanobat')]
    public function modaldeleteprodukdanobat($rowId): void
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
                    Livewire.dispatch('konfirmasideleteprodukdanobat', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasideleteprodukdanobat')]
    public function konfirmasideleteprodukdanobat($rowId): void
    {
        ProdukDanObat::findOrFail($rowId)->delete();

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
