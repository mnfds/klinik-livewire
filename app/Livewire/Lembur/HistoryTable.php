<?php

namespace App\Livewire\Lembur;

use App\Models\Lembur;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class HistoryTable extends PowerGridComponent
{
    public string $tableName = 'history-table-obgl2r-table';

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
        return Lembur::with(['user','user.biodata'])
            ->whereIn('status', [
                'selesai', 'ditolak'
            ])
            ->latest();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('#')
            ->add('tanggal_izin',fn ($row) => \Carbon\Carbon::parse($row->tanggal_lembur)->format('d M Y'))

            ->add('Staff', function ($row){
                return strtoupper($row->user->biodata->nama_lengkap ?? $row->user->dokter->nama_dokter);
            })

            ->add('jam_mulai')
            ->add('jam_selesai')
            ->add('waktu_lembur', function ($row) {
                return
                 '<br><span>' . \Carbon\Carbon::parse($row->tanggal_lembur)->format('d M Y') . ', </span>' .
                 '<br><span>' . ($row->jam_mulai . ' - ' . ($row->jam_selesai ?? '??')) . '</span>';
            })

            ->add('keperluan');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),

            Column::make('Nama', 'Staff')->searchable(),
            
            Column::make('Jam Mulai', 'jam_mulai')->searchable()->hidden(),
            Column::make('Jam Selesai', 'jam_selesai')->searchable()->hidden(),
            Column::make('Waktu Lembur', 'waktu_lembur'),
            
            
            Column::make('Keperluan', 'keperluan'),
            
            Column::action('Action')
        ];
    }

    public function actions(Lembur $row): array
    {
        $historyTable = [];

        Gate::allows('akses', 'Pengajuan Riwayat Lembur Edit') && $historyTable[] =
        Button::add('updateHistoryLembur')  
            ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
            ->attributes([
                'onclick' => 'modalHistoryUpdate.showModal()',
                'class' => 'btn btn-primary btn-sm'
            ])
        ->dispatchTo('lembur.update', 'getHistoryLembur', ['rowId' => $row->id]);

        Gate::allows('akses', 'Pengajuan Riwayat Lembur Hapus') && $historyTable[] =
        Button::add('deleteHistoryLembur')
            ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
            ->class('btn btn-error btn-sm')
        ->dispatch('modalDeleteHistory', ['rowId' => $row->id]);

        return $historyTable;
    }

    #[\Livewire\Attributes\On('modalDeleteHistory')]
    public function modalDeleteHistory($rowId): void
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
                    Livewire.dispatch('konfirmasiDeleteHistory', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasiDeleteHistory')]
    public function konfirmasiDeleteHistory($rowId): void
    {
        if (! Gate::allows('akses', 'Pengajuan Riwayat Lembur Hapus')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        Lembur::findOrFail($rowId)->delete();

        $this->dispatch('pg:eventRefresh')->to(self::class); // refresh PowerGrid

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil dihapus.',
        ]);
    }

    #[\Livewire\Attributes\On('refresh-HistoryTable')]
    public function refreshHIstory()
    {
        $this->dispatch('pg:eventRefresh');
    }
}
