<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Tugas;
use App\Services\PriorityScheduler;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private PriorityScheduler $scheduler)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();

        return $user->isDosen() ? $this->dosen($user) : $this->mahasiswa($user);
    }

    private function mahasiswa($user)
    {
        $semua  = $this->scheduler->daftarUntuk($user);
        $aktif  = $this->scheduler->urutkan($semua->filter(fn ($i) => $i['status'] !== 'selesai'));
        $selesai = $semua->where('status', 'selesai');

        $terlambat = $aktif->filter(fn ($i) => $i['p']['terlambat']);
        $berjalan  = $aktif->reject(fn ($i) => $i['p']['terlambat']);
        $mendesak  = $berjalan->filter(fn ($i) => $i['p']['level']['kode'] === 'mendesak');

        $stat = [
            'aktif'     => $berjalan->count(),
            'mendesak'  => $mendesak->count(),
            'selesai'   => $selesai->count(),
            'terlambat' => $terlambat->count(),
            'total'     => $semua->count(),
        ];
        $stat['persen'] = $stat['total'] > 0 ? round($stat['selesai'] / $stat['total'] * 100) : 0;

        // 7 hari ke depan: jumlah deadline per hari
        $minggu = collect(range(0, 6))->map(function ($i) use ($berjalan) {
            $hari = now()->startOfDay()->addDays($i);

            return [
                'label'  => $hari->copy()->locale('id')->translatedFormat('D'),
                'tgl'    => $hari->format('d'),
                'jumlah' => $berjalan->filter(fn ($x) => $x['tugas']->deadline->isSameDay($hari))->count(),
                'hariIni' => $i === 0,
            ];
        });

        $fokus  = $berjalan->take(3);
        $jadwal = $this->scheduler->jadwalHariIni($berjalan, (float) $user->jam_per_hari);

        return view('dashboard.mahasiswa', [
            'stat'      => $stat,
            'aktif'     => $berjalan->take(8),
            'terlambat' => $terlambat->take(5),
            'fokus'     => $fokus,
            'jadwal'    => $jadwal,
            'minggu'    => $minggu,
            'maxMinggu' => max(1, $minggu->max('jumlah')),
            'notifBaru' => $user->notifikasi()->whereNull('dibaca_at')->latest()->take(4)->get(),
        ]);
    }

    private function dosen($user)
    {
        $kelas = Kelas::where('dosen_id', $user->id)->withCount(['anggota', 'tugas'])->latest()->get();

        $tugas = Tugas::whereIn('kelas_id', $kelas->pluck('id'))
            ->with('kelas:id,nama,warna')
            ->withCount(['statuses as selesai_count' => fn ($q) => $q->where('status', 'selesai')])
            ->orderBy('deadline')
            ->get();

        $anggotaPerKelas = $kelas->pluck('anggota_count', 'id');
        $tugas->each(function ($t) use ($anggotaPerKelas) {
            $t->total_mhs = (int) ($anggotaPerKelas[$t->kelas_id] ?? 0);
            $t->persen = $t->total_mhs > 0 ? round($t->selesai_count / $t->total_mhs * 100) : 0;
            $t->p = $this->scheduler->hitung($t);
        });

        $mendatang = $tugas->filter(fn ($t) => $t->deadline->isFuture());

        return view('dashboard.dosen', [
            'kelas'     => $kelas,
            'stat'      => [
                'kelas'     => $kelas->count(),
                'mahasiswa' => $kelas->sum('anggota_count'),
                'tugas'     => $tugas->count(),
                'mendatang' => $mendatang->count(),
            ],
            'mendatang' => $mendatang->take(6),
            'lewat'     => $tugas->filter(fn ($t) => $t->deadline->isPast())->sortByDesc('deadline')->take(4),
        ]);
    }
}
