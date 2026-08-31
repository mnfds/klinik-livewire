<?php

namespace App\Livewire\Cuti;

use App\Models\Pengajuancuti;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class DaftarPengajuanTable extends PowerGridComponent
{
    public string $tableName = 'daftar-pengajuan-table-dyxkkx-table';

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
        return Pengajuancuti::query()->with(['user', 'tanggals'])
            ->where('status', 'diajukan')
            ->latest();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('nama_karyawan', fn (Pengajuancuti $m) => $m->user->name)
            ->add('jumlah_hari', fn (Pengajuancuti $m) => $m->tanggals->count())
            ->add('tanggal_range', function (Pengajuancuti $m) {
                $t = $m->tanggals
                    ->pluck('tanggal')
                    ->sort();

                return $t->count()
                    ? $t->first()->format('d/m/Y') . ' - ' . $t->last()->format('d/m/Y')
                    : '-';
            })
            ->add('alasan');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),
            Column::make('Karyawan', 'nama_karyawan')->sortable(),
            Column::make('Tanggal Cuti', 'tanggal_range'),
            Column::make('Jumlah Hari', 'jumlah_hari'),
            Column::make('Alasan', 'alasan'),
            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(Pengajuancuti $row): array
    {
        $daftarPengajuanButton = [];

        Gate::allows('akses', 'Pengajuan Cuti Edit') && $daftarPengajuanButton[] =
        Button::add('updatepengajuan')
            ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
            ->attributes([
                'onclick' => 'modaleditpengajuancuti.showModal()',
                'class' => 'btn btn-primary btn-sm'
            ])
        ->dispatchTo('cuti.update', 'getupdatepengajuancuti', ['rowId' => $row->id]);

        Gate::allows('akses', 'Persetujuan Pengajuan Cuti') && $daftarPengajuanButton[] =
        Button::add('approvepengajuan')
            ->slot('<i class="fa-solid fa-check"></i> Disetujui')
            ->attributes([
                'onclick' => 'modalapprovalpengajuancuti.showModal()',
                'class' => 'btn btn-success btn-sm'
            ])
        ->dispatchTo('cuti.approval', 'getapprovecuti', ['rowId' => $row->id]);

        Gate::allows('akses', 'Persetujuan Pengajuan Cuti') && $daftarPengajuanButton[] =
        Button::add('denypengajuan')
            ->slot('<i class="fa-solid fa-x"></i> Ditolak')
            ->attributes([
                'onclick' => 'modalapprovalpengajuancuti.showModal()',
                'class' => 'btn btn-warning btn-sm'
            ])
        ->dispatchTo('cuti.approval', 'getdeniedcuti', ['rowId' => $row->id]);

        Gate::allows('akses', 'Pengajuan Cuti Hapus') && $daftarPengajuanButton[] =
        Button::add('deletepengajuan')
            ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
            ->class('btn btn-error btn-sm')
        ->dispatch('modaldeletedaftarcuti', ['rowId' => $row->id]);

        return $daftarPengajuanButton;
    }

    #[\Livewire\Attributes\On('modaldeletedaftarcuti')]
    public function modaldeletedaftarcuti($rowId): void
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
                    Livewire.dispatch('konfirmasideletedaftarcuti', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasideletedaftarcuti')]
    public function konfirmasideletedaftarcuti($rowId): void
    {
        if (! Gate::allows('akses', 'Pengajuan Cuti Hapus')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        Pengajuancuti::findOrFail($rowId)->delete();

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
