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
    public string $tableName = 'barang-table';

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
        return Barang::with('mutasi');
    }

    public function relationSearch(): array
    {
        return [
            'mutasi' => [
                'tipe',
                'jumlah',
                'lokasi',
                'diajukan_oleh',
                'disetujui_oleh',
                'status',
                'catatan',
            ]
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('nama')
            ->add('kode')
            ->add('stok')
            ->add('mutasi.jumlah')
            // ->add('sisa_stok', fn(Barang $barang) => $barang->stok - $barang->mutasi()->sum('jumlah'))
            ->add('satuan')
            ->add('lokasi')
            ->add('keterangan')
            ->add('tanggal', fn($row) => Carbon::parse($row->created_at)->format('d/m/Y'));
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),
            Column::make('Nama Barang', 'nama')->searchable()->sortable(),
            Column::make('Kode', 'kode')->searchable()->sortable(),
            Column::make('Stok Tersisa', 'stok')->sortable(),
            Column::make('Jumlah Keluar', 'mutasi.jumlah')->sortable(),
            Column::make('Satuan', 'satuan')->searchable(),
            Column::make('Lokasi Disimpan', 'lokasi'),
            Column::make('Ket', 'keterangan'),
            Column::make('Dibuat', 'tanggal')->sortable(),
            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
            // contoh: Filter lokasi
            // Filter::inputText('lokasi')->operators(['contains']),
        ];
    }

    public function actions(Barang $row): array
    {
        return [
            Button::add('editBarang')
                ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
                ->attributes([
                    'onclick' => 'editBarang.showModal()',
                    'class' => 'btn btn-primary'
                ])
                ->dispatchTo('barang.update-barang', 'editBarang', ['rowId' => $row->id]),

            Button::add('delete')
                ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
                ->class('btn btn-error')
                ->dispatch('deleteBarangModal', ['rowId' => $row->id]),
        ];
    }

    #[\Livewire\Attributes\On('deleteBarangModal')]
    public function deleteBarangModal($rowId): void
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
                    Livewire.dispatch('deleteBarangKonfirmasi', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('deleteBarangKonfirmasi')]
    public function deleteBarangKonfirmasi($rowId): void
    {
        Barang::findOrFail($rowId)->delete();

        $this->dispatch('pg:eventRefresh')->to(self::class); // refresh PowerGrid

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil dihapus.',
        ]);
    }
}
