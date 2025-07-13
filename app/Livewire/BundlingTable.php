<?php

namespace App\Livewire;

use App\Models\Bundling;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class BundlingTable extends PowerGridComponent
{
    public string $tableName = 'bundling-table-jn1ccf-table';

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
        return Bundling::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('nama')
            ->add('harga_formatted', fn ($row) => 'Rp'.number_format($row->harga, 0, ',', '.'))
            ->add('diskon', fn ($row) => $row->diskon ? $row->diskon . '%' : '0%')
            ->add('harga_bersih_formatted', fn ($row) => 'Rp'.number_format($row->harga_bersih, 0, ',', '.'))
            ->add('created_at')
            ->add('isi_paket', function (Bundling $bundling) {
                $pelayanan = $bundling->pelayananBundlings()
                    ->with('pelayanan')
                    ->get()
                    ->map(fn($item) => '• ' . e($item->pelayanan->nama_pelayanan) . ' x ' . $item->jumlah);

                $produk = $bundling->produkObatBundlings()
                    ->with('produk')
                    ->get()
                    ->map(fn($item) => '• ' . e($item->produk->nama_dagang) . ' x ' . $item->jumlah);

                $html = '';

                if ($pelayanan->isNotEmpty()) {
                    $html .= '<strong>Layanan:</strong><br>';
                    $html .= $pelayanan->implode('<br>') . '<br>';
                }

                if ($produk->isNotEmpty()) {
                    $html .= '<strong>Produk & Obat:</strong><br>';
                    $html .= $produk->implode('<br>');
                }

                return $html ?: '-';
            });
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),

            Column::make('Nama', 'nama')
                ->sortable()
                ->searchable(),

            Column::make('Harga', 'harga_formatted', 'harga')
                ->sortable(),
                
            Column::make('Diskon', 'diskon')
                ->sortable(),
                
            Column::make('Harga Bersih', 'harga_bersih_formatted', 'harga_bersih')
                ->sortable(),

            Column::make('Isi Paket', 'isi_paket')
                ->bodyAttribute('whitespace-nowrap'),

            Column::action('Aksi')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(Bundling $row): array
    {
        return [
            Button::add('editBundling')  
                ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
                ->attributes([
                    'onclick' => 'modalEditBundling.showModal()',
                    'class' => 'btn btn-primary'
                ])
                ->dispatchTo('bundling.update-bundling', 'editBundling', ['rowId' => $row->id]),

            Button::add('deleteButton')
                ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
                ->class('btn btn-error')
                ->dispatch('deleteModalBundling', ['rowId' => $row->id]),
        ];
    }

    #[\Livewire\Attributes\On('deleteModalBundling')]
    public function deleteModalBundling($rowId): void
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
                    Livewire.dispatch('KonfirmasiDeleteBundling', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('KonfirmasiDeleteBundling')]
    public function KonfirmasiDeleteBundling($rowId): void
    {
        Bundling::findOrFail($rowId)->delete();

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
