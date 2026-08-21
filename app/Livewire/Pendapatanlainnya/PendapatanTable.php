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

    public string $filterStatus = '';
    public string $filterUnitUsaha = '';
    public string $filterMetodePembayaran = '';
    public string $filterTanggalStart = '';
    public string $filterTanggalEnd = '';

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
        return Pendapatanlainnya::query()
            ->latest()
            ->when($this->filterStatus, fn (Builder $q) => $q->where('status', $this->filterStatus))
            ->when($this->filterUnitUsaha, fn (Builder $q) => $q->where('unit_usaha', $this->filterUnitUsaha))
            ->when($this->filterMetodePembayaran, fn (Builder $q) => $q->where('metode_pembayaran', $this->filterMetodePembayaran))
            ->when(
                $this->filterTanggalStart && $this->filterTanggalEnd,
                function (Builder $q) {
                    $q->whereBetween('tanggal_transaksi', [
                        \Carbon\Carbon::parse($this->filterTanggalStart)->startOfDay(),
                        \Carbon\Carbon::parse($this->filterTanggalEnd)->endOfDay(),
                    ]);
                }
            );
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
        ->add('total_dibayarkan')
        ->add('sisa_tagihan', fn ($row) => $row->sisa_tagihan)
        ->add('status')
        ->add('metode_pembayaran')
        ->add('total_dan_status_dan_metode_pembayaran', function ($row) {
            $statusClass = match ($row->status) {
                'lunas' => 'text-success',
                'belum lunas' => 'text-warning',
                'belum bayar', 'batal' => 'text-error',
                default => 'text-gray-500',
            };

            $html = 'Tagihan: Rp ' . number_format($row->total_tagihan, 0, ',', '.')
                . '<br><span class="text-sm text-gray-500">Dibayar (baris ini): Rp ' . number_format($row->total_dibayarkan, 0, ',', '.') . '</span>';

            // sisa tagihan hanya relevan ditampilkan kalau grup masih belum lunas
            if (! $row->is_lunas_group) {
                $html .= '<br><span class="text-sm text-error">Sisa: Rp ' . number_format($row->sisa_tagihan, 0, ',', '.') . '</span>';
            }

            $html .= '<br><span class="text-sm ' . $statusClass . '">'
                . ucfirst($row->status)
                . '</span>'
                . ' (' . ucfirst($row->metode_pembayaran) . ')';

            return $html;
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
            Column::make('Dibayarkan ', 'total_dibayarkan')->searchable()->hidden(),
            Column::make('Sisa ', 'sisa_tagihan')->hidden(),
            Column::make('status ', 'status')->searchable()->hidden(),
            Column::make('metode_pembayaran ', 'metode_pembayaran')->searchable()->hidden(),
            Column::make('Total & Status', 'total_dan_status_dan_metode_pembayaran')->sortable()->searchable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [];
    }

    public function actions(Pendapatanlainnya $row): array
    {
        $pendapatanLainnya = [];

        // Tombol Pelunasan: hanya muncul kalau grup tagihan ini BELUM lunas
        Gate::allows('akses', 'Pendapatan Edit') && ! $row->is_lunas_group && $pendapatanLainnya[] =
        Button::add('pelunasanpendapatan')
            ->slot('<i class="fa-solid fa-money-bill-wave"></i> Pelunasan')
            ->attributes([
                'onclick' => 'modalpelunasanpendapatan.showModal()',
                'class' => 'btn btn-success btn-sm'
            ])
        ->dispatchTo('pendapatanlainnya.pelunasan', 'getpelunasan', ['rowId' => $row->id]);

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

        $this->dispatch('pg:eventRefresh')->to(self::class);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil dihapus.',
        ]);
    }

    // dipanggil dari component Pelunasan setelah berhasil store, supaya table refresh
    #[\Livewire\Attributes\On('pg:eventRefresh')]
    public function refreshTable(): void
    {
        // no-op, biar Livewire listener terdaftar; PowerGrid sudah handle refresh via event ini
    }

    #[\Livewire\Attributes\On('pendapatan-filter-updated')]
    public function setFilters($status = '', $unitUsaha = '', $metodePembayaran = '', $tanggalStart = '', $tanggalEnd = ''): void {
        $this->filterStatus = $status;
        $this->filterUnitUsaha = $unitUsaha;
        $this->filterMetodePembayaran = $metodePembayaran;
        $this->filterTanggalStart = $tanggalStart;
        $this->filterTanggalEnd = $tanggalEnd;

        $this->dispatch('pg:eventRefresh')->to(self::class);
    }
}
