<?php

namespace App\Exports;

use App\Models\Pertandingan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PertandinganExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $pertandingans = Pertandingan::with('kelompoks')->get();

        return $pertandingans->map(function ($p) {
            // Ambil skor dari setiap kelompok (default 0)
            $skorA = $p->kelompoks->where('kode', 'A')->first()->total_skor ?? 0;
            $skorB = $p->kelompoks->where('kode', 'B')->first()->total_skor ?? 0;
            $skorC = $p->kelompoks->where('kode', 'C')->first()->total_skor ?? 0;
            $skorD = $p->kelompoks->where('kode', 'D')->first()->total_skor ?? 0;

            return [
                'Pertandingan' => $p->nama,
                'Keterangan'   => $p->keterangan,
                'Skor A'       => $skorA,
                'Skor B'       => $skorB,
                'Skor C'       => $skorC,
                'Skor D'       => $skorD,
            ];
        });
    }

    public function headings(): array
    {
        return ['Pertandingan', 'Keterangan', 'Skor A', 'Skor B', 'Skor C', 'Skor D'];
    }
}
