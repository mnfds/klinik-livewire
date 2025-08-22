<?php

namespace App\Livewire;

use App\TipeMutasiBarang;
use App\Models\MutasiBarang;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class MutasiTable extends PowerGridComponent
{
    public string $tableName = 'mutasi-table-s2n6fj-table';

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
        return MutasiBarang::with(['barang']);
    }

    public function relationSearch(): array
    {
        return [
            'barang' => ['nama','satuan']
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('tipe')
            ->add('barang.nama')
            ->add('barang.satuan')
            ->add('jumlah')
            ->add('diajukan_oleh')
            ->add('catatan');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),

            Column::make('Tipe', 'tipe')->sortable()->searchable(),
            
            Column::make('Nama', 'barang.nama')->searchable(),

            Column::make('Jumlah', 'jumlah')->sortable(),
            
            Column::make('Satuan', 'barang.satuan')->searchable(),

            Column::make('Orang Terkait', 'diajukan_oleh')->searchable(),
            
            Column::make('Keterangan', 'catatan')->searchable(),

            Column::action('Action')
        ];
    }


    public function filters(): array
    {
        return [
            Filter::enumSelect('tipe', 'tipe')
                ->dataSource(TipeMutasiBarang::cases())
                ->optionLabel('labelPowergridFilter'),
        ];
    }

    public function actions(MutasiBarang $row): array
    {
        return [
            Button::add('updatemutais')  
                ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
                ->attributes([
                    'onclick' => 'modaleditmutasi.showModal()',
                    'class' => 'btn btn-primary'
                ])
                ->dispatchTo('barang.updatemutasi', 'getupdatemutasi', ['rowId' => $row->id]),
            
            Button::add('deletemutasi')
                ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
                ->class('btn btn-error')
                ->dispatch('modaldeletemutasi', ['rowId' => $row->id]),
        ];
    }

    #[\Livewire\Attributes\On('modaldeletemutasi')]
    public function modaldeletemutasi($rowId): void
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
                    Livewire.dispatch('konfirmasideletemutasi', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasideletemutasi')]
    public function konfirmasideletemutasi($rowId): void
    {
        MutasiBarang::findOrFail($rowId)->delete();

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
