@extends('layouts.guest')
@section('title', 'Masuk')

@section('content')
    <h1>Halo lagi! 🍵</h1>
    <p class="muted">Masuk untuk melihat tugas dan deadline kamu.</p>

    <form method="POST" action="{{ route('login.proses') }}" class="mt2">
        @csrf
        <div class="field">
            <label class="lbl" for="email">Email</label>
            <input class="input" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@kampus.ac.id">
            @error('email')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="field">
            <label class="lbl" for="password">Kata sandi</label>
            <input class="input" type="password" id="password" name="password" required placeholder="••••••••">
            @error('password')<div class="err">{{ $message }}</div>@enderror
        </div>
        <label class="row small" style="margin-bottom:1rem"><input type="checkbox" name="ingat" value="1" style="accent-color:var(--matcha)"> Ingat saya</label>
        <button class="btn primary block" type="submit">Masuk</button>
    </form>

    <p class="center small muted mt2">Belum punya akun? <a href="{{ route('register') }}"><b>Daftar sekarang</b></a></p>

    <div class="card mt2" style="background:var(--matcha-soft);box-shadow:none;border:0">
        <b class="small">Akun demo</b>
        <div class="small muted" style="margin-top:.3rem">
            Mahasiswa: <code>mhs@demo.test</code><br>
            Dosen: <code>dosen@demo.test</code><br>
            Kata sandi: <code>password</code>
        </div>
    </div>
@endsection
