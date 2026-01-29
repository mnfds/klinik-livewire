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

final class IzinkeluardisetujuiTable extends PowerGridComponent
{
    public string $tableName = 'izinkeluardisetujui-table-ws84da-table';

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
            ->where('status', 'disetujui');
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

            ->add('user_id')
            ->add('jam_keluar')
            ->add('nama_dan_jam', function ($row) {
                return strtoupper($row->user->biodata->nama_lengkap ?? $row->user->dokter->nama_dokter) .
                 '<br><span class="text-sm text-gray-500">' . \Carbon\Carbon::parse($row->tanggal_izin)->format('d M Y') . ', </span>' .
                 '<br><span class="text-sm text-gray-500">' . $row->jam_keluar . '</span>';
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

            Column::make('Nama', 'user_id')->searchable()->hidden(),
            Column::make('Jam Keluar', 'jam_keluar')->searchable()->hidden(),
            Column::make('Staff Terkait', 'nama_dan_jam'),
            
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
        $izindisetujuitable = [];

        Gate::allows('akses', 'Pengajuan Izin Keluar Selesai') && $izindisetujuitable[] =
        Button::add('selesai')  
            ->slot('<i class="fa-solid fa-circle-check"></i> Selesai')
            ->attributes([
                'class' => 'btn btn-success btn-sm'
            ])
        ->dispatch('selesai', ['rowId' => $row->id]);

        Gate::allows('akses', 'Pengajuan Izin Keluar Edit') && $izindisetujuitable[] =
        Button::add('updateizindisetujui')  
            ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
            ->attributes([
                'onclick' => 'modaleditizindisetujui.showModal()',
                'class' => 'btn btn-primary btn-sm'
            ])
        ->dispatchTo('izinkeluar.update', 'getupdateizindisetujui', ['rowId' => $row->id]);

        Gate::allows('akses', 'Pengajuan Izin Keluar Hapus') && $izindisetujuitable[] =
        Button::add('deleteizindisetujui')
            ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
            ->class('btn btn-error btn-sm')
        ->dispatch('modaldeleteizindisetujui', ['rowId' => $row->id]);

        return $izindisetujuitable;
    }

    #[\Livewire\Attributes\On('selesai')]
    public function selesai($rowId)
    {
        if (! Gate::allows('akses', 'Pengajuan Izin Keluar Selesai')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        Izinkeluar::where('id', $rowId)->update([
            'status' => 'selesai',
            'jam_kembali' => now()->format('H:i'),
        ]);
        $this->dispatch('pg:eventRefresh');
        $this->dispatch('izinkeluar-selesai');
        $this->dispatch('play-notif');
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Izin Keluar Telah Selesai',
        ]);
    }

    #[\Livewire\Attributes\On('modaldeleteizindisetujui')]
    public function modaldeleteizindisetujui($rowId): void
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
                    Livewire.dispatch('konfirmasideleteizindisetujui', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasideleteizindisetujui')]
    public function konfirmasideleteizindisetujui($rowId): void
    {
        if (! Gate::allows('akses', 'Pengajuan Izin Keluar Hapus')) {
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
