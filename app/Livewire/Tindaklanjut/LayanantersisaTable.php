<?php

namespace App\Livewire\Tindaklanjut;

use App\Models\pasien;
use Illuminate\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Detail;

final class LayanantersisaTable extends PowerGridComponent
{
    public string $tableName = 'layanantersisa-table-rz3krk-table';

    public function setUp(): array
    {
        // $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
            PowerGrid::detail()
                ->view('components.pasien-bundling-detail')
                ->showCollapseIcon(),
        ];
    }

    public function datasource(): Builder
    {
        return Pasien::with([
            'pelayananBundlings.pelayanan',
            'produkObatBundlings.produk',
            'treatmentBundlings.treatment',
            'pelayananBundlings.bundling',
            'produkObatBundlings.bundling',
            'treatmentBundlings.bundling',
        ])
        ->where(function ($query) {
            $query->whereHas('pelayananBundlings', fn($q) => $q->whereColumn('jumlah_terpakai', '<', 'jumlah_awal'))
                ->orWhereHas('produkObatBundlings', fn($q) => $q->whereColumn('jumlah_terpakai', '<', 'jumlah_awal'))
                ->orWhereHas('treatmentBundlings', fn($q) => $q->whereColumn('jumlah_terpakai', '<', 'jumlah_awal'));
        });
    }

    public function relationSearch(): array
    {
        return [
            'pelayananBundlings' => ['pelayanan.nama_pelayanan', 'pelayanan.bundling.nama'],
            'produkObatBundlings' => ['produk.nama_dagang', 'produk.bundling.nama'],
            'treatmentBundlings' => ['treatment.nama_treatment', 'treatment.bundling.nama'],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('nama')
            ->add('created_at_formatted', fn ($model) => $model->created_at->format('d/m/Y H:i'))
            ->add('jumlah_bundling', function ($pasien) {
            $jumlah = collect([
                    $pasien->pelayananBundlings,
                    $pasien->produkObatBundlings,
                    $pasien->treatmentBundlings
                ])
                    ->flatten()
                    ->groupBy('bundling_id')
                    ->count();

                return $jumlah . ' Bundling';
            })
            ->add('jumlah_group_aktif', function ($pasien) {
                return collect()
                    ->merge($pasien->pelayananBundlings ?? collect())
                    ->merge($pasien->produkObatBundlings ?? collect())
                    ->merge($pasien->treatmentBundlings ?? collect())
                    ->groupBy('group_bundling')
                    ->filter(fn($group) => $group->contains(fn($i) => $i->jumlah_terpakai < $i->jumlah_awal))
                    ->count();
            })
            ->add('isi_paket', function ($pasien) {
                $bundlings = collect();

                // Gabungkan semua relasi dan kelompokkan per bundling
                $bundlings = $bundlings
                    ->merge($pasien->pelayananBundlings ?? collect())
                    ->merge($pasien->treatmentBundlings ?? collect())
                    ->merge($pasien->produkObatBundlings ?? collect())
                    ->groupBy(fn($item) => $item->bundling->nama ?? 'Tanpa Nama');

                    $html = '';
                    
                    foreach ($bundlings as $bundlingName => $items) {
                    $html .= '<div class="mb-3">';
                    $html .= '<strong class="text-base">' . e($bundlingName) . '</strong><br>';

                    $pelayanan = $items->whereNotNull('pelayanan')
                        ->filter(fn($i) => $i->jumlah_awal > $i->jumlah_terpakai)
                        ->map(fn($i) => '• ' . e($i->pelayanan->nama_pelayanan) . ' (Sisa: ' . ($i->jumlah_awal - $i->jumlah_terpakai) . ')');

                    $treatment = $items->whereNotNull('treatment')
                        ->filter(fn($i) => $i->jumlah_awal > $i->jumlah_terpakai)
                        ->map(fn($i) => '• ' . e($i->treatment->nama_treatment) . ' (Sisa: ' . ($i->jumlah_awal - $i->jumlah_terpakai) . ')');

                    $produk = $items->whereNotNull('produk')
                        ->filter(fn($i) => $i->jumlah_awal > $i->jumlah_terpakai)
                        ->map(fn($i) => '• ' . e($i->produk->nama_dagang) . ' (Sisa: ' . ($i->jumlah_awal - $i->jumlah_terpakai) . ')');

                    if ($pelayanan->isNotEmpty()) {
                        $html .= '<strong>Pelayanan:</strong><br>' . $pelayanan->implode('<br>') . '<br>';
                    }
                    if ($treatment->isNotEmpty()) {
                        $html .= '<strong>Treatment:</strong><br>' . $treatment->implode('<br>') . '<br>';
                    }
                    if ($produk->isNotEmpty()) {
                        $html .= '<strong>Produk & Obat:</strong><br>' . $produk->implode('<br>');
                    }
                    $html .= '</div>';
                }

                return $html ?: '-';
            });
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),
            Column::make('Nama Pasien', 'nama')->sortable()->searchable(),
            Column::make('Bundling Aktif', 'jumlah_group_aktif')->sortable(),
            // Column::make('Bundling Aktif', 'jumlah_bundling')->sortable(),
            // Column::make('Isi Paket Bundling', 'isi_paket')->searchable(),
            // Column::make('Dibuat pada', 'created_at_formatted', 'created_at')->sortable(),
            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(pasien $row): array
    {
        return [
            // Button::add('edit')
            //     ->slot('Edit: '.$row->id)
            //     ->id()
            //     ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
            //     ->dispatch('edit', ['rowId' => $row->id]),
        ];
    }
    
    public function actionsFromView($row): View
    {
        return view('components.action-button-layanantersisa', ['row' => $row]);
    }

    public function actionRules(): array
    {
        return [
            Rule::rows()
                ->when(fn ($user) => $user->id == 1)
                ->detailView('components.pasien-bundling-detail', ['test' => 1]),
        ];
    }
}
