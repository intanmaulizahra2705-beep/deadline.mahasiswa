@extends('layouts.app')
@section('title', 'Tugas')
@section('heading', $tab === 'selesai' ? 'Riwayat Tugas ✅' : 'Tugas 📝')
@section('sub', auth()->user()->isDosen() ? 'Semua tugas di kelas yang kamu ampu' : 'Diurutkan otomatis berdasarkan skor prioritas')

@section('actions')
    <a href="{{ route('tugas.create') }}" class="btn berry">＋ {{ auth()->user()->isDosen() ? 'Buat tugas' : 'Tugas pribadi' }}</a>
@endsection

@section('content')
    @php $q2 = fn ($t) => route('tugas.index', array_filter(['tab' => $t, 'q' => $q, 'kelas' => $kelasId, 'level' => $level])); @endphp

    <div class="tabs reveal">
        <a href="{{ $q2('aktif') }}" class="{{ $tab === 'aktif' ? 'on' : '' }}">Berjalan <b>{{ $hitungan['aktif'] }}</b></a>
        <a href="{{ $q2('terlambat') }}" class="{{ $tab === 'terlambat' ? 'on' : '' }}">Terlambat <b>{{ $hitungan['terlambat'] }}</b></a>
        <a href="{{ $q2('selesai') }}" class="{{ $tab === 'selesai' ? 'on' : '' }}">Selesai <b>{{ $hitungan['selesai'] }}</b></a>
        <a href="{{ $q2('semua') }}" class="{{ $tab === 'semua' ? 'on' : '' }}">Semua <b>{{ $hitungan['semua'] }}</b></a>
    </div>

    <form method="GET" class="filters reveal">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <input class="input" type="search" name="q" value="{{ $q }}" placeholder="🔍 Cari judul tugas atau kelas…">
        <select name="kelas" onchange="this.form.submit()">
            <option value="">Semua kelas</option>
            @if (auth()->user()->isMahasiswa())<option value="pribadi" @selected($kelasId === 'pribadi')>Tugas pribadi</option>@endif
            @foreach ($daftarKelas as $k)<option value="{{ $k->id }}" @selected((string) $kelasId === (string) $k->id)>{{ $k->nama }}</option>@endforeach
        </select>
        <select name="level" onchange="this.form.submit()">
            <option value="">Semua prioritas</option>
            <option value="mendesak" @selected($level === 'mendesak')>🔴 Mendesak</option>
            <option value="sedang" @selected($level === 'sedang')>🟡 Sedang</option>
            <option value="aman" @selected($level === 'aman')>🟢 Aman</option>
        </select>
        <button class="btn" type="submit">Cari</button>
    </form>

    <div class="tasks">
        @forelse ($items as $item)
            @include('partials.tugas-card', ['item' => $item])
        @empty
            <div class="card empty"><span class="big">{{ $tab === 'selesai' ? '🌱' : '🍵' }}</span>Tidak ada tugas yang cocok dengan filter ini.</div>
        @endforelse
    </div>
@endsection
