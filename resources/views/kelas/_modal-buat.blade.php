<div class="modal" id="modal-kelas" @if ($errors->has('nama') || $errors->has('warna')) data-auto-open @endif>
    <div class="box">
        <div class="row between"><h2>Buat kelas baru 🍵</h2><button class="icon-btn" data-close type="button">✕</button></div>
        <form method="POST" action="{{ route('kelas.store') }}" class="mt">
            @csrf
            <div class="field">
                <label class="lbl">Nama mata kuliah / kelas</label>
                <input class="input" name="nama" value="{{ old('nama') }}" required placeholder="mis. Pemrograman Web Lanjut">
                @error('nama')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label class="lbl">Deskripsi (opsional)</label>
                <textarea class="input" name="deskripsi" style="min-height:80px">{{ old('deskripsi') }}</textarea>
            </div>
            <div class="field">
                <label class="lbl">Warna kelas</label>
                <div class="color-pick">
                    @foreach (\App\Models\Kelas::WARNA as $i => $w)
                        <label><input type="radio" name="warna" value="{{ $w }}" {{ old('warna', 'matcha') === $w ? 'checked' : '' }}><span class="c-{{ $w }}"></span></label>
                    @endforeach
                </div>
            </div>
            <button class="btn primary block" type="submit">Buat kelas</button>
        </form>
    </div>
</div>
