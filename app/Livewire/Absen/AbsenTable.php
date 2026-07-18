<?php

namespace App\Livewire\Absen;

use App\Models\Absen;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class AbsenTable extends PowerGridComponent
{
    public string $tableName = 'absen-table-fjvn6z-table';

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
        return Absen::with(['user','user.biodata'])
            ->whereDate('tanggal_absen', today())
            ->latest();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('nama_staff', function ($row){
                return strtoupper($row->user->biodata->nama_lengkap);
            })
            ->add('jam_masuk_formatted', fn($row) => $row->jam_masuk ? \Carbon\Carbon::parse($row->jam_masuk)->format('H:i') : '-')
            ->add('jam_pulang_formatted', fn($row) => $row->jam_pulang ? \Carbon\Carbon::parse($row->jam_pulang)->format('H:i') : '-')
            ->add('keterangan');
    }

    public function columns(): array
    {
        return [
            Column::make('Nama Staff', 'nama_staff')
                ->sortable()
                ->searchable(),

            Column::make('Jam Masuk', 'jam_masuk_formatted')
                ->sortable(),

            Column::make('Jam Pulang', 'jam_pulang_formatted')
                ->sortable(),

            Column::make('Keterangan', 'keterangan')
                ->searchable(),

            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(Absen $row): array
    {
        $absenTable = [];
        
        Gate::allows('akses', 'Absen Detail') && $absenTable[] =
        Button::add('detailAbsen')  
            ->slot('<i class="fa-solid fa-eye"></i> Detail')
            ->tag('button')
            ->attributes([
                'title' => 'Daftar Absensi ' . $row->user->biodata->nama_lengkap,
                'onclick' => "Livewire.navigate('" . route('absen.detail', ['id' => $row->user->id]) . "')",
                'class' => 'btn btn-secondary',
            ]);

        Gate::allows('akses', 'Absen Edit') && $absenTable[] =
        Button::add('updateAbsen')  
            ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
            ->attributes([
                'onclick' => 'modalUpdateAbsen.showModal()',
                'class' => 'btn btn-info'
            ])
        ->dispatchTo('absen.update', 'getUpdateAbsen', ['rowId' => $row->id]);
        
        Gate::allows('akses', 'Absen Hapus') && $absenTable[] =
        Button::add('deleteAbsen')
            ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
            ->class('btn btn-error')
        ->dispatch('modaldeleteAbsen', ['rowId' => $row->id]);

        return $absenTable;
    }

    #[\Livewire\Attributes\On('modaldeleteAbsen')]
    public function modaldeleteAbsen($rowId): void
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
                    Livewire.dispatch('konfirmasiDeleteAbsen', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasiDeleteAbsen')]
    public function konfirmasiDeleteAbsen($rowId): void
    {
        if (! Gate::allows('akses', 'Absen Hapus')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        Absen::findOrFail($rowId)->delete();

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
