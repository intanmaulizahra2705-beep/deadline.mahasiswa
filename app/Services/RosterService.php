<?php

namespace App\Services;

/**
 * Membaca config/roster.php dan mengubahnya menjadi daftar jadwal per kelas.
 * Hanya-baca: tidak menyentuh database.
 */
class RosterService
{
    /** @return array<string, array<int, array<string, mixed>>> */
    public function semua(): array
    {
        $cfg    = config('roster');
        $matkul = $cfg['matkul'];
        $dosen  = $cfg['dosen'];
        $hasil  = [];

        foreach ($cfg['kelas'] as $nama => $baris) {
            $items = [];
            $no    = 0;

            foreach ($baris as [$kode, $hari, $mulai, $selesai, $ruang, $kodeDosen]) {
                $mk = $matkul[$kode];

                $items[] = [
                    'no'      => ++$no,
                    'kode'    => $kode,
                    'mk'      => $mk['nama'],
                    'singkat' => $mk['singkat'],
                    'sks'     => $mk['sks'],
                    'warna'   => $mk['warna'],
                    'hari'    => $hari,
                    'mulai'   => $mulai,
                    'selesai' => $selesai,
                    'ruang'   => $ruang,
                    'dosen'   => $dosen[$kodeDosen],
                ];
            }

            $hasil[$nama] = $items;
        }

        return $hasil;
    }
}
