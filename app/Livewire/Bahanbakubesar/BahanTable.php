<?php

namespace App\Livewire\Bahanbakubesar;

use App\Models\BahanBakuBesar;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class BahanTable extends PowerGridComponent
{
    public string $tableName = 'bahan-table-dyevco-table';

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
        return BahanBakuBesar::query()
            ->select('bahan_baku_besars.*')
            ->selectSub(function ($query) {
                $query->from('mutasi_bahan_baku_besars')
                    ->selectRaw('COALESCE(SUM(jumlah), 0)')
                    ->whereColumn('mutasi_bahan_baku_besars.bahan_baku_besar_id', 'bahan_baku_besars.id')
                    ->where('tipe', 'masuk');
            }, 'stok_masuk')
            ->selectSub(function ($query) {
                $query->from('mutasi_bahan_baku_besars')
                    ->selectRaw('COALESCE(SUM(jumlah), 0)')
                    ->whereColumn('mutasi_bahan_baku_besars.bahan_baku_besar_id', 'bahan_baku_besars.id')
                    ->where('tipe', 'keluar');
            }, 'stok_keluar')
            ->selectSub(function ($query) {
                $query->from('mutasi_bahan_baku_besars')
                    ->selectRaw(
                        '(COALESCE(SUM(CASE WHEN tipe = "masuk" THEN jumlah END), 0) 
                        - COALESCE(SUM(CASE WHEN tipe = "keluar" THEN jumlah END), 0))'
                    )
                    ->whereColumn('mutasi_bahan_baku_besars.bahan_baku_besar_id', 'bahan_baku_besars.id');
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
            ->add('expired_at')
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
            Column::make('Kadaluarsa', 'expired_at')->sortable()->searchable(),
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

    public function actions(BahanBakuBesar $row): array
    {
        $bahanBakuBesarButton = [];
        
        Gate::allows('akses', 'Persediaan Bahan Baku Edit') && $bahanBakuBesarButton[] =
        Button::add('updatebahanbakubesar')  
            ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
            ->attributes([
                'onclick' => 'modaleditbahanbakubesar.showModal()',
                'class' => 'btn btn-primary'
            ])
            ->dispatchTo('bahanbakubesar.update', 'getupdatebahanbakubesar', ['rowId' => $row->id]);
        
        Gate::allows('akses', 'Persediaan Bahan Baku') && $bahanBakuBesarButton[] =
        Button::add('deletebahanbakubesar')
            ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
            ->class('btn btn-error')
            ->dispatch('modaldeletebahanbakubesar', ['rowId' => $row->id]);

        return $bahanBakuBesarButton;
    }

    #[\Livewire\Attributes\On('modaldeletebahanbakubesar')]
    public function modaldeletebahanbakubesar($rowId): void
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
                    Livewire.dispatch('konfirmasideletebahanbakubesar', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasideletebahanbakubesar')]
    public function konfirmasideletebahanbakubesar($rowId): void
    {
        if (! Gate::allows('akses', 'Persediaan Bahan Baku Hapus')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        BahanBakuBesar::findOrFail($rowId)->delete();

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
                    $row->expired_at &&
                    $row->reminder &&
                    Carbon::parse($row->expired_at)->format('Y-m')
                        <= Carbon::now()->addMonths((int) $row->reminder)->format('Y-m')
                )
                ->setAttribute('class', 'text-red-600 font-semibold'),
        ];
    }
}
