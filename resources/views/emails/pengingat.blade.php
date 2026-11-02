<!doctype html>
<html lang="id">
<body style="margin:0;padding:24px;background:#fbf7f0;font-family:Arial,Helvetica,sans-serif;color:#2a382a">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;margin:0 auto;background:#ffffff;border-radius:18px;overflow:hidden;border:1px solid #e8e3d4">
    <tr><td style="background:linear-gradient(135deg,#7ba05b,#e5486e);padding:22px 26px;color:#fff;font-size:20px;font-weight:bold">🍓 MatchaDeadline</td></tr>
    <tr><td style="padding:26px">
        <p style="margin:0 0 6px;font-size:13px;color:#6d7d69">Halo, {{ $user->name }}</p>
        <h2 style="margin:0 0 12px;font-size:20px">{{ $notif->judul }}</h2>
        <p style="margin:0 0 20px;line-height:1.6">{{ $notif->pesan }}</p>
        <a href="{{ $notif->tugas_id ? route('tugas.show', $notif->tugas_id) : route('dashboard') }}"
           style="display:inline-block;background:#e5486e;color:#fff;text-decoration:none;padding:12px 22px;border-radius:12px;font-weight:bold">Buka tugas</a>
    </td></tr>
    <tr><td style="padding:14px 26px;background:#f5f2e9;font-size:12px;color:#6d7d69">Sistem Pengingat Deadline Akademik Mahasiswa · Metode Priority Scheduling.<br>Kamu menerima email ini karena pengingat email aktif. Matikan di menu Pengaturan.</td></tr>
</table>
</body>
</html>
