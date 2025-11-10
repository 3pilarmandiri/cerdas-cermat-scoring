<?php

namespace App\Http\Controllers;

use App\Models\Kelompok;
use App\Models\Pertandingan;
use Illuminate\Http\Request;
use App\Services\BroadcastService;


use App\Exports\PertandinganExport;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;

class PertandinganController extends Controller
{
    public function index()
    {
        $pertandingans = Pertandingan::with('kelompoks')->latest()->get();
        return view('pertandingan.index', compact('pertandingans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kelompok.A' => 'required|string|max:255',
            'kelompok.B' => 'required|string|max:255',
            'kelompok.C' => 'required|string|max:255',
            'kelompok.D' => 'required|string|max:255',
        ]);

        // Buat pertandingan
        $pertandingan = Pertandingan::create([
            'nama' => $request->nama,
            'keterangan' => $request->keterangan,
        ]);

        // Buat kelompok A–D
        foreach ($request->kelompok as $kode => $namaKelompok) {
            $pertandingan->kelompoks()->create([
                'kode' => $kode,
                'nama_peserta' => $namaKelompok,
                'total_skor' => 0,
            ]);
        }

        return redirect()->route('pertandingan.index')->with('success', 'Pertandingan berhasil dibuat.');
    }


    public function mulai($id, BroadcastService $broadcast)
    {
        $pertandingan = Pertandingan::with('kelompoks')->findOrFail($id);

        // kirim data ke websocket
        $broadcast->send($pertandingan);

        return view('pertandingan.mulai', compact('pertandingan'));
    }

    public function destroy($id)
    {
        $pertandingan = \App\Models\Pertandingan::findOrFail($id);
        foreach ($pertandingan->kelompoks as $kelompok) {
            $kelompok->skorHistories()->delete();
            $kelompok->delete();
        }
        $pertandingan->delete();

        // If AJAX or API call
        if (request()->expectsJson()) {
            return response()->json(['message' => 'Pertandingan berhasil dihapus'], 200);
        }

        // If normal form submit
        return redirect()->route('pertandingan.index')->with('success', 'Pertandingan berhasil dihapus');
    }


    public function export()
    {
        $filename = "brida-data-pertandingan-" . date("d-m-Y-H-I") . ".xlsx";
        // dd($filename);
        return Excel::download(new PertandinganExport, $filename);
    }
}
