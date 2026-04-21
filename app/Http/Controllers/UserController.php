<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->requirePimpinan();
        $query = User::query();
        if ($request->q) $query->where(fn($w) => $w->where('nama','like','%'.$request->q.'%')->orWhere('email','like','%'.$request->q.'%'));
        if ($request->role) $query->where('role', $request->role);
        $users = $query->orderBy('nama')->paginate(20);
        return view('user.index', compact('users'));
    }

    public function store(Request $request)
    {
        $this->requirePimpinan();
        $request->validate([
            'nama'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|in:pimpinan,manajerial,operator_gudang',
            'jabatan'  => 'nullable|string',
            'no_hp'    => 'nullable|string',
        ]);

        $user = User::create([
            'nama'     => $request->nama,
            'email'    => $request->email,
            'password' => password_hash($request->password, PASSWORD_BCRYPT),
            'role'     => $request->role,
            'jabatan'  => $request->jabatan,
            'no_hp'    => $request->no_hp,
            'is_active'=> true,
        ]);

        AuditLog::catat('tambah_user','user',$user->id,$user->nama);
        return back()->with('toast','User berhasil ditambahkan!');
    }

    public function update(Request $request, User $user)
    {
        $this->requirePimpinan();
        $request->validate([
            'nama'    => 'required|string|max:100',
            'email'   => 'required|email|unique:users,email,'.$user->id,
            'role'    => 'required|in:pimpinan,manajerial,operator_gudang',
            'jabatan' => 'nullable|string',
            'no_hp'   => 'nullable|string',
        ]);

        $data = $request->only(['nama','email','role','jabatan','no_hp']);
        $data['is_active'] = $request->boolean('is_active', true);
        if ($request->filled('password')) {
            $data['password'] = password_hash($request->password, PASSWORD_BCRYPT);
        }

        $user->update($data);
        AuditLog::catat('edit_user','user',$user->id,$user->nama);
        return back()->with('toast','User berhasil diupdate!');
    }

    public function destroy(User $user)
    {
        $this->requirePimpinan();
        if ($user->id == session('user_id')) return back()->withErrors('Tidak bisa menonaktifkan akun sendiri.');
        $user->update(['is_active' => false]);
        AuditLog::catat('nonaktif_user','user',$user->id,$user->nama);
        return back()->with('toast','User dinonaktifkan.','warning');
    }

    private function requirePimpinan()
    {
        if (session('user_role') !== 'pimpinan') abort(403);
    }
}