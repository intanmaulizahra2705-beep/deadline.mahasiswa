@php
    /** @var array $item  ['tugas' => Tugas, 'status' => string, 'p' => array] */
    $t = $item['tugas'];
    $p = $item['p'];
    $selesai = $item['status'] === 'selesai';
    $level = $selesai ? 'selesai' : $p['level']['kode'];
    $bolehCentang = auth()->user()->isMahasiswa();
@endphp
<article class="task {{ $level }} reveal">
    <div>
        @if ($bolehCentang)
            <button type="button" class="check {{ $selesai ? 'on' : '' }}"
                    data-status="{{ $selesai ? 'belum' : 'selesai' }}" data-url="{{ route('tugas.status', $t) }}"
                    title="{{ $selesai ? 'Tandai belum selesai' : 'Tandai selesai' }}">✓</button>
        @else
            <div class="score" style="--v: {{ $p['skor'] }}"><b>{{ round($p['skor']) }}</b><small>SKOR</small></div>
        @endif
    </div>

    <div style="min-width:0">
        <a href="{{ route('tugas.show', $t) }}" class="task-title">{{ $t->judul }}</a>
        <div class="task-meta">
            @if ($t->kelas)
                <span class="badge {{ $t->kelas->warna }}">{{ $t->kelas->nama }}</span>
            @else
                <span class="badge pribadi">Pribadi</span>
            @endif
            <span>📅 {{ $t->deadline_singkat }}</span>
            <span>⏱ {{ $t->estimasi_label }} jam</span>
            <span>⚖️ {{ $t->bobot }}%</span>
            @if ($item['status'] === 'dikerjakan')<span class="badge dikerjakan">Dikerjakan</span>@endif
        </div>
    </div>

    <div class="task-right">
        @if ($selesai)
            <span class="badge selesai">✓ Selesai</span>
        @else
            <span class="countdown {{ $p['terlambat'] ? 'late' : '' }}" data-deadline="{{ $t->deadline->toIso8601String() }}">{{ $p['sisa'] }}</span>
            <div class="bar {{ $p['level']['kode'] === 'mendesak' ? 'berry' : '' }}" title="Tekanan waktu"><i data-w="{{ $p['urgensi'] }}"></i></div>
            <div class="row" style="gap:.4rem">
                @if ($bolehCentang)
                    <span class="badge {{ $p['level']['kode'] }}">{{ $p['level']['label'] }} · {{ round($p['skor']) }}</span>
                @else
                    <span class="badge {{ $p['level']['kode'] }}">{{ $p['level']['label'] }}</span>
                @endif
            </div>
        @endif
    </div>
</article>
