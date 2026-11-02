@extends('layouts.app')
@section('title', 'Kalender')
@section('heading', 'Kalender Deadline 📅')
@section('sub', $bulan->copy()->locale('id')->translatedFormat('F Y'))

@section('actions')
    <a class="icon-btn" href="{{ route('kalender', ['bulan' => $prev]) }}" aria-label="Bulan sebelumnya" style="color:inherit">‹</a>
    <a class="btn ghost" href="{{ route('kalender') }}">Hari ini</a>
    <a class="icon-btn" href="{{ route('kalender', ['bulan' => $next]) }}" aria-label="Bulan berikutnya" style="color:inherit">›</a>
@endsection

@section('content')
    <section class="card reveal">
        <div class="cal">
            @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $d)<div class="dow">{{ $d }}</div>@endforeach

            @foreach ($minggu as $pekan)
                @foreach ($pekan as $hari)
                    <div class="day {{ $hari['bulanIni'] ? '' : 'out' }} {{ $hari['hariIni'] ? 'today' : '' }}">
                        <span class="n">{{ $hari['tgl']->day }}</span>
                        @foreach ($hari['tugas']->take(3) as $it)
                            <a href="{{ route('tugas.show', $it['tugas']) }}"
                               class="ev {{ $it['status'] === 'selesai' ? 'done' : $it['p']['level']['kode'] }}"
                               title="{{ $it['tugas']->judul }} · {{ $it['tugas']->deadline->format('H:i') }}">
                                {{ $it['tugas']->deadline->format('H:i') }} {{ $it['tugas']->judul }}
                            </a>
                        @endforeach
                        @if ($hari['tugas']->count() > 3)<span class="xs muted">+{{ $hari['tugas']->count() - 3 }} lagi</span>@endif
                    </div>
                @endforeach
            @endforeach
        </div>
        <div class="row mt small muted">
            <span class="badge mendesak">Mendesak</span><span class="badge sedang">Sedang</span><span class="badge aman">Aman</span><span class="badge neutral">Selesai (dicoret)</span>
        </div>
    </section>
@endsection
