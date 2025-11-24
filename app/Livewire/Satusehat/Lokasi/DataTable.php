<?php

namespace App\Livewire\Satusehat\Lokasi;

use App\Models\Locations;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;

final class DataTable extends PowerGridComponent
{
    public string $tableName = 'data-table-yynvzs-table';

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
        return Locations::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('description')
            ->add('alamat', function ($row) {
                $prov = Province::find($row->province_code)?->name;
                $city = Regency::find($row->city_code)?->name;
                $dist = District::find($row->district_code)?->name;
                $vill = Village::find($row->village_code)?->name;

                return collect([
                    $row->alamat ?? null,
                    $vill ? 'Kel. ' . $vill : null,
                    $dist ? 'Kec. ' . $dist : null,
                    $city ?? null,
                    $prov ?? null,
                    ($row->rt || $row->rw) ? 'RT ' . ($row->rt ?? '-') . '/RW ' . ($row->rw ?? '-') : null,
                    $row->kode_pos ?? null,
                ])->filter()->join(', ') ?: '-';
            })
            ->add('kontak', function ($row) {
                return "
                    <div>" .
                        "<div><i class='fa-solid fa-phone'></i> " . ($row->no_telp ?? '-') . "</div>" .
                        "<div><i class='fa-solid fa-envelope'></i> " . ($row->email ?? '-') . "</div>" .
                        "<div><i class='fa-solid fa-globe'></i> " .  ($row->web ?? '-') . "</div>" .
                    "</div>
                ";
            })
            ->add('koordinat', function ($row) {
                return "
                    <div>" .
                        "<div>Lat: " . ($row->latitude ?? '-') . "</div>" .
                        "<div>Long: " . ($row->longitude ?? '-') . "</div>" .
                        "<div>Alt: " .  ($row->altitude ?? '-') . "</div>" .
                    "</div>
                ";
            })
            ->add('id_satusehat');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),
            Column::make('Id SatuSehat', 'id_satusehat',)
                ->sortable(),
            Column::make('Nama Lokasi/Ruang', 'name',),
            Column::make('Deskripsi', 'description',),
            Column::make('Alamat', 'alamat',),
            Column::make('Kontak', 'kontak',)
                ->bodyAttribute('whitespace-nowrap'),
            Column::make('Koordinat', 'koordinat',)
                ->bodyAttribute('whitespace-nowrap'),

            // Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    // public function actions(Locations $row): array
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
