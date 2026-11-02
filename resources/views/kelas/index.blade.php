@extends('layouts.app')
@section('title', 'Kelas')
@section('heading', 'Kelas 🍵')
@section('sub', auth()->user()->isDosen() ? 'Kelas yang kamu ampu' : 'Kelas yang kamu ikuti')

@section('actions')
    @if (auth()->user()->isDosen())
        <button class="btn primary" data-open="modal-kelas">＋ Buat kelas</button>
    @else
        <button class="btn primary" data-open="modal-gabung">＋ Gabung kelas</button>
    @endif
@endsection

@section('content')
    <div class="grid g3">
        @forelse ($kelas as $k)
            <a href="{{ route('kelas.show', $k) }}" class="card hover kelas-card reveal" style="color:inherit;display:block">
                <div class="kelas-cover c-{{ $k->warna }}"><span class="kelas-ico">🍵</span><h3>{{ $k->nama }}</h3></div>
                <div class="kelas-body">
                    <p class="muted small" style="min-height:2.6em">{{ \Illuminate\Support\Str::limit($k->deskripsi ?: 'Belum ada deskripsi.', 90) }}</p>
                    <div class="row between">
                        <span class="small muted">
                            @if (auth()->user()->isMahasiswa()) 👩‍🏫 {{ $k->dosen->name }} @else 🎓 {{ $k->anggota_count }} mahasiswa @endif
                        </span>
                        <span class="badge neutral">📝 {{ $k->tugas_count }} tugas</span>
                    </div>
                    @if (auth()->user()->isDosen())
                        <div class="row between mt"><span class="xs muted">Kode gabung</span><span class="code" data-copy="{{ $k->kode }}">{{ $k->kode }}</span></div>
                    @endif
                </div>
            </a>
        @empty
            <div class="card empty reveal" style="grid-column:1/-1">
                <span class="big">🍃</span>
                @if (auth()->user()->isDosen()) Belum ada kelas. Buat kelas pertamamu lalu bagikan kodenya ke mahasiswa.
                @else Kamu belum bergabung di kelas mana pun. Minta kode kelas ke dosenmu. @endif
            </div>
        @endforelse
    </div>

    @if (auth()->user()->isDosen())
        @include('kelas._modal-buat')
    @else
        <div class="modal" id="modal-gabung" @if ($errors->has('kode')) data-auto-open @endif>
            <div class="box">
                <div class="row between"><h2>Gabung kelas 🍓</h2><button class="icon-btn" data-close type="button">✕</button></div>
                <p class="muted small">Masukkan kode kelas 6 karakter dari dosenmu.</p>
                <form method="POST" action="{{ route('kelas.gabung') }}">
                    @csrf
                    <div class="field">
                        <input class="input" name="kode" value="{{ old('kode') }}" maxlength="10" placeholder="mis. WEB101" required
                               style="text-transform:uppercase;letter-spacing:.2em;font-weight:800;text-align:center;font-size:1.2rem">
                        @error('kode')<div class="err">{{ $message }}</div>@enderror
                    </div>
                    <button class="btn berry block" type="submit">Gabung</button>
                </form>
            </div>
        </div>
    @endif
@endsection
