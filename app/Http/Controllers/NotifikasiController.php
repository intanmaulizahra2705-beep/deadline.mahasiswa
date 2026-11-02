<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Services\ReminderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function index(Request $request)
    {
        $notifikasi = $request->user()->notifikasi()->with('tugas:id,judul')->latest()->paginate(15);

        return view('notifikasi.index', compact('notifikasi'));
    }

    public function baca(Request $request, Notifikasi $notifikasi): RedirectResponse
    {
        abort_unless($notifikasi->user_id === $request->user()->id, 403);

        $notifikasi->update(['dibaca_at' => now()]);

        return $notifikasi->tugas_id
            ? redirect()->route('tugas.show', $notifikasi->tugas_id)
            : back();
    }

    public function bacaSemua(Request $request): RedirectResponse
    {
        $request->user()->notifikasi()->whereNull('dibaca_at')->update(['dibaca_at' => now()]);

        return back()->with('toast', ['ok', 'Semua notifikasi ditandai sudah dibaca.']);
    }

    /** Tombol "Cek pengingat sekarang" (tanpa menunggu scheduler). */
    public function cek(Request $request, ReminderService $service): RedirectResponse
    {
        $jumlah = $service->prosesUser($request->user());

        return back()->with('toast', $jumlah > 0
            ? ['ok', "{$jumlah} pengingat baru dibuat 🔔"]
            : ['warn', 'Belum ada pengingat baru saat ini.']);
    }
}
