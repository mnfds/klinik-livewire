<?php

namespace App\Livewire\AntrianTable;

use App\Models\NomorAntrian;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Responsive;

final class Masuk extends PowerGridComponent
{
    public string $tableName = 'masuk-njx3xm-table';

    public function setUp(): array
    {
        // $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
            PowerGrid::Responsive()
                ->fixedColumns('kode_nomor', 'actions'),
        ];
    }

    public function datasource(): Builder
    {
        return NomorAntrian::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('kode_nomor', function ($row) {
                return $row->kode . '-' . $row->nomor_antrian;
            })
            ->add('actions', fn ($row) => '')
            ->add('created_at_formatted', function ($row) {
                return Carbon::parse($row->created_at)->format('D,d M Y H:i');
            });
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),
            Column::make('Nomor Antrian', 'kode_nomor')->searchable()->sortable(),
            Column::make('Tanggal', 'created_at_formatted')->sortable(),
            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(NomorAntrian $row): array
    {
        return [
            Button::add('pindahButton')
                ->slot('<i class="fa-solid fa-arrow-right"></i> Pindah')
                ->class('btn btn-primary')
                ->dispatch('pindahModalButton', ['rowId' => $row->id]),

            Button::add('deleteButton')
                ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
                ->class('btn btn-error')
                ->dispatch('deleteModalNomorAntrian', ['rowId' => $row->id]),
        ];
    }

    #[\Livewire\Attributes\On('pindahModalButton')]
    public function pindahModalButton($rowId): void
    {
        $this->js(<<<JS
            Swal.fire({
                title: 'Pindah Antrian?',
                text: 'Antrian Yang Telah Dipindah Tidak bisa Dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Pindah'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('KonfirmasiPindahAntrian', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('KonfirmasiPindahAntrian')]
    public function KonfirmasiPindahAntrian($rowId): void
    {
        NomorAntrian::findOrFail($rowId)->delete();

        $this->dispatch('pg:eventRefresh')->to(self::class); // refresh PowerGrid

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil dihapus.',
        ]);
    }

    #[\Livewire\Attributes\On('deleteModalNomorAntrian')]
    public function deleteModalNomorAntrian($rowId): void
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
                    Livewire.dispatch('KonfirmasiDeleteNomorAntrian', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('KonfirmasiDeleteNomorAntrian')]
    public function KonfirmasiDeleteNomorAntrian($rowId): void
    {
        NomorAntrian::findOrFail($rowId)->delete();

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
