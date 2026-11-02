# 🍓 MatchaDeadline

**Sistem Pengingat Deadline Akademik Mahasiswa Berbasis Web Menggunakan Laravel — Metode Priority Scheduling**

Tema matcha × strawberry, ada mode gelap, animasi, dan konsep "mini Classroom" (dosen buat kelas & tugas, mahasiswa gabung pakai kode).

> Folder ini adalah **kumpulan file aplikasi** (controller, model, view, migration, dll.) yang ditempel ke project Laravel baru. Folder `vendor/` tidak disertakan, jadi ikuti langkah di bawah.

## Kebutuhan
- PHP 8.2+ dan Composer (paling mudah lewat **Laragon** atau XAMPP)
- Laravel 11 atau 12 (memakai SQLite bawaan, tidak perlu setting MySQL)

## Cara pasang (±5 menit)

```bash
# 1. Buat project Laravel baru
composer create-project laravel/laravel deadline-reminder
cd deadline-reminder

# 2. Salin SEMUA isi folder zip ini ke dalam project (timpa jika ditanya)
#    (app, config, database, lang, public, resources, routes)

# 3. Atur zona waktu & bahasa di file .env
#    APP_TIMEZONE=Asia/Jakarta
#    APP_LOCALE=id
#    APP_FALLBACK_LOCALE=id
#    APP_FAKER_LOCALE=id_ID

# 4. Siapkan database + data demo
php artisan migrate:fresh --seed

# 5. Jalankan
php artisan serve
```

Buka http://127.0.0.1:8000

> Jika `php artisan migrate` menanyakan membuat file `database.sqlite`, jawab **yes**.

### Akun demo (kata sandi: `password`)
| Peran | Email |
|---|---|
| Mahasiswa | `mhs@demo.test` |
| Dosen | `dosen@demo.test` |
| Dosen 2 | `dosen2@demo.test` |

Kode kelas demo: `WEB101`, `DB2026`, `AI2026`.

## Menjalankan pengingat otomatis
Pengingat dihitung oleh Laravel Scheduler tiap 10 menit (`routes/console.php`).

```bash
php artisan schedule:work        # saat development, biarkan terminal terbuka
php artisan reminders:send       # jalankan manual sekali
```

Di server produksi, tambahkan cron: `* * * * * cd /path/project && php artisan schedule:run >> /dev/null 2>&1`

**Email:** secara default `MAIL_MAILER=log`, jadi email tertulis di `storage/logs/laravel.log`. Untuk mengirim sungguhan, isi `MAIL_*` di `.env` (mis. SMTP Gmail atau Mailtrap).

## Metode Priority Scheduling

```
Skor = (0,5 × Urgensi) + (0,3 × Nilai) + (0,2 × Kesulitan)        (skala 0–100)

Urgensi   = 100 × (1 − sisa_jam ÷ (7 × 24))    → naik otomatis mendekati deadline (aging)
Nilai     = min(bobot% ÷ 40, 1) × 100
Kesulitan = (tingkat 1–5 ÷ 5) × 100
```

- Urutan: skor tertinggi dulu → deadline paling awal → estimasi paling singkat (tie-break).
- Level: **Mendesak** ≥ 70, **Sedang** 40–69, **Aman** < 40. Tugas yang sudah lewat deadline otomatis Mendesak.
- Bobot dan ambang bisa diubah di `config/priority.php` (jelaskan alasannya di laporan).
- Kode algoritma: `app/Services/PriorityScheduler.php`.
- Halaman **Priority Scheduling** di aplikasi menampilkan rumus, simulator interaktif, dan rincian skor tiap tugasmu (bagus untuk demo sidang).

## Pengingat
| Jenis | Kapan | Ikon |
|---|---|---|
| H-3 | sisa ≤ 72 jam | 🍃 |
| H-1 | sisa ≤ 24 jam | 🍓 |
| JAM | sisa ≤ 6 jam | ⏰ |
| TELAT | lewat deadline (maks. 2 hari) | 🚨 |

Tiap jenis dikirim sekali per tugas (dijaga unique index). Mahasiswa bisa mematikan tiap jenis di **Pengaturan**.

## Fitur
- Login/daftar dengan peran **Dosen** dan **Mahasiswa**
- Kelas: dosen buat kelas (kode gabung), mahasiswa gabung / keluar
- Tugas: judul, deskripsi, deadline, bobot nilai, kesulitan, estimasi jam; tugas pribadi mahasiswa
- Status tugas: belum / dikerjakan / selesai (centang tanpa reload)
- Dashboard mahasiswa: tugas paling prioritas, daftar terurut, hitung mundur real-time, donut progres, grafik 7 hari, jadwal kerja harian, pengingat terbaru
- Dashboard dosen: kelas, deadline mendatang, progres penyelesaian mahasiswa per tugas
- Kalender bulanan, pencarian & filter, riwayat, pusat notifikasi, pengaturan pengingat
- **Roster kuliah** (Semester V, kelas V.1–V.5): tampilan mingguan dan daftar, penanda hari ini / sedang berlangsung / berikutnya, kartu "Kuliah Hari Ini" di dashboard mahasiswa, bisa dicetak
- Mode gelap/terang, animasi, responsif di HP

## Roster kuliah
Menu **Roster** (`/roster`) membaca jadwal dari `config/roster.php`, tanpa database dan tanpa migrasi.
Untuk mengubah jadwal, edit file itu: satu baris = `[kode_matkul, hari, jam_mulai, jam_selesai, ruang, kode_dosen]`.
Penanda "hari ini" dan "sedang berlangsung" memakai jam perangkat pengguna. Kelas terakhir yang dipilih diingat di browser.

## Struktur penting
```
app/Services/PriorityScheduler.php   ← inti algoritma
app/Services/ReminderService.php     ← logika pengingat
app/Services/RosterService.php       ← membaca config/roster.php
app/Http/Controllers/RosterController.php
config/roster.php                    ← data jadwal kuliah
app/Console/Commands/KirimPengingat.php
config/priority.php                  ← bobot & ambang
database/migrations, database/seeders
resources/views/…                    ← tampilan (Blade)
public/css/app.css, public/js/app.js ← tema & animasi
public/js/roster.js                  ← penanda hari ini pada roster
```
