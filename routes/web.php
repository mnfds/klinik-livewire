<?php

use App\Models\User;
use App\Models\Pasien;
use Illuminate\Http\Request;
use App\Livewire\Pasien\Detail;
use App\Livewire\Barang\Riwayat;
use App\Livewire\Users\DataUsers;
use App\Livewire\Users\StoreUsers;
use App\Livewire\Users\UpdateUsers;
use Illuminate\Support\Facades\Route;
use App\Livewire\JamKerja\DataJamKerja;
use App\Models\NomorAntrian;

// Route::view('/', 'welcome');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
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
    // ====== PRODUK & OBAT ====== //

    // ====== PELAYANAN ====== //
    Route::view('/pelayanan', 'pelayanan.data')->name('pelayanan.data');
    // ====== PELAYANAN ====== //

    // ====== PELAYANAN ====== //
    Route::view('/bundling', 'bundling.data')->name('bundling.data');
    // ====== PELAYANAN ====== //

    // ====== INVENTORY BARANG ====== //
    Route::view('/peyimpanan-barang', 'barang.data')->name('barang.data');
    Route::get('/penyimpanan-barang/riwayat', Riwayat::class)->name('barang.riwayat');
    // ====== INVENTORY BARANG ====== //

    // ====== ANTRIAN PASIEN ====== //
    Route::view('/antrian', 'antrian.data')->name('antrian.data');
    Route::view('/antrian/ambil-nomor', 'antrian.display')->name('antrian.display');
    Route::get('/penyimpanan-barang/riwayat', Riwayat::class)->name('barang.riwayat');
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
    // ====== KAJIAN AWAL ====== //

});


require __DIR__.'/auth.php';
