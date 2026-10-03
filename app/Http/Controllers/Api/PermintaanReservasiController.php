<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DokterPoli;
use App\Models\PermintaanReservasi;
use App\Models\PoliKlinik;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermintaanReservasiController extends Controller
{
    public function poliklinik(): JsonResponse
    {
        $data = PoliKlinik::where('status', true)
            ->select('id', 'nama_poli')
            ->orderBy('nama_poli')
            ->get();

        return response()->json(['data' => $data]);
    }

    public function dokter(Request $request): JsonResponse
    {
        $request->validate([
            'poli_id' => ['required', 'exists:poli_kliniks,id'],
        ]);

        $data = DokterPoli::with('dokter:id,nama_dokter')
            ->where('poli_id', $request->poli_id)
            ->get()
            ->pluck('dokter')
            ->filter() // buang null kalau ada dokter yang sudah terhapus tapi pivot masih ada
            ->values();

        return response()->json(['data' => $data]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'no_telp' => ['required', 'string', 'max:20'],
            'nik' => ['nullable', 'digits:16'],
            'no_register' => ['nullable', 'string', 'max:255'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'poli_id' => ['required', 'exists:poli_kliniks,id'],
            'dokter_id' => ['nullable',Rule::exists(DokterPoli::class, 'dokter_id')->where('poli_id', $request->poli_id),],
            'tanggal_reservasi' => ['required', 'date', 'after_or_equal:today', 'before_or_equal:' . now()->addMonths(2)->toDateString()],
            'jam_reservasi' => ['nullable', 'date_format:H:i'],
            'pasien_baru' => ['required', 'boolean'],
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'no_telp.required' => 'Nomor telepon wajib diisi.',
            'poli_id.required' => 'Poliklinik wajib dipilih.',
            'poli_id.exists' => 'Poliklinik tidak ditemukan.',
            'dokter_id.exists' => 'Dokter tidak ditemukan.',
            'tanggal_reservasi.after_or_equal' => 'Tanggal reservasi tidak boleh di masa lalu.',
        ]);

        PermintaanReservasi::create($validated);

        return response()->json([
            'message' => 'Permintaan reservasi berhasil dikirim.',
        ], 201);
    }
}