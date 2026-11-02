<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = ['dosen_id', 'nama', 'kode', 'deskripsi', 'warna'];

    public const WARNA = ['matcha', 'strawberry', 'latte', 'peach'];

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function anggota(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'kelas_anggota', 'kelas_id', 'user_id')->withTimestamps();
    }

    public function tugas(): HasMany
    {
        return $this->hasMany(Tugas::class, 'kelas_id');
    }

    public static function buatKode(): string
    {
        do {
            $kode = Str::upper(Str::random(6));
        } while (static::where('kode', $kode)->exists());

        return $kode;
    }
}
