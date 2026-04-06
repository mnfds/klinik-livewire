<?php

namespace App\Livewire\Rekammedis;

use Illuminate\Support\Carbon;
use App\Models\PasienTerdaftar;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class RiwayatTable extends PowerGridComponent
{
    public string $tableName = 'riwayat-table-1beegm-table';

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

    public ?int $pasien_id = null;

    public function datasource(): Builder
    {
        return PasienTerdaftar::with(['pasien', 'poliklinik', 'dokter'])
            ->orderByDesc('tanggal_kunjungan')
            ->when($this->pasien_id, fn ($q) => $q->where('pasien_id', $this->pasien_id));
    }

    public function relationSearch(): array
    {
        return [
            'pasien' => ['nama', 'no_register'],
            'poliklinik' => ['nama_poli'],
            'dokter' => ['nama_dokter'],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('poliklinik.nama_poli', fn ($row) => $row->poliklinik->nama_poli ?? '-') // Nama Poli
            ->add('created_at_formatted', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)
                    ->locale('id')
                    ->translatedFormat('l, d F Y');
            });
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),
            Column::make('Tanggal Input', 'created_at_formatted')->sortable(),
            Column::make('Poliklinik', 'poliklinik.nama_poli')->searchable(),
            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(PasienTerdaftar $row): array
    {
        $riwayatRekamMedis = [];
        
        Gate::allows('akses', 'Detail Rekam Medis') && $riwayatRekamMedis[] =
        Button::add('detail_kunjungan')
            ->slot('<i class="fa-solid fa-magnifying-glass"></i> Detail')
            ->tag('button')
            ->attributes([
                'title' => 'Detail Kunjungan Medis',
                'onclick' => "Livewire.navigate('" . route('rekam-medis-pasien.detail', ['pasien_terdaftar_id' => $row->id]) . "')",
                'class' => 'btn btn-primary',
            ]);
        Gate::allows('akses', 'Rekam Medis Hapus') && $riwayatRekamMedis[] =
        Button::add('deleterekammedispasien')
            ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
            ->class('btn btn-error')
            ->dispatch('modaldeleterekammedispasien', ['rowId' => $row->id]);
        
        return $riwayatRekamMedis;
    }

    #[\Livewire\Attributes\On('modaldeleterekammedispasien')]
    public function modaldeleterekammedispasien($rowId): void
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
                    Livewire.dispatch('konfirmasideleterekammedispasien', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasideleterekammedispasien')]
    public function konfirmasideleterekammedispasien($rowId): void
    {
        if (! Gate::allows('akses', 'Rekam Medis Hapus')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        PasienTerdaftar::findOrFail($rowId)->delete();

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
