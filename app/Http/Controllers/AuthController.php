<?php

namespace App\Http\Controllers;
 
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
 
class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('user_id') && (session('kantor_id') || session('user_role') === 'admin')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }
 
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);
 
        $user = User::where('email', $request->email)->where('is_active', true)->first();
 
        if (!$user || !password_verify($request->password, $user->password)) {
            return back()->withErrors(['Email atau password salah.'])->withInput();
        }
 
        // ── Role ADMIN: tidak terikat kantor, langsung masuk ──
        if ($user->role === 'admin') {
            session([
                'user_id'    => $user->id,
                'user_nama'  => $user->nama,
                'user_role'  => 'admin',
                'user_email' => $user->email,
                'kantor_id'  => null,  // admin tidak terikat kantor
                'kantor_nama'=> 'Semua Kantor',
                'kantor_kota'=> '-',
                'kantor_kode'=> 'ALL',
            ]);
            AuditLog::catat('login', 'auth', $user->id, 'Login sebagai Admin (lintas kantor)');
            return redirect()->route('dashboard');
        }
 
        // ── Role lain: cek kantor ─────────────────────────────
        $kantors = $user->kantors()->where('is_active', true)->get();
 
        if ($kantors->isEmpty()) {
            return back()->withErrors(['Akun Anda belum memiliki akses ke kantor manapun. Hubungi administrator.'])->withInput();
        }
 
        session([
            'user_id'        => $user->id,
            'user_nama'      => $user->nama,
            'user_role'      => $user->role,
            'user_email'     => $user->email,
            'kantor_pilihan' => $kantors->toArray(),
        ]);
 
        if ($kantors->count() === 1) {
            $kantor = $kantors->first();
            session([
                'kantor_id'   => $kantor->id,
                'kantor_nama' => $kantor->nama,
                'kantor_kota' => $kantor->kota,
                'kantor_kode' => $kantor->kode,
            ]);
            session()->forget('kantor_pilihan');
            AuditLog::catat('login', 'auth', $user->id, 'Login berhasil - ' . $kantor->nama);
            return redirect()->route('dashboard');
        }
 
        return redirect()->route('pilih.kantor');
    }
 
    public function showPilihKantor()
    {
        if (!session('user_id')) return redirect()->route('login');
        if (session('kantor_id') || session('user_role') === 'admin') return redirect()->route('dashboard');
 
        $kantors = collect(session('kantor_pilihan', []));
        if ($kantors->isEmpty()) return redirect()->route('login');
 
        return view('auth.pilih-kantor', compact('kantors'));
    }
 
    public function pilihKantor(Request $request)
    {
        if (!session('user_id')) return redirect()->route('login');
        $request->validate(['kantor_id' => 'required|integer']);
 
        $kantorPilihan = collect(session('kantor_pilihan', []));
        $kantor = $kantorPilihan->firstWhere('id', (int) $request->kantor_id);
        if (!$kantor) return back()->withErrors(['Kantor tidak valid.']);
 
        session([
            'kantor_id'   => $kantor['id'],
            'kantor_nama' => $kantor['nama'],
            'kantor_kota' => $kantor['kota'],
            'kantor_kode' => $kantor['kode'],
        ]);
        session()->forget('kantor_pilihan');
        AuditLog::catat('login', 'auth', session('user_id'), 'Login berhasil - ' . $kantor['nama']);
        return redirect()->route('dashboard');
    }
 
    public function logout(Request $request)
    {
        AuditLog::catat('logout', 'auth', session('user_id'), 'Logout dari ' . session('kantor_nama', 'sistem'));
        $request->session()->flush();
        return redirect()->route('login');
    }
}