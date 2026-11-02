<!doctype html>
<html lang="id">
<head>
    @include('layouts.head')
    <title>MatchaDeadline · Sistem Pengingat Deadline Akademik Mahasiswa Berbasis Web Menggunakan Laravel</title>
    <meta name="description" content="Sistem Pengingat Deadline Akademik Mahasiswa Berbasis Web Menggunakan Laravel — Metode Priority Scheduling.">
</head>
<body>
<header class="land-nav">
    <a href="{{ route('welcome') }}" class="brand" style="padding:0"><span class="logo">🍓</span> MatchaDeadline</a>
    <div class="row">
        <button class="icon-btn" data-theme-toggle aria-label="Ganti tema">🌙</button>
        <a class="btn ghost" href="{{ route('login') }}">Masuk</a>
        <a class="btn berry" href="{{ route('register') }}">Mulai gratis</a>
    </div>
</header>

<section class="hero">
    <div>
        <span class="badge matcha reveal">🍵 Metode Priority Scheduling · Laravel</span>
        <h1 class="reveal" style="margin-top:.8rem">Sistem Pengingat <em>Deadline Akademik</em> Mahasiswa</h1>
        <p class="official reveal">Berbasis Web Menggunakan Laravel — Metode Priority Scheduling</p>
        <p class="muted reveal" style="font-size:1.1rem;max-width:520px">
            Sistem menghitung <b>skor prioritas</b> tiap tugas dari sisa waktu, bobot nilai, dan tingkat kesulitan —
            lalu mengingatkanmu sebelum terlambat.
        </p>
        <div class="row reveal" style="margin-top:1.4rem">
            <a class="btn berry" href="{{ route('register') }}">Coba sekarang →</a>
            <a class="btn ghost" href="#fitur">Lihat fitur</a>
        </div>
        <div class="row reveal small muted" style="margin-top:1.4rem;gap:1.2rem">
            <span>✅ Pengingat H-3 · H-1 · jam</span><span>✅ Kelas ala Classroom</span><span>✅ Roster kuliah</span><span>✅ Mode gelap</span>
        </div>
    </div>

    <div class="hero-art reveal">
        <div class="orb"></div>
        <div class="mock">
            <div class="row between"><b>🔥 Fokus hari ini</b><span class="badge mendesak"><span class="pulse-dot"></span> 3 mendesak</span></div>
            <div class="tasks mt">
                <div class="task mendesak" style="grid-template-columns:1fr auto;padding:.7rem .9rem"><div><span class="task-title">Laporan Praktikum Laravel</span><span class="xs muted">Pemrograman Web · 04:59:12</span></div><span class="badge mendesak">92</span></div>
                <div class="task sedang" style="grid-template-columns:1fr auto;padding:.7rem .9rem"><div><span class="task-title">Rancangan ERD Perpustakaan</span><span class="xs muted">Basis Data · 1h 05:59</span></div><span class="badge sedang">64</span></div>
                <div class="task aman" style="grid-template-columns:1fr auto;padding:.7rem .9rem"><div><span class="task-title">Makalah Algoritma A*</span><span class="xs muted">Kecerdasan Buatan · 3h 11:30</span></div><span class="badge aman">31</span></div>
            </div>
        </div>
        <span class="float-emo" style="top:2%;right:6%">🍓</span>
        <span class="float-emo" style="bottom:4%;left:2%;animation-delay:-2s">🍵</span>
    </div>
</section>

<div class="marquee"><div>
    @for ($i = 0; $i < 2; $i++)
        <span>🍓 Priority Scheduling</span><span>🍵 Aging otomatis</span><span>🔔 Pengingat email &amp; web</span><span>📅 Kalender deadline</span><span>🗓️ Roster kuliah</span><span>📊 Statistik progres</span><span>🌙 Mode gelap</span>
    @endfor
</div></div>

<section class="section" id="fitur">
    <div class="center reveal" style="max-width:640px;margin:0 auto 2rem">
        <h2 style="font-size:2rem">Semua yang kamu butuhkan untuk tidak telat</h2>
        <p class="muted">Fokus pada satu hal: deadline akademik yang terkelola dan terukur.</p>
    </div>
    <div class="grid g3">
        @foreach ([
            ['🧮', 'matcha', 'Priority Scheduling', 'Skor 0–100 dari urgensi, bobot nilai, dan kesulitan. Makin dekat deadline, skor naik otomatis (aging).'],
            ['🔔', 'berry', 'Pengingat Otomatis', 'Notifikasi web dan email pada H-3, H-1, beberapa jam sebelum, hingga peringatan keterlambatan.'],
            ['🍵', 'latte', 'Kelas ala Classroom', 'Dosen membuat kelas dan tugas, mahasiswa gabung lewat kode. Tugas langsung muncul di dashboard.'],
            ['📅', 'amber', 'Kalender Deadline', 'Lihat semua deadline per tanggal dalam satu tampilan bulanan berwarna.'],
            ['🗓️', 'matcha', 'Jadwal Kerja Harian', 'Sistem menyusun urutan pengerjaan sesuai prioritas dan jatah jam belajarmu per hari.'],
            ['📊', 'berry', 'Statistik Progres', 'Pantau tugas selesai, berjalan, dan terlambat lewat grafik yang interaktif.'],
            ['🗓️', 'amber', 'Roster Kuliah', 'Jadwal kuliah per kelas dalam tampilan mingguan atau daftar, lengkap dengan ruang dan dosen.'],
            ['🌙', 'latte', 'Mode Gelap & Responsif', 'Nyaman dibuka malam hari, dan tetap rapi di laptop maupun HP.'],
            ['👩‍🏫', 'matcha', 'Peran Dosen & Mahasiswa', 'Dosen memantau progres kelas, mahasiswa fokus pada tugas yang paling mendesak.'],
        ] as $i => $f)
            <div class="card hover reveal feature">
                <div class="ico {{ $f[1] }}">{{ $f[0] }}</div>
                <h3>{{ $f[2] }}</h3>
                <p class="muted small">{{ $f[3] }}</p>
            </div>
        @endforeach
    </div>
</section>

<section class="section">
    <div class="card reveal" style="background:var(--grad);color:#fff;border:0;text-align:center;padding:3rem 1.4rem">
        <h2 style="color:#fff;font-size:2rem">Siap mengatur deadline-mu? 🍓</h2>
        <p style="opacity:.92">Buat akun dalam semenit, atau coba akun demo di halaman masuk.</p>
        <a class="btn" href="{{ route('register') }}" style="background:#fff;color:var(--berry-deep)">Daftar gratis</a>
    </div>
    <p class="center xs muted mt2 official-foot">© {{ date('Y') }} MatchaDeadline · Sistem Pengingat Deadline Akademik Mahasiswa Berbasis Web Menggunakan Laravel — Metode Priority Scheduling</p>
</section>

<script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
