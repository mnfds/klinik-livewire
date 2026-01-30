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

final class ApproveTable extends PowerGridComponent
{
    public string $tableName = 'approve-table-sncfmy-table';

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
            ->where('status', 'disetujui')
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

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(Lembur $row): array
    {
        $aprroveTable = [];

        Gate::allows('akses', 'Pengajuan Lembur Selesai') && $aprroveTable[] =
        Button::add('selesai')  
        ->slot('<i class="fa-solid fa-circle-check"></i> Selesai')
        ->attributes([
            'class' => 'btn btn-success btn-sm'
            ])
        ->dispatch('selesai', ['rowId' => $row->id]);

        Gate::allows('akses', 'Pengajuan Lembur Edit') && $aprroveTable[] =
        Button::add('updateApproveLembur')  
            ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
            ->attributes([
                'onclick' => 'modalApproveUpdate.showModal()',
                'class' => 'btn btn-primary btn-sm'
            ])
        ->dispatchTo('lembur.update', 'getApproveLembur', ['rowId' => $row->id]);

        Gate::allows('akses', 'Pengajuan Lembur Hapus') && $aprroveTable[] =
        Button::add('deleteApproveLembur')
            ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
            ->class('btn btn-error btn-sm')
        ->dispatch('modalDeleteApprove', ['rowId' => $row->id]);

        return $aprroveTable;
    }

    #[\Livewire\Attributes\On('selesai')]
    public function selesai($rowId)
    {
        if (! Gate::allows('akses', 'Pengajuan Lembur Selesai')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        Lembur::where('id', $rowId)->update([
            'status' => 'selesai',
            'jam_selesai' => now()->format('H:i'),
        ]);
        $this->dispatch('pg:eventRefresh');
        $this->dispatch('refresh-HistoryTable');
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data Lembur Telah Berhasil Diperbarui',
        ]);
    }

    #[\Livewire\Attributes\On('modalDeleteApprove')]
    public function modalDeleteApprove($rowId): void
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
                    Livewire.dispatch('konfirmasiDeleteApprove', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasiDeleteApprove')]
    public function konfirmasiDeleteApprove($rowId): void
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

    #[\Livewire\Attributes\On('refresh-ApproveTable')]
    public function refreshApprove()
    {
        $this->dispatch('pg:eventRefresh');
    }
}
