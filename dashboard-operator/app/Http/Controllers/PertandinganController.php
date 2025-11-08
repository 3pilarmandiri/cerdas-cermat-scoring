<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\Pertandingan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class PertandinganController extends Controller
{
    public function index()
    {
        $pertandingans = Pertandingan::with('kelompoks')->latest()->get();
        return view('pertandingan.index', compact('pertandingans'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama' => 'required']);
        $pertandingan = Pertandingan::create($request->only('nama', 'keterangan'));

        // otomatis buat kelompok A-D
        foreach (['A', 'B', 'C', 'D'] as $kode) {
            Kelompok::create([
                'pertandingan_id' => $pertandingan->id,
                'kode' => $kode,
                'nama_peserta' => "Kelompok $kode",
                'total_skor' => 0,
            ]);
        }

        return redirect()->route('pertandingan.index');
    }

    public function mulai($id)
    {
        $pertandingan = Pertandingan::with(['kelompoks' => function ($q) {
            $q->orderBy('kode', 'asc');
        }])->findOrFail($id);


        // === Kirim broadcast awal ke WS_URL ===
        $payload = $pertandingan->kelompoks->pluck('total_skor', 'kode')->toArray();
        $wsUrl = env('WS_URL');

        if ($wsUrl && !empty($payload)) {
            try {
                Http::timeout(2)->post($wsUrl, $payload);
                Log::info('📡 Broadcast awal dikirim ke ' . $wsUrl, $payload);
            } catch (\Throwable $e) {
                // Jangan hentikan proses jika gagal
                Log::warning('⚠️ Broadcast awal gagal ke ' . $wsUrl . ': ' . $e->getMessage());
            }
        }


        return view('pertandingan.mulai', compact('pertandingan'));
    }
}
