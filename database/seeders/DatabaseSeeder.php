<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Tugas;
use App\Models\TugasStatus;
use App\Models\User;
use App\Services\ReminderService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- Akun demo (kata sandi semua: password) ----------
        $dosen = User::create([
            'name' => 'Dr. Sari Wulandari, M.Kom.', 'email' => 'dosen@demo.test',
            'password' => 'password', 'role' => 'dosen', 'nim_nip' => '198503122010012001',
        ]);
        $dosen2 = User::create([
            'name' => 'Budi Santoso, M.T.', 'email' => 'dosen2@demo.test',
            'password' => 'password', 'role' => 'dosen', 'nim_nip' => '197812052008011003',
        ]);

        $mhs = User::create([
            'name' => 'Intan Maulida', 'email' => 'mhs@demo.test',
            'password' => 'password', 'role' => 'mahasiswa', 'nim_nip' => '2210512001',
        ]);
        $teman = collect([
            ['Rafi Pratama', 'rafi@demo.test', '2210512002'],
            ['Nadia Putri', 'nadia@demo.test', '2210512003'],
            ['Dimas Aditya', 'dimas@demo.test', '2210512004'],
        ])->map(fn ($d) => User::create([
            'name' => $d[0], 'email' => $d[1], 'password' => 'password', 'role' => 'mahasiswa', 'nim_nip' => $d[2],
        ]));

        // ---------- Kelas ----------
        $web = Kelas::create(['dosen_id' => $dosen->id, 'nama' => 'Pemrograman Web Lanjut', 'kode' => 'WEB101', 'warna' => 'matcha',
            'deskripsi' => 'Laravel, REST API, dan deployment aplikasi web.']);
        $basis = Kelas::create(['dosen_id' => $dosen->id, 'nama' => 'Basis Data', 'kode' => 'DB2026', 'warna' => 'strawberry',
            'deskripsi' => 'Perancangan ERD, normalisasi, dan SQL lanjutan.']);
        $ai = Kelas::create(['dosen_id' => $dosen2->id, 'nama' => 'Kecerdasan Buatan', 'kode' => 'AI2026', 'warna' => 'latte',
            'deskripsi' => 'Pencarian, representasi pengetahuan, dan machine learning dasar.']);

        foreach ([$web, $basis, $ai] as $k) {
            $k->anggota()->attach($mhs->id);
        }
        foreach ($teman as $t) {
            $web->anggota()->attach($t->id);
            $basis->anggota()->attach($t->id);
        }

        // ---------- Tugas (deadline relatif terhadap hari ini) ----------
        $buat = function (Kelas $k, User $pembuat, string $judul, string $desk, $deadline, int $bobot, int $sulit, float $jam) {
            return Tugas::create([
                'kelas_id' => $k->id, 'user_id' => $pembuat->id, 'judul' => $judul, 'deskripsi' => $desk,
                'deadline' => $deadline, 'bobot' => $bobot, 'kesulitan' => $sulit, 'estimasi_jam' => $jam,
            ]);
        };

        $t1 = $buat($web, $dosen, 'Laporan Praktikum Laravel CRUD', 'Buat aplikasi CRUD sederhana dengan Laravel dan unggah laporannya.', now()->addHours(5), 15, 3, 3);
        $t2 = $buat($basis, $dosen, 'Rancangan ERD Sistem Perpustakaan', 'Gambar ERD lengkap dengan relasi dan kardinalitas.', now()->addHours(30), 20, 4, 4);
        $t3 = $buat($ai, $dosen2, 'Makalah Algoritma A*', 'Minimal 5 halaman, sertakan contoh kasus pencarian jalur.', now()->addDays(2)->setTime(23, 59), 25, 5, 6);
        $t4 = $buat($web, $dosen, 'Quiz Middleware & Auth', 'Quiz daring 30 menit.', now()->addDays(3)->setTime(10, 0), 10, 2, 1);
        $t5 = $buat($basis, $dosen, 'Normalisasi 1NF - 3NF', 'Kerjakan 5 soal normalisasi.', now()->addDays(5)->setTime(17, 0), 10, 3, 2);
        $t6 = $buat($ai, $dosen2, 'Proyek Mini: Chatbot Sederhana', 'Chatbot rule-based dengan Python.', now()->addDays(9)->setTime(23, 59), 30, 5, 10);
        $t7 = $buat($web, $dosen, 'Presentasi Proyek Akhir', 'Presentasi 10 menit + demo.', now()->addDays(12)->setTime(8, 0), 35, 4, 5);
        $t8 = $buat($basis, $dosen, 'Latihan Soal SQL JOIN', 'Selesaikan 10 soal JOIN.', now()->subHours(20), 5, 2, 1.5);
        $t9 = $buat($web, $dosen, 'Tugas Pengantar HTML & CSS', 'Landing page sederhana.', now()->subDays(6), 10, 2, 2);

        // Tugas pribadi mahasiswa
        Tugas::create([
            'kelas_id' => null, 'user_id' => $mhs->id, 'judul' => 'Belajar untuk UTS Jaringan Komputer',
            'deskripsi' => 'Ulas bab 1-5 dan kerjakan latihan soal.', 'deadline' => now()->addDays(4)->setTime(7, 30),
            'bobot' => 30, 'kesulitan' => 4, 'estimasi_jam' => 6,
        ]);
        Tugas::create([
            'kelas_id' => null, 'user_id' => $mhs->id, 'judul' => 'Daftar organisasi kampus',
            'deskripsi' => 'Isi formulir pendaftaran UKM.', 'deadline' => now()->addDays(6)->setTime(16, 0),
            'bobot' => 5, 'kesulitan' => 1, 'estimasi_jam' => 0.5,
        ]);

        // ---------- Status ----------
        TugasStatus::create(['tugas_id' => $t9->id, 'user_id' => $mhs->id, 'status' => 'selesai', 'selesai_at' => now()->subDays(7)]);
        TugasStatus::create(['tugas_id' => $t2->id, 'user_id' => $mhs->id, 'status' => 'dikerjakan']);
        foreach ($teman as $i => $t) {
            if ($i < 2) {
                TugasStatus::create(['tugas_id' => $t9->id, 'user_id' => $t->id, 'status' => 'selesai', 'selesai_at' => now()->subDays(6)]);
            }
            if ($i === 0) {
                TugasStatus::create(['tugas_id' => $t1->id, 'user_id' => $t->id, 'status' => 'dikerjakan']);
            }
        }

        // ---------- Notifikasi awal (tanpa email) ----------
        app(ReminderService::class)->prosesSemua(false);
    }
}
