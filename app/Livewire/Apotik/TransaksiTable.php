<?php

namespace App\Livewire\Apotik;

use Illuminate\Support\Carbon;
use Illuminate\Support\Number;
use App\Models\TransaksiApotik;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class TransaksiTable extends PowerGridComponent
{
    public string $tableName = 'transaksi-table-yzeedt-table';

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
        return TransaksiApotik::query()->with(['riwayat', 'riwayat.produk']);
    }

    public function relationSearch(): array
    {
        return [
            'riwayat' => ['potongan', 'diskon'],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
        ->add('no_transaksi')
        ->add('kasir_nama')
        ->add('total_potongan', fn ($row) => number_format($row->riwayat->sum('potongan'), 0, ',', '.'))
        ->add('total_diskon', fn ($row) => $row->riwayat->sum('diskon') ? $row->riwayat->sum('diskon') . '%' : '0%')
        ->add('total_harga')
        ->add('total_harga_format', fn ($row) => Number::currency($row->total_harga, in: 'IDR', locale: 'id_ID', precision: 0))
        ->add('tanggal', fn ($row) => \Carbon\Carbon::parse($row->tanggal)->format('d M Y H:i'));
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),

            Column::make('No. Transaksi', 'no_transaksi')
                ->sortable()
                ->searchable(),

            Column::make('Kasir', 'kasir_nama')
                ->searchable(),

            Column::make('Potongan Harga', 'total_potongan')
                ->sortable(),

            Column::make('Diskon', 'total_diskon')
                ->sortable(),

            Column::make('Bayar', 'total_harga_format', 'total_harga')
                ->withSum('Total', header: false, footer: true)
                ->sortable(),

            Column::make('Tanggal', 'tanggal')
                ->sortable()
                ->searchable(),

            Column::action('Action')
        ];
    }

    public function summarizeFormat(): array
    {
        return [
            'total_harga.{sum}' => fn ($value) => Number::currency($value, in: 'IDR', locale: 'id_ID', precision : 0),
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(TransaksiApotik $row): array
    {
        return [
            Button::add('detail')
                ->slot('<i class="fas fa-eye"></i> Detail')
                ->tag('button') // supaya tidak jadi <a>
                ->attributes([
                    'title' => 'Lihat detail',
                    'onclick' => "Livewire.navigate('".route('users.edit', $row->id)."')",
                    'class' => 'btn btn-primary'
                ]),

            Button::add('update')
                ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
                ->tag('button') // supaya tidak jadi <a>
                ->attributes([
                    'title' => 'Udit Data',
                    'onclick' => "Livewire.navigate('".route('users.edit', $row->id)."')",
                    'class' => 'btn btn-secondary'
                ]),

            Button::add('delete')
                ->slot('<i class="fa-solid fa-eraser"></i></i> Hapus')
                ->class('btn btn-error')
                ->dispatch('delete', ['rowId' => $row->id]),
        ];
    }

    #[\Livewire\Attributes\On('delete')]
    public function confirmDelete($rowId): void
    {
        $this->js(<<<JS
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Data transaksi ini tidak bisa dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('deleteConfirmed', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('deleteConfirmed')]
    public function deleteConfirmed($rowId): void
    {
        TransaksiApotik::findOrFail($rowId)->delete();

        $this->dispatch('pg:eventRefresh')->to(self::class); // refresh PowerGrid

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data transaksi berhasil dihapus.',
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
