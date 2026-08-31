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
        $query = Lembur::with(['user', 'user.biodata', 'approver.biodata',])
            ->whereIn('status', [
                'disetujui',
                'ditolak',
            ]);

        // Jika memiliki akses, tampilkan semua data
        if (! Gate::allows('akses', 'Persetujuan Ajuan Lembur')) {
            // Jika tidak memiliki akses, hanya tampilkan data milik sendiri
            $query->where('user_id', auth()->id());
        }

        return $query->latest();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('#')
            ->add('tanggal_lembur',fn ($row) => \Carbon\Carbon::parse($row->tanggal_lembur)->format('d M Y'))

            ->add('Staff', function ($row){
                return strtoupper($row->user->biodata->nama_lengkap ?? $row->user->dokter->nama_dokter);
            })

            ->add('perkiraan_durasi')
            ->add('waktu_lembur', function ($row) {
                return
                 '<br><span>' . \Carbon\Carbon::parse($row->tanggal_lembur)->format('d M Y') . ', </span>' .
                 '<br><span>' . ($row->jam_mulai . ' - ' . ($row->perkiraan_durasi ?? '??')) . ' Jam</span>';
            })
            
            ->add('keperluan')
            ->add('disetujui_oleh')
            ->add('status')
            ->add('persetujuan', function ($row) {
                $nama = $row->approver?->biodata?->nama_lengkap
                    ?? $row->approver?->dokter?->nama_dokter
                    ?? '-';

                return
                    '<br><span>' . ucfirst($row->status) . ' oleh</span>' .
                    '<span class="font-semibold ml-1">' . strtoupper($nama) . '</span>';
            });
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),

            Column::make('Karyawan Terkait', 'Staff')->searchable(),
            
            Column::make('perkiraan_durasi', 'perkiraan_durasi')->searchable()->hidden(),
            Column::make('Waktu Lembur', 'waktu_lembur'),
            
            
            Column::make('Keperluan', 'keperluan'),
            Column::make('stat', 'status')->searchable()->hidden(),
            Column::make('Status', 'persetujuan'),
            
            Column::action('Action')
        ];
    }

    public function actions(Lembur $row): array
    {
        $historyTable = [];

        Gate::allows('akses', 'Riwayat Pengajuan Lembur Edit') && $historyTable[] =
        Button::add('updateHistoryLembur')  
            ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
            ->attributes([
                'onclick' => 'modalHistoryUpdate.showModal()',
                'class' => 'btn btn-primary btn-sm'
            ])
        ->dispatchTo('lembur.update', 'getHistoryLembur', ['rowId' => $row->id]);

        Gate::allows('akses', 'Riwayat Pengajuan Lembur Hapus') && $historyTable[] =
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
