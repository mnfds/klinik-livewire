<?php

namespace App\Livewire;

use App\Models\JamKerja;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class JamKerjaTable extends PowerGridComponent
{
    public string $tableName = 'jam-kerja-table-xhprc8-table';

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
        return JamKerja::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            // ->add('id')
            ->add('nama_shift')
            ->add('tipe_shift')
            ->add('jam_mulai')
            ->add('jam_selesai')
            ->add('lewat_hari');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),

            Column::make('Kode Shift', 'nama_shift')->sortable(),

            Column::make('Tipe Shift', 'tipe_shift')->searchable(),

            Column::make('Jam Mulai', 'jam_mulai'),

            Column::make('Jam Selesai', 'jam_selesai'),

            Column::make('Lintas Hari', 'lewat_hari')->toggleable(),

            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function onUpdatedToggleable(string|int $id, string $field, string $value): void
    {
        JamKerja::query()->find($id)->update([
            $field => e($value),
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Jam Kerja berhasil diperbarui.'
        ]);

        $this->skipRender(); // agar tidak render ulang seluruh table
    }

    public function actions(JamKerja $row): array
    {
        return [
            Button::add('editJamKerja')  
                ->slot('<i class="fas fa-edit mr-1"></i> Edit')
                ->attributes([
                    'onclick' => 'my_modal_1.showModal()',
                    'class' => 'btn btn-primary'
                ])
                ->dispatchTo('jamkerja.update-jamkerja', 'editJamKerja', ['rowId' => $row->id]),

            Button::add('delete')
                ->slot('<i class="fas fa-trash-alt mr-1"></i> Hapus')
                ->class('btn btn-error')
                ->dispatch('delete', ['rowId' => $row->id]),
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
        JamKerja::findOrFail($rowId)->delete();

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
