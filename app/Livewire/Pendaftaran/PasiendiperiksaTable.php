<?php

namespace App\Livewire\Pendaftaran;

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
use PowerComponents\LivewirePowerGrid\Facades\Rule; 

final class PasiendiperiksaTable extends PowerGridComponent
{
    public string $tableName = 'pasiendiperiksa-table-5vuqpr-table';

    public function boot(): void{
        config(['livewire-powergrid.filter' => 'outside']);
    }

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
        return PasienTerdaftar::whereIn('status_terdaftar', ['konsultasi','selesai'])
            ->when(
                $this->hasTanggalFilter(),
                function ($q){
                    $range = $this->getTanggalFilter();
                    $q->whereBetween(
                        'tanggal_kunjungan',
                        [$range['start'], $range['end']]
                    );
                },
                fn ($q) => $q->whereDate('tanggal_kunjungan', today())
            )
            ->orderByDesc('tanggal_kunjungan')
            ->orderByDesc('id')
            ->with(['pasien', 'poliklinik', 'dokter']);
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
            ->add('#') // untuk nomor urut
            ->add('pasien.nama', fn ($row) => $row->pasien->nama ?? '-') // Nama Pasien
            ->add('pasien.no_register', fn ($row) => $row->pasien->no_register ?? '-') // No 
            ->add('nama_dan_register', function($row){
                return strtoupper($row->pasien->nama) . '<br><span class="text-sm text-gray-500">' . $row->pasien->no_register . '</span>';
            })
            ->add('poliklinik.nama_poli', fn ($row) => $row->poliklinik->nama_poli ?? '-') // Nama Poli
            ->add('dokter.nama_dokter', fn ($row) => $row->dokter->nama_dokter ?? '-') // Dokter yang menangani
            ->add('dokter_dan_poli', function($row){
                return strtoupper($row->poliklinik->nama_poli) . '<br><span class="text-sm text-gray-500">' . $row->dokter->nama_dokter . '</span>';
            })
            ->add('status', fn ($row) =>
                $row->status_terdaftar === 'konsultasi'
                    ? '<span class="badge badge-primary">Menunggu Konsultasi</span>'
                    : ($row->status_terdaftar ?? '-')
            )
            ->add('tanggal_kunjungan') // Jika ingin menampilkan tanggal kunjungan juga
            ->add('jenis_kunjungan')  // Jika ingin menampilkan jenis kunjungan juga
            ->add('kunjungan', function($row){
                return strtoupper($row->jenis_kunjungan) . '<br><span class="text-sm text-gray-500">' . $row->tanggal_kunjungan . '</span>';
            });
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),

            Column::make('Nama Pasien', 'pasien.nama')
                ->searchable()
                ->hidden(),

            Column::make('No. Register', 'pasien.no_register')
                ->searchable()
                ->hidden(),

            Column::make('Pasien', 'nama_dan_register')
                ->bodyAttribute('whitespace-nowrap'),

            Column::make('Poli Tujuan', 'poliklinik.nama_poli')
                ->searchable()
                ->hidden(),

            Column::make('Dokter', 'dokter.nama_dokter')
                ->searchable()
                ->hidden(),

            Column::make('Poli dan Dokter', 'dokter_dan_poli')
                ->bodyAttribute('whitespace-nowrap'),

            Column::make('Tanggal Kunjungan', 'tanggal_kunjungan')
                ->hidden()
                ->sortable(),

            Column::make('Jenis Kunjungan', 'jenis_kunjungan')
                ->hidden(),

            Column::make('Kunjungan', 'kunjungan')
                ->bodyAttribute('whitespace-nowrap'),

            Column::action('Action') // untuk tombol edit/delete
        ];
    }

    public function filters(): array
    {
        return [
            Filter::datepicker('tanggal_kunjungan', 'tanggal_kunjungan'),
        ];
    }

    protected function hasTanggalFilter(): bool
    {
        return ! empty(
            data_get($this->filters, 'date.tanggal_kunjungan.start')
        );
    }

    protected function getTanggalFilter(): array
    {
        return [
            'start' => \Carbon\Carbon::parse(
                data_get($this->filters, 'date.tanggal_kunjungan.start')
            )->toDateString(),

            'end' => \Carbon\Carbon::parse(
                data_get($this->filters, 'date.tanggal_kunjungan.end')
            )->toDateString(),
        ];
    }

    public function actions(PasienTerdaftar $row): array
    {
        $diperiksaButton = [];
        
        Gate::allows('akses', 'Rekam Medis') && $diperiksaButton[] =
        Button::add('rekammedisbutton')
            ->slot('<i class="fa-solid fa-book-medical"></i> Rekam Medis')
            ->tag('button')
            ->attributes([
                'title' => 'Isi Rekam Medis Pasien',
                'onclick' => "Livewire.navigate('" . route('rekam-medis-pasien.create', ['pasien_terdaftar_id' => $row->id]) . "')",
                'class' => 'btn btn-secondary',
            ]);

        $diperiksaButton[] = Button::add('selesai')
            ->slot('<i class="fa-regular fa-circle-check"></i> Selesai')
            ->tag('button')
            ->attributes([
                'class' => 'badge badge-success',
            ]);

        return $diperiksaButton;
    }

    public function actionRules($row): array
    {
       return [

            Rule::button('selesai')
                ->when(fn($row) => $row->status_terdaftar !== 'selesai')
                ->hide(),

            Rule::button('rekammedisbutton')
                ->when(fn($row) => $row->status_terdaftar !== 'konsultasi')
                ->hide(),
        ];
    }
}
