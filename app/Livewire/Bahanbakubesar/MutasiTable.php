<?php

namespace App\Livewire\Bahanbakubesar;

use App\Models\MutasiBahanBakuBesar;
use App\TipeRiwayatBahanbaku;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class MutasiTable extends PowerGridComponent
{
    public string $tableName = 'mutasi-table-ttaylx-table';

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
        return MutasiBahanBakuBesar::with(['bahanbakubesar'])
        ->latest();
    }

    public function relationSearch(): array
    {
        return [
            'bahanbakubesar' => ['nama', 'satuan']
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('tipe')
            ->add('bahanbakubesar.nama')
            ->add('bahanbakubesar.satuan')
            ->add('jumlah')
            ->add('diajukan_oleh')
            ->add('catatan');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),

            Column::make('Tipe', 'tipe')->sortable()->searchable(),
            
            Column::make('Nama', 'bahanbakubesar.nama')->searchable(),

            Column::make('Jumlah', 'jumlah')->sortable(),
            
            Column::make('Satuan', 'bahanbakubesar.satuan')->searchable(),

            Column::make('Orang Terkait', 'diajukan_oleh')->searchable(),
            
            Column::make('Keterangan', 'catatan')->searchable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::enumSelect('tipe', 'tipe')
                ->dataSource(TipeRiwayatBahanbaku::cases())
                ->optionLabel('labelPowergridFilter'),
        ];
    }

    public function actions(MutasiBahanBakuBesar $row): array
    {
        $mutasiBahanBakuButton = [];
        
        Gate::allows('akses', 'Persediaan Riwayat Bahan Baku Edit') && $mutasiBahanBakuButton[] =
        Button::add('updatemutasibahanbesar')  
            ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
            ->attributes([
                'onclick' => 'modaleditmutasibahanbesar.showModal()',
                'class' => 'btn btn-primary'
            ])
            ->dispatchTo('bahanbakubesar.mutasiupdate', 'getupdatemutasibahanbesar', ['rowId' => $row->id]);

        Gate::allows('akses', 'Persediaan Riwayat Bahan Baku Hapus') && $mutasiBahanBakuButton[] =
        Button::add('deletemutasibahanbesar')
            ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
            ->class('btn btn-error')
            ->dispatch('modaldeletemutasibahanbesar', ['rowId' => $row->id]);

        return $mutasiBahanBakuButton;
    }

    #[\Livewire\Attributes\On('modaldeletemutasibahanbesar')]
    public function modaldeletemutasibahanbesar($rowId): void
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
                    Livewire.dispatch('konfirmasideletemutasibahanbesar', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasideletemutasibahanbesar')]
    public function konfirmasideletemutasibahanbesar($rowId): void
    {
        if (! Gate::allows('akses', 'Persediaan Bahan Baku Hapus')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        MutasiBahanBakuBesar::findOrFail($rowId)->delete();

        $this->dispatch('pg:eventRefresh')->to(self::class); // refresh PowerGrid

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil dihapus.',
        ]);
    }
}
