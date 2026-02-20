<?php

use App\Models\Icd;
use App\Models\User;
use App\Models\Pasien;
use App\Models\KfaObat;
use App\Models\Pelayanan;
use App\Models\NomorAntrian;
use Illuminate\Http\Request;
use App\Models\ProdukDanObat;
use App\Livewire\Pasien\Detail;
use App\Livewire\Users\DataUsers;
use App\Livewire\Users\StoreUsers;
use App\Livewire\Users\UpdateUsers;
use Illuminate\Support\Facades\Route;
use App\Livewire\Bahan\Riwayat as BahanRiwayat;
use App\Livewire\Bahanbakubesar\Mutasi;
use App\Livewire\Barang\Riwayat as BarangRiwayat;
use App\Livewire\Produkdanobat\Mutasi\Riwayat;
use App\Models\MutasiProdukDanObat;

// Route::view('/', 'welcome');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::view('dashboard', 'dashboard')
    ->middleware('auth')
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware('auth')
    ->name('profile');

Route::middleware(['auth'])->group(function () {
    Route::get('/users', DataUsers::class)->name('users.data');
    Route::get('/users/create', StoreUsers::class)->name('users.create');
    // Route::get('/users/update/{user}', UpdateUsers::class)->name('users.edit');
    Route::get('/users/update/{user}', function (User $user) {
        return view('pengguna.update', compact('user'));
    })->name('users.edit');
    Route::get('/users/create', function () {
        return view('pengguna.store');
    })->name('users.create');

    // ====== DOKTER ====== //
    Route::view('/dokter', 'dokter.data')->name('dokter.data');
    Route::view('/dokter/create', 'dokter.create')->name('dokter.create');
    Route::get('/dokter/{id}/update', function ($id) { 
        return view('dokter.update', ['id' => $id]);
    })->name('dokter.update');
    Route::get('/dokter/{id}/detail', function ($id) {
        return view('dokter.detail', ['id' => $id]);
    })->name('dokter.detail');
    // ====== DOKTER ====== //

    // ====== JAM KERJA ====== //
    Route::view('/jam-kerja', 'jamkerja.data')->name('jamkerja.data');
    // ====== JAM KERJA ====== //

    // ====== POLIKLINIK ====== //
    Route::view('/poliklinik', 'poli.data')->name('poliklinik.data');
    // ====== POLIKLINIK ====== //

    // ====== ROLE & AKSES ====== //
    Route::view('/role-akses', 'role.data')->name('role-akses.data');
    // ====== ROLE & AKSES ====== //

    // ====== PRODUK & OBAT ====== //
    Route::view('/produk-obat', 'produkdanobat.data')->name('produk-obat.data');
    Route::get('/produk-obat/riwayat', Riwayat::class)->name('produk-obat.riwayat');
    // ====== PRODUK & OBAT ====== //

    // ====== PELAYANAN ====== //
    Route::view('/pelayanan', 'pelayanan.data')->name('pelayanan.data');
    // ====== PELAYANAN ====== //

    // ====== BUNDLING ====== //
    Route::view('/bundling', 'bundling.data')->name('bundling.data');
    // ====== BUNDLING ====== //

    // ====== INVENTORY BARANG ====== //
    Route::view('/peyimpanan-barang', 'barang.data')->name('barang.data');
    Route::get('/penyimpanan-barang/riwayat', BarangRiwayat::class)->name('barang.riwayat');

    Route::view('/peyimpanan-bahan-baku', 'bahanbaku.data')->name('bahanbaku.data');
    Route::get('/penyimpanan-bahan-baku/riwayat', BahanRiwayat::class)->name('bahanbaku.riwayat');
    // ====== INVENTORY BARANG ====== //

    // ====== ANTRIAN PASIEN ====== //
    Route::view('/antrian', 'antrian.data')->name('antrian.data');
    Route::view('/antrian/ambil-nomor', 'antrian.display')->name('antrian.display');
    // ====== ANTRIAN PASIEN ====== //

    // ====== PASIEN ====== //
    Route::view('/pasien', 'pasien.data')->name('pasien.data');
    Route::view('/pasien/create', 'pasien.create')->name('pasien.create');
    Route::get('/pasien/{id}/update', function ($id) { 
        return view('pasien.update', ['id' => $id]);
    })->name('pasien.update');
    Route::get('/pasien/{id}/detail', function ($id) {
        return view('pasien.detail', ['id' => $id]);
    })->name('pasien.detail');
    Route::get('/search/pasien', function (Request $request) {
        $search = $request->q;
        return \App\Models\Pasien::query()
            ->where('nama', 'like', "%$search%")
            ->orWhere('no_register', 'like', "%$search%")
            ->limit(10)
            ->get()
            ->map(fn($pasien) => [
                'id' => $pasien->id,
                'text' => $pasien->no_register . ' - ' . $pasien->nama,
            ]);
    });
    // ====== PASIEN ====== //

    // ====== PENDAFTARAN ====== //
    Route::view('/pendaftaran', 'pendaftaran.data')->name('pendaftaran.data');
    // Route::view('/pendaftaran/search', 'pendaftaran.search')->name('pendaftaran.search');
    Route::get('/pendaftaran/search', function (\Illuminate\Http\Request $request) {
        $antrianId = $request->query('id');
        $antrian = NomorAntrian::find($antrianId);

        return view('pendaftaran.search', compact('antrian'));
    })->name('pendaftaran.search');
    Route::get('/api/pasien/search', function (Request $request) {
        $search = $request->q;

        $results = Pasien::query()
            ->where('nama', 'like', '%' . $search . '%')
            ->orWhere('no_register', 'like', '%' . $search . '%')
            ->limit(10)
            ->get()
            ->map(function ($pasien) {
                return [
                    'id' => $pasien->id,
                    'text' => $pasien->nama,
                    'no_register' => $pasien->no_register,
                ];
            });

        return response()->json($results);
    })->name('api.pasien.search');
    Route::view('/pendaftaran/create', 'pendaftaran.create')->name('pendaftaran.create');
    // ====== PENDAFTARAN ====== //

    // ====== KAJIAN AWAL ====== //
    Route::view('/kajian-awal/create', 'kajian.create')->name('kajian.create');
    Route::get('/ajax/obat-kfa', function (Request $request) {
        $query = $request->get('q', '');
        return \App\Models\KfaObat::where('nama_obat_aktual', 'like', "%{$query}%")
            ->limit(20)
            ->get()
            ->map(fn ($obat) => [
                'id' => $obat->id,
                'text' => $obat->nama_obat_aktual . ' (' . $obat->bentuk_sediaan . ')',
            ]);
    });
    // ====== RIWAYAT KUNJUNGAN ATAU REKAM MEDIS PASIEN ====== //
    Route::view('/rekam-medis-pasien', 'rekammedis.data')->name('rekam-medis-pasien.data');
    Route::view('/rekam-medis-pasien/create', 'rekammedis.create')->name('rekam-medis-pasien.create');
    Route::view('/rekam-medis-pasien/detail', 'rekammedis.detail')->name('rekam-medis-pasien.detail');

    Route::get('/ajax/icd_10', function (Request $request) {
        $query = $request->get('q', '');

        return Icd::where('code', 'like', "%{$query}%")
            ->orWhere('name_id', 'like', "%{$query}%")
            ->orWhere('name_en', 'like', "%{$query}%")
            ->limit(20)
            ->get()
            ->map(fn ($icd) => [
                'code'    => $icd->code,
                'name_id' => $icd->name_id,
                'name_en' => $icd->name_en,
            ]);
    });
    // ajax get data KFA OBAT dan ProdukDanObat (internal)
    Route::get('/ajax/obat-kfa', function (Request $request) {
        $query = $request->get('q', '');

        // Ambil data dari KfaObat
        $kfaObat = KfaObat::where('nama_obat_aktual', 'like', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(fn ($obat) => [
                'id' => 'kfa_' . $obat->id, // prefix biar unik
                'text' => $obat->nama_obat_aktual,
                'source' => 'KFA'
            ]);

        // Ambil data dari ProdukDanObat (internal)
        $produkObat = ProdukDanObat::where('nama_dagang', 'like', "%{$query}%")
            ->orWhere('kode', 'like', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(fn ($produk) => [
                'id' => 'produk_' . $produk->id, // prefix biar unik
                'text' => $produk->nama_dagang . ' - ' . $produk->sediaan ,
                'source' => 'INTERNAL'
            ]);

        // Gabungkan hasil
        $merged = $kfaObat->concat($produkObat);

        return response()->json($merged->values());
    });

    Route::get('/ajax/produk', function (Request $request) {
        $query = $request->get('q', '');
        return \App\Models\ProdukDanObat::where('nama_dagang', 'like', "%{$query}%")
            ->orderBy('nama_dagang')
            ->take(20)
            ->get()
            ->map(fn($produk) => [
                'id' => $produk->id,
                'text' => $produk->nama_dagang,
                'harga' => $produk->harga_dasar,
                'potongan' => $produk->potongan,
                'diskon' => $produk->diskon,
            ]);
    });

    Route::get('/ajax/treatment', function (Request $request) {
        $query = $request->get('q', '');
        return \App\Models\Treatment::where('nama_treatment', 'like', "%{$query}%")
            ->limit(20)
            ->get()
            ->map(fn ($t) => [
                'id' => $t->id,
                'text' => $t->nama_treatment,
                'harga' => $t->harga_treatment,
                'potongan' => $t->potongan,
                'diskon' => $t->diskon,
            ]);
    });

    Route::get('/ajax/bundling', function (Request $request) {
        $query = $request->get('q', '');
        return \App\Models\Bundling::where('nama', 'like', "%{$query}%")
            ->limit(20)
            ->get()
            ->map(fn ($b) => [
                'id' => $b->id,
                'text' => $b->nama,
                'harga' => $b->harga, // supaya bisa dipakai di calcHargaAsli
                'potongan' => $b->potongan, 
                'diskon' => $b->diskon, 
            ]);
    });

    Route::get('/ajax/layanan', function (Request $request) {
        $query = $request->get('q', '');
        return Pelayanan::where('nama_pelayanan', 'like', "%{$query}%")
            ->limit(20)
            ->get()
            ->map(fn ($layanan) => [
                'id' => $layanan->id,
                'nama' => $layanan->nama_pelayanan,
            ]);
    })->name('ajax.layanan');

    // ====== RIWAYAT KUNJUNGAN ATAU REKAM MEDIS PASIEN ====== //

    // ====== RESEP OBAT ====== //
    Route::view('/resep', 'resep.data')->name('resep.data');
    Route::view('/resep-pasien', 'resep.detail')->name('resep.detail');
    Route::view('/resep-pasien-tebus-obat', 'resep.tebus')->name('resep.tebus');
    Route::get('/search-produk-obat', function (\Illuminate\Http\Request $request) {
        $search = $request->q;

        $obat = \App\Models\ProdukDanObat::query()
            ->when($search, function ($query, $search) {
                $query->where('nama_dagang', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%");
            })
            ->select('id', 'nama_dagang', 'sediaan', 'harga_bersih', 'stok', 'diskon', 'potongan')
            ->limit(20)
            ->get();

        return response()->json($obat->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => "{$item->nama_dagang} {$item->sediaan} - Rp " . number_format($item->harga_bersih) . " sisa: ({$item->stok})",
                'satuan' => $item->sediaan,
                'harga' => $item->harga_bersih,
            ];
        }));
    })->name('search.ProdukObat');

    // ====== RESEP OBAT ====== //


    // ====== KASIR APOTIK ====== //

    Route::view('/apotik', 'apotik.kasir')->name('apotik.kasir');
    Route::view('/apotik/create', 'apotik.create')->name('apotik.create');

    Route::get('/apotik/{id}/update', function ($id) { 
        return view('apotik.update', ['id' => $id]);
    })->name('apotik.update');

    Route::get('/apotik/{id}/detail', function ($id) {
        return view('apotik.detail', ['id' => $id]);
    })->name('apotik.detail');
    // ====== KASIR APOTIK ====== //
    
    // ====== KASIR KLINIK ====== //
    Route::view('/transaksi/klinik', 'transaksi.data')->name('transaksi.kasir');
    Route::get('/transaksi/klinik/{id}/detail', function ($id) {
        return view('transaksi.detail', ['id' => $id]);
    })->name('transaksi.detail');
    Route::get('/transaksi/klinik/{id}/mutasi', function ($id) {
        return view('transaksi.mutasi', ['id' => $id]);
    })->name('transaksi.mutasi');
    // ====== KASIR KLINIK ====== //
    
    // ====== RESERVASI ====== //
    Route::view('/reservasi', 'reservasi.data')->name('reservasi.data');
    // ====== RESERVASI ====== //

    // ====== RESERVASI ====== //
    Route::view('/tindak-lanjut', 'tindaklanjut.data')->name('tindaklanjut.data');
    Route::view('/tindak-lanjut/detail', 'tindaklanjut.detail')->name('tindaklanjut.detail');
    // ====== RESERVASI ====== //

    // ====== SATU SEHAT CONFIGURATION ====== //
    Route::view('/satusehat/praktisi', 'satusehat.praktisi.data')->name('satusehat.praktisi.data');
    Route::view('/satusehat/lokasi', 'satusehat.lokasi.data')->name('satusehat.lokasi.data');
    Route::view('/satusehat/organisasi', 'satusehat.organisasi.data')->name('satusehat.organisasi.data');
    // ====== SATU SEHAT CONFIGURATION ====== //

    // ====== AJAX INDONESIA REGION ========= //
    Route::get('/wilayah/kabupaten/{prov}', function ($prov) {
        return App\Models\Regency::where('province_id', $prov)->get();
    });

    Route::get('/wilayah/kecamatan/{kab}', function ($kab) {
        return App\Models\District::where('regency_id', $kab)->get();
    });

    Route::get('/wilayah/kelurahan/{kec}', function ($kec) {
        return App\Models\Village::where('district_id', $kec)->get();
    });
    // ====== AJAX INDONESIA REGION ========= //

    // ====== LAPORAN ARUS KAS ====== //
    Route::view('/aruskas', 'aruskas.data')->name('aruskas.data');    
    // ====== LAPORAN ARUS KAS ====== //

    // ====== LAPORAN KUNJUNGAN ====== //
    Route::view('/kunjungan', 'kunjungan.data')->name('kunjungan.data');    
    // ====== LAPORAN KUNJUNGAN ====== //

    // ====== LAPORAN KINERJA ====== //
    Route::view('/kinerja', 'kinerja.data')->name('kinerja.data');    
    // ====== LAPORAN KINERJA ====== //

    // ====== DOKUMEN ====== //
    Route::view('/dokumen', 'dokumen.data')->name('dokumen.data');    
    // ====== DOKUMEN ====== //

    // ====== PENGAJUAN UANG KELUAR ====== //
    Route::view('/uangkeluar', 'uangkeluar.data')->name('uangkeluar.data');    
    // ====== PENGAJUAN UANG KELUAR ====== //

    // ====== PENGAJUAN IZIN KELUAR ====== //
    Route::view('/izinkeluar', 'izinkeluar.data')->name('izinkeluar.data');    
    // ====== PENGAJUAN IZIN KELUAR ====== //

    // ====== PENGAJUAN LEMBUR ====== //
    Route::view('/lembur', 'lembur.data')->name('lembur.data');    
    // ====== PENGAJUAN LEMBUR ====== //

    // ====== INVENTARIS ====== //
    Route::view('/inventaris', 'inventaris.data')->name('inventaris.data');    
    // ====== INVENTARIS ====== //
});


require __DIR__.'/auth.php';
