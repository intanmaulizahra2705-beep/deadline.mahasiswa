<?php

namespace App\Http\Controllers;

use App\Services\PriorityScheduler;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KalenderController extends Controller
{
    public function __construct(private PriorityScheduler $scheduler)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();

        try {
            $bulan = Carbon::createFromFormat('Y-m', $request->query('bulan', now()->format('Y-m')))->startOfMonth();
        } catch (\Throwable) {
            $bulan = now()->startOfMonth();
        }

        $awal  = $bulan->copy()->startOfWeek(Carbon::MONDAY);
        $akhir = $bulan->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $items = $this->scheduler->daftarUntuk($user)
            ->filter(fn ($i) => $i['tugas']->deadline->between($awal, $akhir));

        $perHari = $items->groupBy(fn ($i) => $i['tugas']->deadline->format('Y-m-d'));

        $minggu = [];
        for ($d = $awal->copy(); $d <= $akhir; $d->addDay()) {
            $minggu[(int) floor($awal->diffInDays($d) / 7)][] = [
                'tgl'      => $d->copy(),
                'bulanIni' => $d->month === $bulan->month,
                'hariIni'  => $d->isToday(),
                'tugas'    => ($perHari[$d->format('Y-m-d')] ?? collect())->sortBy(fn ($i) => $i['tugas']->deadline)->values(),
            ];
        }

        return view('kalender.index', [
            'bulan'  => $bulan,
            'minggu' => $minggu,
            'prev'   => $bulan->copy()->subMonth()->format('Y-m'),
            'next'   => $bulan->copy()->addMonth()->format('Y-m'),
        ]);
    }
}
