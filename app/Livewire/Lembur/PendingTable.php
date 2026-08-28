<?php

namespace App\Livewire\Lembur;

use App\Models\Lembur;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
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
        return Lembur::with(['user', 'user.biodata'])
            ->where('user_id', Auth::id())
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
            ->add('perkiraan_durasi', function ($row) {
                return $row->perkiraan_durasi . ' Jam';
            })
            ->add('nama_dan_jam', function ($row) {
                return strtoupper($row->user->biodata->nama_lengkap ?? $row->user->dokter->nama_dokter) .
                 '<br><span class="text-sm text-gray-500">' . \Carbon\Carbon::parse($row->tanggal_lembur)->format('d M Y') . ', </span>';
            })

            ->add('keperluan')
            ->add('status');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),

            Column::make('Nama', 'user_id')->searchable()->hidden(),
            Column::make('Karyawan Terkait', 'nama_dan_jam'),
            
            Column::make('Waktu Lembur', 'perkiraan_durasi'),
            Column::make('Keperluan', 'keperluan'),
            Column::make('Status', 'status'),
            
            Column::action('Action')
        ];
    }

    public function actions(Lembur $row): array
    {
        $pendingTable = [];

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
