<?php

/*
|--------------------------------------------------------------------------
| Roster / Jadwal Kuliah
|--------------------------------------------------------------------------
| Sumber: Jadwal Kuliah Semester V Fakultas Teknik dan Sains,
| Universitas Jabal Ghafur, Tahun Akademik Ganjil 2026/2027.
|
| Cara mengubah jadwal: edit bagian 'kelas' di bawah. Satu baris =
| [kode_matkul, hari, jam_mulai, jam_selesai, ruang, kode_dosen]
| Tidak perlu migrasi atau database.
*/

return [
    'kampus'   => 'Universitas Jabal Ghafur · Fakultas Teknik dan Sains',
    'semester' => 'V',
    'tahun'    => 'Ganjil 2026/2027',

    'hari' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],

    // warna: matcha, strawberry, amber, latte, peach, sage, plum
    'matkul' => [
        'imk'   => ['nama' => 'Interaksi Manusia & Komputer',        'singkat' => 'IMK',        'sks' => 3, 'warna' => 'matcha'],
        'jarkom'=> ['nama' => 'Jaringan Komputer II',                'singkat' => 'Jarkom II',  'sks' => 3, 'warna' => 'amber'],
        'grafik'=> ['nama' => 'Komputer Grafik',                     'singkat' => 'Komgraf',    'sks' => 3, 'warna' => 'strawberry'],
        'dwdm'  => ['nama' => 'Konsep Data Warehouse & Data Mining', 'singkat' => 'DW & DM',    'sks' => 3, 'warna' => 'plum'],
        'pbo'   => ['nama' => 'Pemrograman Berorientasi Objek',      'singkat' => 'PBO',        'sks' => 3, 'warna' => 'sage'],
        'ppl'   => ['nama' => 'Proyek Perangkat Lunak',              'singkat' => 'PPL',        'sks' => 3, 'warna' => 'latte'],
        'rpl2'  => ['nama' => 'Rekayasa Perangkat Lunak II',         'singkat' => 'RPL II',     'sks' => 3, 'warna' => 'peach'],
    ],

    'dosen' => [
        'junaidi' => 'Junaidi Salat, S.Kom., M.Kom',
        'sayed'   => 'Sayed Achmady, ST., M.Kom',
        'wahyuni' => 'Wahyuni Harahap, ST., MM',
        'jessika' => 'Jessika, S.Kom., M.Kom',
        'mukhsin' => 'Ir. Mukhsin Nuzula, ST., MT',
        'ilal'    => 'Ilal Mahdi, S.T., M.T',
        'zikrul'  => 'Zikrul Khalid, ST., MT',
    ],

    'kelas' => [
        'V.1' => [
            ['imk',    'Selasa', '08.30', '11.00', 'FTI-01', 'junaidi'],
            ['jarkom', 'Rabu',   '08.30', '11.00', 'LAB-03', 'sayed'],
            ['grafik', 'Senin',  '08.30', '11.00', 'LAB-05', 'wahyuni'],
            ['dwdm',   'Selasa', '14.30', '17.00', 'FTI-11', 'jessika'],
            ['pbo',    'Rabu',   '11.00', '13.30', 'LAB-04', 'jessika'],
            ['ppl',    'Kamis',  '08.30', '11.00', 'LAB-04', 'ilal'],
            ['rpl2',   'Selasa', '11.00', '13.30', 'FTI-06', 'zikrul'],
        ],
        'V.2' => [
            ['imk',    'Selasa', '11.00', '13.30', 'FTI-01', 'junaidi'],
            ['jarkom', 'Rabu',   '11.00', '13.30', 'LAB-03', 'sayed'],
            ['grafik', 'Senin',  '11.00', '13.30', 'LAB-05', 'wahyuni'],
            ['dwdm',   'Rabu',   '08.30', '11.00', 'LAB-04', 'jessika'],
            ['pbo',    'Sabtu',  '14.30', '17.00', 'FTI-01', 'mukhsin'],
            ['ppl',    'Sabtu',  '11.00', '13.30', 'FTI-05', 'ilal'],
            ['rpl2',   'Selasa', '14.30', '17.00', 'LAB-04', 'zikrul'],
        ],
        'V.3' => [
            ['imk',    'Selasa', '14.30', '17.00', 'FTI-01', 'junaidi'],
            ['jarkom', 'Rabu',   '14.30', '17.00', 'LAB-03', 'sayed'],
            ['grafik', 'Selasa', '08.30', '11.00', 'LAB-05', 'wahyuni'],
            ['dwdm',   'Senin',  '11.00', '13.30', 'LAB-03', 'jessika'],
            ['pbo',    'Sabtu',  '11.00', '13.30', 'FTI-04', 'mukhsin'],
            ['ppl',    'Selasa', '11.00', '13.30', 'FTI-02', 'ilal'],
            ['rpl2',   'Senin',  '08.30', '11.00', 'LAB-02', 'zikrul'],
        ],
        'V.4' => [
            ['imk',    'Kamis',  '08.30', '11.00', 'FTI-10', 'wahyuni'],
            ['jarkom', 'Kamis',  '14.30', '17.00', 'LAB-04', 'sayed'],
            ['grafik', 'Selasa', '14.30', '17.00', 'LAB-03', 'wahyuni'],
            ['dwdm',   'Senin',  '14.30', '17.00', 'LAB-02', 'jessika'],
            ['pbo',    'Selasa', '11.00', '13.30', 'LAB-02', 'mukhsin'],
            ['ppl',    'Kamis',  '11.00', '13.30', 'LAB-02', 'ilal'],
            ['rpl2',   'Senin',  '11.00', '13.30', 'LAB-02', 'zikrul'],
        ],
        'V.5' => [
            ['imk',    'Kamis',  '14.30', '17.00', 'FTI-10', 'wahyuni'],
            ['jarkom', 'Kamis',  '11.00', '13.30', 'LAB-04', 'sayed'],
            ['grafik', 'Rabu',   '14.30', '17.00', 'LAB-04', 'wahyuni'],
            ['dwdm',   'Senin',  '08.30', '11.00', 'LAB-03', 'jessika'],
            ['pbo',    'Selasa', '14.30', '17.00', 'FTI-03', 'ilal'],
            ['ppl',    'Selasa', '08.30', '11.00', 'FTI-02', 'ilal'],
            ['rpl2',   'Rabu',   '08.30', '11.00', 'FTI-04', 'zikrul'],
        ],
    ],
];
