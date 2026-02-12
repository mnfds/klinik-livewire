<?php

namespace App\Livewire;

use App\Models\Barang;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class BarangTable extends PowerGridComponent
{
    public string $tableName = 'barang-table-hbu5km-table';

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
        return Barang::query()->latest();
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
            ->add('nama_kode', function($row){
                return strtoupper($row->nama) . '<br><span class="text-sm text-gray-500">Kode Item : ' . $row->kode . '</span>';
            })

            ->add('stok', fn ($row) => number_format($row->stok, 0, ',', '.'))
            ->add('satuan')
            ->add('stok_satuan', function($row){
                return strtoupper($row->stok) . ' ' . $row->satuan . '</span>';
            })

            ->add('harga_dasar', fn ($row) => number_format($row->harga_dasar, 0, ',', '.'))
            ->add('potongan', fn ($row) => number_format($row->potongan, 0, ',', '.'))
            ->add('diskon', fn ($row) => $row->diskon ? $row->diskon . '%' : '0%')
            ->add('harga_bersih', fn ($row) => number_format($row->harga_bersih, 0, ',', '.'))
            
            ->add('lokasi')
            ->add('keterangan');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),
            Column::make('Barang', 'nama')->searchable()->hidden(),
            Column::make('Kode Barang', 'kode')->searchable()->hidden(),
            Column::make('Nama Barang', 'nama_kode')->bodyAttribute('whitespace-nowrap'),
            
            Column::make('Stok ', 'stok')->searchable()->hidden(),
            Column::make('satuan ', 'satuan')->searchable()->hidden(),
            Column::make('Sisa Stok', 'stok_satuan')->bodyAttribute('whitespace-nowrap'),

            Column::make('Harga Jual', 'harga_dasar')->sortable(),
            Column::make('Potongan', 'potongan')->sortable(),
            Column::make('Diskon', 'diskon')->sortable(),
            Column::make('Harga Bersih', 'harga_bersih')->sortable(),
                
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

    public function actions(Barang $row): array
    {
        $barangButton = [];

        Gate::allows('akses', 'Persediaan Barang Edit') && $barangButton[] =
        Button::add('updatebarang')  
            ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
            ->attributes([
                'onclick' => 'modaleditbarang.showModal()',
                'class' => 'btn btn-primary'
            ])
            ->dispatchTo('barang.update', 'getupdatebarang', ['rowId' => $row->id]);

        Gate::allows('akses', 'Persediaan Barang Hapus') && $barangButton[] =
        Button::add('deletebarang')
            ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
            ->class('btn btn-error')
            ->dispatch('modaldeletebarang', ['rowId' => $row->id]);

        return $barangButton;
    }

    #[\Livewire\Attributes\On('modaldeletebarang')]
    public function modaldeletebarang($rowId): void
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
                    Livewire.dispatch('konfirmasideletebarang', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasideletebarang')]
    public function konfirmasideletebarang($rowId): void
    {
        if (! Gate::allows('akses', 'Persediaan Barang Hapus')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        Barang::findOrFail($rowId)->delete();

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
