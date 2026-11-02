<?php

/*
|--------------------------------------------------------------------------
| Konfigurasi Priority Scheduling
|--------------------------------------------------------------------------
| Skor prioritas (0-100) =
|     (bobot.urgensi   x Urgensi)
|   + (bobot.nilai     x Nilai)
|   + (bobot.kesulitan x Kesulitan)
|
| Jumlah ketiga bobot sebaiknya = 1.0
*/

return [

    'bobot' => [
        'urgensi'   => 0.5,   // seberapa dekat deadline (aging)
        'nilai'     => 0.3,   // bobot nilai tugas terhadap nilai akhir
        'kesulitan' => 0.2,   // tingkat kesulitan 1-5
    ],

    // Urgensi mulai naik dari 0 saat sisa waktu = jendela_hari, sampai 100 saat deadline tiba.
    'jendela_hari' => 7,

    // Bobot nilai (%) yang dianggap "maksimal" untuk normalisasi. 40% atau lebih = 100 poin.
    'bobot_maks' => 40,

    // Ambang level prioritas
    'batas_mendesak' => 70,   // skor >= 70  -> Mendesak (merah/strawberry)
    'batas_sedang'   => 40,   // skor >= 40  -> Sedang   (kuning)
                              // skor <  40  -> Aman     (hijau/matcha)

    'jam_per_hari_default' => 4,
];
