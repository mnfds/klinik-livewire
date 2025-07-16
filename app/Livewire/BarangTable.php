<?php

namespace App\Livewire;

use App\Models\Barang;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
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
        return Barang::query();
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
            ->add('stok_masuk', function (Barang $barang) {
                $stok_masuk = $barang->mutasi()
                    ->where('tipe', 'masuk')
                    ->sum('jumlah');

                return $stok_masuk;
            })
            ->add('stok_keluar', function (Barang $barang) {
                return $barang->mutasi()
                    ->where('tipe', 'keluar')
                    ->sum('jumlah');

            })
            ->add('sisa_stok', function (Barang $barang) {
                $masuk = $barang->mutasi()->where('tipe', 'masuk')->sum('jumlah');
                $keluar = $barang->mutasi()->where('tipe', 'keluar')->sum('jumlah');
                return $masuk - $keluar;
            })
            ->add('lokasi')
            ->add('keterangan');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),
            Column::make('Barang', 'nama')->searchable(),
            Column::make('Kode Barang', 'kode')->searchable(),
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

    public function actions(Barang $row): array
    {
        return [
            Button::add('updatebarang')  
                ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
                ->attributes([
                    'onclick' => 'modaleditbarang.showModal()',
                    'class' => 'btn btn-primary'
                ])
                ->dispatchTo('barang.update', 'getupdatebarang', ['rowId' => $row->id]),
            
            Button::add('deletebarang')
                ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
                ->class('btn btn-error')
                ->dispatch('modaldeletebarang', ['rowId' => $row->id]),
        ];
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
