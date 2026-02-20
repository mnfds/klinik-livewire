<?php

namespace App\Livewire\Produkdanobat\Mutasi;

use App\Models\MutasiProdukDanObat;
use App\Models\ProdukDanObat;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class RiwayatTable extends PowerGridComponent
{
    public string $tableName = 'riwayat-table-djex9q-table';

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
        return MutasiProdukDanObat::with(['produkdanobat'])
        ->latest();
    }

    public function relationSearch(): array
    {
        return [
            'produkdanobat' => ['nama_dagang', 'sediaan']
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('tipe')
            ->add('produkdanobat.nama_dagang')

            ->add('jumlah')
            ->add('produkdanobat.sediaan')
            ->add('jumlah_dan_sediaan', function($row){
                return strtoupper($row->jumlah) . ' ' . $row->produkdanobat->sediaan;
            })

            ->add('diajukan_oleh')
            ->add('catatan');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),

            Column::make('Tipe', 'tipe')->sortable()->searchable(),
            
            Column::make('Nama', 'produkdanobat.nama_dagang')->searchable(),

            Column::make('Jumlah Stok', 'jumlah')->sortable()->hidden(),
            Column::make('Satuan', 'produkdanobat.sediaan')->searchable()->hidden(),
            Column::make('Jumlah', 'jumlah_dan_sediaan')->bodyAttribute('whitespace-nowrap'),
            

            Column::make('Orang Terkait', 'diajukan_oleh')->searchable(),
            
            Column::make('Keterangan', 'catatan')->searchable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(MutasiProdukDanObat $row): array
    {
        $riwayatProdukObatButton = [];
        
        Gate::allows('akses', 'Persediaan Riwayat Produk & Obat Edit') && $riwayatProdukObatButton[] =
        Button::add('updateriwayatprodukdanobat')  
            ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
            ->attributes([
                'onclick' => 'modaleditriwayatprodukdanobat.showModal()',
                'class' => 'btn btn-primary'
            ])
            ->dispatchTo('produkdanobat.mutasi.updateriwayat', 'getupdateriwayatprodukdanobat', ['rowId' => $row->id]);

        Gate::allows('akses', 'Persediaan Riwayat Produk & Obat Hapus') && $riwayatProdukObatButton[] =
        Button::add('deleteriwayatprodukdanobat')
            ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
            ->class('btn btn-error')
            ->dispatch('modaldeleteriwayatprodukdanobat', ['rowId' => $row->id]);

        return $riwayatProdukObatButton;
    }

    #[\Livewire\Attributes\On('modaldeleteriwayatprodukdanobat')]
    public function modaldeleteriwayatprodukdanobat($rowId): void
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
                    Livewire.dispatch('konfirmasideleteriwayatprodukdanobat', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasideleteriwayatprodukdanobat')]
    public function konfirmasideleteriwayatprodukdanobat($rowId): void
    {
        if (! Gate::allows('akses', 'Persediaan Riwayat Produk & Obat Hapus')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        $riwayat = MutasiProdukDanObat::findOrFail($rowId);
        $produkLama = ProdukDanObat::findOrFail($riwayat->produk_id);
        if ($riwayat->tipe === 'keluar') {
            $produkLama->stok += $riwayat->jumlah;
        } else { // masuk
            $produkLama->stok -= $riwayat->jumlah;
        }
        $produkLama->save();

        MutasiProdukDanObat::findOrFail($rowId)->delete();

        $this->dispatch('pg:eventRefresh')->to(self::class); // refresh PowerGrid

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil dihapus.',
        ]);
    }
}
