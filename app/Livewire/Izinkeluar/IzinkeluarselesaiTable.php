<?php

namespace App\Livewire\Izinkeluar;

use App\Models\Izinkeluar;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class IzinkeluarselesaiTable extends PowerGridComponent
{
    public string $tableName = 'izinkeluarselesai-table-ri6nvp-table';

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
        return Izinkeluar::with(['user','user.biodata'])
            ->where('status', 'selesai')
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
            ->add('tanggal_izin',fn ($row) => \Carbon\Carbon::parse($row->tanggal_izin)->format('d M Y'))

            ->add('Staff', function ($row){
                return strtoupper($row->user->biodata->nama_lengkap ?? $row->user->dokter->nama_dokter);
            })

            ->add('jam_keluar')
            ->add('jam_kembali')
            ->add('waktu_izin', function ($row) {
                return
                 '<br><span>' . \Carbon\Carbon::parse($row->tanggal_izin)->format('d M Y') . ', </span>' .
                 '<br><span>' . $row->jam_keluar .' - '. $row->jam_kembali .'</span>';
            })

            ->add('keperluan')

            ->add('disetujui_oleh', function ($row){
                return strtoupper($row->approver->biodata->nama_lengkap);
            });
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),

            Column::make('Staff Terkait', 'Staff'),

            Column::make('Jam Keluar', 'jam_keluar')->searchable()->hidden(),
            Column::make('Jam Kembali', 'jam_kembali')->searchable()->hidden(),
            Column::make('Waktu Izin', 'waktu_izin'),
            
            Column::make('Keperluan', 'keperluan'),
            Column::make('Diketahui Oleh', 'disetujui_oleh'),
            
            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(Izinkeluar $row): array
    {
        $izinselesaitable = [];

        Gate::allows('akses', 'Pengajuan Riwayat Izin Keluar Edit') && $izinselesaitable[] =
        Button::add('updateizinselesai')  
            ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
            ->attributes([
                'onclick' => 'modaleditizinselesai.showModal()',
                'class' => 'btn btn-primary btn-sm'
            ])
        ->dispatchTo('izinkeluar.update', 'getupdateizinselesai', ['rowId' => $row->id]);

        Gate::allows('akses', 'Pengajuan Riwayat Izin Keluar Hapus') && $izinselesaitable[] =
        Button::add('deleteizinselesai')
            ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
            ->class('btn btn-error btn-sm')
        ->dispatch('modaldeleteizinselesai', ['rowId' => $row->id]);

        return $izinselesaitable;
    }
    
    #[\Livewire\Attributes\On('izinkeluar-selesai')]
    public function refreshSelesai()
    {
        $this->dispatch('pg:eventRefresh');
    }

    #[\Livewire\Attributes\On('modaldeleteizinselesai')]
    public function modaldeleteizinselesai($rowId): void
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
                    Livewire.dispatch('konfirmasideleteizinselesai', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasideleteizinselesai')]
    public function konfirmasideleteizinselesai($rowId): void
    {
        if (! Gate::allows('akses', 'Pengajuan Riwayat Izin Keluar Hapus')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        Izinkeluar::findOrFail($rowId)->delete();

        $this->dispatch('pg:eventRefresh')->to(self::class); // refresh PowerGrid

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil dihapus.',
        ]);
    }
}
