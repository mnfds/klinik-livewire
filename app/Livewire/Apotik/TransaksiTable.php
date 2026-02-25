<?php

namespace App\Livewire\Apotik;

use Illuminate\Support\Carbon;
use Illuminate\Support\Number;
use App\Models\TransaksiApotik;
use Illuminate\Support\Facades\Gate;
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

    public function boot(): void
    {
        config(['livewire-powergrid.filter' => 'outside']);
    }

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
        return TransaksiApotik::query()
        ->when(
            $this->hasTanggalFilter(),
            function ($q){
                $range = $this->getTanggalFilter();
                $q->whereBetween(
                    'tanggal',
                    [$range['start'], $range['end']]
                );
            },
            fn($q) => $q->whereDate('tanggal', today()) 
        )
        ->orderByDesc('tanggal')
        ->orderByDesc('id') 
        ->with([
            'riwayat', 'riwayat.produk',
            'riwayatBarang', 'riwayatBarang.barang'
            ]);
    }

    public function relationSearch(): array
    {
        return [
            'riwayat' => ['potongan', 'diskon'],
            'riwayatBarang' => ['potongan', 'diskon'],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
        ->add('no_transaksi')
        ->add('kasir_nama')
        // ->add('total_potongan', fn ($row) => number_format($row->riwayat->sum('potongan'), 0, ',', '.'))
        // ->add('total_diskon', fn ($row) => $row->riwayat->sum('diskon') ? $row->riwayat->sum('diskon') . '%' : '0%')
        ->add('total_harga')
        ->add('total_harga_format', fn ($row) =>'Rp ' . number_format($row->total_harga, 0, ',', '.'))
        // ->add('total_harga_format', fn ($row) => Number::currency($row->total_harga, in: 'IDR', locale: 'id_ID', precision: 0))
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

            // Column::make('Potongan Harga', 'total_potongan')
            //     ->sortable(),

            // Column::make('Diskon', 'total_diskon')
            //     ->sortable(),

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
            Filter::datepicker('tanggal', 'tanggal'),
        ];
    }

    public function actions(TransaksiApotik $row): array
    {
        $transaksiApotikButton = [];

        Gate::allows('akses', 'Transaksi Apotik Detail') && $transaksiApotikButton[] =
        Button::add('detail')
            ->slot('<i class="fas fa-eye"></i> Detail')
            ->tag('button') // supaya tidak jadi <a>
            ->attributes([
                'title' => 'Lihat detail',
                'onclick' => "Livewire.navigate('".route('apotik.detail', $row->id)."')",
                'class' => 'btn btn-primary'
            ]);

        Gate::allows('akses', 'Transaksi Apotik Edit') && $transaksiApotikButton[] =
        Button::add('update')
            ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
            ->tag('button') // supaya tidak jadi <a>
            ->attributes([
                'title' => 'Edit Data',
                'onclick' => "Livewire.navigate('".route('apotik.update', $row->id)."')",
                'class' => 'btn btn-secondary'
            ]);

        Gate::allows('akses', 'Transaksi Apotik Hapus') && $transaksiApotikButton[] =
        Button::add('delete')
            ->slot('<i class="fa-solid fa-eraser"></i></i> Hapus')
            ->class('btn btn-error')
            ->dispatch('delete', ['rowId' => $row->id]);
    
        return $transaksiApotikButton;
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
        if (! Gate::allows('akses', 'Transaksi Apotik Hapus')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        TransaksiApotik::findOrFail($rowId)->delete();

        $this->dispatch('pg:eventRefresh')->to(self::class); // refresh PowerGrid

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data transaksi berhasil dihapus.',
        ]);
    }

    protected function hasTanggalFilter(): bool
    {
        return ! empty(
            data_get($this->filters, 'date.tanggal.start')
        );
    }

    protected function getTanggalFilter(): array
    {
        return [
            'start' => \Carbon\Carbon::parse(
                data_get($this->filters, 'date.tanggal.start')
            )->toDateString(),

            'end' => \Carbon\Carbon::parse(
                data_get($this->filters, 'date.tanggal.end')
            )->toDateString(),
        ];
    }
}
