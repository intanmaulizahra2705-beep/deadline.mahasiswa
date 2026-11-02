<!doctype html>
<html lang="id">
<head>
    @include('layouts.head')
    <title>@yield('title') · MatchaDeadline · Pengingat Deadline Akademik</title>
</head>
<body>
<div class="guest-wrap">
    <section class="guest-art">
        <span class="blob" style="width:260px;height:260px;top:-60px;right:-60px"></span>
        <span class="blob" style="width:180px;height:180px;bottom:40px;left:-50px;animation-delay:-2s"></span>
        <span class="float-emo" style="top:12%;left:14%">🍓</span>
        <span class="float-emo" style="top:62%;right:12%;animation-delay:-1.5s">🍵</span>
        <span class="float-emo" style="bottom:10%;left:30%;animation-delay:-3s">⏰</span>

        <a href="{{ route('welcome') }}" class="brand" style="color:#fff;padding-left:0">
            <span class="logo" style="background:#fff">🍓</span> MatchaDeadline
        </a>
        <h1 style="font-size:clamp(2rem,3.4vw,3rem);margin-top:1rem">Deadline tertata,<br><em>hidup lebih manis.</em></h1>
        <p class="official">Sistem Pengingat Deadline Akademik Mahasiswa Berbasis Web Menggunakan Laravel — Metode Priority Scheduling</p>
        <p style="max-width:430px;opacity:.92">Tugas diurutkan otomatis dari yang paling mendesak, lalu kamu diingatkan sebelum terlambat.</p>
        <div class="row" style="margin-top:1rem">
            <span class="badge" style="background:rgba(255,255,255,.22);color:#fff">⚡ Skor prioritas otomatis</span>
            <span class="badge" style="background:rgba(255,255,255,.22);color:#fff">🔔 Pengingat H-3 · H-1 · jam</span>
        </div>
    </section>

    <section class="guest-form">
        <div class="box">
            @yield('content')
        </div>
    </section>
</div>
<button class="icon-btn" data-theme-toggle style="position:fixed;top:1rem;right:1rem" aria-label="Ganti tema">🌙</button>
@if (session('toast'))
    <div hidden data-toast="{{ session('toast')[1] }}" data-type="{{ session('toast')[0] }}"></div>
@endif
<div class="toasts"></div>
<script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
