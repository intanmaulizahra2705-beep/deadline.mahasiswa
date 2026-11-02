<?php

namespace App\Services;

use App\Mail\PengingatDeadlineMail;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Membuat notifikasi pengingat (web + email) berdasarkan sisa waktu:
 *   > 72 jam         : belum ada pengingat
 *   <= 72 jam (H-3)  : pengingat H-3
 *   <= 24 jam (H-1)  : pengingat H-1
 *   <= 6 jam         : pengingat JAM
 *   lewat deadline   : peringatan TELAT (maks. 2 hari setelah deadline)
 *
 * Tiap jenis pengingat hanya dikirim SEKALI per tugas per mahasiswa
 * (dijaga unique index user_id + tugas_id + tipe).
 */
class ReminderService
{
    public function __construct(private PriorityScheduler $scheduler)
    {
    }

    /** @return int jumlah notifikasi baru yang dibuat */
    public function prosesUser(User $user, bool $kirimEmail = true): int
    {
        $dibuat = 0;

        foreach ($this->scheduler->aktifTerurut($user) as $item) {
            $t = $item['tugas'];
            $p = $item['p'];
            $jam = $p['sisa_jam'];

            $tipe = null;
            if ($jam <= 0 && $jam > -48) {
                $tipe = 'TELAT';
            } elseif ($jam > 0 && $jam <= 6 && $user->remind_hours) {
                $tipe = 'JAM';
            } elseif ($jam > 6 && $jam <= 24 && $user->remind_h1) {
                $tipe = 'H-1';
            } elseif ($jam > 24 && $jam <= 72 && $user->remind_h3) {
                $tipe = 'H-3';
            }

            if (! $tipe) {
                continue;
            }

            $notif = Notifikasi::firstOrCreate(
                ['user_id' => $user->id, 'tugas_id' => $t->id, 'tipe' => $tipe],
                [
                    'judul' => $this->judul($tipe, $t->judul),
                    'pesan' => $this->pesan($tipe, $t->judul, $p['sisa'], $t->deadline_label, $p['skor']),
                ]
            );

            if (! $notif->wasRecentlyCreated) {
                continue;
            }

            $dibuat++;

            if ($kirimEmail && $user->email_enabled) {
                try {
                    Mail::to($user->email)->send(new PengingatDeadlineMail($user, $notif));
                } catch (\Throwable $e) {
                    Log::warning('Gagal kirim email pengingat: '.$e->getMessage());
                }
            }
        }

        return $dibuat;
    }

    public function prosesSemua(bool $kirimEmail = true): int
    {
        $total = 0;
        User::where('role', 'mahasiswa')->each(function (User $u) use (&$total, $kirimEmail) {
            $total += $this->prosesUser($u, $kirimEmail);
        });

        return $total;
    }

    private function judul(string $tipe, string $tugas): string
    {
        return match ($tipe) {
            'H-3'   => "H-3 · {$tugas}",
            'H-1'   => "Besok deadline · {$tugas}",
            'JAM'   => "Tinggal beberapa jam · {$tugas}",
            'TELAT' => "Terlambat · {$tugas}",
        };
    }

    private function pesan(string $tipe, string $tugas, string $sisa, string $deadline, float $skor): string
    {
        $dasar = "Tugas \"{$tugas}\" — {$sisa} (deadline {$deadline}). Skor prioritas: {$skor}.";

        return match ($tipe) {
            'H-3'   => "Masih ada waktu, mulai dicicil ya. {$dasar}",
            'H-1'   => "Deadline kurang dari 24 jam! {$dasar}",
            'JAM'   => "Segera selesaikan sekarang. {$dasar}",
            'TELAT' => "Deadline sudah lewat. Kerjakan secepatnya atau hubungi dosen. {$dasar}",
        };
    }
}
