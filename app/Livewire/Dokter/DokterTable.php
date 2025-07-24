<?php

namespace App\Livewire\Dokter;

use App\Models\Dokter;
use App\Models\PoliKlinik;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class DokterTable extends PowerGridComponent
{
    public string $tableName = 'dokter-table-mfxkwq-table';

    public function setUp(): array
    {
        return [
            PowerGrid::header()->showSearchInput(),
            PowerGrid::footer()->showPerPage()->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Dokter::with(['user', 'dokterpoli.poli']);
    }

    public function relationSearch(): array
    {
        return [
            'user' => ['email'],
            'dokterpoli.poli' => ['nama_poli'],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('nama_dokter')
            ->add('user.email')
            ->add('telepon')

            // Wajib: untuk menampilkan filter poli (field harus ada agar filter muncul)
            ->add('filter_poli_id', fn($row) => optional($row->dokterpoli->first())->poli_id)

            // Optional: tampilkan nama poli gabungan
            ->add('nama_poli', function ($row) {
                return $row->dokterpoli
                    ->map(fn($dp) => $dp->poli->nama_poli ?? '-')
                    ->implode(', ');
            });
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),
            Column::make('Nama', 'nama_dokter')->sortable()->searchable(),
            Column::make('Email', 'user.email')->searchable(),
            Column::make('Telepon', 'telepon')->searchable(),

            // Ini akan tetap menampilkan nama poli
            Column::make('Poli', 'nama_poli')->searchable(),

            // Kolom aksi
            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('nama_poli', 'filter_poli_id') // Nama field HARUS ada di fields()
                ->dataSource(PoliKlinik::all())
                ->optionLabel('nama_poli')
                ->optionValue('id')
                ->builder(function (Builder $query, $value) {
                    return $query->whereHas('dokterpoli.poli', fn($q) => $q->where('id', $value));
                }),
        ];
    }

    public function actions(Dokter $row): array
    {
        return [
            Button::add('detail')
                ->slot('<i class="fas fa-eye"></i> Detail')
                ->tag('button')
                ->attributes([
                    'title' => 'Lihat detail',
                    'onclick' => "Livewire.navigate('" . route('dokter.detail', $row->id) . "')",
                    'class' => 'btn btn-primary',
                ]),

            Button::add('Edit')
                ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
                ->tag('button')
                ->attributes([
                    'title' => 'Edit data',
                    'onclick' => "Livewire.navigate('" . route('dokter.update', $row->id) . "')",
                    'class' => 'btn btn-secondary',
                ]),

            Button::add('delete')
                ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
                ->class('btn btn-error')
                ->dispatch('delete', ['rowId' => $row->id]),
        ];
    }
}
