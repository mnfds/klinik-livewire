<?php

namespace App\Livewire\Cuti;

use App\Models\Pengajuancuti;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class RiwayatPengajuanTable extends PowerGridComponent
{
    public string $tableName = 'riwayat-pengajuan-table-jz832n-table';

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
        $query = Pengajuancuti::query()
            ->with(['user', 'approver', 'tanggals'])
            ->whereIn('status', ['disetujui', 'ditolak'])
            ->latest();

        // Kalau tidak memiliki akses, hanya melihat riwayat miliknya sendiri
        if (!Gate::allows('akses', 'Riwayat Pengajuan Cuti')) {
            $query->where('user_id', auth()->id());
        }

        return $query;
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
            ->add('tanggal_range', function (Pengajuancuti $m) {
                $t = $m->tanggals->pluck('tanggal')->sort();
                return $t->count() ? $t->first()->format('d/m/Y').' - '.$t->last()->format('d/m/Y') : '-';
            })
            ->add('jumlah_hari', fn (Pengajuancuti $m) => $m->tanggals->count())
            ->add('status_label', fn (Pengajuancuti $m) => match ($m->status) {
                'diajukan' => '<span class="badge badge-warning">Diajukan</span>',
                'disetujui' => '<span class="badge badge-success">Disetujui</span>',
                'ditolak' => '<span class="badge badge-error">Ditolak</span>',
                'dibatalkan' => '<span class="badge badge-ghost">Dibatalkan</span>',
            })
            ->add('diproses_oleh', fn (Pengajuancuti $m) => $m->approver?->name ?? '-')
            ->add('catatan_admin');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),
            Column::make('Karyawan', 'nama_karyawan')->sortable(),
            Column::make('Tanggal Cuti', 'tanggal_range'),
            Column::make('Jumlah Hari', 'jumlah_hari'),
            Column::make('Status', 'status_label')->sortable('status'),
            Column::make('Diproses Oleh', 'diproses_oleh'),
            Column::make('Catatan', 'catatan_admin'),
            Column::Action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::enumSelect('status', 'status')
                ->dataSource([
                    ['value' => 'diajukan', 'label' => 'Diajukan'],
                    ['value' => 'disetujui', 'label' => 'Disetujui'],
                    ['value' => 'ditolak', 'label' => 'Ditolak'],
                    ['value' => 'dibatalkan', 'label' => 'Dibatalkan'],
                ])->optionValue('value')->optionLabel('label'),
        ];
    }

    public function actions(Pengajuancuti $row): array
    {
        $riwayatPengajuanButton = [];

        Gate::allows('akses', 'Pengajuan Cuti Hapus') && $riwayatPengajuanButton[] =
        Button::add('deleteriwayatpengajuan')
            ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
            ->class('btn btn-error btn-sm')
        ->dispatch('modaldeleteriwayatpengajuan', ['rowId' => $row->id]);

        return $riwayatPengajuanButton;
    }
    #[\Livewire\Attributes\On('modaldeleteriwayatpengajuan')]
    public function modaldeleteriwayatpengajuan($rowId): void
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
                    Livewire.dispatch('konfirmasideleteriwayatcuti', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasideleteriwayatcuti')]
    public function konfirmasideleteriwayatcuti($rowId): void
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
