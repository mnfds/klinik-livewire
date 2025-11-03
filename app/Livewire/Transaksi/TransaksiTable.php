<?php

namespace App\Livewire\Transaksi;

use Illuminate\Support\Carbon;
use App\Models\PasienTerdaftar;
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
        return PasienTerdaftar::whereIn('status_terdaftar', ['peresepan', 'pembayaran', 'lunas', 'selesai'])
            // ->whereDate('created_at', today())
            ->with([
                'pasien',
                'poliklinik', 
                'dokter',
        ]);
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

            Column::make('Tanggal Kunjungan', 'tanggal_kunjungan')
                ->hidden()
                ->sortable(),

            Column::make('status', 'status'),
            
            Column::make('Jenis Kunjungan', 'jenis_kunjungan')
                ->hidden(),

            Column::action('Action') // untuk tombol edit/delete
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(PasienTerdaftar $row): array
    {
        return [
            Button::add('cekresepbutton')
                ->slot('<i class="fa-solid fa-tablets"></i> Peresepan')
                ->tag('button')
                ->attributes([
                    'title' => 'Lihat Resep Obat',
                    'onclick' => "Livewire.navigate('" . route('transaksi.detail', ['id' => $row->id]) . "')",
                    'class' => 'btn btn-accent',
                ]),
            Button::add('bayarbutton')
                ->slot('<i class="fa-solid fa-hand-holding-dollar"></i> Pembayaran')
                ->tag('button')
                ->attributes([
                    'title' => 'Pembayaran Obat',
                    'onclick' => "Livewire.navigate('" . route('transaksi.detail', ['id' => $row->id]) . "')",
                    'class' => 'btn btn-secondary',
                ]),
        ];
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
        ];
    }

}
