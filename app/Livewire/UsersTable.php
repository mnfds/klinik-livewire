<?php

namespace App\Livewire;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
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
        return \App\Models\User::with(['biodata','role']);
    }

    public function relationSearch(): array
    {
        return [
            'biodata' => ['nama_lengkap', 'telepon'],
            'role' => ['id', 'nama_role'],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            // ->add('id')
            ->add('biodata.nama_lengkap')
            ->add('name') //column ini isinya username
            ->add('email')
            ->add('biodata.telepon')
            ->add('role.nama_role')
            ->add('role_id');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),
            
            // Column::make('Id', 'id'),

            Column::make('Nama', 'biodata.nama_lengkap')->sortable()->searchable(),

            Column::make('Username', 'name')->sortable()->searchable(),

            Column::make('Alamat Email', 'email')->searchable(),

            Column::make('Telepon', 'biodata.telepon'),
            Column::make('Role', 'role.nama_role', 'role_id'),

            Column::action('Action'),

        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('role.nama_role', 'role_id')
                ->dataSource(Role::all())
                ->optionLabel('nama_role')
                ->optionValue('id'),
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
        if (! Gate::allows('akses', 'Staff Hapus')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        User::findOrFail($rowId)->delete();

        $this->dispatch('pg:eventRefresh')->to(self::class); // refresh PowerGrid

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil dihapus.',
        ]);
    }

    public function actions($row): array
    {
        $buttons = [];

        Gate::allows('akses', 'Staff Edit') && $buttons[] =
            Button::add('detail')
                ->slot('<i class="fas fa-eye"></i> Detail')
                ->tag('button')
                ->attributes([
                    'onclick' => "Livewire.navigate('".route('users.edit', $row->id)."')",
                    'class' => 'btn btn-primary'
                ]);

        Gate::allows('akses', 'Staff Hapus') && $buttons[] =
            Button::add('delete')
                ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
                ->class('btn btn-error')
                ->dispatch('delete', ['rowId' => $row->id]);

        return $buttons;
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
