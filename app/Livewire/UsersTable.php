<?php

namespace App\Livewire;

use App\Models\Role;
use App\Models\User;
use App\Models\Kuotalibur;
use App\Models\Kuotacuti;
use App\Models\Jadwal;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Query\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class UsersTable extends PowerGridComponent
{
    public string $tableName = 'users-table-jp2a1c-table';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }
    public function header(): array
    {
        return [
            Button::add('bulk-tambah-kuota-libur')
                ->slot('Tambah Kuota Libur (<span x-text="window.pgBulkActions.count(\'' . $this->tableName . '\')"></span>)')
                ->class('btn btn-primary m-1')
                ->dispatch('bulkTambahKuota.' . $this->tableName, []),

            Button::add('bulk-tambah-kuota-cuti')
                ->slot('Tambah Kuota Cuti  (<span x-text="window.pgBulkActions.count(\'' . $this->tableName . '\')"></span>)')
                ->class('btn btn-secondary m-1')
                ->dispatch('bulkTambahKuotaCuti.' . $this->tableName, []),
        ];
    }

    public function datasource(): \Illuminate\Database\Eloquent\Builder
    {
        // return DB::table('users');
        return \App\Models\User::with(['biodata','role'])
            ->where('role_id', '!=', 2);
    }

    public function relationSearch(): array
    {
        return [
            'biodata' => ['nama_lengkap', 'telepon'],
            'role' => ['id', 'nama_role'],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            // ->add('id')
            ->add('biodata.nama_lengkap')
            ->add('name') //column ini isinya username
            ->add('email')
            ->add('biodata.telepon')
            ->add('role.nama_role')
            ->add('role_id');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),
            
            // Column::make('Id', 'id'),

            Column::make('Nama', 'biodata.nama_lengkap')->sortable()->searchable(),

            Column::make('Username', 'name')->sortable()->searchable(),

            Column::make('Alamat Email', 'email')->searchable(),

            Column::make('Telepon', 'biodata.telepon'),
            Column::make('Role', 'role.nama_role', 'role_id'),

            Column::action('Action'),

        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('role.nama_role', 'role_id')
                ->dataSource(Role::all())
                ->optionLabel('nama_role')
                ->optionValue('id'),
        ];
    }

    #[\Livewire\Attributes\On('delete')]
    public function confirmDelete($rowId): void
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
                    Livewire.dispatch('deleteConfirmed', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('deleteConfirmed')]
    public function deleteConfirmed($rowId): void
    {
        if (! Gate::allows('akses', 'Staff Hapus')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        User::findOrFail($rowId)->delete();

        $this->dispatch('pg:eventRefresh')->to(self::class); // refresh PowerGrid

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil dihapus.',
        ]);
    }

    #[\Livewire\Attributes\On('bulkTambahKuota.{tableName}')]
    public function bulkTambahKuota(): void
    {
        $bulanTahunIni = now()->format('Y-m');

        $this->js(<<<JS
            const ids = window.pgBulkActions.get('$this->tableName');

            if (!ids || ids.length === 0) {
                Swal.fire('Pilih data dulu', 'Belum ada data yang dicentang.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Tambah Kuota Libur',
                html:
                    '<div class="text-left space-y-3">' +
                        '<div>' +
                            '<label class="block text-sm font-medium text-gray-700 mb-1">Periode</label>' +
                            '<input id="swal-periode" type="month" class="input !m-0 !w-full" value="$bulanTahunIni">' +
                        '</div>' +
                        '<div>' +
                            '<label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Kuota</label>' +
                            '<input id="swal-kuota" type="number" class="input !m-0 !w-full" value="4" placeholder="Jumlah Kuota">' +
                        '</div>' +
                    '</div>',
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                preConfirm: () => {
                    const periode = document.getElementById('swal-periode').value;
                    const kuota = document.getElementById('swal-kuota').value;

                    if (!periode || !kuota) {
                        Swal.showValidationMessage('Semua field wajib diisi');
                        return false;
                    }

                    const [tahun, bulan] = periode.split('-');

                    return { bulan, tahun, kuota };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('bulkTambahKuotaConfirmed', {
                        userIds: ids,
                        bulan: parseInt(result.value.bulan),
                        tahun: parseInt(result.value.tahun),
                        kuota: parseInt(result.value.kuota),
                    });
                }
            });
        JS);
    }

    private function hitungTerpakai($userId, $bulan, $tahun)
    {
        $today = today();
        $bulanIni = Carbon::create($tahun, $bulan, 1);

        $cutoff = match (true) {
            $bulanIni->isSameMonth($today) && $bulanIni->isSameYear($today) => $today,
            $bulanIni->lt($today) => $bulanIni->copy()->endOfMonth(),
            default => $bulanIni->copy()->startOfMonth()->subDay(),
        };

        return Jadwal::where('user_id', $userId)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->whereDate('tanggal', '<=', $cutoff)
            ->whereHas('jamkerja', fn ($q) => $q->where('tipe_shift', 'libur'))
            ->count();
    }

    private function hitungSisaKuotaBulanLalu($userId, $bulan, $tahun)
    {
        $bulanDipilih = Carbon::create($tahun, $bulan, 1);
        $bulanLalu = $bulanDipilih->copy()->subMonth();

        $kuotaLalu = Kuotalibur::where('user_id', $userId)
            ->where('bulan', $bulanLalu->month)
            ->where('tahun', $bulanLalu->year)
            ->first();

        $dimilikiLalu = $kuotaLalu->kuota_dimiliki ?? 0;
        $sisaCarryLalu = $kuotaLalu->kuota_sisa_bulan_sebelumnya ?? 0;
        $totalLalu = $dimilikiLalu + $sisaCarryLalu;

        $terpakaiLalu = $this->hitungTerpakai($userId, $bulanLalu->month, $bulanLalu->year);

        return max(0, $totalLalu - $terpakaiLalu);
    }

    #[\Livewire\Attributes\On('bulkTambahKuotaConfirmed')]
    public function bulkTambahKuotaConfirmed(array $userIds, int $bulan, int $tahun, int $kuota): void
    {
        if (! Gate::allows('akses', 'Staff Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        $bulanDipilih = Carbon::create($tahun, $bulan, 1);
        $today = today();

        // Samakan pembatasan dengan proses single: hanya boleh bulan berjalan
        if (! $bulanDipilih->isSameMonth($today) || ! $bulanDipilih->isSameYear($today)) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Kuota libur hanya bisa diinput untuk bulan berjalan (' . $today->format('F Y') . ').',
            ]);
            return;
        }

        $berhasil = 0;

        foreach ($userIds as $userId) {
            $sisaBulanLalu = $this->hitungSisaKuotaBulanLalu($userId, $bulan, $tahun);

            Kuotalibur::updateOrCreate(
                [
                    'user_id' => $userId,
                    'bulan'   => $bulan,
                    'tahun'   => $tahun,
                ],
                [
                    'kuota_dimiliki' => $kuota,
                    'kuota_sisa_bulan_sebelumnya' => $sisaBulanLalu,
                ]
            );

            $berhasil++;
        }

        $this->dispatch('pg:eventRefresh')->to(self::class);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "{$berhasil} user berhasil ditambahkan kuota libur bulan {$bulan}/{$tahun}.",
        ]);
    }

    #[\Livewire\Attributes\On('bulkTambahKuotaCuti.{tableName}')]
    public function bulkTambahKuotaCuti(): void
    {
        $tahunIni = now()->year;

        $this->js(<<<JS
            const ids = window.pgBulkActions.get('$this->tableName');

            if (!ids || ids.length === 0) {
                Swal.fire('Pilih data dulu', 'Belum ada data yang dicentang.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Tambah Kuota Cuti',
                html:
                    '<input id="swal-tahun" type="number" class="swal2-input" value="$tahunIni" placeholder="Tahun">' +
                    '<input id="swal-kuota" type="number" class="swal2-input" value="12" placeholder="Jumlah Kuota">',
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                preConfirm: () => {
                    const tahun = document.getElementById('swal-tahun').value;
                    const kuota = document.getElementById('swal-kuota').value;

                    if (!tahun || !kuota) {
                        Swal.showValidationMessage('Semua field wajib diisi');
                        return false;
                    }

                    return { tahun, kuota };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('bulkTambahKuotaCutiConfirmed', {
                        userIds: ids,
                        tahun: parseInt(result.value.tahun),
                        kuota: parseInt(result.value.kuota),
                    });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('bulkTambahKuotaCutiConfirmed')]
    public function bulkTambahKuotaCutiConfirmed(array $userIds, int $tahun, int $kuota): void
    {
        if (! Gate::allows('akses', 'Staff Edit')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }

        $berhasil = 0;

        foreach ($userIds as $userId) {
            KuotaCuti::updateOrCreate(
                [
                    'user_id' => $userId,
                    'tahun'   => $tahun,
                ],
                [
                    'kuota_dimiliki' => $kuota,
                ]
            );

            $berhasil++;
        }

        $this->dispatch('pg:eventRefresh')->to(self::class);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "{$berhasil} user berhasil ditambahkan kuota cuti tahun {$tahun}.",
        ]);
    }

    public function actions($row): array
    {
        $buttons = [];

        Gate::allows('akses', 'Staff Edit') && $buttons[] =
            Button::add('detail')
                ->slot('<i class="fas fa-eye"></i> Detail')
                ->tag('button')
                ->attributes([
                    'onclick' => "Livewire.navigate('".route('users.edit', $row->id)."')",
                    'class' => 'btn btn-primary'
                ]);

        Gate::allows('akses', 'Staff Hapus') && $buttons[] =
            Button::add('delete')
                ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
                ->class('btn btn-error')
                ->dispatch('delete', ['rowId' => $row->id]);

        return $buttons;
    }

    
    // public function actionRules($row): array
    // {
    //    return [
    //         // Hide button edit for ID 1
    //         // Rule::button('edit')
    //         //     ->when(fn($row) => $row->id === 1)
    //         //     ->hide(),
    //     ];
    // }

}
