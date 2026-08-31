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

final class PengajuanSayaTable extends PowerGridComponent
{
    public string $tableName = 'pengajuan-saya-table-qmtzb3-table';

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
        return Pengajuancuti::query()->with('tanggals')
            ->where('user_id', auth()->id())
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
            ->add('alasan')
            ->add('jumlah_hari', fn (Pengajuancuti $m) => $m->tanggals->count())
            ->add('tanggal_range', function (Pengajuancuti $m) {
                $t = $m->tanggals->pluck('tanggal')->sort();
                return $t->count() ? $t->first()->format('d/m/Y').' - '.$t->last()->format('d/m/Y') : '-';
            })
            ->add('status_label', fn (Pengajuancuti $m) => match ($m->status) {
                'diajukan'   => '<span class="badge badge-warning">Diajukan</span>',
                'disetujui'  => '<span class="badge badge-success">Disetujui</span>',
                'ditolak'    => '<span class="badge badge-error">Ditolak</span>',
                'dibatalkan' => '<span class="badge badge-ghost">Dibatalkan</span>',
                default      => '<span class="badge badge-ghost">-</span>',
            });
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),
            Column::make('Tanggal Cuti', 'tanggal_range'),
            Column::make('Jumlah Hari', 'jumlah_hari'),
            Column::make('Alasan', 'alasan'),
            Column::make('Status', 'status_label'),
            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    public function actions(Pengajuancuti $row): array
    {
        $pengajuanSayaButton = [];

        Gate::allows('akses', 'Pengajuan Cuti Edit') && $pengajuanSayaButton[] =
        Button::add('updatepengajuan')  
            ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
            ->attributes([
                'onclick' => 'modaleditpengajuancuti.showModal()',
                'class' => 'btn btn-primary btn-sm'
            ])
        ->dispatchTo('cuti.update', 'getupdatepengajuancuti', ['rowId' => $row->id]);

        Gate::allows('akses', 'Pengajuan Cuti Hapus') && $pengajuanSayaButton[] =
        Button::add('deletepengajuan')
            ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
            ->class('btn btn-error btn-sm')
        ->dispatch('modaldeletepengajuancuti', ['rowId' => $row->id]);

        return $pengajuanSayaButton;
    }

    #[\Livewire\Attributes\On('modaldeletepengajuancuti')]
    public function modaldeletepengajuancuti($rowId): void
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
                    Livewire.dispatch('konfirmasideletepengajuancuti', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasideletepengajuancuti')]
    public function konfirmasideletepengajuancuti($rowId): void
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
