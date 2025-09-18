<?php

namespace App\Livewire\Bahan;

use App\Models\BahanBaku;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class BahanTable extends PowerGridComponent
{
    public string $tableName = 'bahan-table-uhp6j3-table';

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
        return BahanBaku::query()
            ->select('bahan_bakus.*')
            ->selectSub(function ($query) {
                $query->from('mutasi_bahanbakus')
                    ->selectRaw('COALESCE(SUM(jumlah), 0)')
                    ->whereColumn('mutasi_bahanbakus.bahan_baku_id', 'bahan_bakus.id')
                    ->where('tipe', 'masuk');
            }, 'stok_masuk')
            ->selectSub(function ($query) {
                $query->from('mutasi_bahanbakus')
                    ->selectRaw('COALESCE(SUM(jumlah), 0)')
                    ->whereColumn('mutasi_bahanbakus.bahan_baku_id', 'bahan_bakus.id')
                    ->where('tipe', 'keluar');
            }, 'stok_keluar')
            ->selectSub(function ($query) {
                $query->from('mutasi_bahanbakus')
                    ->selectRaw(
                        '(COALESCE(SUM(CASE WHEN tipe = "masuk" THEN jumlah END), 0) 
                        - COALESCE(SUM(CASE WHEN tipe = "keluar" THEN jumlah END), 0))'
                    )
                    ->whereColumn('mutasi_bahanbakus.bahan_baku_id', 'bahan_bakus.id');
            }, 'sisa_stok');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('#')
            ->add('nama')
            ->add('kode')
            ->add('satuan')
            ->add('stok_masuk')
            ->add('stok_keluar')
            ->add('sisa_stok')
            ->add('lokasi')
            ->add('keterangan');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),
            Column::make('Bahan', 'nama')->searchable(),
            Column::make('Kode Bahan', 'kode')->searchable(),
            Column::make('satuan ', 'satuan')->searchable(),
            Column::make('Stok Masuk ', 'stok_masuk')->sortable(),
            Column::make('Stok Keluar ', 'stok_keluar')->sortable(),
            Column::make('Stok Tersisa ', 'sisa_stok')->sortable(),
            Column::make('Lokasi Disimpan ', 'lokasi')->searchable(),
            Column::make('Keterangan ', 'keterangan'),
            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(BahanBaku $row): array
    {
        return [
            Button::add('updatebahanbaku')  
                ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
                ->attributes([
                    'onclick' => 'modaleditbahanbaku.showModal()',
                    'class' => 'btn btn-primary'
                ])
                ->dispatchTo('bahan.update', 'getupdatebahanbaku', ['rowId' => $row->id]),
            
            Button::add('deletebahanbaku')
                ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
                ->class('btn btn-error')
                ->dispatch('modaldeletebahanbaku', ['rowId' => $row->id]),
        ];
    }

    #[\Livewire\Attributes\On('modaldeletebahanbaku')]
    public function modaldeletebahanbaku($rowId): void
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
                    Livewire.dispatch('konfirmasideletebahanbaku', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasideletebahanbaku')]
    public function konfirmasideletebahanbaku($rowId): void
    {
        BahanBaku::findOrFail($rowId)->delete();

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
