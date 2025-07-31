<?php

namespace App\View\Components\kajianawal;

use App\Models\KfaObat;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class datakesehatan extends Component
{
    public array $listPenyakit;
    public array $listObat;
    public array $listAlergi;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // Data dummy
        $this->listPenyakit = [
            'Alergi Panadol','Anemia','Appendicitis','Arthritis','Asma',
            'Asma (Terkontrol)','Asam Urat','Ayan','Batuk','Batuk, Gatal Tenggorok','Batuk, Pilek',
            'Batu Ginjal','Batu Ginjal Tahun 2020','Cancer Payudara Post Operasi Pengangkatan 1 Thn',
            'Cacar Air','Caries Molar 3','Cemas','Cervicalgia','Cholesterol','Alergi Amoxilin','Covid',
            'Diabetes Melitus','Diabetes Melitus Type 2','Diabetes','Diare','Dislipidemia','Dm',
            'Dyspepsia','Dyspesia','Flu','Gangangguan Pencernaan (Maag)','Gatak','Genitic','Glukosa',
            'Hemoroid','Hepatitis','Hiperkolesterolemia','Hipertensi','Hypertensi','Hypertention',
            'Hypotensi','Isk','Ischaemia Heart Disease','Jantung','Kanker','Kanker Usus Besar',
            'Keloid','Kencing Manis','Kista Indung Telur','Kolesterol','Maag,','Meriang','Migrain',
            'Neuritis','Nefrolithiasis','Obesitas','Operasi 1X Di Rs','Osteoarthritis',
            'Oesteoartritis','Parkinson','Pemasangan Ring Jantung','Pembengkakan Jantung',
            'Pjk','Post Amputasi','Post Debridement','Post Operasi Acl','Pusing Baru',
            'Readang Paru','Rematoid Artritis','Rhinitis','Sakit Gigi Terus Menerus Akibat Tidak Gosok Gigi',
            'Sakit Maag','Sakit Ulu Hati','Sinusitis','Skizofrenia','Stroke','Suspect Virus',
            'Tbc Kelenjar Tahun 2009 Dan 2017','Tb Paru','Talasemia','Thalasemia','Tifoid',
            'Tifus','Tinea','Tipes','Tonsilitis','Tuberculosis','Vertigo',
        ];


        $this->listObat = [
            ''
        ];


        $this->listAlergi = [
            'DINGIN','Seledri','Debu','Amoxillin','Udang','Soba','Telur','Ikan','Buah',
            'Bawang Putih','Gandum','Jagung','Susu','Moster','Kacang','Daging Unggas',
            'Daging Merah','Beras','Wijen','Kerang','Kedelai','Sulfit','Tartrazine',
            'Kacang Pohon','Gandum','Serbuk Sari','Kucing','Anjing','Sengatan Serangga',
            'Cetakan','Parfum','Kosmetik','Getah','Air','Rangsangan Dingin','Tungau Debu Rumah',
            'Nikel','Emas','Kromium','Kobalt Klorida','Formaldehida','Pengembang Fotografi',
            'Riwaat Sinusitis','Susu Dan Telur','Toluenesulfonamide Formaldehyde',
            'Glyceryl Monothioglycolate','Paraphenylenediamine','Getah',
            'Dimethylaminopropylamine (Dmapa)','Fungisida',
        ];

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.kajianawal.datakesehatan', [
            'listPenyakit' => $this->listPenyakit,
        ]);
    }
}
