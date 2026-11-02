<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function edit(Request $request)
    {
        return view('pengaturan.edit', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'nim_nip'      => ['nullable', 'string', 'max:30'],
            'jam_per_hari' => ['required', 'integer', 'min:1', 'max:16'],
            'password'     => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->fill($data + [
            'remind_h3'     => $request->boolean('remind_h3'),
            'remind_h1'     => $request->boolean('remind_h1'),
            'remind_hours'  => $request->boolean('remind_hours'),
            'email_enabled' => $request->boolean('email_enabled'),
        ])->save();

        return back()->with('toast', ['ok', 'Pengaturan disimpan ✨']);
    }
}
