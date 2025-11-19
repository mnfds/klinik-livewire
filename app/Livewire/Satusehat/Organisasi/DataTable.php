<?php

namespace App\Livewire\Satusehat\Organisasi;

use App\Models\Organization;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class DataTable extends PowerGridComponent
{
    public string $tableName = 'data-table-s0aqoj-table';

    public function setUp(): array
    {
        // $this->showCheckBox();

        return [
            // PowerGrid::header()
            //     ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Organization::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('id_satusehat')
            ->add('departemen')
            ->add('status')
            ->add('alamat_full', function ($row) {
                return $row->alamat . ', ' . $row->kota . ', ' .$row->kode_pos;
            })
            ->add('full_contact', function ($row) {
                return "
                    <div>" .
                        "<div><i class='fa-solid fa-phone'></i> " . ($row->no_telp ?? '-') . "</div>" .
                        "<div><i class='fa-solid fa-envelope'></i> " . ($row->email ?? '-') . "</div>" .
                        "<div><i class='fa-solid fa-globe'></i> " .  ($row->web ?? '-') . "</div>" .
                    "</div>
                ";
            })
            ->add('status_badge', function ($row) {
                return $row->status == "1"
                    ? '<div class="badge badge-success"><i class="fa-regular fa-circle-check"></i></div>'
                    : '<div class="badge badge-danger"><i class="fa-regular fa-circle-xmark"></i></div>';
            });
    }

    public function columns(): array
    {
        return [
            Column::make('#','')->index(),

            Column::make('Id SatuSehat', 'id_satusehat'),
            Column::make('Departemen', 'departemen'),
            Column::make('Alamat', 'alamat_full')
                ->bodyAttribute('whitespace-nowrap'),
            Column::make('Kontak', 'full_contact')
                ->bodyAttribute('whitespace-nowrap'),
            Column::make('Status', 'status_badge'),
            // Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    // public function actions(Organization $row): array
    // {
    //     return [
    //     ];
    // }

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
