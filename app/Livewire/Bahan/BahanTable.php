<?php

namespace App\Livewire\Bahan;

use App\Models\BahanBaku;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
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
        return BahanBaku::query()->latest();
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
            ->add('expired_at')
            ->add('nama_dan_expired', function($row){
                return strtoupper($row->nama) . '<br><span class="text-sm text-gray-500">Exp : ' . \Carbon\Carbon::parse($row->expired_at)->format('d M Y') . '</span>';
            })

            // ->add('kode')
            ->add('stok_besar')
            ->add('satuan_besar')
            ->add('stok_besar_satuan', function($row){
                return strtoupper($row->stok_besar) . ' ' . $row->satuan_besar;
            })

            ->add('stok_kecil')
            ->add('satuan_kecil')
            ->add('stok_kecil_satuan', function($row){
                return strtoupper($row->stok_kecil) . ' ' . $row->satuan_kecil;
            })

            ->add('lokasi')
            ->add('keterangan');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),
            Column::make('Bahan', 'nama')->searchable()->hidden(),
            Column::make('Kadaluarsa', 'expired_at')->sortable()->searchable()->hidden(),
            Column::make('Nama', 'nama_dan_expired')->bodyAttribute('whitespace-nowrap'),
            // Column::make('Kode Bahan', 'kode')->searchable(),

            Column::make('Stok_Besar', 'stok_besar')->searchable()->hidden(),
            Column::make('Satuan_Besar', 'satuan_besar')->searchable()->hidden(),
            Column::make('Stok Besar', 'stok_besar_satuan')->bodyAttribute('whitespace-nowrap'),

            Column::make('Stok_Kecil', 'stok_kecil')->searchable()->hidden(),
            Column::make('Satuan_Kecil', 'satuan_kecil')->searchable()->hidden(),
            Column::make('Stok Kecil', 'stok_kecil_satuan')->bodyAttribute('whitespace-nowrap'),

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
        $bahanBakuButton = [];
        
        Gate::allows('akses', 'Persediaan Bahan Baku Edit') && $bahanBakuButton[] =
        Button::add('updatebahanbaku')  
            ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
            ->attributes([
                'onclick' => 'modaleditbahanbaku.showModal()',
                'class' => 'btn btn-primary'
            ])
            ->dispatchTo('bahan.update', 'getupdatebahanbaku', ['rowId' => $row->id]);
        
        Gate::allows('akses', 'Persediaan Bahan Baku') && $bahanBakuButton[] =
        Button::add('deletebahanbaku')
            ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
            ->class('btn btn-error')
            ->dispatch('modaldeletebahanbaku', ['rowId' => $row->id]);

        return $bahanBakuButton;
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
        if (! Gate::allows('akses', 'Persediaan Bahan Baku Hapus')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        BahanBaku::findOrFail($rowId)->delete();

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
                ->setAttribute('class', 'text-red-600'),
        ];
    }
}
