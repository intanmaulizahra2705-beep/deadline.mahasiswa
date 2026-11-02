<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tugas extends Model
{
    protected $table = 'tugas';

    protected $fillable = [
        'kelas_id', 'user_id', 'judul', 'deskripsi', 'deadline', 'bobot', 'kesulitan', 'estimasi_jam',
    ];

    protected function casts(): array
    {
        return [
            'deadline'     => 'datetime',
            'bobot'        => 'integer',
            'kesulitan'    => 'integer',
            'estimasi_jam' => 'float',
        ];
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(TugasStatus::class, 'tugas_id');
    }

    public function getIsPribadiAttribute(): bool
    {
        return $this->kelas_id === null;
    }

    public function getDeadlineLabelAttribute(): string
    {
        return $this->deadline->copy()->locale('id')->translatedFormat('D, d M Y · H:i');
    }

    public function getDeadlineSingkatAttribute(): string
    {
        return $this->deadline->copy()->locale('id')->translatedFormat('d M, H:i');
    }

    public function getEstimasiLabelAttribute(): string
    {
        $v = (float) $this->estimasi_jam;

        return rtrim(rtrim(number_format($v, 1, ',', ''), '0'), ',');
    }
}
