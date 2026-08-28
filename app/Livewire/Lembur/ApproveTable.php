<?php

namespace App\Livewire\Lembur;

use App\Models\Lembur;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class ApproveTable extends PowerGridComponent
{
    public string $tableName = 'approve-table-sncfmy-table';

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
        return Lembur::with([
            'user',
            'user.biodata',
            'user.absen',
            'user.jadwal.jamkerja',
        ])
            ->where('status', 'pending')
            ->latest();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('#')
            ->add('tanggal_izin',fn ($row) => \Carbon\Carbon::parse($row->tanggal_lembur)->format('d M Y'))

            ->add('user_id')
            ->add('perkiraan_durasi', function ($row) {
                $absen = $row->user->absen->first(function ($absen) use ($row) {
                    return Carbon::parse($absen->tanggal_absen)->isSameDay(
                        Carbon::parse($row->tanggal_lembur)
                    );
                });

                // Belum ada jam pulang
                if (!$absen || !$absen->jam_pulang) {
                    return $row->perkiraan_durasi . ' Jam';
                }

                $jadwal = $row->user->jadwal->first(function ($jadwal) use ($row) {
                    return Carbon::parse($jadwal->tanggal)->isSameDay(
                        Carbon::parse($row->tanggal_lembur)
                    );
                });

                // Belum ada jadwal / jam kerja
                if (!$jadwal || !$jadwal->jamkerja) {
                    return $row->perkiraan_durasi . ' Jam';
                }

                $jamKerjaSelesai = Carbon::parse(
                    $jadwal->jamkerja->jam_selesai
                );

                $jamPulang = Carbon::parse(
                    $absen->jam_pulang
                );

                $selisihMenit = $jamKerjaSelesai->diffInMinutes(
                    $jamPulang,
                    false
                );

                // Ada overtime
                if ($selisihMenit > 0) {
                    $overtimeJam = intdiv($selisihMenit, 60);
                    $overtimeMenit = $selisihMenit % 60;

                    return 'Perkiraan Lembur: ' . $row->perkiraan_durasi . ' Jam <br>'
                        . ' <span class="text-success font-medium">'
                        . 'Overtime: ' . str_pad($overtimeJam, 2, '0', STR_PAD_LEFT)
                        . ' Jam, '
                        . str_pad($overtimeMenit, 2, '0', STR_PAD_LEFT)
                        . ' Menit'
                        . '</span>';
                }

                return $row->perkiraan_durasi . ' Jam';
            })
            ->add('nama_dan_jam', function ($row) {
                return strtoupper($row->user->biodata->nama_lengkap ?? $row->user->dokter->nama_dokter) .
                 '<br><span class="text-sm text-gray-500">' . \Carbon\Carbon::parse($row->tanggal_lembur)->format('d M Y') . ', </span>' .
                 '<br><span class="text-sm text-gray-500">' . $row->jam_mulai . '</span>';
            })

            ->add('keperluan');
    }

    public function columns(): array
    {
        return [
            Column::make('#', '')->index(),

            Column::make('Nama', 'user_id')->searchable()->hidden(),
            Column::make('Karyawan Terkait', 'nama_dan_jam'),
            Column::make('Waktu Lembur', 'perkiraan_durasi'),
            
            Column::make('Keperluan', 'keperluan'),
            
            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(Lembur $row): array
    {
        $aprroveTable = [];

        Gate::allows('akses', 'Persetujuan Ajuan Lembur') && $aprroveTable[] =
        Button::add('setujui')  
        ->slot('<i class="fa-solid fa-circle-check"></i> Setujui')
        ->attributes([
            'class' => 'btn btn-success btn-sm'
            ])
        ->dispatch('setujui', ['rowId' => $row->id]);
        
        Gate::allows('akses', 'Persetujuan Ajuan Lembur') && $aprroveTable[] =
        Button::add('tolak')  
            ->slot('<i class="fa-solid fa-circle-xmark"></i> Tolak')
            ->attributes([
                'class' => 'btn btn-warning btn-sm'
            ])
        ->dispatch('tolak', ['rowId' => $row->id]);

        // Gate::allows('akses', 'Pengajuan Lembur Selesai') && $aprroveTable[] =
        // Button::add('selesai')  
        // ->slot('<i class="fa-solid fa-circle-check"></i> Selesai')
        // ->attributes([
        //     'class' => 'btn btn-success btn-sm'
        //     ])
        // ->dispatch('selesai', ['rowId' => $row->id]);

        Gate::allows('akses', 'Pengajuan Lembur Edit') && $aprroveTable[] =
        Button::add('updateApproveLembur')  
            ->slot('<i class="fa-solid fa-pen-clip"></i> Edit')
            ->attributes([
                'onclick' => 'modalApproveUpdate.showModal()',
                'class' => 'btn btn-primary btn-sm'
            ])
        ->dispatchTo('lembur.update', 'getApproveLembur', ['rowId' => $row->id]);

        Gate::allows('akses', 'Pengajuan Lembur Hapus') && $aprroveTable[] =
        Button::add('deleteApproveLembur')
            ->slot('<i class="fa-solid fa-eraser"></i> Hapus')
            ->class('btn btn-error btn-sm')
        ->dispatch('modalDeleteApprove', ['rowId' => $row->id]);

        return $aprroveTable;
    }

    #[\Livewire\Attributes\On('selesai')]
    public function selesai($rowId)
    {
        if (! Gate::allows('akses', 'Pengajuan Lembur Selesai')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        Lembur::where('id', $rowId)->update([
            'status' => 'selesai',
            'jam_selesai' => now()->format('H:i'),
        ]);
        $this->dispatch('pg:eventRefresh');
        $this->dispatch('refresh-HistoryTable');
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data Lembur Telah Berhasil Diperbarui',
        ]);
    }

    #[\Livewire\Attributes\On('setujui')]
    public function setujui($rowId)
    {
        if (! Gate::allows('akses', 'Persetujuan Ajuan Lembur')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        Lembur::where('id', $rowId)->update([
            'status' => 'disetujui',
            'disetujui_oleh' => auth()->id(),
        ]);
        $this->dispatch('pg:eventRefresh');
        $this->dispatch('refresh-ApproveTable');
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Pengajuan Telah Berhasil Disetujui',
        ]);
    }

    #[\Livewire\Attributes\On('tolak')]
    public function tolak($rowId)
    {
        if (! Gate::allows('akses', 'Persetujuan Ajuan Lembur')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        Lembur::where('id', $rowId)->update([
            'status' => 'ditolak',
            'disetujui_oleh' => auth()->id(),
        ]);
        $this->dispatch('pg:eventRefresh');
        $this->dispatch('refresh-HistoryTable');
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Pengajuan Telah Berhasil Ditolakkk',
        ]);
    }

    #[\Livewire\Attributes\On('modalDeleteApprove')]
    public function modalDeleteApprove($rowId): void
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
                    Livewire.dispatch('konfirmasiDeleteApprove', { rowId: $rowId });
                }
            });
        JS);
    }

    #[\Livewire\Attributes\On('konfirmasiDeleteApprove')]
    public function konfirmasiDeleteApprove($rowId): void
    {
        if (! Gate::allows('akses', 'Pengajuan Lembur Hapus')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            return;
        }
        Lembur::findOrFail($rowId)->delete();

        $this->dispatch('pg:eventRefresh')->to(self::class); // refresh PowerGrid

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Data berhasil dihapus.',
        ]);
    }

    #[\Livewire\Attributes\On('refresh-ApproveTable')]
    public function refreshApprove()
    {
        $this->dispatch('pg:eventRefresh');
    }

    public function actionRules($row): array
    {
        return [
            Rule::button('setujui')
                ->when(function ($row) {
                    $absen = $row->user->absen->first(function ($absen) use ($row) {
                        return Carbon::parse($absen->tanggal_absen)
                            ->isSameDay(Carbon::parse($row->tanggal_lembur));
                    });

                    return !$absen || !$absen->jam_pulang;
                })
                ->hide(),
        ];
    }
}
