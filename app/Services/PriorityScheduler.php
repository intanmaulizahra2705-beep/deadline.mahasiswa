<?php

namespace App\Services;

use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Inti sistem: Priority Scheduling.
 *
 * Tiap tugas diberi skor 0-100 dari tiga kriteria:
 *   1. Urgensi   (sisa waktu menuju deadline, makin dekat makin tinggi -> AGING)
 *   2. Nilai     (bobot tugas terhadap nilai akhir)
 *   3. Kesulitan (tingkat kesulitan 1-5)
 *
 * Tugas diurutkan dari skor tertinggi. Jika skor sama (tie-break):
 *   deadline lebih awal dulu, lalu estimasi pengerjaan lebih singkat dulu (SJF).
 */
class PriorityScheduler
{
    /** Hitung skor & rincian untuk satu tugas. */
    public function hitung(Tugas $tugas, ?Carbon $sekarang = null): array
    {
        $sekarang ??= now();
        $cfg = config('priority');

        $sisaJam = $sekarang->floatDiffInHours($tugas->deadline, false); // negatif = lewat
        $jendela = $cfg['jendela_hari'] * 24;

        if ($sisaJam <= 0) {
            $urgensi = 100.0;
        } else {
            $urgensi = max(0, min(100, 100 * (1 - $sisaJam / $jendela)));
        }

        $nilai     = min($tugas->bobot / $cfg['bobot_maks'], 1) * 100;
        $kesulitan = ($tugas->kesulitan / 5) * 100;

        $skor = $cfg['bobot']['urgensi'] * $urgensi
              + $cfg['bobot']['nilai'] * $nilai
              + $cfg['bobot']['kesulitan'] * $kesulitan;

        $skor = round($skor, 1);

        return [
            'skor'      => $skor,
            'urgensi'   => round($urgensi, 1),
            'nilai'     => round($nilai, 1),
            'kesulitan' => round($kesulitan, 1),
            'sisa_jam'  => $sisaJam,
            'terlambat' => $sisaJam <= 0,
            // tugas yang sudah lewat deadline selalu dianggap Mendesak
            'level'     => $sisaJam <= 0 ? ['kode' => 'mendesak', 'label' => 'Mendesak'] : $this->level($skor),
            'sisa'      => $this->labelSisa($sisaJam),
        ];
    }

    /** Level prioritas berdasarkan skor. */
    public function level(float $skor): array
    {
        $cfg = config('priority');

        if ($skor >= $cfg['batas_mendesak']) {
            return ['kode' => 'mendesak', 'label' => 'Mendesak'];
        }
        if ($skor >= $cfg['batas_sedang']) {
            return ['kode' => 'sedang', 'label' => 'Sedang'];
        }

        return ['kode' => 'aman', 'label' => 'Aman'];
    }

    /** "2 hari 4 jam", "5 jam 20 menit", "Terlambat 3 jam" */
    public function labelSisa(float $sisaJam): string
    {
        $menitTotal = (int) round(abs($sisaJam) * 60);
        $hari  = intdiv($menitTotal, 1440);
        $jam   = intdiv($menitTotal % 1440, 60);
        $menit = $menitTotal % 60;

        if ($hari > 0) {
            $teks = $hari.' hari'.($jam > 0 ? ' '.$jam.' jam' : '');
        } elseif ($jam > 0) {
            $teks = $jam.' jam'.($menit > 0 ? ' '.$menit.' mnt' : '');
        } else {
            $teks = max($menit, 1).' mnt';
        }

        return $sisaJam <= 0 ? 'Terlambat '.$teks : $teks.' lagi';
    }

    /**
     * Ambil semua tugas milik mahasiswa beserta skor & statusnya,
     * lalu urutkan memakai priority scheduling.
     *
     * @return Collection<int, array{tugas: Tugas, status: string, p: array}>
     */
    public function daftarUntuk(User $user, ?Carbon $sekarang = null): Collection
    {
        $sekarang ??= now();

        $tugas = $user->tugasQuery()
            ->with([
                'kelas:id,nama,warna',
                'statuses' => fn ($q) => $q->where('user_id', $user->id),
            ])
            ->get();

        return $tugas->map(function (Tugas $t) use ($sekarang) {
            return [
                'tugas'  => $t,
                'status' => $t->statuses->first()?->status ?? 'belum',
                'p'      => $this->hitung($t, $sekarang),
            ];
        })->values();
    }

    /** Urutkan: skor desc -> deadline asc -> estimasi asc. */
    public function urutkan(Collection $items): Collection
    {
        return $items->sort(function ($a, $b) {
            return [$b['p']['skor'], $a['tugas']->deadline->timestamp, $a['tugas']->estimasi_jam]
               <=> [$a['p']['skor'], $b['tugas']->deadline->timestamp, $b['tugas']->estimasi_jam];
        })->values();
    }

    /** Tugas yang masih harus dikerjakan (belum selesai), terurut prioritas. */
    public function aktifTerurut(User $user): Collection
    {
        return $this->urutkan(
            $this->daftarUntuk($user)->filter(fn ($i) => $i['status'] !== 'selesai')
        );
    }

    /**
     * Susun jadwal kerja hari ini: tugas diambil berurutan sesuai prioritas
     * sampai jatah jam belajar/hari habis. Tugas yang tidak muat dipotong
     * (dikerjakan sebagian hari ini, sisanya hari berikutnya).
     *
     * @return array{blok: array, total: float, jatah: float}
     */
    public function jadwalHariIni(Collection $aktifTerurut, float $jatahJam): array
    {
        $sisa = $jatahJam;
        $blok = [];

        foreach ($aktifTerurut as $item) {
            if ($sisa <= 0) {
                break;
            }
            $butuh   = (float) $item['tugas']->estimasi_jam;
            $alokasi = min($butuh, $sisa);

            $blok[] = [
                'item'     => $item,
                'jam'      => $alokasi,
                'sebagian' => $alokasi < $butuh,
            ];
            $sisa -= $alokasi;
        }

        return ['blok' => $blok, 'total' => $jatahJam - $sisa, 'jatah' => $jatahJam];
    }
}
