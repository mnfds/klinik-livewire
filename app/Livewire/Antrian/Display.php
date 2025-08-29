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
        return view('livewire.antrian.display');
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
        
    //=====  PEMALSUAAN HARGA MAKAN MAKAN =====//
        // try {
        //     $connector = new WindowsPrintConnector("b21"); //nama printer
        //     $profile = CapabilityProfile::load("simple");
        //     $printer = new Printer($connector, $profile);

        //     $printer->setJustification(Printer::JUSTIFY_CENTER);

        //     // Heading Klinik
        //     $printer->setTextSize(1, 1);
        //     $printer->text("Day Avenue Cafe & Restaurant\n");
        //     // $printer->text("\n");

        //     $printer->setTextSize(1, 1);
        //     $printer->text("Jl. Gatot Subroto No.40\n");
        //     $printer->text("Banjarmasin\n");
        //     $printer->text("Wa : 0576 9530 5453\n");
        //     $printer->text("===============================\n");
            
        //     $printer->setJustification(Printer::JUSTIFY_RIGHT);
        //     $printer->text("26/08/2025 13:30\n");
        //     $printer->text("\n");

        //     $printer->setJustification(Printer::JUSTIFY_LEFT);
        //     $printer->text("Struk : 6381360W\n");
        //     $printer->text("Kasir : MISDA\n");
        //     $printer->text("--------------------------------\n");
        //     $printer->text("HAMBURG STEAK\n");
        //     $printer->text("11 x 150,000           1,650,000\n");
        //     $printer->text("--------------------------------\n");
        //     $printer->text("FRUIT COCKTAIL\n");
        //     $printer->text("11 x 30,000              330,000\n");
        //     $printer->text("--------------------------------\n");
        //     $printer->text("\n");
        //     $printer->setEmphasis(true); 
        //     $printer->text("Total Belanja          1,980,000\n");
        //     $printer->setEmphasis(false); 
        //     $printer->text("--------------------------------\n");
        //     $printer->text("\n");
        //     $printer->text("Tunai                  2,000,000\n");
        //     $printer->text("--------------------------------\n");
        //     $printer->text("\n");
        //     $printer->text("kembali                   20,000\n");
        //     $printer->setJustification(Printer::JUSTIFY_CENTER);
        //     $printer->text("Harga Sudah Termasuk PPN\n");
        //     $printer->text("\n");
        //     $printer->text("\n");
        //     $printer->text("\n");
        //     $printer->text("\n");
        //     $printer->text("Terima kasih\n");
        //     $printer->text("Atas Kunjungan Anda\n");
            
        //     // Potong kertas
        //     $printer->cut();

        //     $printer->close();
        // } catch (\Exception $e) {
        //     // Log error atau tampilkan pesan jika perlu
        //     $this->dispatch('toast', [
        //         'type' => 'success',
        //         'message' => $e->getMessage()
        //     ]);
        //     session()->flash('error', 'Printer error: ' . $e->getMessage());
        // }

        // $this->dispatch('toast', [
        //     'type' => 'success',
        //     'message' => 'Nomor Antrian Berhasil Dibuat'
        // ]);
    //===== PEMALSUAAN HARGA MAKAN MAKAN =====//
    }
}
