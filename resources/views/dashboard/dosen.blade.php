@extends('layouts.app')
@section('title', 'Dashboard Dosen')
@section('heading', 'Selamat datang, '.explode(' ', trim(str_replace(['Dr.', 'Prof.'], '', auth()->user()->name)))[0].' 👩‍🏫')
@section('sub', 'Pantau kelas, tugas, dan progres mahasiswa')

@section('actions')
    <a href="{{ route('tugas.create') }}" class="btn berry">＋ Buat tugas</a>
@endsection

@section('content')
    <div class="grid g4">
        <div class="card hover reveal stat"><div class="ico matcha">🍵</div><div><div class="num" data-count="{{ $stat['kelas'] }}">0</div><div class="lbl">Kelas diampu</div></div></div>
        <div class="card hover reveal stat"><div class="ico berry">🎓</div><div><div class="num" data-count="{{ $stat['mahasiswa'] }}">0</div><div class="lbl">Total mahasiswa</div></div></div>
        <div class="card hover reveal stat"><div class="ico amber">📝</div><div><div class="num" data-count="{{ $stat['tugas'] }}">0</div><div class="lbl">Tugas dibuat</div></div></div>
        <div class="card hover reveal stat"><div class="ico latte">⏳</div><div><div class="num" data-count="{{ $stat['mendatang'] }}">0</div><div class="lbl">Deadline mendatang</div></div></div>
    </div>

    <div class="grid split mt">
        <section class="card reveal">
            <div class="card-head"><h2>Deadline Mendatang</h2><a href="{{ route('tugas.index') }}" class="small"><b>Semua tugas →</b></a></div>
            <div class="scroll-x">
                <table class="table">
                    <thead><tr><th>Tugas</th><th>Kelas</th><th>Deadline</th><th>Progres</th></tr></thead>
                    <tbody>
                    @forelse ($mendatang as $t)
                        <tr>
                            <td><a href="{{ route('tugas.show', $t) }}"><b>{{ $t->judul }}</b></a></td>
                            <td><span class="badge {{ $t->kelas->warna }}">{{ $t->kelas->nama }}</span></td>
                            <td>{{ $t->deadline_singkat }}<div class="xs muted countdown" data-deadline="{{ $t->deadline->toIso8601String() }}"></div></td>
                            <td style="min-width:140px">
                                <div class="bar"><i data-w="{{ $t->persen }}"></i></div>
                                <span class="xs muted">{{ $t->selesai_count }}/{{ $t->total_mhs }} selesai</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty"><span class="big">🍃</span>Belum ada deadline mendatang. Buat tugas baru!</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div class="grid" style="gap:1.1rem">
            <section class="card reveal">
                <div class="card-head"><h3>Kelas Saya</h3><button class="btn sm primary" data-open="modal-kelas">＋ Kelas</button></div>
                <div class="tasks" style="gap:.6rem">
                    @forelse ($kelas as $k)
                        <a href="{{ route('kelas.show', $k) }}" class="row between" style="padding:.7rem .9rem;border-radius:14px;background:var(--surface-2);color:var(--ink)">
                            <span><b>{{ $k->nama }}</b><br><span class="xs muted">{{ $k->anggota_count }} mhs · {{ $k->tugas_count }} tugas</span></span>
                            <span class="code" data-copy="{{ $k->kode }}">{{ $k->kode }}</span>
                        </a>
                    @empty
                        <p class="muted small">Belum ada kelas.</p>
                    @endforelse
                </div>
            </section>

            <section class="card reveal">
                <div class="card-head"><h3>Baru Lewat Deadline</h3></div>
                @forelse ($lewat as $t)
                    <div class="row between" style="padding:.5rem 0;border-bottom:1px dashed var(--line)">
                        <a href="{{ route('tugas.show', $t) }}"><b class="small">{{ $t->judul }}</b></a>
                        <span class="badge {{ $t->persen >= 80 ? 'aman' : ($t->persen >= 40 ? 'sedang' : 'mendesak') }}">{{ $t->persen }}%</span>
                    </div>
                @empty
                    <p class="muted small" style="margin:0">Belum ada.</p>
                @endforelse
            </section>
        </div>
    </div>

    @include('kelas._modal-buat')
@endsection
