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
        if (! Gate::allows('akses', 'Ambil Nomor')) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Anda tidak memiliki akses.',
            ]);
            $this->redirectRoute('dashboard');
        }
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


        //=====  PEMALSUAAN STRUK PEGADAIAN MBA LILIE =====//

            // $printer->setJustification(Printer::JUSTIFY_LEFT);
            // $printer->setTextSize(1,2);
            // $printer->setEmphasis(true);
            // $printer->text("PT PEGADAIAN (PERSERO)          \n");
            // $printer->text("CABANG CP TELUK DALAM           \n");
            // $printer->setEmphasis(false);
            // $printer->text("\n");

            // $printer->setJustification(Printer::JUSTIFY_CENTER);
            // $printer->setTextSize(1,1);
            // $printer->text("NOTA TRANSAKSI TUNAI \n");
            // $printer->text("PELUNASAN GADAI KCA UMI \n");
            // $printer->text("────────────────────────────────\n");
            // $printer->setJustification(Printer::JUSTIFY_LEFT);
            // $printer->text("Tanggal            :02-10-2025\n");
            // $printer->text("No Transaksi       :168956632186\n");
            // $printer->text("                    510995\n");
            // $printer->text("No Kredit          :109552301002\n");
            // $printer->text("                    6867\n");
            // $printer->text("\n");
            // $printer->text("Transaksi Terakhir :12-9-2025\n");
            // $printer->text("Jumlah Hari/Tarif  :45 / 3.6%\n");
            // $printer->text("Jumlah Hari Real   :36\n");
            // $printer->setEmphasis(true);  // aktifkan bold
            // $printer->text("Perhihungan Sewa\n");
            // $printer->setEmphasis(false);  // non aktif bold
            // $printer->text("Uang Pinjaman      :Rp 4,300,000\n");
            // $printer->text("Sewa Modal         :Rp   154,000\n");
            // $printer->text("Diskon SM UMI      :Rp         0\n");
            // $printer->text("Diskon SM UMI      ─────────────\n");
            // $printer->text("Kewajiban Bayar    :Rp 4,454,800\n");
            // $printer->text("Jumlah Diterima    :Rp 4,500,000\n");
            // $printer->text("                   ─────────────\n");
            // $printer->text("Uang Kembali       :Rp    45,200\n");
            // $printer->text("Bank               :           ─\n");
            // $printer->text("Approval code      :           ─\n");
            // $printer->text("\n");
            // $printer->setEmphasis(true);
            // $printer->text("Barang Jaminan                  \n");
            // $printer->setEmphasis(false);
            // $printer->text("SATU CINCIN MT HIJAU DITAKSIR   \n");
            // $printer->text("PERHIASAN EMAS 23 KARAT BERAT   \n");
            // $printer->text("8.8/5.5 GRAM ***   \n");
            // $printer->text("\n");
            // $printer->text("\n");
            // $printer->text("Barang Jaminan dapat diambil di:\n");
            // $printer->setEmphasis(true);
            // $printer->setTextSize(1,2);
            // $printer->text("CABANG CP TELUK DALAM\n");
            // $printer->setTextSize(1,1);
            // $printer->setEmphasis(false);
            // $printer->setJustification(Printer::JUSTIFY_CENTER);
            // $printer->text("Sampai dengan tanggal 09-10-2025\n");
            // $printer->text("apabila melampaui tanggal\n");
            // $printer->text("09-10-2025, maka nasabah akan\n");
            // $printer->text("dikenakan biaya jasa titipan\n");
            // $printer->text("sebesar Rp 0 per periode atas\n");
            // $printer->text("barang jaminan yang belum\n");
            // $printer->text("belum diambil\n");
            // $printer->text("\n");
            // $printer->text("Terimakasih atas Kepercayaan\n");
            // $printer->text("anda\n");
            // $printer->text("\n");
            // $printer->setJustification(Printer::JUSTIFY_LEFT);
            // $printer->text("Nama Petugas        Nama Nasabah\n");
            // $printer->text("\n");
            // $printer->text("\n");
            // $printer->text(" JULIA DEWI            ARBIAN  \n");
            // $printer->text("  BMS03127\n");
            // $printer->text("Thur Oct 02 10:58:41 WIB 2025    \n");
            // $printer->text("\n");
            // $printer->text("\n");
            // $printer->text("\n");
            // $printer->setJustification(Printer::JUSTIFY_CENTER);
            // $printer->text("Dapatkan informasi jumlah\n");
            // $printer->text("dan cara redeem poin\n");
            // $printer->text("melalui poin.pegadaian.co.id,\n");
            // $printer->text("Pegadaian Digital dan 1500569\n");
            // $printer->feed(1); // kasih jarak 3 baris ke bawah
            // $printer->cut();
        //=====  PEMALSUAAN STRUK PEGADAIAN MBA LILIE =====//


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
