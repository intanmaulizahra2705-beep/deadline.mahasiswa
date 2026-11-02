@extends('layouts.app')
@section('title', 'Dashboard')
@section('heading', 'Halo, '.explode(' ', auth()->user()->name)[0].'! 👋')
@section('sub', now()->locale('id')->translatedFormat('l, d F Y').' · tugas diurutkan otomatis dengan Priority Scheduling')

@section('actions')
    <a href="{{ route('tugas.create') }}" class="btn berry">＋ Tugas pribadi</a>
@endsection

@section('content')
    {{-- Statistik --}}
    <div class="grid g4">
        <div class="card hover reveal stat"><div class="ico matcha">📚</div><div><div class="num" data-count="{{ $stat['aktif'] }}">0</div><div class="lbl">Tugas berjalan</div></div></div>
        <div class="card hover reveal stat"><div class="ico berry">🔥</div><div><div class="num" data-count="{{ $stat['mendesak'] }}">0</div><div class="lbl">Mendesak</div></div></div>
        <div class="card hover reveal stat"><div class="ico amber">✅</div><div><div class="num" data-count="{{ $stat['selesai'] }}">0</div><div class="lbl">Selesai</div></div></div>
        <div class="card hover reveal stat"><div class="ico latte">⏰</div><div><div class="num" data-count="{{ $stat['terlambat'] }}">0</div><div class="lbl">Terlambat</div></div></div>
    </div>

    <div class="grid split mt">
        <div class="grid" style="gap:1.1rem">
            {{-- Fokus hari ini --}}
            <section class="card hero-focus reveal">
                <div class="card-head">
                    <div><h2>🔥 Tugas Paling Prioritas Hari Ini</h2><span class="small" style="opacity:.9">Kerjakan berurutan dari atas</span></div>
                </div>
                @forelse ($fokus as $i => $it)
                    <a class="focus-item" href="{{ route('tugas.show', $it['tugas']) }}">
                        <span class="no">{{ $i + 1 }}</span>
                        <div class="grow" style="min-width:0">
                            <b style="display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $it['tugas']->judul }}</b>
                            <span class="xs" style="opacity:.9">{{ $it['tugas']->kelas?->nama ?? 'Pribadi' }} · skor {{ round($it['p']['skor']) }}</span>
                        </div>
                        <b class="countdown" style="color:#fff" data-deadline="{{ $it['tugas']->deadline->toIso8601String() }}">{{ $it['p']['sisa'] }}</b>
                    </a>
                @empty
                    <p style="margin:0">🎉 Tidak ada tugas yang menunggu. Nikmati harimu!</p>
                @endforelse
            </section>

            {{-- Daftar tugas --}}
            <section class="reveal">
                <div class="card-head">
                    <h2>Daftar Tugas Terurut</h2>
                    <a href="{{ route('tugas.index') }}" class="small"><b>Lihat semua →</b></a>
                </div>
                <div class="tasks">
                    @forelse ($aktif as $item)
                        @include('partials.tugas-card', ['item' => $item])
                    @empty
                        <div class="card empty"><span class="big">🍵</span>Belum ada tugas. Gabung kelas atau tambah tugas pribadi.</div>
                    @endforelse
                </div>
            </section>

            @if ($terlambat->isNotEmpty())
                <section class="reveal">
                    <div class="card-head"><h2>🚨 Terlambat</h2></div>
                    <div class="tasks">
                        @foreach ($terlambat as $item) @include('partials.tugas-card', ['item' => $item]) @endforeach
                    </div>
                </section>
            @endif
        </div>

        <div class="grid" style="gap:1.1rem">
            {{-- Kuliah hari ini (dari roster) --}}
            <section class="card reveal" id="kuliahHariIni" data-url="{{ route('roster') }}">
                <script type="application/json" id="rosterAll">@json(app(\App\Services\RosterService::class)->semua())</script>
                <div class="card-head kh-head">
                    <h3>🗓️ Kuliah Hari Ini</h3>
                    <select id="kuliahSelect" aria-label="Pilih kelas"></select>
                </div>
                <div id="kuliahList"></div>
                <a href="{{ route('roster') }}" id="kuliahLink" class="small" hidden><b>Lihat roster lengkap →</b></a>
            </section>

            {{-- Progres --}}
            <section class="card reveal">
                <div class="card-head"><h3>Progres Tugas</h3><span class="badge matcha">{{ $stat['persen'] }}% selesai</span></div>
                @php
                    $tot = max(1, $stat['total']);
                    $a = round($stat['selesai'] / $tot * 100);
                    $b = round($stat['aktif'] / $tot * 100);
                @endphp
                <div class="row" style="gap:1.2rem;flex-wrap:nowrap">
                    <div class="donut" style="--a: {{ $a }}; --b: {{ $b }}">
                        <div class="mid"><div><b data-count="{{ $stat['persen'] }}">0</b><b>%</b><br><span>selesai</span></div></div>
                    </div>
                    <div class="legend">
                        <div><i style="background:var(--matcha)"></i>Selesai · {{ $stat['selesai'] }}</div>
                        <div><i style="background:var(--amber)"></i>Berjalan · {{ $stat['aktif'] }}</div>
                        <div><i style="background:var(--berry)"></i>Terlambat · {{ $stat['terlambat'] }}</div>
                    </div>
                </div>
            </section>

            {{-- Jadwal kerja --}}
            <section class="card reveal">
                <div class="card-head">
                    <h3>🗓️ Jadwal Kerja Hari Ini</h3>
                    <span class="badge neutral">{{ rtrim(rtrim(number_format($jadwal['total'], 1, ',', ''), '0'), ',') ?: '0' }}/{{ $jadwal['jatah'] }} jam</span>
                </div>
                <div class="timeline">
                    @php $mulai = 0; @endphp
                    @forelse ($jadwal['blok'] as $blk)
                        @php
                            $it = $blk['item'];
                            $dari = $mulai; $mulai += $blk['jam'];
                            $f = fn ($x) => rtrim(rtrim(number_format($x, 1, ',', ''), '0'), ',');
                        @endphp
                        <div class="tl">
                            <div class="t">{{ $f($dari) }}–{{ $f($mulai) }}j</div>
                            <div class="blk {{ $it['p']['level']['kode'] }}">
                                <b>{{ $it['tugas']->judul }}</b>
                                <div class="xs muted">{{ $f($blk['jam']) }} jam{{ $blk['sebagian'] ? ' (sebagian, lanjut besok)' : '' }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="muted small" style="margin:0">Tidak ada yang perlu dijadwalkan.</p>
                    @endforelse
                </div>
                <p class="hint">Urutan mengikuti skor prioritas, sesuai jatah {{ $jadwal['jatah'] }} jam belajar/hari. <a href="{{ route('pengaturan') }}">Ubah jatah</a></p>
            </section>

            {{-- Deadline 7 hari --}}
            <section class="card reveal">
                <div class="card-head"><h3>Deadline 7 Hari ke Depan</h3></div>
                <div class="week">
                    @foreach ($minggu as $h)
                        <div class="col {{ $h['hariIni'] ? 'today' : '' }}">
                            <div class="b" data-h="{{ $h['jumlah'] > 0 ? max(14, round($h['jumlah'] / $maxMinggu * 100)) : 3 }}">{{ $h['jumlah'] ?: '' }}</div>
                            <span>{{ $h['label'] }}</span><span>{{ $h['tgl'] }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Notifikasi --}}
            <section class="card reveal">
                <div class="card-head"><h3>🔔 Pengingat Terbaru</h3><a href="{{ route('notifikasi.index') }}" class="small"><b>Semua</b></a></div>
                <div class="tasks" style="gap:.5rem">
                    @forelse ($notifBaru as $n)
                        <form method="POST" action="{{ route('notifikasi.baca', $n) }}">
                            @csrf
                            <button class="notif new" type="submit"><span class="em">{{ $n->ikon }}</span><span><b class="small">{{ $n->judul }}</b><span class="xs muted">{{ $n->created_at->diffForHumans() }}</span></span></button>
                        </form>
                    @empty
                        <p class="muted small" style="margin:0">Tidak ada pengingat baru. ✨</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/roster.js') }}" defer></script>
@endpush
