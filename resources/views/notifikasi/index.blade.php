@extends('layouts.app')
@section('title', 'Notifikasi')
@section('heading', 'Pusat Notifikasi 🔔')
@section('sub', 'Pengingat otomatis H-3, H-1, jam sebelum deadline, dan peringatan keterlambatan')

@section('actions')
    @if (auth()->user()->isMahasiswa())
        <form method="POST" action="{{ route('notifikasi.cek') }}">@csrf<button class="btn ghost" type="submit">🔄 Cek pengingat</button></form>
    @endif
    <form method="POST" action="{{ route('notifikasi.baca-semua') }}">@csrf<button class="btn primary" type="submit">✓ Tandai semua dibaca</button></form>
@endsection

@section('content')
    <div class="tasks" style="max-width:820px">
        @forelse ($notifikasi as $n)
            <form method="POST" action="{{ route('notifikasi.baca', $n) }}" class="reveal">
                @csrf
                <button type="submit" class="notif {{ $n->dibaca_at ? '' : 'new' }}">
                    <span class="em">{{ $n->ikon }}</span>
                    <span class="grow">
                        <b>{{ $n->judul }}</b>
                        <span class="small muted" style="display:block">{{ $n->pesan }}</span>
                        <span class="xs muted">{{ $n->created_at->diffForHumans() }}@if (! $n->dibaca_at) · <b style="color:var(--berry-deep)">baru</b>@endif</span>
                    </span>
                    <span class="badge {{ ['H-3' => 'aman', 'H-1' => 'sedang', 'JAM' => 'mendesak', 'TELAT' => 'telat'][$n->tipe] ?? 'neutral' }}">{{ $n->tipe }}</span>
                </button>
            </form>
        @empty
            <div class="card empty"><span class="big">🔕</span>Belum ada notifikasi. Pengingat muncul otomatis saat deadline mendekat.</div>
        @endforelse
    </div>

    {{ $notifikasi->links() }}
@endsection
