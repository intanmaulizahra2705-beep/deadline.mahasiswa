@extends('layouts.app')
@section('title', $tugas->judul)
@section('heading', $tugas->judul)
@section('sub', $tugas->kelas ? $tugas->kelas->nama.' · oleh '.$tugas->pembuat->name : 'Tugas pribadi')

@section('actions')
    @if ($tugas->user_id === auth()->id())
        <a class="btn ghost" href="{{ route('tugas.edit', $tugas) }}">✏️ Ubah</a>
        <form method="POST" action="{{ route('tugas.destroy', $tugas) }}" onsubmit="return confirm('Hapus tugas ini?')">
            @csrf @method('DELETE')
            <button class="btn danger" type="submit">🗑 Hapus</button>
        </form>
    @endif
@endsection

@section('content')
    <div class="grid split">
        <div class="grid" style="gap:1.1rem">
            <section class="card reveal">
                <div class="row between">
                    <div class="row">
                        <span class="badge {{ $p['level']['kode'] }}">{{ $p['level']['label'] }}</span>
                        @if ($tugas->kelas)<span class="badge {{ $tugas->kelas->warna }}">{{ $tugas->kelas->nama }}</span>@else<span class="badge pribadi">Pribadi</span>@endif
                        @if ($status)<span class="badge {{ $status }}">{{ ['belum' => 'Belum dikerjakan', 'dikerjakan' => 'Sedang dikerjakan', 'selesai' => 'Selesai'][$status] }}</span>@endif
                    </div>
                    <div class="countdown {{ $p['terlambat'] ? 'late' : '' }}" style="font-size:1.3rem" data-deadline="{{ $tugas->deadline->toIso8601String() }}">{{ $p['sisa'] }}</div>
                </div>

                <h3 class="mt">Deskripsi</h3>
                <p style="white-space:pre-line">{{ $tugas->deskripsi ?: 'Tidak ada deskripsi.' }}</p>

                <div class="grid g4 mt">
                    <div><div class="xs muted">Deadline</div><b>{{ $tugas->deadline_label }}</b></div>
                    <div><div class="xs muted">Bobot nilai</div><b>{{ $tugas->bobot }}%</b></div>
                    <div><div class="xs muted">Kesulitan</div><b>{{ str_repeat('★', $tugas->kesulitan).str_repeat('☆', 5 - $tugas->kesulitan) }}</b></div>
                    <div><div class="xs muted">Estimasi</div><b>{{ $tugas->estimasi_label }} jam</b></div>
                </div>

                @if ($status !== null)
                    <h3 class="mt2">Status pengerjaanku</h3>
                    <div class="seg">
                        @foreach (['belum' => 'Belum', 'dikerjakan' => 'Dikerjakan', 'selesai' => '✓ Selesai'] as $k => $lbl)
                            <button type="button" class="s-{{ $k }} {{ $status === $k ? 'on' : '' }}" data-status="{{ $k }}" data-url="{{ route('tugas.status', $tugas) }}">{{ $lbl }}</button>
                        @endforeach
                    </div>
                @endif
            </section>

            @if ($progress)
                <section class="card reveal">
                    <div class="card-head"><h2>Progres Mahasiswa</h2>
                        <span class="badge matcha">{{ $progress->where('status', 'selesai')->count() }}/{{ $progress->count() }} selesai</span></div>
                    <div class="scroll-x">
                        <table class="table">
                            <thead><tr><th>Nama</th><th>NIM</th><th>Status</th></tr></thead>
                            <tbody>
                            @foreach ($progress as $m)
                                <tr><td>{{ $m['nama'] }}</td><td>{{ $m['nim'] ?: '-' }}</td>
                                    <td><span class="badge {{ $m['status'] }}">{{ ['belum' => 'Belum', 'dikerjakan' => 'Dikerjakan', 'selesai' => 'Selesai'][$m['status']] }}</span></td></tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif
        </div>

        <section class="card reveal">
            <div class="card-head"><h3>🧮 Skor Prioritas</h3></div>
            <div class="row" style="gap:1.2rem;flex-wrap:nowrap">
                <div class="score" style="--v: {{ $p['skor'] }};--accent: var(--{{ ['mendesak' => 'berry', 'sedang' => 'amber', 'aman' => 'matcha'][$p['level']['kode']] }});width:96px;height:96px">
                    <b style="font-size:1.6rem">{{ round($p['skor']) }}</b><small>/ 100</small>
                </div>
                <div class="small muted">Dihitung otomatis dari tiga kriteria berikut.</div>
            </div>
            @php $cfg = config('priority.bobot'); @endphp
            <div class="mt">
                @foreach ([['Urgensi (sisa waktu)', 'urgensi', 'berry'], ['Bobot nilai', 'nilai', 'matcha'], ['Kesulitan', 'kesulitan', 'amber']] as $r)
                    <div style="margin-bottom:.8rem">
                        <div class="row between small"><span>{{ $r[0] }} <span class="muted">× {{ $cfg[$r[1]] }}</span></span><b>{{ $p[$r[1]] }}</b></div>
                        <div class="bar"><i data-w="{{ $p[$r[1]] }}" style="background:var(--{{ $r[2] }})"></i></div>
                    </div>
                @endforeach
            </div>
            <div class="formula small">
                {{ $cfg['urgensi'] }}×{{ $p['urgensi'] }} + {{ $cfg['nilai'] }}×{{ $p['nilai'] }} + {{ $cfg['kesulitan'] }}×{{ $p['kesulitan'] }}<br>= <b>{{ $p['skor'] }}</b>
            </div>
            @if (auth()->user()->isMahasiswa())
                <a href="{{ route('algoritma') }}" class="btn ghost block mt">Pelajari algoritmanya →</a>
            @endif
        </section>
    </div>
@endsection
