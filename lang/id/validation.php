<?php

return [
    'required'  => ':attribute wajib diisi.',
    'email'     => ':attribute harus berupa alamat email yang valid.',
    'unique'    => ':attribute sudah dipakai.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'date'      => ':attribute bukan tanggal yang valid.',
    'integer'   => ':attribute harus berupa angka bulat.',
    'numeric'   => ':attribute harus berupa angka.',
    'exists'    => ':attribute yang dipilih tidak valid.',
    'in'        => ':attribute yang dipilih tidak valid.',
    'string'    => ':attribute harus berupa teks.',
    'max' => [
        'string'  => ':attribute maksimal :max karakter.',
        'numeric' => ':attribute maksimal :max.',
    ],
    'min' => [
        'string'  => ':attribute minimal :min karakter.',
        'numeric' => ':attribute minimal :min.',
    ],

    'attributes' => [
        'name' => 'Nama', 'email' => 'Email', 'password' => 'Kata sandi', 'role' => 'Peran',
        'nim_nip' => 'NIM/NIP', 'judul' => 'Judul', 'deskripsi' => 'Deskripsi', 'deadline' => 'Deadline',
        'bobot' => 'Bobot nilai', 'kesulitan' => 'Tingkat kesulitan', 'estimasi_jam' => 'Estimasi jam',
        'kelas_id' => 'Kelas', 'kode' => 'Kode kelas', 'nama' => 'Nama kelas', 'warna' => 'Warna',
        'jam_per_hari' => 'Jam belajar per hari',
    ],
];
