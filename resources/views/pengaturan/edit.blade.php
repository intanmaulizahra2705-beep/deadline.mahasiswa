@extends('layouts.app')
@section('title', 'Pengaturan')
@section('heading', 'Pengaturan ⚙️')
@section('sub', 'Profil dan preferensi pengingat')

@section('content')
    <form method="POST" action="{{ route('pengaturan.update') }}" class="grid g2" style="align-items:start">
        @csrf @method('PUT')

        <section class="card reveal">
            <div class="card-head"><h2>Profil</h2><span class="badge matcha">{{ $user->isDosen() ? 'Dosen' : 'Mahasiswa' }}</span></div>
            <div class="field">
                <label class="lbl">Nama</label>
                <input class="input" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label class="lbl">{{ $user->isDosen() ? 'NIP' : 'NIM' }}</label>
                <input class="input" name="nim_nip" value="{{ old('nim_nip', $user->nim_nip) }}">
            </div>
            <div class="field">
                <label class="lbl">Email</label>
                <input class="input" value="{{ $user->email }}" disabled>
            </div>
            <div class="grid g2" style="gap:.8rem">
                <div class="field">
                    <label class="lbl">Kata sandi baru</label>
                    <input class="input" type="password" name="password" minlength="6" placeholder="Kosongkan jika tidak diubah">
                </div>
                <div class="field">
                    <label class="lbl">Ulangi</label>
                    <input class="input" type="password" name="password_confirmation">
                </div>
            </div>
            @error('password')<div class="err" style="margin-top:-.5rem">{{ $message }}</div>@enderror
        </section>

        <section class="card reveal">
            <div class="card-head"><h2>Pengingat & Jadwal</h2></div>
            @if ($user->isMahasiswa())
                <label class="switch"><span><b>Pengingat H-3</b><br><span class="xs muted">3 hari sebelum deadline</span></span><input type="checkbox" name="remind_h3" value="1" @checked($user->remind_h3)></label>
                <label class="switch"><span><b>Pengingat H-1</b><br><span class="xs muted">Kurang dari 24 jam sebelum deadline</span></span><input type="checkbox" name="remind_h1" value="1" @checked($user->remind_h1)></label>
                <label class="switch"><span><b>Pengingat jam-jaman</b><br><span class="xs muted">6 jam sebelum deadline</span></span><input type="checkbox" name="remind_hours" value="1" @checked($user->remind_hours)></label>
                <label class="switch"><span><b>Kirim juga lewat email</b><br><span class="xs muted">{{ $user->email }}</span></span><input type="checkbox" name="email_enabled" value="1" @checked($user->email_enabled)></label>

                <div class="field mt">
                    <label class="lbl">Jatah jam belajar per hari</label>
                    <div class="range-wrap"><input type="range" name="jam_per_hari" min="1" max="12" value="{{ old('jam_per_hari', $user->jam_per_hari) }}" data-out="v-jam"><span class="range-val" id="v-jam">4</span></div>
                    <div class="hint">Dipakai menyusun "Jadwal Kerja Hari Ini" di dashboard.</div>
                </div>
            @else
                <input type="hidden" name="jam_per_hari" value="{{ $user->jam_per_hari }}">
                <p class="muted small">Pengingat otomatis berlaku untuk mahasiswa. Sebagai dosen, kamu bisa memantau progres mahasiswa langsung di halaman tiap tugas.</p>
            @endif
        </section>

        <div style="grid-column:1/-1"><button class="btn primary" type="submit">Simpan pengaturan</button></div>
    </form>
@endsection
