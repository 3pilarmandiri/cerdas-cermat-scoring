<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\SkorHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SkorController extends Controller
{
    public function tambah(Request $request)
    {
        $request->validate([
            'kelompok_id' => 'required|uuid',
            'nilai' => 'required|integer',
        ]);

        $kelompok = Kelompok::findOrFail($request->kelompok_id);

        SkorHistory::create([
            'kelompok_id' => $kelompok->id,
            'nilai' => $request->nilai,
        ]);

        $kelompok->increment('total_skor', $request->nilai);

        // Kirim hasil ke endpoint broadcast
        $payload = $kelompok->pertandingan->kelompoks->pluck('total_skor', 'kode')->toArray();

        $wsUrl = env('WS_URL', null);

        if ($wsUrl) {
            try {
                Http::timeout(2)->post($wsUrl, $payload);
            } catch (\Throwable $e) {
                // Hanya tulis ke log, tidak hentikan proses
                Log::warning('Broadcast gagal ke ' . $wsUrl . ': ' . $e->getMessage());
            }
        }

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
