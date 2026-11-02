<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TugasStatus extends Model
{
    protected $table = 'tugas_status';

    protected $fillable = ['tugas_id', 'user_id', 'status', 'selesai_at'];

    protected function casts(): array
    {
        return ['selesai_at' => 'datetime'];
    }

    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class, 'tugas_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
