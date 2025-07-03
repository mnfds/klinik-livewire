<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class UsersTable extends PowerGridComponent
{
    public string $tableName = 'users-table-jp2a1c-table';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): \Illuminate\Database\Eloquent\Builder
    {
        // return DB::table('users');
        return \App\Models\User::with('biodata');
    }

    public function relationSearch(): array
    {
        return [
            'biodata' => ['nama_lengkap', 'telepon'],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            // ->add('id')
            ->add('biodata.nama_lengkap')
            ->add('name') //column ini isinya username
            ->add('email')
            ->add('biodata.telepon');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),
            
            // Column::make('Id', 'id'),

            Column::make('Nama', 'biodata.nama_lengkap')->sortable()->searchable(),

            Column::make('Username', 'name')->sortable()->searchable(),

            Column::make('Alamat Email', 'email'),

            Column::make('Telepon', 'biodata.telepon'),

            Column::action('Action'),

        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    #[\Livewire\Attributes\On('delete')]
    public function confirmDelete($rowId): void
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
                    Livewire.dispatch('deleteConfirmed', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('deleteConfirmed')]
    public function deleteConfirmed($rowId): void
    {
        User::findOrFail($rowId)->delete();

        $this->dispatch('pg:eventRefresh')->to(self::class); // refresh PowerGrid

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil dihapus.',
        ]);
    }

    public function actions($row): array
    {
        return [
            Button::add('detail')
                ->slot('<i class="fas fa-eye"></i> Detail')
                ->tag('button') // supaya tidak jadi <a>
                ->attributes([
                    'title' => 'Lihat detail',
                    'onclick' => "Livewire.navigate('".route('users.edit', $row->id)."')",
                    'class' => 'btn btn-primary'
                ]),

            Button::add('delete')
                ->slot('<i class="fas fa-trash-alt mr-1"></i> Hapus')
                ->class('btn btn-error')
                ->dispatch('delete', ['rowId' => $row->id]),
        ];
    }

    
    // public function actionRules($row): array
    // {
    //    return [
    //         // Hide button edit for ID 1
    //         // Rule::button('edit')
    //         //     ->when(fn($row) => $row->id === 1)
    //         //     ->hide(),
    //     ];
    // }

}
