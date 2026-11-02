@extends('layouts.app')
@section('title', 'Priority Scheduling')
@section('heading', 'Cara Kerja Priority Scheduling 🧮')
@section('sub', 'Kenapa tugas tertentu muncul di urutan teratas? Berikut penjelasannya.')

@section('content')
    <div class="grid g2" style="align-items:start">
        <section class="card reveal">
            <h2>Rumus skor prioritas</h2>
            <p class="muted small">Setiap tugas aktif diberi skor 0–100. Skor tertinggi dikerjakan lebih dulu.</p>
            <div class="formula">Skor = ({{ $cfg['bobot']['urgensi'] }} × Urgensi) + ({{ $cfg['bobot']['nilai'] }} × Nilai) + ({{ $cfg['bobot']['kesulitan'] }} × Kesulitan)</div>

            <div class="weights mt">
                <div class="w" style="background:var(--berry-soft)"><b style="color:var(--berry-deep)">{{ $cfg['bobot']['urgensi'] * 100 }}%</b><span class="small">Urgensi<br><span class="xs muted">sisa waktu</span></span></div>
                <div class="w" style="background:var(--matcha-soft)"><b style="color:var(--matcha-deep)">{{ $cfg['bobot']['nilai'] * 100 }}%</b><span class="small">Nilai<br><span class="xs muted">bobot tugas</span></span></div>
                <div class="w" style="background:var(--amber-soft)"><b style="color:#a8710f">{{ $cfg['bobot']['kesulitan'] * 100 }}%</b><span class="small">Kesulitan<br><span class="xs muted">skala 1–5</span></span></div>
            </div>

            <h3 class="mt2">Langkah perhitungan</h3>
            <ol class="steps">
                <li><div><b>Urgensi (aging).</b> Mulai naik dari 0 saat sisa waktu {{ $cfg['jendela_hari'] }} hari, mencapai 100 saat deadline. Makin dekat, makin tinggi — tugas lama tidak akan "kelaparan".<div class="xs muted mt">Urgensi = 100 × (1 − sisa_jam ÷ {{ $cfg['jendela_hari'] * 24 }})</div></div></li>
                <li><div><b>Nilai.</b> Bobot tugas dinormalisasi: {{ $cfg['bobot_maks'] }}% atau lebih dianggap 100.<div class="xs muted mt">Nilai = min(bobot ÷ {{ $cfg['bobot_maks'] }}, 1) × 100</div></div></li>
                <li><div><b>Kesulitan.</b> Skala 1–5 diubah ke 0–100.<div class="xs muted mt">Kesulitan = (tingkat ÷ 5) × 100</div></div></li>
                <li><div><b>Urutkan.</b> Skor terbesar di atas. Jika sama: deadline lebih awal dulu, lalu estimasi pengerjaan lebih singkat dulu.</div></li>
                <li><div><b>Hitung ulang otomatis.</b> Laravel Scheduler menjalankan <code>reminders:send</code> tiap 10 menit, sehingga skor selalu mengikuti waktu.</div></li>
            </ol>

            <h3 class="mt2">Level prioritas</h3>
            <div class="row">
                <span class="badge mendesak">Mendesak · skor ≥ {{ $cfg['batas_mendesak'] }}</span>
                <span class="badge sedang">Sedang · {{ $cfg['batas_sedang'] }}–{{ $cfg['batas_mendesak'] - 1 }}</span>
                <span class="badge aman">Aman · &lt; {{ $cfg['batas_sedang'] }}</span>
            </div>
        </section>

        {{-- Simulator --}}
        <section class="card reveal" id="sim"
                 data-bobot='@json($cfg['bobot'])' data-jendela="{{ $cfg['jendela_hari'] }}" data-bmaks="{{ $cfg['bobot_maks'] }}"
                 data-mendesak="{{ $cfg['batas_mendesak'] }}" data-sedang="{{ $cfg['batas_sedang'] }}">
            <h2>🎛️ Simulator skor</h2>
            <p class="muted small">Geser slider dan lihat skor berubah secara langsung.</p>

            <div class="field">
                <label class="lbl">Sisa waktu: <span id="v-jam" style="color:var(--berry-deep)"></span></label>
                <input type="range" id="s-jam" min="0" max="240" step="1" value="48">
            </div>
            <div class="field">
                <label class="lbl">Bobot nilai: <span id="v-bobot-sim" style="color:var(--berry-deep)"></span></label>
                <input type="range" id="s-bobot" min="1" max="50" value="20" oninput="document.getElementById('v-bobot-sim').textContent=this.value+'%'">
            </div>
            <div class="field">
                <label class="lbl">Kesulitan: <span id="v-sulit-sim" style="color:var(--berry-deep)"></span></label>
                <input type="range" id="s-sulit" min="1" max="5" value="3" oninput="document.getElementById('v-sulit-sim').textContent=this.value">
            </div>

            <div class="card" style="background:var(--surface-2);box-shadow:none">
                <div class="row between">
                    <span class="small muted">Skor prioritas</span><span id="o-badge" class="badge aman">Aman</span>
                </div>
                <div style="font-family:Fraunces,serif;font-size:3rem;font-weight:700;line-height:1.1"><span id="o-skor">0</span><span class="muted" style="font-size:1rem"> / 100</span></div>
                <div class="bar mt"><i id="o-bar" style="width:0;background:var(--grad)"></i></div>
                <div class="row mt small muted" style="gap:1rem"><span>Urgensi <b id="o-u" style="color:var(--ink)">0</b></span><span>Nilai <b id="o-n" style="color:var(--ink)">0</b></span><span>Kesulitan <b id="o-k" style="color:var(--ink)">0</b></span></div>
            </div>
        </section>
    </div>

    <section class="card reveal mt">
        <div class="card-head"><h2>Urutan tugasmu saat ini</h2><span class="badge neutral">{{ $items->count() }} tugas aktif</span></div>
        <div class="scroll-x">
            <table class="table">
                <thead><tr><th>#</th><th>Tugas</th><th>Sisa waktu</th><th>Urgensi</th><th>Nilai</th><th>Kesulitan</th><th>Rincian</th><th>Skor</th></tr></thead>
                <tbody>
                @forelse ($items as $i => $it)
                    @php $p = $it['p']; @endphp
                    <tr>
                        <td><b>{{ $i + 1 }}</b></td>
                        <td><a href="{{ route('tugas.show', $it['tugas']) }}"><b>{{ $it['tugas']->judul }}</b></a><div class="xs muted">{{ $it['tugas']->kelas?->nama ?? 'Pribadi' }}</div></td>
                        <td>{{ $p['sisa'] }}</td>
                        <td>{{ $p['urgensi'] }}</td><td>{{ $p['nilai'] }}</td><td>{{ $p['kesulitan'] }}</td>
                        <td>
                            <div class="mini-bars">
                                <div><i class="u" style="width:{{ $p['urgensi'] }}%"></i></div>
                                <div><i class="v" style="width:{{ $p['nilai'] }}%"></i></div>
                                <div><i class="k" style="width:{{ $p['kesulitan'] }}%"></i></div>
                            </div>
                        </td>
                        <td><span class="badge {{ $p['level']['kode'] }}">{{ $p['skor'] }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty"><span class="big">🍃</span>Belum ada tugas aktif untuk dihitung.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <p class="hint">Bar: <span style="color:var(--berry)">■</span> urgensi · <span style="color:var(--matcha)">■</span> nilai · <span style="color:var(--amber)">■</span> kesulitan. Bobot dapat diubah di <code>config/priority.php</code>.</p>
    </section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('v-bobot-sim').textContent = document.getElementById('s-bobot').value + '%';
        document.getElementById('v-sulit-sim').textContent = document.getElementById('s-sulit').value;
    });
</script>
@endpush
