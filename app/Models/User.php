<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'nim_nip',
        'remind_h3', 'remind_h1', 'remind_hours', 'email_enabled', 'jam_per_hari',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'remind_h3'         => 'boolean',
            'remind_h1'         => 'boolean',
            'remind_hours'      => 'boolean',
            'email_enabled'     => 'boolean',
            'jam_per_hari'      => 'integer',
        ];
    }

    public function isDosen(): bool
    {
        return $this->role === 'dosen';
    }

    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    /** Kelas yang diampu (dosen) */
    public function kelasDiajar(): HasMany
    {
        return $this->hasMany(Kelas::class, 'dosen_id');
    }

    /** Kelas yang diikuti (mahasiswa) */
    public function kelasDiikuti(): BelongsToMany
    {
        return $this->belongsToMany(Kelas::class, 'kelas_anggota', 'user_id', 'kelas_id')->withTimestamps();
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class);
    }

    public function statusTugas(): HasMany
    {
        return $this->hasMany(TugasStatus::class);
    }

    /**
     * Semua tugas yang terlihat oleh user ini.
     *  - Dosen     : tugas di kelas yang ia ampu
     *  - Mahasiswa : tugas di kelas yang diikuti + tugas pribadinya
     */
    public function tugasQuery(): Builder
    {
        if ($this->isDosen()) {
            return Tugas::query()->whereHas('kelas', fn ($q) => $q->where('dosen_id', $this->id));
        }

        $kelasIds = $this->kelasDiikuti()->pluck('kelas.id');

        return Tugas::query()->where(function ($q) use ($kelasIds) {
            $q->whereIn('kelas_id', $kelasIds)
              ->orWhere(fn ($p) => $p->whereNull('kelas_id')->where('user_id', $this->id));
        });
    }
}
