<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    public function index()
    {
        $user = User::findOrFail(session('user_id'));
        return view('profil.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = User::findOrFail(session('user_id'));
        $request->validate([
            'nama'  => 'required|string|max:100',
            'no_hp' => 'nullable|string',
        ]);

        $data = $request->only(['nama','no_hp','jabatan']);

        if ($request->filled('password')) {
            $request->validate(['password'=>'min:6','password_confirmation'=>'same:password']);
            $data['password'] = password_hash($request->password, PASSWORD_BCRYPT);
        }

        if ($request->hasFile('ttd')) {
            $data['ttd_path'] = $request->file('ttd')->store('ttd','public');
        }

        $user->update($data);
        session(['user_nama' => $user->nama]);

        return back()->with('toast','Profil berhasil diupdate!');
    }
}