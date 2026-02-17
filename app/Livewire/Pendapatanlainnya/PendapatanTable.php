<?php

namespace App\Livewire\Pendapatanlainnya;

use Illuminate\Support\Carbon;
use Illuminate\Support\Number;
use App\Models\Pendapatanlainnya;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class PendapatanTable extends PowerGridComponent
{
    public string $tableName = 'pendapatan-table-fh6qyp-table';

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
        return Pendapatanlainnya::query()->latest();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
        ->add('no_transaksi')
        ->add('tanggal_transaksi', fn ($row) => \Carbon\Carbon::parse($row->tanggal_transaksi)->format('d M Y H:i'))
        ->add('no_dan_tanggal', function($row){
            return ucfirst($row->no_transaksi) . '<br><span class="text-sm text-gray-500">' . \Carbon\Carbon::parse($row->tanggal_transaksi)->format('d M Y H:i') . '</span>';
        })

        ->add('keterangan')
        ->add('unit_usaha')
        ->add('keterangan_dan_unit', function($row){
            return ucfirst($row->keterangan) . '<br><span class="text-sm text-gray-500">Unit Usaha : ' . $row->unit_usaha . '</span>';
        })

        ->add('total_tagihan')
        ->add('status')
        ->add('total_dan_status', function ($row) {
            $total = 'Rp ' . number_format($row->total_tagihan, 0, ',', '.');

            $statusColor = match ($row->status_pembayaran) {
                'Lunas' => 'text-success',
                'Belum Lunas' => 'text-error',
                default => 'text-gray-500',
            };

            return $total . 
                '<br><span class="text-sm ' . $statusColor . '">' . 
                e($row->status_pembayaran) . 
                '</span>';
        });
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),

            Column::make('No. Transaksi', 'no_transaksi')->searchable()->hidden(),
            Column::make('Tanggal', 'tanggal_transaksi')->searchable()->hidden(),
            Column::make('Tanggal ', 'no_dan_tanggal')->bodyAttribute('whitespace-nowrap'),
            

            Column::make('Ket ', 'keterangan')->searchable()->hidden(),
            Column::make('Unit ', 'unit_usaha')->searchable()->hidden(),
            Column::make('keterangan ', 'keterangan_dan_unit')->bodyAttribute('whitespace-nowrap'),
            
            Column::make('Tagihan ', 'total_tagihan')->searchable()->hidden(),
            Column::make('status ', 'status')->searchable()->hidden(),
            Column::make('Total & Status', 'total_dan_status')->sortable()->searchable(),
            
            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(Pendapatanlainnya $row): array
    {
        $pendapatanLainnya = [];
        Gate::allows('akses', 'Pendapatan Edit') && $pendapatanLainnya[] =
        Button::add('updatependapatan')  
            ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
            ->attributes([
                'onclick' => 'modaleditpendapatan.showModal()',
                'class' => 'btn btn-primary btn-sm'
            ])
        ->dispatchTo('pendapatanlainnya.update', 'getupdatependapatan', ['rowId' => $row->id]);

        Gate::allows('akses', 'Pendapatan Hapus') && $pendapatanLainnya[] =
        Button::add('deletependapatan')
            ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
            ->class('btn btn-error btn-sm')
        ->dispatch('modaldeletependapatan', ['rowId' => $row->id]);

        return $pendapatanLainnya;
    }

    #[\Livewire\Attributes\On('modaldeletependapatan')]
    public function modaldeletependapatan($rowId): void
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
                    Livewire.dispatch('konfirmasideletependapatan', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasideletependapatan')]
    public function konfirmasideletependapatan($rowId): void
    {
        if (! Gate::allows('akses', 'Pendapatan Hapus')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        Pendapatanlainnya::findOrFail($rowId)->delete();

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
