<?php

namespace App\Livewire;

use App\Models\Treatment;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class TreatmentTable extends PowerGridComponent
{
    public string $tableName = 'treatment-table-rwwu8u-table';

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
        return Treatment::query()->with('treatmentbahan.bahanbaku');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('nama_treatment')
            ->add('harga_treatment', fn ($treatment) => number_format($treatment->harga_treatment, 0, ',', '.'))
            ->add('diskon', fn ($row) => $row->diskon ? $row->diskon . '%' : '0%')
            ->add('harga_bersih', fn ($treatment) => number_format($treatment->harga_bersih, 0, ',', '.'))
            ->add('deskripsi')
            ->add('nama_bahan', function ($row) {
                return $row->treatmentbahan
                    ->map(fn($item) => $item->bahanbaku->nama ?? '-')
                    ->implode(', ');
            });
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),

            Column::make('Nama Pelayanan', 'nama_treatment')
                ->sortable()
                ->searchable(),

            Column::make('Harga Dasar', 'harga_treatment')
                ->sortable()
                ->searchable(),

            Column::make('Diskon', 'diskon'),

            Column::make('Harga Bersih', 'harga_bersih'),

            Column::make('Deskripsi', 'deskripsi'),

            Column::make('Bahan Baku Terkait', 'nama_bahan')
                            ->sortable()
                            ->searchable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(Treatment $row): array
    {
        return [
            Button::add('updatebahan')  
                ->slot('<i class="fa-solid fa-pump-medical"></i> Bahan')
                ->attributes([
                    'onclick' => 'modaleditbahan.showModal()',
                    'class' => 'btn btn-secondary'
                ])
                ->dispatchTo('pelayanan.updatebahan', 'getupdatebahan', ['rowId' => $row->id]),
            
            Button::add('editTreatment')  
                ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
                ->attributes([
                    'onclick' => 'modaleditpelayananEstetika.showModal()',
                    'class' => 'btn btn-primary'
                ])
                ->dispatchTo('pelayanan.update-treatment', 'editTreatment', ['rowId' => $row->id]),
            
            Button::add('deleteTreatment')
                ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
                ->class('btn btn-error')
                ->dispatch('modaldeletepelayananEstetika', ['rowId' => $row->id]),
        ];
    }

    #[\Livewire\Attributes\On('modaldeletepelayananEstetika')]
    public function modaldeletepelayananEstetika($rowId): void
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
                    Livewire.dispatch('konfirmasideletepelayananestetika', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasideletepelayananestetika')]
    public function konfirmasideletepelayananestetika($rowId): void
    {
        Treatment::findOrFail($rowId)->delete();

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
