<?php

namespace App\Livewire;

use App\Models\ProdukDanObat;
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

final class ProdukObatTable extends PowerGridComponent
{
    public string $tableName = 'produk-obat-table-cjy7oz-table';

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
        return ProdukDanObat::query()->latest();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('nama_dagang')
            ->add('golongan')
            ->add('kode')
            ->add('sediaan')
            ->add('harga_dasar', fn ($produkdanobat) => number_format($produkdanobat->harga_dasar, 0, ',', '.'))
            ->add('potongan', fn ($produkdanobat) => number_format($produkdanobat->potongan, 0, ',', '.'))
            ->add('diskon', fn ($row) => $row->diskon ? $row->diskon . '%' : '0%')
            ->add('harga_bersih', fn ($produkdanobat) => number_format($produkdanobat->harga_bersih, 0, ',', '.'))
            ->add('stok', fn ($produkdanobat) => number_format($produkdanobat->stok, 0, ',', '.'))
            ->add('expired_at')
            ->add('reminder')
            ->add('batch')
            ->add('lokasi')
            ->add('supplier');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),

            // Identitas produk
            Column::make('Nama Produk/Obat', 'nama_dagang')
                ->searchable(),
            Column::make('Golongan Obat', 'golongan')
                ->searchable(),
            Column::make('Kode', 'kode')
                ->searchable(),
            Column::make('Satuan', 'sediaan')
                ->searchable(),

            // Harga
            Column::make('Harga Jual', 'harga_dasar')
                ->sortable(),
            Column::make('Potongan', 'potongan')
                ->sortable(),
            Column::make('Diskon', 'diskon')
                ->sortable(),
            Column::make('Harga Bersih', 'harga_bersih')
                ->sortable(),

            // Ketersediaan
            Column::make('Stok Tersisa', 'stok')
                ->sortable(),
            Column::make('Kadaluarsa', 'expired_at')
                ->sortable()
                ->searchable(),

            // Info tambahan
            Column::make('Batch', 'batch')
                ->searchable(),
            Column::make('Lokasi', 'lokasi')
                ->searchable(),
            Column::make('Supplier', 'supplier')
                ->searchable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(ProdukDanObat $row): array
    {
        $produkButton = [];
         Gate::allows('akses', 'Persediaan Produk & Obat Edit') && $produkButton[] =
         Button::add('updateprodukobat')  
             ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
             ->attributes([
                 'onclick' => 'modaleditprodukdanobat.showModal()',
                 'class' => 'btn btn-primary'
             ])
             ->dispatchTo('produkdanobat.update', 'getupdateprodukobat', ['rowId' => $row->id]);
             
          Gate::allows('akses', 'Persediaan Produk & Obat Hapus') && $produkButton[] =
          Button::add('deleteprodukdanobat')
              ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
              ->class('btn btn-error')
              ->dispatch('modaldeleteprodukdanobat', ['rowId' => $row->id]);

        return $produkButton;
    }

    #[\Livewire\Attributes\On('modaldeleteprodukdanobat')]
    public function modaldeleteprodukdanobat($rowId): void
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
                    Livewire.dispatch('konfirmasideleteprodukdanobat', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasideleteprodukdanobat')]
    public function konfirmasideleteprodukdanobat($rowId): void
    {
        if (! Gate::allows('akses', 'Persediaan Produk & Obat Hapus')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        ProdukDanObat::findOrFail($rowId)->delete();

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
                        <= Carbon::now()->addMonths($row->reminder)->format('Y-m')
                )
                ->setAttribute('class', 'text-red-600 font-semibold'),
        ];
    }
}
