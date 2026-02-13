<?php

namespace App\Livewire\Dokumen;

use App\Models\Dokumen;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class DokumenTable extends PowerGridComponent
{
    public string $tableName = 'dokumen-table-sceuyn-table';

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

    public function datasource(): Builder
    {
        return Dokumen::query()->latest();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('nama')
            ->add('lembaga')
            ->add('dokumen_dan_lembaga', function ($row) {
                return strtoupper($row->nama) . '<br><span class="text-sm text-gray-500">' . strtoupper($row->lembaga);
            })
            ->add('tanggal_berlaku')
            ->add('tanggal_tidak_berlaku')
            ->add('kadaluarsa', function ($row) {
                return \Carbon\Carbon::parse($row->tanggal_berlaku)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($row->tanggal_tidak_berlaku)->format('d M Y');
            })

            ->add('keterangan');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),

            Column::make('Nama', 'nama')->searchable()->hidden(),
            Column::make('Lembaga Terkait', 'lembaga')->searchable()->hidden(),
            Column::make('Dokumen', 'dokumen_dan_lembaga'),

            Column::make('Berlaku', 'tanggal_berlaku')->searchable()->hidden(),
            Column::make('Tidak Berlaku', 'tanggal_tidak_berlaku')->sortable()->searchable()->hidden(),
            Column::make('Kadaluarsa', 'kadaluarsa'),

            Column::make('Keterangan', 'keterangan')->searchable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(Dokumen $row): array
    {
        $dokumenButton = [];

        Gate::allows('akses', 'Dokumen Edit') && $dokumenButton[] =
        Button::add('updatedokumen')  
            ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
            ->attributes([
                'onclick' => 'modaleditdokumen.showModal()',
                'class' => 'btn btn-primary btn-sm'
            ])
        ->dispatchTo('dokumen.update', 'getupdatedokumen', ['rowId' => $row->id]);

        Gate::allows('akses', 'Dokumen Hapus') && $dokumenButton[] =
        Button::add('deletedokumen')
            ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
            ->class('btn btn-error btn-sm')
        ->dispatch('modaldeletedokumen', ['rowId' => $row->id]);

        return $dokumenButton;
    }

    #[\Livewire\Attributes\On('modaldeletedokumen')]
    public function modaldeletedokumen($rowId): void
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
                    Livewire.dispatch('konfirmasideletedokumen', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasideletedokumen')]
    public function konfirmasideletedokumen($rowId): void
    {
        if (! Gate::allows('akses', 'Dokumen Hapus')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        Dokumen::findOrFail($rowId)->delete();

        $this->dispatch('pg:eventRefresh')->to(self::class); // refresh PowerGrid

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil dihapus.',
        ]);
    }

    public function actionRules($row): array
    {
        return [
            Rule::rows()
                ->when(fn ($row) =>
                    $row->tanggal_tidak_berlaku &&
                    $row->reminder &&
                    Carbon::parse($row->tanggal_tidak_berlaku)->format('Y-m')
                        <= Carbon::now()->addMonths((int) $row->reminder)->format('Y-m')
                )
                ->setAttribute('class', 'text-red-600'),
        ];
    }
}
