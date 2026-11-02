<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    protected $fillable = ['user_id', 'tugas_id', 'tipe', 'judul', 'pesan', 'dibaca_at'];

    protected function casts(): array
    {
        return ['dibaca_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class, 'tugas_id');
    }

    public function getIkonAttribute(): string
    {
        return match ($this->tipe) {
            'H-3'   => '🍃',
            'H-1'   => '🍓',
            'JAM'   => '⏰',
            'TELAT' => '🚨',
            default => '🔔',
        };
    }
}
