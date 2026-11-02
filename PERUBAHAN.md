# Daftar perubahan (rapikan tampilan + roster)

Tidak ada migrasi baru dan tidak ada perubahan database. Logika algoritma, pengingat, route lama,
controller lama, model, dan form tidak diubah.

## File baru
- config/roster.php                          data jadwal kuliah V.1–V.5 (edit di sini jika jadwal berubah)
- app/Services/RosterService.php             membaca config roster
- app/Http/Controllers/RosterController.php  halaman /roster
- resources/views/roster/index.blade.php     tampilan roster (mingguan / daftar)
- public/js/roster.js                        penanda hari ini, sedang berlangsung, berikutnya; kartu dashboard

## File diubah
- routes/web.php                             + 1 route: GET /roster (di dalam grup auth)
- public/css/app.css                         + bagian "Penyempurnaan tampilan" di akhir file (aturan lama tidak dihapus)
- resources/views/layouts/app.blade.php      + 1 menu "Roster" di sidebar
- resources/views/layouts/guest.blade.php    judul resmi di panel login/daftar
- resources/views/welcome.blade.php          judul resmi, fitur Roster, footer
- resources/views/dashboard/mahasiswa.blade.php  + kartu "Kuliah Hari Ini"
- resources/views/kelas/index.blade.php      + ikon di sampul kartu kelas
- resources/views/kelas/show.blade.php       + ikon di sampul kelas
- resources/views/emails/pengingat.blade.php teks footer email
- README.md                                  keterangan roster

## Tidak disertakan di zip
vendor/, .env, dan database/database.sqlite (supaya data dan pengaturanmu tidak tertimpa).
