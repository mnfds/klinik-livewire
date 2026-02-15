<?php

namespace App\Livewire\Transaksi;

use Illuminate\Support\Carbon;
use App\Models\PasienTerdaftar;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class TransaksiTable extends PowerGridComponent
{
    public string $tableName = 'transaksi-table-ngctmf-table';

    public function boot(): void
    {
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
        return PasienTerdaftar::query()
            ->whereIn('status_terdaftar', ['peresepan', 'pembayaran', 'lunas', 'selesai'])
            ->when(
                $this->hasTanggalFilter(),
                function ($q) {
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
            ->with(['pasien', 'poliklinik', 'dokter'])
            ->latest();
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
                $row->status_terdaftar === 'peresepan'
                    ? '<span class="badge badge-accent">Diproses</span>'
                    : ($row->status_terdaftar === 'pembayaran'
                        ? '<span class="badge badge-secondary">Pembayaran</span>'
                        : ($row->status_terdaftar === 'lunas'
                            ? '<span class="badge badge-primary">Siap Tebus</span>'
                            : ($row->status_terdaftar === 'selesai'
                                ? '<span class="badge badge-success">Selesai</span>'
                                : ($row->status_terdaftar ?? '-')
                            )
                        )
                    )
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
            Column::make('Tanggal Kunjungan', 'tanggal_kunjungan'),

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

            // Column::make('Tanggal Kunjungan', 'tanggal_kunjungan')
            //     ->hidden()
            //     ->sortable(),

            Column::make('status', 'status'),
            
            Column::make('Jenis Kunjungan', 'jenis_kunjungan')
                ->hidden(),

            Column::action('Action') // untuk tombol edit/delete
        ];
    }

    public function filters(): array
    {
        return [
            Filter::datepicker('tanggal_kunjungan', 'tanggal_kunjungan'),
        ];
    }

    public function actions(PasienTerdaftar $row): array
    {
        $transaksiButton = [];

        $transaksiButton[] = Button::add('cekresepbutton')
            ->slot('<i class="fa-solid fa-tablets"></i> Peresepan')
            ->tag('button')
            ->attributes([
                'title' => 'Lihat Resep Obat',
                'onclick' => "Livewire.navigate('" . route('transaksi.detail', ['id' => $row->id]) . "')",
                'class' => 'btn btn-accent',
            ]);

        Gate::allows('akses', 'Transaksi Klinik Detail') && $transaksiButton[] =
        Button::add('bayarbutton')
            ->slot('<i class="fa-solid fa-hand-holding-dollar"></i> Pembayaran')
            ->tag('button')
            ->attributes([
                'title' => 'Pembayaran Obat',
                'onclick' => "Livewire.navigate('" . route('transaksi.detail', ['id' => $row->id]) . "')",
                'class' => 'btn btn-secondary',
            ]);

        $transaksiButton[] = Button::add('mutasi')
            ->slot('<i class="fa-solid fa-file-invoice-dollar"></i> Detail')
            ->tag('button')
            ->attributes([
                'title' => 'Mutasi',
                'onclick' => "Livewire.navigate('" . route('transaksi.mutasi', ['id' => $row->id]) . "')",
                'class' => 'btn btn-primary',
            ]);

        $transaksiButton[] =
        Button::add('invoice')
            ->slot('<i class="fa-solid fa-print"></i>Print Invoice')
            ->tag('button')
            ->attributes([
                'title' => 'Print Invoice Transaksi',
                'class' => 'btn btn-secondary',
            ])
            ->dispatchTo('transaksi.invoice', 'print', ['rowId' => $row->id]);

        return $transaksiButton; 
    }

    public function actionRules($row): array
    {
        return [
            Rule::button('cekresepbutton')
                ->when(fn($row) => $row->status_terdaftar !== 'peresepan')
                ->hide(),

            Rule::button('bayarbutton')
                ->when(fn($row) => $row->status_terdaftar !== 'pembayaran')
                ->hide(),

            Rule::button('invoice')
                ->when(fn($row) => ! in_array($row->status_terdaftar, ['lunas', 'selesai']))
                ->hide(),

            Rule::button('mutasi')
                ->when(fn($row) => ! in_array($row->status_terdaftar, ['lunas', 'selesai']))
                ->hide(),
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

}
