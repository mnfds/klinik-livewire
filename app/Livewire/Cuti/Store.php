<?php

namespace App\Livewire\Cuti;

use App\Models\Pengajuancuti;
use App\Models\Pengajuancutitanggal;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class Store extends Component   
{
    public int $activeTab = 0;

    public array $items = [
        ['user_id' => '', 'tanggal_mulai' => '', 'tanggal_selesai' => '', 'alasan' => ''],
    ];

    public $users = [];

    public function mount(): void
    {
        $this->users = User::with(['biodata', 'dokter'])->get()
            ->map(fn ($user) => [
                'id' => $user->id,
                'nama' => $user->biodata?->nama_lengkap
                    ?? $user->dokter?->nama_dokter
                    ?? '-',])
        ->toArray();
    }

    public function addTab(): void
    {
        $this->items[] = ['user_id' => '', 'tanggal_mulai' => '', 'tanggal_selesai' => '', 'alasan' => ''];
        $this->activeTab = count($this->items) - 1;
    }

    public function removeTab(int $index): void
    {
        if (count($this->items) <= 1) return;

        array_splice($this->items, $index, 1);

        if ($this->activeTab >= count($this->items)) {
            $this->activeTab = count($this->items) - 1;
        }
    }

    public function store()
    {
        $this->validate([
            'items.*.user_id' => 'required|exists:users,id',
            'items.*.tanggal_mulai' => 'required|date',
            'items.*.tanggal_selesai' => 'required|date|after_or_equal:items.*.tanggal_mulai',
            'items.*.alasan' => 'required|string|max:500',
        ]);

        // expand tiap tab jadi daftar tanggal harian, sekaligus cek bentrok
        // sebelum ada satupun yang disimpan ke DB
        $tanggalPerItem = [];

        foreach ($this->items as $i => $item) {
            $periode = Carbon::parse($item['tanggal_mulai'])->toPeriod($item['tanggal_selesai']);
            $tanggals = collect($periode)->map(fn ($t) => $t->format('Y-m-d'))->values();

            // bentrok dengan tab lain untuk user yang sama dalam submit ini
            foreach ($tanggalPerItem as $j => $lain) {
                if ($lain['user_id'] == $item['user_id'] && $tanggals->intersect($lain['tanggals'])->isNotEmpty()) {
                    $this->addError("items.$i.tanggal_mulai", 'Tanggal bentrok dengan Form '.($j + 1).'.');
                    $this->activeTab = $i;
                    return;
                }
            }

            // bentrok dengan pengajuan lain yang masih aktif di DB
            $bentrokDb = Pengajuancutitanggal::whereIn('tanggal', $tanggals)
                ->whereHas('pengajuanCuti', fn ($q) => $q->where('user_id', $item['user_id'])
                    ->whereIn('status', ['diajukan', 'disetujui']))
                ->exists();

            if ($bentrokDb) {
                $this->addError("items.$i.tanggal_mulai", 'Terdapat tanggal yang sudah diajukan/disetujui sebelumnya untuk karyawan ini.');
                $this->activeTab = $i;
                return;
            }

            $tanggalPerItem[$i] = ['user_id' => $item['user_id'], 'tanggals' => $tanggals];
        }

        DB::transaction(function () use ($tanggalPerItem) {
            foreach ($this->items as $i => $item) {
                $pengajuan = Pengajuancuti::create([
                    'user_id' => $item['user_id'],
                    'alasan' => $item['alasan'],
                    'status' => 'diajukan',
                ]);

                $pengajuan->tanggals()->createMany(
                    $tanggalPerItem[$i]['tanggals']->map(fn ($t) => ['tanggal' => $t])->toArray()
                );
            }
        });

        $this->reset(['items', 'activeTab']);
        $this->items = [['user_id' => '', 'tanggal_mulai' => '', 'tanggal_selesai' => '', 'alasan' => '']];
        $this->reset();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Pengajuan Cuti Berhasil Disimpan'
        ]);
        $this->dispatch('closestoreModalCuti');
        return redirect()->route('cuti.data');
    }

    public function render()
    {
        return view('livewire.cuti.store');
    }
}
