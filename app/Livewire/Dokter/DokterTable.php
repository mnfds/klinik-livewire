<?php

namespace App\Livewire\Dokter;

use App\Models\User;
use App\Models\Dokter;
use App\Models\PoliKlinik;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class DokterTable extends PowerGridComponent
{
    public string $tableName = 'dokter-table-mfxkwq-table';

    public function setUp(): array
    {
        return [
            PowerGrid::header()->showSearchInput(),
            PowerGrid::footer()->showPerPage()->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Dokter::with(['user', 'dokterpoli.poli']);
    }

    public function relationSearch(): array
    {
        return [
            'user' => ['email'],
            'dokterpoli.poli' => ['nama_poli'],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('nama_dokter')
            ->add('user.email')
            ->add('telepon')

            // Wajib: untuk menampilkan filter poli (field harus ada agar filter muncul)
            ->add('filter_poli_id', fn($row) => optional($row->dokterpoli->first())->poli_id)

            // Optional: tampilkan nama poli gabungan
            ->add('nama_poli', function ($row) {
                return $row->dokterpoli
                    ->map(fn($dp) => $dp->poli->nama_poli ?? '-')
                    ->implode(', ');
            });
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),
            Column::make('Nama', 'nama_dokter')->sortable()->searchable(),
            Column::make('Email', 'user.email')->searchable(),
            Column::make('Telepon', 'telepon')->searchable(),

            // Ini akan tetap menampilkan nama poli
            Column::make('Poli', 'nama_poli')->searchable(),

            // Kolom aksi
            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('nama_poli', 'filter_poli_id') // Nama field HARUS ada di fields()
                ->dataSource(PoliKlinik::all())
                ->optionLabel('nama_poli')
                ->optionValue('id')
                ->builder(function (Builder $query, $value) {
                    return $query->whereHas('dokterpoli.poli', fn($q) => $q->where('id', $value));
                }),
        ];
    }

    public function actions(Dokter $row): array
    {
        $actionDokter = [];
        Gate::allows('akses', 'Dokter Detail') && $actionDokter[] =
            Button::add('detailDokter')
                    ->slot('<i class="fas fa-eye"></i> Detail')
                    ->tag('button')
                    ->attributes([
                        'title' => 'Lihat detail',
                        'onclick' => "Livewire.navigate('" . route('dokter.detail', $row->id) . "')",
                        'class' => 'btn btn-primary',
                    ]);
        Gate::allows('akses', 'Dokter Edit') && $actionDokter[] =
            Button::add('EditDokter')
                ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
                ->tag('button')
                ->attributes([
                    'title' => 'Edit data',
                    'onclick' => "Livewire.navigate('" . route('dokter.update', $row->id) . "')",
                    'class' => 'btn btn-secondary',
                ]);
        Gate::allows('akses', 'Dokter Hapus') && $actionDokter[] =
            Button::add('deleteDokter')
                ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
                ->class('btn btn-error')
                ->dispatch('deleteModalDokter', ['rowId' => $row->id]);
        
            return $actionDokter;
    }

    #[\Livewire\Attributes\On('deleteModalDokter')]
    public function deleteModalDokter($rowId): void
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
                    Livewire.dispatch('konfirmasihapusdokter', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasihapusdokter')]
    public function konfirmasihapusdokter($rowId): void
    {
        if (! Gate::allows('akses', 'Dokter Hapus')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        Dokter::findOrFail($rowId)->delete();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil dihapus.',
        ]);
        
        $this->dispatch('pg:eventRefresh')->to(self::class); // refresh PowerGrid

    }
}
