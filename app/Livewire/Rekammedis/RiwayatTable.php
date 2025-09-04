<?php

namespace App\Livewire\Rekammedis;

use App\Models\PasienTerdaftar;
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
    public string $tableName = 'riwayat-table-1beegm-table';

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

    public ?int $pasien_id = null;

    public function datasource(): Builder
    {
        return PasienTerdaftar::with(['pasien', 'poliklinik', 'dokter'])
            ->when($this->pasien_id, fn ($q) => $q->where('pasien_id', $this->pasien_id));
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('poliklinik.nama_poli', fn ($row) => $row->poliklinik->nama_poli ?? '-') // Nama Poli
            ->add('created_at_formatted', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)
                    ->locale('id')
                    ->translatedFormat('l, d F Y');
            });
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),
            Column::make('Tanggal Input', 'created_at_formatted')->sortable(),
            Column::make('Poliklinik', 'poliklinik.nama_poli'),
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

    public function actions(PasienTerdaftar $row): array
    {
        return [
            Button::add('detail_kunjungan')
                ->slot('<i class="fa-solid fa-magnifying-glass"></i> Detail')
                ->tag('button')
                ->attributes([
                    'title' => 'Detail Kunjungan Medis',
                    'onclick' => "Livewire.navigate('" . route('rekam-medis-pasien.detail', ['pasien_terdaftar_id' => $row->id]) . "')",
                    'class' => 'btn btn-primary',
                ]),
        ];
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
