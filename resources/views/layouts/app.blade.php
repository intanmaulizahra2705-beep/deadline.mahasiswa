<!doctype html>
<html lang="id">
<head>
    @include('layouts.head')
    <title>@yield('title', 'Dashboard') · MatchaDeadline</title>
</head>
<body>
@php
    $u = auth()->user();
    $aktif = fn (...$names) => request()->routeIs(...$names) ? 'active' : '';
@endphp

<div class="app">
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('dashboard') }}" class="brand">
            <span class="logo">🍓</span>
            <span>MatchaDeadline<small>Pengingat deadline akademik</small></span>
        </a>

        <nav class="nav">
            <div class="nav-label">Menu</div>
            <a href="{{ route('dashboard') }}" class="{{ $aktif('dashboard') }}"><span class="ic">🏠</span> Dashboard</a>
            <a href="{{ route('kelas.index') }}" class="{{ $aktif('kelas.*') }}"><span class="ic">🍵</span> Kelas</a>
            <a href="{{ route('tugas.index') }}" class="{{ $aktif('tugas.*') }}"><span class="ic">📝</span> Tugas</a>
            <a href="{{ route('kalender') }}" class="{{ $aktif('kalender') }}"><span class="ic">📅</span> Kalender</a>
            <a href="{{ route('roster') }}" class="{{ $aktif('roster') }}"><span class="ic">🗓️</span> Roster</a>

            @if ($u->isMahasiswa())
                <a href="{{ route('tugas.index', ['tab' => 'selesai']) }}" class="{{ request('tab') === 'selesai' ? 'active' : '' }}"><span class="ic">✅</span> Riwayat</a>
                <a href="{{ route('algoritma') }}" class="{{ $aktif('algoritma') }}"><span class="ic">🧮</span> Priority Scheduling</a>
            @endif

            <div class="nav-label">Akun</div>
            <a href="{{ route('notifikasi.index') }}" class="{{ $aktif('notifikasi.*') }}">
                <span class="ic">🔔</span> Notifikasi
                @if ($notifBelumDibaca > 0)<span class="count">{{ $notifBelumDibaca }}</span>@endif
            </a>
            <a href="{{ route('pengaturan') }}" class="{{ $aktif('pengaturan') }}"><span class="ic">⚙️</span> Pengaturan</a>
        </nav>

        <div class="sidebar-foot">
            <div class="user-chip">
                <div class="avatar">{{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}</div>
                <div class="grow" style="min-width:0">
                    <b style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $u->name }}</b>
                    <span>{{ $u->isDosen() ? 'Dosen' : 'Mahasiswa' }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt">
                @csrf
                <button class="btn ghost sm block" type="submit">Keluar</button>
            </form>
        </div>
    </aside>
    <div class="scrim"></div>

    <main class="main page-in">
        <div class="topbar">
            <button class="icon-btn menu-btn" data-menu aria-label="Menu">☰</button>
            <div class="title">
                <h1>@yield('heading')</h1>
                @hasSection('sub')<p>@yield('sub')</p>@endif
            </div>
            @yield('actions')
            <button class="icon-btn" data-theme-toggle aria-label="Ganti tema">🌙</button>
            <a class="icon-btn" href="{{ route('notifikasi.index') }}" aria-label="Notifikasi" style="color:inherit">
                🔔 @if ($notifBelumDibaca > 0)<span class="dot">{{ $notifBelumDibaca }}</span>@endif
            </a>
        </div>

        @yield('content')
    </main>
</div>

@if (session('toast'))
    <div hidden data-toast="{{ session('toast')[1] }}" data-type="{{ session('toast')[0] }}"></div>
@endif
<div class="toasts"></div>
<script src="{{ asset('js/app.js') }}" defer></script>
@stack('scripts')
</body>
</html>
