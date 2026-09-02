<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\PermintaanReservasiController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('reservasi')->group(function () {
    Route::get('/poliklinik', [PermintaanReservasiController::class, 'poliklinik']);
    Route::get('/dokter', [PermintaanReservasiController::class, 'dokter']); // ?poli_id=1
    Route::post('/', [PermintaanReservasiController::class, 'store']);
});