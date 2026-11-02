<?php

namespace App\Http\Controllers;

use App\Services\RosterService;
use Illuminate\Http\Request;

class RosterController extends Controller
{
    public function index(Request $request, RosterService $roster)
    {
        $semua  = $roster->semua();
        $daftar = array_keys($semua);

        $kelas    = (string) $request->query('kelas', '');
        $adaParam = in_array($kelas, $daftar, true);
        if (! $adaParam) {
            $kelas = $daftar[0];
        }

        $tampil = $request->query('tampil') === 'daftar' ? 'daftar' : 'mingguan';
        $items  = $semua[$kelas];
        $hari   = config('roster.hari');

        // Jam kuliah yang dipakai kelas ini (baris pada grid mingguan)
        $slots = collect($items)
            ->map(fn ($i) => [$i['mulai'], $i['selesai']])
            ->unique(fn ($s) => $s[0].'-'.$s[1])
            ->sortBy(fn ($s) => $s[0])
            ->values()
            ->all();

        $grid = [];
        foreach ($slots as $si => $s) {
            foreach ($hari as $h) {
                $grid[$si][$h] = collect($items)
                    ->filter(fn ($i) => $i['hari'] === $h && $i['mulai'] === $s[0] && $i['selesai'] === $s[1])
                    ->values()
                    ->all();
            }
        }

        $perHari = [];
        foreach ($hari as $h) {
            $perHari[$h] = collect($items)->where('hari', $h)->sortBy('mulai')->values()->all();
        }

        $ringkas = [
            'matkul'   => count($items),
            'sks'      => collect($items)->sum('sks'),
            'hariAktif' => collect($items)->pluck('hari')->unique()->count(),
            'dosen'    => collect($items)->pluck('dosen')->unique()->count(),
        ];

        return view('roster.index', [
            'cfg'      => config('roster'),
            'daftar'   => $daftar,
            'kelas'    => $kelas,
            'adaParam' => $adaParam,
            'tampil'   => $tampil,
            'items'    => $items,
            'hari'     => $hari,
            'slots'    => $slots,
            'grid'     => $grid,
            'perHari'  => $perHari,
            'ringkas'  => $ringkas,
        ]);
    }
}
