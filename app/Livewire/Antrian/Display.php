<?php

namespace App\Livewire\Antrian;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\PoliKlinik;
use Mike42\Escpos\Printer;
use App\Models\NomorAntrian;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class Display extends Component
{
    public $poli;

    public function mount()
    {
        $this->poli = PoliKlinik::where('status', true)->get();
    }

    public function render()
    {
        return view('livewire.antrian.display');
    }

    public function addNomorAntrian($poliId)
    {
        $poli = PoliKlinik::findOrFail($poliId);

        // Ambil nomor antrian terakhir dari poli ini
        $last = NomorAntrian::where('poli_id', $poliId)->orderBy('nomor_antrian', 'desc')->first();
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
}
