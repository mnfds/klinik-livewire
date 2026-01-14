<?php

namespace App\Livewire\Uangkeluar;

use App\Models\Uangkeluar;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class PendingTable extends PowerGridComponent
{
    public string $tableName = 'pending-table-ibuvor-table';

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
        return Uangkeluar::query()
            ->where('status', 'Menunggu');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('#')
            ->add('tanggal_pengajuan',fn ($row) => \Carbon\Carbon::parse($row->tanggal_pengajuan)->format('d M Y H:i'))

            ->add('diajukan_oleh')
            ->add('role')
            ->add('pengaju_dan_role', function($row){
                return strtoupper($row->diajukan_oleh) . '<br><span class="text-sm text-gray-500">' . $row->role . '</span>';
            })

            ->add('keterangan')
            ->add('unit_usaha')
            ->add('keterangan_dan_unit', function($row){
                return ucfirst($row->keterangan) . '<br><span class="text-sm text-gray-500">Unit Usaha : ' . $row->unit_usaha . '</span>';
            })

            ->add('jumlah_uang')
            ->add('jenis_pengeluaran')
            ->add('jumlah_dan_jenis', function($row){
                return strtoupper(Number::currency($row->jumlah_uang, in: 'IDR', locale: 'id_ID', precision: 0)) . '<br><span class="text-sm text-gray-500">' . $row->jenis_pengeluaran . '</span>';
            });
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),
            Column::make('Tanggal Ajuan ', 'tanggal_pengajuan'),

            Column::make('Pengaju ', 'diajukan_oleh')->searchable()->hidden(),
            Column::make('Divisi ', 'role')->searchable()->hidden(),
            Column::make('Pengaju ', 'pengaju_dan_role')->bodyAttribute('whitespace-nowrap'),

            Column::make('Ket ', 'keterangan')->searchable()->hidden(),
            Column::make('Unit ', 'unit_usaha')->searchable()->hidden(),
            Column::make('keterangan ', 'keterangan_dan_unit')->bodyAttribute('whitespace-nowrap'),

            Column::make('Jumlah Uang ', 'jumlah_uang')->searchable()->hidden(),
            Column::make('Jenis ', 'jenis_pengeluaran')->searchable()->hidden(),
            Column::make('Nominal/Kategori ', 'jumlah_dan_jenis')->bodyAttribute('whitespace-nowrap'),
                
            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(Uangkeluar $row): array
    {
        $pendingTable = [];
        Gate::allows('akses', 'Pengajuan Pengeluaran Persetujuan') && $pendingTable[] =
        Button::add('disetujui')  
            ->slot('<i class="fa-solid fa-circle-check"></i> Setuju')
            ->attributes([
                'class' => 'btn btn-success btn-sm'
            ])
        ->dispatch('getdisetujui', ['rowId' => $row->id]);

        Gate::allows('akses', 'Pengajuan Pengeluaran Persetujuan') && $pendingTable[] =
        Button::add('ditolak')  
            ->slot('<i class="fa-solid fa-circle-xmark"></i> Tolak')
            ->attributes([
                'class' => 'btn btn-error btn-sm'
            ])
        ->dispatch('getditolak', ['rowId' => $row->id]);

        Gate::allows('akses', 'Pengajuan Pengeluaran Pending Edit') && $pendingTable[] =
        Button::add('updatepending')  
            ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
            ->attributes([
                'onclick' => 'modaleditpending.showModal()',
                'class' => 'btn btn-primary btn-sm'
            ])
        ->dispatchTo('uangkeluar.update', 'getupdatepending', ['rowId' => $row->id]);

        Gate::allows('akses', 'Pengajuan Pengeluaran Pending Hapus') && $pendingTable[] =
        Button::add('deletepending')
            ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
            ->class('btn btn-error btn-sm')
        ->dispatch('modaldeletepending', ['rowId' => $row->id]);

        return $pendingTable;
    }

    #[\Livewire\Attributes\On('getdisetujui')]
    public function getdisetujui($rowId)
    {
        if (! Gate::allows('akses', 'Pengajuan Pengeluaran Persetujuan')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        Uangkeluar::where('id', $rowId)->update([
            'status' => 'Disetujui',
        ]);
        $this->dispatch('pg:eventRefresh');
        $this->dispatch('uangkeluar-disetujui');
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Ajuan Berhasil Disetujui',
        ]);
    }

    #[\Livewire\Attributes\On('getditolak')]
    public function getditolak($rowId)
    {
        if (! Gate::allows('akses', 'Pengajuan Pengeluaran Persetujuan')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        Uangkeluar::where('id', $rowId)->update([
            'status' => 'Ditolak',
        ]);
        $this->dispatch('pg:eventRefresh');
        $this->dispatch('uangkeluar-ditolak');
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Ajuan Telah Ditolak',
        ]);
    }

    #[\Livewire\Attributes\On('modaldeletepending')]
    public function modaldeletepending($rowId): void
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
                    Livewire.dispatch('konfirmasideletepending', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasideletepending')]
    public function konfirmasideletepending($rowId): void
    {
        if (! Gate::allows('akses', 'Pengajuan Pengeluaran Pending Hapus')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        Uangkeluar::findOrFail($rowId)->delete();

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
