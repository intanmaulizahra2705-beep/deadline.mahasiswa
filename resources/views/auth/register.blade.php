@extends('layouts.guest')
@section('title', 'Daftar')

@section('content')
    <h1>Buat akun baru 🍓</h1>
    <p class="muted">Gratis dan cuma butuh semenit.</p>

    <form method="POST" action="{{ route('register.proses') }}" class="mt2">
        @csrf
        <div class="field">
            <label class="lbl">Saya adalah</label>
            <div class="seg" style="width:100%" id="rolePick">
                <button type="button" class="on" data-role="mahasiswa" style="flex:1">🎓 Mahasiswa</button>
                <button type="button" data-role="dosen" style="flex:1">👩‍🏫 Dosen</button>
            </div>
            <input type="hidden" name="role" id="roleInput" value="{{ old('role', 'mahasiswa') }}">
            @error('role')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="field">
            <label class="lbl" for="name">Nama lengkap</label>
            <input class="input" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="field">
            <label class="lbl" for="nim_nip" id="nimLabel">NIM</label>
            <input class="input" id="nim_nip" name="nim_nip" value="{{ old('nim_nip') }}">
            @error('nim_nip')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="field">
            <label class="lbl" for="email">Email</label>
            <input class="input" type="email" id="email" name="email" value="{{ old('email') }}" required>
            @error('email')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="grid g2" style="gap:.8rem">
            <div class="field">
                <label class="lbl" for="password">Kata sandi</label>
                <input class="input" type="password" id="password" name="password" required minlength="6">
            </div>
            <div class="field">
                <label class="lbl" for="password_confirmation">Ulangi</label>
                <input class="input" type="password" id="password_confirmation" name="password_confirmation" required>
            </div>
        </div>
        @error('password')<div class="err" style="margin-top:-.6rem;margin-bottom:.8rem">{{ $message }}</div>@enderror
        <button class="btn berry block" type="submit">Daftar</button>
    </form>

    <p class="center small muted mt2">Sudah punya akun? <a href="{{ route('login') }}"><b>Masuk</b></a></p>

    <script>
        (function () {
            var input = document.getElementById('roleInput'), label = document.getElementById('nimLabel');
            var btns = document.querySelectorAll('#rolePick button');
            function set(r) {
                input.value = r;
                label.textContent = r === 'dosen' ? 'NIP' : 'NIM';
                btns.forEach(function (b) { b.classList.toggle('on', b.dataset.role === r); });
            }
            btns.forEach(function (b) { b.addEventListener('click', function () { set(b.dataset.role); }); });
            set(input.value || 'mahasiswa');
        })();
    </script>
@endsection
