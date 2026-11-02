@extends('layouts.app')
@section('title', 'Roster Kuliah')
@section('heading', 'Roster Kuliah 🗓️')
@section('sub', 'Semester '.$cfg['semester'].' · Tahun akademik '.$cfg['tahun'])

@section('actions')
    <button type="button" class="btn ghost" onclick="window.print()">🖨️ Cetak</button>
@endsection

@section('content')
<div id="rosterPage" data-kelas="{{ $kelas }}" data-param="{{ $adaParam ? '1' : '0' }}">
    <script type="application/json" id="rosterKelasList">@json($daftar)</script>
    <script type="application/json" id="rosterItems">@json($items)</script>

    {{-- Ringkasan --}}
    <div class="grid g4">
        <div class="card hover reveal stat"><div class="ico matcha">📚</div><div><div class="num" data-count="{{ $ringkas['matkul'] }}">0</div><div class="lbl">Mata kuliah</div></div></div>
        <div class="card hover reveal stat"><div class="ico berry">🎯</div><div><div class="num" data-count="{{ $ringkas['sks'] }}">0</div><div class="lbl">Total SKS</div></div></div>
        <div class="card hover reveal stat"><div class="ico amber">📆</div><div><div class="num" data-count="{{ $ringkas['hariAktif'] }}">0</div><div class="lbl">Hari kuliah</div></div></div>
        <div class="card hover reveal stat"><div class="ico latte">👩‍🏫</div><div><div class="num" data-count="{{ $ringkas['dosen'] }}">0</div><div class="lbl">Dosen pengampu</div></div></div>
    </div>

    {{-- Pilih kelas & tampilan --}}
    <div class="row between mt2" style="align-items:flex-start">
        <div class="tabs" style="margin:0">
            @foreach ($daftar as $k)
                <a href="{{ route('roster', ['kelas' => $k, 'tampil' => $tampil]) }}" class="{{ $k === $kelas ? 'on' : '' }}">Kelas {{ $k }}</a>
            @endforeach
        </div>
        <div class="seg">
            <a href="{{ route('roster', ['kelas' => $kelas, 'tampil' => 'mingguan']) }}" class="{{ $tampil === 'mingguan' ? 'on' : '' }}">Mingguan</a>
            <a href="{{ route('roster', ['kelas' => $kelas, 'tampil' => 'daftar']) }}" class="{{ $tampil === 'daftar' ? 'on' : '' }}">Daftar</a>
        </div>
    </div>

    {{-- Kuliah berikutnya (diisi JavaScript sesuai jam perangkat) --}}
    <div class="rnext reveal" id="rNext" hidden>
        <span class="badge" id="rNextBadge">Berikutnya</span>
        <div class="grow" style="min-width:0">
            <b id="rNextMk"></b>
            <div class="xs" id="rNextInfo"></div>
        </div>
    </div>

    @if ($tampil === 'mingguan')
        {{-- Grid mingguan (desktop) --}}
        <section class="card reveal rwrap">
            <div class="scroll-x">
                <div class="rgrid">
                    <div></div>
                    @foreach ($hari as $h)
                        <div class="rh" data-hari="{{ $h }}">{{ $h }}</div>
                    @endforeach

                    @foreach ($slots as $si => $s)
                        <div class="rt">{{ $s[0] }}<br><span>{{ $s[1] }}</span></div>
                        @foreach ($hari as $h)
                            <div class="rcell" data-hari="{{ $h }}">
                                @foreach ($grid[$si][$h] as $it)
                                    <div class="rslot rc-{{ $it['warna'] }}" data-slot data-h="{{ $h }}" data-mulai="{{ $it['mulai'] }}" data-selesai="{{ $it['selesai'] }}" title="{{ $it['mk'] }} · {{ $it['dosen'] }}">
                                        <b>{{ $it['singkat'] }}</b>
                                        <span>{{ $it['ruang'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
            <p class="hint" style="margin:.9rem 0 0">Arahkan kursor ke blok untuk melihat nama lengkap mata kuliah dan dosen. Lihat tab <b>Daftar</b> untuk rincian lengkap.</p>
        </section>

        {{-- Per hari (HP) --}}
        <div class="rdays">
            @foreach ($perHari as $h => $list)
                @if (count($list))
                    <section class="card reveal rday" data-hari="{{ $h }}">
                        <div class="card-head"><h3>{{ $h }}</h3><span class="badge neutral">{{ count($list) }} mata kuliah</span></div>
                        <div class="tasks" style="gap:.6rem">
                            @foreach ($list as $it)
                                <div class="rslot rc-{{ $it['warna'] }}" data-slot data-h="{{ $h }}" data-mulai="{{ $it['mulai'] }}" data-selesai="{{ $it['selesai'] }}">
                                    <span class="xs">{{ $it['mulai'] }}–{{ $it['selesai'] }} · {{ $it['ruang'] }}</span>
                                    <b>{{ $it['mk'] }}</b>
                                    <span class="xs">{{ $it['dosen'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            @endforeach
        </div>
    @else
        <section class="card reveal">
            <div class="card-head"><h2>Daftar mata kuliah · Kelas {{ $kelas }}</h2><span class="badge matcha">{{ $ringkas['sks'] }} SKS</span></div>
            <div class="scroll-x">
                <table class="table">
                    <thead>
                    <tr><th>No</th><th>Mata kuliah</th><th>SKS</th><th>Hari</th><th>Jam</th><th>Ruang</th><th>Dosen pengampu</th></tr>
                    </thead>
                    <tbody>
                    @foreach ($items as $it)
                        <tr data-hari="{{ $it['hari'] }}" data-slot data-h="{{ $it['hari'] }}" data-mulai="{{ $it['mulai'] }}" data-selesai="{{ $it['selesai'] }}">
                            <td>{{ $it['no'] }}</td>
                            <td><span class="rdot rc-{{ $it['warna'] }}"></span><b>{{ $it['mk'] }}</b></td>
                            <td>{{ $it['sks'] }}</td>
                            <td><span class="badge neutral">{{ $it['hari'] }}</span></td>
                            <td style="white-space:nowrap">{{ $it['mulai'] }}–{{ $it['selesai'] }}</td>
                            <td><span class="badge matcha">{{ $it['ruang'] }}</span></td>
                            <td>{{ $it['dosen'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    <p class="xs muted center mt2">{{ $cfg['kampus'] }} · Jadwal dapat berubah, cek pengumuman resmi dari fakultas.</p>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/roster.js') }}" defer></script>
@endpush
