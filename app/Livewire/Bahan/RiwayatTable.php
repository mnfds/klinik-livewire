<?php

namespace App\Livewire\Bahan;

use App\Models\MutasiBahanBaku;
use App\TipeRiwayatBahanbaku;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class RiwayatTable extends PowerGridComponent
{
    public string $tableName = 'riwayat-table-wuwfnl-table';

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
        return MutasiBahanBaku::with(['bahanbaku']);
    }

    public function relationSearch(): array
    {
        return [
            'bahanbaku' => ['nama','satuan']
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('tipe')
            ->add('bahanbaku.nama')
            ->add('bahanbaku.satuan')
            ->add('jumlah')
            ->add('diajukan_oleh')
            ->add('catatan');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),

            Column::make('Tipe', 'tipe')->sortable()->searchable(),
            
            Column::make('Nama', 'bahanbaku.nama')->searchable(),

            Column::make('Jumlah', 'jumlah')->sortable(),
            
            Column::make('Satuan', 'bahanbaku.satuan')->searchable(),

            Column::make('Orang Terkait', 'diajukan_oleh')->searchable(),
            
            Column::make('Keterangan', 'catatan')->searchable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::enumSelect('tipe', 'tipe')
                ->dataSource(TipeRiwayatBahanbaku::cases())
                ->optionLabel('labelPowergridFilter'),
        ];
    }

    public function actions(MutasiBahanBaku $row): array
    {
        return [
            Button::add('updatemutasibahan')  
                ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
                ->attributes([
                    'onclick' => 'modaleditmutasibahan.showModal()',
                    'class' => 'btn btn-primary'
                ])
                ->dispatchTo('bahan.updatemutasi', 'getupdatemutasibahan', ['rowId' => $row->id]),
            
            Button::add('deletemutasibahan')
                ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
                ->class('btn btn-error')
                ->dispatch('modaldeletemutasibahan', ['rowId' => $row->id]),
        ];
    }

    #[\Livewire\Attributes\On('modaldeletemutasibahan')]
    public function modaldeletemutasibahan($rowId): void
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
                    Livewire.dispatch('konfirmasideletemutasibahan', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasideletemutasibahan')]
    public function konfirmasideletemutasibahan($rowId): void
    {
        MutasiBahanBaku::findOrFail($rowId)->delete();

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
