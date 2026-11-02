<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use App\Models\TugasStatus;
use App\Services\PriorityScheduler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TugasController extends Controller
{
    public function __construct(private PriorityScheduler $scheduler)
    {
    }

    /** Daftar tugas + pencarian + filter. Tab: aktif | selesai | terlambat | semua */
    public function index(Request $request)
    {
        $user = $request->user();
        $tab  = $request->query('tab', 'aktif');
        $q    = trim((string) $request->query('q', ''));
        $kelasId = $request->query('kelas');
        $level   = $request->query('level');

        $items = $this->scheduler->daftarUntuk($user);

        $hitungan = [
            'aktif'     => $items->filter(fn ($i) => $i['status'] !== 'selesai' && ! $i['p']['terlambat'])->count(),
            'terlambat' => $items->filter(fn ($i) => $i['status'] !== 'selesai' && $i['p']['terlambat'])->count(),
            'selesai'   => $items->where('status', 'selesai')->count(),
            'semua'     => $items->count(),
        ];

        $items = match ($tab) {
            'selesai'   => $items->where('status', 'selesai'),
            'terlambat' => $items->filter(fn ($i) => $i['status'] !== 'selesai' && $i['p']['terlambat']),
            'semua'     => $items,
            default     => $items->filter(fn ($i) => $i['status'] !== 'selesai' && ! $i['p']['terlambat']),
        };

        if ($q !== '') {
            $items = $items->filter(fn ($i) => str_contains(mb_strtolower($i['tugas']->judul.' '.($i['tugas']->kelas?->nama ?? '')), mb_strtolower($q)));
        }
        if ($kelasId === 'pribadi') {
            $items = $items->filter(fn ($i) => $i['tugas']->kelas_id === null);
        } elseif ($kelasId) {
            $items = $items->filter(fn ($i) => (string) $i['tugas']->kelas_id === (string) $kelasId);
        }
        if (in_array($level, ['mendesak', 'sedang', 'aman'], true)) {
            $items = $items->filter(fn ($i) => $i['p']['level']['kode'] === $level);
        }

        $items = $tab === 'selesai'
            ? $items->sortByDesc(fn ($i) => $i['tugas']->statuses->first()?->selesai_at)->values()
            : $this->scheduler->urutkan($items);

        $daftarKelas = $user->isDosen()
            ? $user->kelasDiajar()->get(['id', 'nama'])
            : $user->kelasDiikuti()->get(['kelas.id', 'kelas.nama']);

        return view('tugas.index', compact('items', 'tab', 'q', 'kelasId', 'level', 'hitungan', 'daftarKelas'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $kelasList = $user->isDosen() ? $user->kelasDiajar()->get(['id', 'nama']) : collect();

        return view('tugas.form', [
            'tugas'     => new Tugas(['bobot' => 10, 'kesulitan' => 3, 'estimasi_jam' => 2, 'kelas_id' => $request->query('kelas')]),
            'kelasList' => $kelasList,
            'mode'      => 'buat',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $data = $this->validasi($request);

        if ($user->isDosen()) {
            $request->validate(['kelas_id' => ['required', Rule::exists('kelas', 'id')->where('dosen_id', $user->id)]]);
            $data['kelas_id'] = (int) $request->kelas_id;
        } else {
            $data['kelas_id'] = null; // mahasiswa hanya membuat tugas pribadi
        }

        $tugas = Tugas::create($data + ['user_id' => $user->id]);

        return redirect()->route('tugas.show', $tugas)->with('toast', ['ok', 'Tugas ditambahkan 🍓']);
    }

    public function show(Request $request, Tugas $tugas)
    {
        $user = $request->user();
        $this->izinkanLihat($user, $tugas);

        $tugas->load('kelas.dosen:id,name', 'pembuat:id,name');
        $p = $this->scheduler->hitung($tugas);
        $status = $user->isMahasiswa()
            ? ($tugas->statuses()->where('user_id', $user->id)->value('status') ?? 'belum')
            : null;

        $progress = null;
        if ($user->isDosen() && $tugas->kelas) {
            $mhs = $tugas->kelas->anggota()->get(['users.id', 'users.name', 'users.nim_nip']);
            $st  = $tugas->statuses()->pluck('status', 'user_id');
            $progress = $mhs->map(fn ($m) => [
                'nama'   => $m->name,
                'nim'    => $m->nim_nip,
                'status' => $st[$m->id] ?? 'belum',
            ]);
        }

        return view('tugas.show', compact('tugas', 'p', 'status', 'progress'));
    }

    public function edit(Request $request, Tugas $tugas)
    {
        abort_unless($tugas->user_id === $request->user()->id, 403);

        $user = $request->user();

        return view('tugas.form', [
            'tugas'     => $tugas,
            'kelasList' => $user->isDosen() ? $user->kelasDiajar()->get(['id', 'nama']) : collect(),
            'mode'      => 'ubah',
        ]);
    }

    public function update(Request $request, Tugas $tugas): RedirectResponse
    {
        abort_unless($tugas->user_id === $request->user()->id, 403);

        $tugas->update($this->validasi($request));

        return redirect()->route('tugas.show', $tugas)->with('toast', ['ok', 'Tugas diperbarui.']);
    }

    public function destroy(Request $request, Tugas $tugas): RedirectResponse
    {
        abort_unless($tugas->user_id === $request->user()->id, 403);

        $tugas->delete();

        return redirect()->route('tugas.index')->with('toast', ['ok', 'Tugas dihapus.']);
    }

    /** Mahasiswa mengubah status: belum | dikerjakan | selesai */
    public function status(Request $request, Tugas $tugas): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        abort_unless($user->isMahasiswa(), 403);
        $this->izinkanLihat($user, $tugas);

        $status = $request->validate(['status' => ['required', Rule::in(['belum', 'dikerjakan', 'selesai'])]])['status'];

        TugasStatus::updateOrCreate(
            ['tugas_id' => $tugas->id, 'user_id' => $user->id],
            ['status' => $status, 'selesai_at' => $status === 'selesai' ? now() : null]
        );

        $pesan = match ($status) {
            'selesai'    => 'Mantap, tugas selesai! 🎉',
            'dikerjakan' => 'Status: sedang dikerjakan 🍵',
            default      => 'Status dikembalikan ke belum dikerjakan.',
        };

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'status' => $status, 'pesan' => $pesan]);
        }

        return back()->with('toast', ['ok', $pesan]);
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'judul'        => ['required', 'string', 'max:150'],
            'deskripsi'    => ['nullable', 'string', 'max:2000'],
            'deadline'     => ['required', 'date'],
            'bobot'        => ['required', 'integer', 'min:1', 'max:100'],
            'kesulitan'    => ['required', 'integer', 'min:1', 'max:5'],
            'estimasi_jam' => ['required', 'numeric', 'min:0.5', 'max:99'],
        ]);
    }

    private function izinkanLihat($user, Tugas $tugas): void
    {
        $boleh = $user->tugasQuery()->whereKey($tugas->id)->exists();
        abort_unless($boleh, 403, 'Kamu tidak punya akses ke tugas ini.');
    }
}
