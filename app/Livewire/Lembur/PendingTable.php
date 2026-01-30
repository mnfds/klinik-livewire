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

final class PendingTable extends PowerGridComponent
{
    public string $tableName = 'pending-table-btzwtv-table';

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
            ->where('status', 'pending')
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

            ->add('user_id')
            ->add('jam_mulai')
            ->add('nama_dan_jam', function ($row) {
                return strtoupper($row->user->biodata->nama_lengkap ?? $row->user->dokter->nama_dokter) .
                 '<br><span class="text-sm text-gray-500">' . \Carbon\Carbon::parse($row->tanggal_lembur)->format('d M Y') . ', </span>' .
                 '<br><span class="text-sm text-gray-500">' . $row->jam_mulai . '</span>';
            })

            ->add('keperluan');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),

            Column::make('Nama', 'user_id')->searchable()->hidden(),
            Column::make('Jam Keluar', 'jam_mulai')->searchable()->hidden(),
            Column::make('Karyawan Terkait', 'nama_dan_jam'),
            
            Column::make('Keperluan', 'keperluan'),
            
            Column::action('Action')
        ];
    }

    public function actions(Lembur $row): array
    {
        $pendingTable = [];

        Gate::allows('akses', 'Persetujuan Ajuan Lembur') && $pendingTable[] =
        Button::add('setujui')  
        ->slot('<i class="fa-solid fa-circle-check"></i> Setujui')
        ->attributes([
            'class' => 'btn btn-success btn-sm'
            ])
        ->dispatch('setujui', ['rowId' => $row->id]);
        
        Gate::allows('akses', 'Persetujuan Ajuan Lembur') && $pendingTable[] =
        Button::add('tolak')  
            ->slot('<i class="fa-solid fa-circle-xmark"></i> Tolak')
            ->attributes([
                'class' => 'btn btn-error btn-sm'
            ])
        ->dispatch('tolak', ['rowId' => $row->id]);

        Gate::allows('akses', 'Pengajuan Lembur Edit') && $pendingTable[] =
        Button::add('updatePendingLembur')  
            ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
            ->attributes([
                'onclick' => 'modalPendingUpdate.showModal()',
                'class' => 'btn btn-primary btn-sm'
            ])
        ->dispatchTo('lembur.update', 'getPendingLembur', ['rowId' => $row->id]);

        Gate::allows('akses', 'Pengajuan Lembur Hapus') && $pendingTable[] =
        Button::add('deletePendingLembur')
            ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
            ->class('btn btn-error btn-sm')
        ->dispatch('modalDeletePending', ['rowId' => $row->id]);

        return $pendingTable;
    }

    #[\Livewire\Attributes\On('setujui')]
    public function setujui($rowId)
    {
        if (! Gate::allows('akses', 'Persetujuan Ajuan Lembur')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        Lembur::where('id', $rowId)->update([
            'status' => 'disetujui',
        ]);
        $this->dispatch('pg:eventRefresh');
        $this->dispatch('refresh-ApproveTable');
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Pengajuan Telah Berhasil Disetujui',
        ]);
    }

    #[\Livewire\Attributes\On('tolak')]
    public function tolak($rowId)
    {
        if (! Gate::allows('akses', 'Persetujuan Ajuan Lembur')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        Lembur::where('id', $rowId)->update([
            'status' => 'ditolak',
        ]);
        $this->dispatch('pg:eventRefresh');
        $this->dispatch('refresh-HistoryTable');
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Pengajuan Telah Berhasil Ditolak',
        ]);
    }

    #[\Livewire\Attributes\On('modalDeletePending')]
    public function modalDeletePending($rowId): void
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
                    Livewire.dispatch('konfirmasiDeletePending', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasiDeletePending')]
    public function konfirmasiDeletePending($rowId): void
    {
        if (! Gate::allows('akses', 'Pengajuan Lembur Hapus')) {
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

}
