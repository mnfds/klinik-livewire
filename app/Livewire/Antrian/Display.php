<?php

namespace App\Livewire\Antrian;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\PoliKlinik;
use Mike42\Escpos\Printer;
use App\Models\NomorAntrian;
use Illuminate\Support\Facades\Gate;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class Display extends Component
{
    public $poli;
    public $namaPengantri = null;
    public $poliId = null;

    public function mount()
    {
        $this->updateNomor();
    }

    public function updateNomor()
    {
        $this->poli = PoliKlinik::where('status', true)
            ->get()
            ->map(function ($poli) {
                $poli->nomor_terakhir = NomorAntrian::where('poli_id', $poli->id)
                    ->whereDate('created_at', now())
                    ->latest('nomor_antrian')
                    ->first()?->nomor_antrian ?? 0;
                return $poli;
            });
    }

    public function render()
    {
        if (! Gate::allows('akses', 'Ambil Nomor') && ! Gate::allows('akses', 'Daftarkan Nama')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
        return view('livewire.antrian.display');
    }

    public function setPoliId(int $id): void
    {
        $this->poliId = $id;
        $this->namaPengantri = null;          // reset the field each time
        $this->dispatch('poli-id-set');       // browser event caught by x-on in blade
    }

    public function addNomorAntrian($poliId)
    {
        $poli = PoliKlinik::findOrFail($poliId);

        // Ambil nomor antrian terakhir dari poli ini
        $last = NomorAntrian::where('poli_id', $poliId)
            ->orderBy('nomor_antrian', 'desc')
            ->whereDate('created_at', now())
            ->first();
        $nextNumber = $last ? $last->nomor_antrian + 1 : 1;

        // Simpan nomor antrian
        $antrian = NomorAntrian::create([
            'poli_id' => $poliId,
            'kode' => $poli->kode,
            'nomor_antrian' => $nextNumber,
        ]);
        // (Optional) Cetak menggunakan printer thermal

        try {
            $connector = new WindowsPrintConnector("b21"); //nama printer
            $profile = CapabilityProfile::load("simple");
            $printer = new Printer($connector, $profile);

            $printer->setJustification(Printer::JUSTIFY_CENTER);

            // Heading Klinik
            $printer->setTextSize(2, 1);
            $printer->text("Klinik Dokter L\n");
            $printer->text("\n");

            $printer->setTextSize(1, 1);
            $printer->text("Jl. Gatot Subroto No.88, Kuripan\n");
            $printer->text("Kec. Banjarmasin Timur, kota\n");
            $printer->text("Banjarmasin, Kalimantan Selatan\n");

            $printer->text("\n");
            $printer->text("Nomor Antrian\n");

            // Garis Atas
            $printer->setTextSize(2, 2);
            $printer->text("================\n");

            // Nomor Antrian
            $printer->setTextSize(4,4);
            $printer->text($antrian->kode . '-' . $antrian->nomor_antrian . "\n");

            // Garis Bawah
            $printer->setTextSize(2, 2);
            $printer->text("================\n");

            // Nama Poli
            $printer->setTextSize(1, 1);
            $printer->text($poli->nama_poli . "\n");

            // Tanggal & Jam
            $printer->text(Carbon::now('Asia/Makassar')->locale('id')->isoFormat('D MMMM Y') . "\n");
            $printer->text(Carbon::now('Asia/Makassar')->format('H:i:s') . " WITA\n");

            // Potong kertas
            $printer->cut();

            $printer->close();
        } catch (\Exception $e) {
            // Log error atau tampilkan pesan jika perlu
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => $e->getMessage()
            ]);
            session()->flash('error', 'Printer error: ' . $e->getMessage());
        }

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Nomor Antrian Berhasil Dibuat'
        ]);
    }

    public function addAntrianByName(){
        $poli = PoliKlinik::findOrFail($this->poliId);
        // Ambil nomor antrian terakhir dari poli ini
        $last = NomorAntrian::where('poli_id', $poli->id)
            ->orderBy('nomor_antrian', 'desc')
            ->whereDate('created_at', now())
            ->first();
        $nextNumber = $last ? $last->nomor_antrian + 1 : 1;

        // Simpan nomor antrian
        $antrian = NomorAntrian::create([
            'poli_id' => $poli->id,
            'kode' => $poli->kode,
            'nomor_antrian' => $nextNumber,
            'nama_pengantri' => $this->namaPengantri,
        ]);
        $this->reset('namaPengantri', 'poliId');
        $this->dispatch('closeshowNameInput');
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Antrian Berhasil Dibuat'
        ]);
    }
}
