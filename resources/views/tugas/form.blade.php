@extends('layouts.app')
@section('title', $mode === 'buat' ? 'Tugas baru' : 'Ubah tugas')
@section('heading', $mode === 'buat' ? 'Tugas baru ✨' : 'Ubah tugas ✏️')
@section('sub', auth()->user()->isDosen() ? 'Tugas akan muncul otomatis di dashboard semua mahasiswa kelas' : 'Tugas pribadi hanya terlihat olehmu')

@section('content')
    @php
        $dl = old('deadline', $tugas->deadline?->format('Y-m-d\TH:i') ?? now()->addDays(3)->setTime(23, 59)->format('Y-m-d\TH:i'));
    @endphp
    <form method="POST" action="{{ $mode === 'buat' ? route('tugas.store') : route('tugas.update', $tugas) }}" class="card reveal" style="max-width:760px">
        @csrf
        @if ($mode === 'ubah') @method('PUT') @endif

        @if (auth()->user()->isDosen() && $mode === 'buat')
            <div class="field">
                <label class="lbl">Kelas</label>
                <select name="kelas_id" required>
                    <option value="">— pilih kelas —</option>
                    @foreach ($kelasList as $k)<option value="{{ $k->id }}" @selected((string) old('kelas_id', $tugas->kelas_id) === (string) $k->id)>{{ $k->nama }}</option>@endforeach
                </select>
                @error('kelas_id')<div class="err">{{ $message }}</div>@enderror
            </div>
        @endif

        <div class="field">
            <label class="lbl">Judul tugas</label>
            <input class="input" name="judul" value="{{ old('judul', $tugas->judul) }}" required placeholder="mis. Laporan Praktikum Bab 3">
            @error('judul')<div class="err">{{ $message }}</div>@enderror
        </div>
        <div class="field">
            <label class="lbl">Deskripsi</label>
            <textarea class="input" name="deskripsi" placeholder="Petunjuk pengerjaan, format pengumpulan, dll.">{{ old('deskripsi', $tugas->deskripsi) }}</textarea>
        </div>
        <div class="field">
            <label class="lbl">Deadline</label>
            <input class="input" type="datetime-local" name="deadline" value="{{ $dl }}" required>
            @error('deadline')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="grid g2">
            <div class="field">
                <label class="lbl">Bobot nilai (% dari nilai akhir)</label>
                <div class="range-wrap"><input type="range" name="bobot" min="1" max="50" value="{{ old('bobot', $tugas->bobot) }}" data-out="v-bobot"><span class="range-val" id="v-bobot">10</span></div>
                <div class="hint">Makin besar bobotnya, makin tinggi skor prioritasnya.</div>
                @error('bobot')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label class="lbl">Tingkat kesulitan (1–5)</label>
                <div class="range-wrap"><input type="range" name="kesulitan" min="1" max="5" value="{{ old('kesulitan', $tugas->kesulitan) }}" data-out="v-sulit"><span class="range-val" id="v-sulit">3</span></div>
                <div class="hint">1 = sangat mudah · 5 = sangat sulit</div>
                @error('kesulitan')<div class="err">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="field" style="max-width:260px">
            <label class="lbl">Estimasi waktu pengerjaan (jam)</label>
            <input class="input" type="number" step="0.5" min="0.5" max="99" name="estimasi_jam" value="{{ old('estimasi_jam', $tugas->estimasi_jam) }}" required>
            <div class="hint">Dipakai menyusun jadwal kerja harian.</div>
            @error('estimasi_jam')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="row" style="margin-top:1.2rem">
            <button class="btn primary" type="submit">{{ $mode === 'buat' ? 'Simpan tugas' : 'Simpan perubahan' }}</button>
            <a class="btn ghost" href="{{ url()->previous() }}">Batal</a>
        </div>
    </form>
@endsection
