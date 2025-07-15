<?php

namespace App\Livewire;

use App\Models\Role;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class RoleTable extends PowerGridComponent
{
    public string $tableName = 'role-table-2eyg23-table';

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

    public function datasource(): ?Builder
    {
        return Role::query()->with('aksesrole.akses');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('nama_role')
            ->add('nama_akses', function ($data) {
                return $data->aksesrole
                    ->map(fn($item) => $item->akses->nama_akses ?? '-')
                    ->implode(', ');
            });
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),

            Column::make('Nama Role', 'nama_role')
                ->editOnClick(hasPermission: true)
                ->sortable()
                ->searchable(),

            Column::make('Nama Akses', 'nama_akses')
                ->sortable()
                ->searchable(),

            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(Role $row): array
    {
        return [
            Button::add('updateakses')  
                ->slot('<i class="fa-solid fa-pen-clip"></i> Akses Role')
                ->attributes([
                    'onclick' => 'modaleditrole.showModal()',
                    'class' => 'btn btn-primary'
                ])
                ->dispatchTo('role.updateakses', 'getupdaterole', ['rowId' => $row->id]),
            
            Button::add('deleterole')
                ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
                ->class('btn btn-error')
                ->dispatch('modaldeleterole', ['rowId' => $row->id]),
        ];
    }

    #[\Livewire\Attributes\On('modaldeleterole')]
    public function modaldeleterole($rowId): void
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
                    Livewire.dispatch('konfirmasideleteprole', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasideleteprole')]
    public function konfirmasideleteprole($rowId): void
    {
        Role::findOrFail($rowId)->delete();

        $this->dispatch('pg:eventRefresh')->to(self::class); // refresh PowerGrid

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil dihapus.',
        ]);
    }

    protected function validationAttributes(): array
    {
        return [
            'nama_role.*' => 'Nama Role',
        ];
    }

    protected function messages(): array
    {
        return [
            'nama_role.*.required' => 'Nama Role wajib diisi.',
        ];
    }

    public function onUpdatedEditable(string|int $id, string $field, string $value): void
    {
        // Validasi langsung di sini
        $validator = validator(
            [$field => $value],
            [$field => ['required', 'string', 'max:255']],
            [$field.'.required' => 'Kolom tidak boleh kosong.']
        );

        if ($validator->fails()) {
            // Batal update jika error
            $this->dispatch('pg:editable-cancel', field: $field, id: $id);
            return;
        }

        // Update ke database
        Role::query()->findOrFail($id)->update([
            $field => $value,
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
