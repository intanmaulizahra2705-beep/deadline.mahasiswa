<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Services\PriorityScheduler;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    public function __construct(private PriorityScheduler $scheduler)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $kelas = $user->isDosen()
            ? $user->kelasDiajar()->withCount(['anggota', 'tugas'])->latest()->get()
            : $user->kelasDiikuti()->withCount(['anggota', 'tugas'])->with('dosen:id,name')->latest('kelas_anggota.created_at')->get();

        return view('kelas.index', compact('kelas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama'      => ['required', 'string', 'max:120'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'warna'     => ['required', Rule::in(Kelas::WARNA)],
        ]);

        $request->user()->kelasDiajar()->create($data + ['kode' => Kelas::buatKode()]);

        return redirect()->route('kelas.index')->with('toast', ['ok', 'Kelas berhasil dibuat 🍵']);
    }

    public function gabung(Request $request): RedirectResponse
    {
        $request->validate(['kode' => ['required', 'string', 'max:10']]);

        $kelas = Kelas::where('kode', strtoupper(trim($request->kode)))->first();

        if (! $kelas) {
            return back()->withErrors(['kode' => 'Kode kelas tidak ditemukan.'])->withInput();
        }

        if ($kelas->anggota()->where('user_id', $request->user()->id)->exists()) {
            return back()->with('toast', ['warn', 'Kamu sudah tergabung di kelas ini.']);
        }

        $kelas->anggota()->attach($request->user()->id);

        return redirect()->route('kelas.show', $kelas)->with('toast', ['ok', "Berhasil bergabung ke {$kelas->nama} 🍓"]);
    }

    public function show(Request $request, Kelas $kelas)
    {
        $user = $request->user();
        $this->izinkanAkses($user, $kelas);

        $kelas->load('dosen:id,name')->loadCount('anggota');

        if ($user->isDosen()) {
            $tugas = $kelas->tugas()
                ->withCount(['statuses as selesai_count' => fn ($q) => $q->where('status', 'selesai')])
                ->orderBy('deadline')->get();
            $tugas->each(fn ($t) => $t->p = $this->scheduler->hitung($t));
            $anggota = $kelas->anggota()->orderBy('name')->get();

            return view('kelas.show', ['kelas' => $kelas, 'tugas' => $tugas, 'anggota' => $anggota, 'items' => collect()]);
        }

        $items = $this->scheduler->urutkan(
            $this->scheduler->daftarUntuk($user)->filter(fn ($i) => $i['tugas']->kelas_id === $kelas->id)
        );
        $anggota = collect();

        return view('kelas.show', ['kelas' => $kelas, 'items' => $items, 'anggota' => $anggota, 'tugas' => collect()]);
    }

    public function keluar(Request $request, Kelas $kelas): RedirectResponse
    {
        $kelas->anggota()->detach($request->user()->id);

        return redirect()->route('kelas.index')->with('toast', ['ok', 'Kamu keluar dari kelas.']);
    }

    public function destroy(Request $request, Kelas $kelas): RedirectResponse
    {
        abort_unless($kelas->dosen_id === $request->user()->id, 403);

        $kelas->delete();

        return redirect()->route('kelas.index')->with('toast', ['ok', 'Kelas dihapus.']);
    }

    private function izinkanAkses($user, Kelas $kelas): void
    {
        $boleh = $user->isDosen()
            ? $kelas->dosen_id === $user->id
            : $kelas->anggota()->where('user_id', $user->id)->exists();

        abort_unless($boleh, 403, 'Kamu tidak punya akses ke kelas ini.');
    }
}
