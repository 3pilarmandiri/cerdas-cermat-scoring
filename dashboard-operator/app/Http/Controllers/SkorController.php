<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\SkorHistory;
use Illuminate\Http\Request;
use App\Services\BroadcastService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SkorController extends Controller
{
    public function tambah(Request $request, BroadcastService $broadcast)
    {
        $kelompok = Kelompok::with('pertandingan')->findOrFail($request->kelompok_id);
        $kelompok->skorHistories()->create(['nilai' => $request->nilai]);
        $kelompok->increment('total_skor', $request->nilai);


        $pertandingan = $kelompok->pertandingan()->with('kelompoks')->first();

        // 🔥 kirim data terbaru
        $broadcast->send($pertandingan);

        // return response()->json(['message' => 'Skor berhasil ditambahkan']);
        return response()->json([
            'success' => true,
            'payload' => $kelompok->pertandingan
                ->kelompoks()
                ->pluck('total_skor', 'kode')
        ]);
    }

    public function history($kelompok_id)
    {
        $histories = SkorHistory::where('kelompok_id', $kelompok_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($row) => [
                'nilai' => $row->nilai,
                'waktu' => $row->created_at->format('d/m/Y H:i')
            ]);

        return response()->json([
            'success' => true,
            'items' => $histories
        ]);
    }
}
