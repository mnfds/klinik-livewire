<?php

namespace App\Livewire\Satusehat\Praktisi;

use App\Models\Practitioner;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class Datatable extends PowerGridComponent
{
    public string $tableName = 'datatable-vdqulm-table';

    public function setUp(): array
    {
        // $this->showCheckBox();

        return [
            // PowerGrid::header()
                // ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Practitioner::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('nomor', function ($row) {
                return "
                    <div>" .
                        "<div> NIK: " . ($row->nik ?? '-') . "</div>" .
                        "<div> IHS: " . ($row->id_satusehat ?? '-') . "</div>" .
                    "</div>
                ";
            })
            ->add('praktisi', function ($row) {
                return "
                    <div>" .
                        "<div class='font-bold'>" . ($row->name ?? '-') . "</div>" .
                        "<div>" . ($row->gender ?? '-') .", ". ($row->birthdate ?? '-') . "</div>" .
                    "</div>
                ";
            })
            ->add('alamat', function ($row) {
                return "
                    <div>" . 
                        ($row->address_line) . ", " . ($row->city) .
                    "</div>
                ";
            })
            ->add('city')
            ->add('address_line');
    }

    public function columns(): array
    {
        return [
            Column::make('NIK & IHS', 'nomor')
                ->bodyAttribute('whitespace-nowrap'),
            Column::make('Praktisi', 'praktisi')
                ->bodyAttribute('whitespace-nowrap'),
            Column::make('Alamat', 'alamat')
                ->bodyAttribute('whitespace-nowrap'),
            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(Practitioner $row): array
    {
        return [
            Button::add('deleteButton')
                ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
                ->class('btn btn-error')
                ->dispatch('deletePraktisi', ['rowId' => $row->id]),
        ];
    }

    #[\Livewire\Attributes\On('deletePraktisi')]
    public function deletePraktisi($rowId): void
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
                    Livewire.dispatch('konfirmasideletePraktisi', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasideletePraktisi')]
    public function konfirmasideletePraktisi($rowId): void
    {
        Practitioner::findOrFail($rowId)->delete();

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
