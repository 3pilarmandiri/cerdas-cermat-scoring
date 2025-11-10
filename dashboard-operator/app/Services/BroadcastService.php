<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Models\Pertandingan;

class BroadcastService
{
    public function send(Pertandingan $pertandingan)
    {
        try {
            $kelompoks = $pertandingan->kelompoks()->get();

            $payload = [
                'pertandingan' => $pertandingan->nama,
                'skor' => [
                    'A' => $kelompoks->where('kode', 'A')->first()->total_skor ?? 0,
                    'B' => $kelompoks->where('kode', 'B')->first()->total_skor ?? 0,
                    'C' => $kelompoks->where('kode', 'C')->first()->total_skor ?? 0,
                    'D' => $kelompoks->where('kode', 'D')->first()->total_skor ?? 0,
                ],
                'kelompok' => [
                    'A' => $kelompoks->where('kode', 'A')->first()->nama_peserta ?? '',
                    'B' => $kelompoks->where('kode', 'B')->first()->nama_peserta ?? '',
                    'C' => $kelompoks->where('kode', 'C')->first()->nama_peserta ?? '',
                    'D' => $kelompoks->where('kode', 'D')->first()->nama_peserta ?? '',
                ],
            ];

            $wsUrl = env('WS_URL', 'http://host.docker.internal:3003/broadcast-data');

            $client = new Client(['timeout' => 2]);
            $client->post($wsUrl, ['json' => $payload]);

            Log::info('📡 Broadcast success', $payload);
        } catch (\Throwable $e) {
            Log::warning("⚠️ Broadcast gagal ke WS_URL: {$e->getMessage()}");
        }
    }
}
