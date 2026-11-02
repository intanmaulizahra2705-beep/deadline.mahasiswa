@extends('layouts.app')
@section('title', $kelas->nama)
@section('heading', $kelas->nama)
@section('sub', 'Dosen: '.$kelas->dosen->name.' · '.$kelas->anggota_count.' mahasiswa')

@section('actions')
    @if (auth()->user()->isDosen())
        <a href="{{ route('tugas.create', ['kelas' => $kelas->id]) }}" class="btn berry">＋ Tugas baru</a>
    @endif
@endsection

@section('content')
    <section class="card kelas-card reveal" style="margin-bottom:1.1rem">
        <div class="kelas-cover c-{{ $kelas->warna }}" style="height:110px"><span class="kelas-ico">🍵</span><h3>{{ $kelas->nama }}</h3></div>
        <div class="kelas-body row between">
            <p class="muted small" style="margin:0;max-width:640px">{{ $kelas->deskripsi ?: 'Belum ada deskripsi.' }}</p>
            <div class="row">
                @if (auth()->user()->isDosen())
                    <span class="xs muted">Kode gabung</span><span class="code" data-copy="{{ $kelas->kode }}">{{ $kelas->kode }}</span>
                    <form method="POST" action="{{ route('kelas.destroy', $kelas) }}" onsubmit="return confirm('Hapus kelas ini beserta semua tugasnya?')">
                        @csrf @method('DELETE')
                        <button class="btn danger sm" type="submit">Hapus</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('kelas.keluar', $kelas) }}" onsubmit="return confirm('Keluar dari kelas ini?')">
                        @csrf
                        <button class="btn ghost sm" type="submit">Keluar kelas</button>
                    </form>
                @endif
            </div>
        </div>
    </section>

    @if (auth()->user()->isDosen())
        <div class="grid split">
            <section class="card reveal">
                <div class="card-head"><h2>Tugas Kelas</h2></div>
                <div class="scroll-x">
                    <table class="table">
                        <thead><tr><th>Tugas</th><th>Deadline</th><th>Bobot</th><th>Selesai</th></tr></thead>
                        <tbody>
                        @forelse ($tugas as $t)
                            @php $persen = $kelas->anggota_count ? round($t->selesai_count / $kelas->anggota_count * 100) : 0; @endphp
                            <tr>
                                <td><a href="{{ route('tugas.show', $t) }}"><b>{{ $t->judul }}</b></a></td>
                                <td>{{ $t->deadline_singkat }}</td>
                                <td>{{ $t->bobot }}%</td>
                                <td style="min-width:130px"><div class="bar"><i data-w="{{ $persen }}"></i></div><span class="xs muted">{{ $t->selesai_count }}/{{ $kelas->anggota_count }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="empty"><span class="big">📝</span>Belum ada tugas di kelas ini.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
            <section class="card reveal">
                <div class="card-head"><h3>Anggota ({{ $anggota->count() }})</h3></div>
                @forelse ($anggota as $m)
                    <div class="user-chip" style="padding:.5rem 0">
                        <div class="avatar">{{ mb_strtoupper(mb_substr($m->name, 0, 1)) }}</div>
                        <div><b>{{ $m->name }}</b><span>{{ $m->nim_nip ?: $m->email }}</span></div>
                    </div>
                @empty
                    <p class="muted small">Belum ada mahasiswa. Bagikan kode <b>{{ $kelas->kode }}</b>.</p>
                @endforelse
            </section>
        </div>
    @else
        <h2 class="reveal">Tugas di kelas ini (urut prioritas)</h2>
        <div class="tasks">
            @forelse ($items as $item)
                @include('partials.tugas-card', ['item' => $item])
            @empty
                <div class="card empty"><span class="big">🍃</span>Belum ada tugas dari dosen.</div>
            @endforelse
        </div>
    @endif
@endsection
