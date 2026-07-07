<?php

namespace App\Livewire\Surat;

use App\Models\SuratKeterangan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class SuratTable extends PowerGridComponent
{
    public string $tableName = 'surat-table-mzx2gq-table';

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
        return SuratKeterangan::query()
            ->join('pasien_terdaftars', 'surat_keterangans.pasien_terdaftar_id', '=', 'pasien_terdaftars.id')
            ->join('pasiens', 'pasien_terdaftars.pasien_id', '=', 'pasiens.id')
            ->join('dokters', 'pasien_terdaftars.dokter_id', '=', 'dokters.id')
            ->select([
                'surat_keterangans.*',
                'pasiens.nama          as nama_pasien',
                'pasiens.no_register   as no_register',
                'dokters.nama_dokter   as nama_dokter',
            ])
            ->latest();
    }

    public function relationSearch(): array
    {
        return [
            'pasienTerdaftar.pasien' => ['nama', 'no_register'],
            'pasienTerdaftar.dokter' => ['nama_dokter'],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('#')
            ->add('no_surat')
            ->add('pasien.nama', fn ($row) => $row->pasienTerdaftar->pasien->nama ?? '-') // Nama Pasien
            ->add('pasien.no_register', fn ($row) => $row->pasienTerdaftar->pasien->no_register ?? '-') // No 
            ->add('nama_dan_register', function($row){
                return strtoupper($row->pasienTerdaftar->pasien->nama) . '<br><span class="text-sm text-gray-500">' . $row->pasienTerdaftar->pasien->no_register . '</span>';
                })
            ->add('nama_dokter')
            ->add('kondisi', fn ($row) => $row->sakit ?? '-');
    }

    public function columns(): array
    {
        return [
            Column::make('No. Surat', 'no_surat'),
            Column::make('Nama Pasien', 'pasien.nama')
                ->searchable()
                ->hidden(),
            Column::make('No. Register', 'pasien.no_register')
                ->searchable()
                ->hidden(),
            Column::make('Pasien', 'nama_dan_register')
                ->bodyAttribute('whitespace-nowrap'),
            Column::make('kondisi', 'sakit'),
            Column::action('Action') // untuk tombol edit/delete
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(SuratKeterangan $row): array
    {
        $suratKeterangan = [];
        
        Gate::allows('akses', 'Surat Keterangan Edit') && $suratKeterangan[] =
        Button::add('updateSurat')  
            ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
            ->attributes([
                'onclick' => 'modalupdatesurat.showModal()',
                'class' => 'btn btn-primary'
            ])
            ->dispatchTo('surat.update', 'getupdatesurat', ['rowId' => $row->id]);

        Gate::allows('akses', 'Surat Keterangan Hapus') && $suratKeterangan[] =
        Button::add('deleteSurat')
            ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
            ->class('btn btn-error')
            ->dispatch('modalDeleteSurat', ['rowId' => $row->id]);

        return $suratKeterangan;
    }

    #[\Livewire\Attributes\On('modalDeleteSurat')]
    public function modalDeleteSurat($rowId): void
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
                    Livewire.dispatch('konfirmasiDeleteSurat', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasiDeleteSurat')]
    public function konfirmasiDeleteSurat($rowId): void
    {
        if (! Gate::allows('akses', 'Surat Keterangan Hapus')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        SuratKeterangan::findOrFail($rowId)->delete();

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
