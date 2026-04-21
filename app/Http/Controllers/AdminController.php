<?php

namespace App\Http\Controllers;

use App\Models\Kantor;
use App\Models\User;
use App\Models\Barang;
use App\Models\PermintaanBarang;
use App\Models\RiwayatStok;
use App\Models\AuditLog;
use Illuminate\Http\Request;

/**
 * AdminController — khusus role 'admin', bisa lihat & kelola semua kantor.
 * Route prefix: /admin/...
 */
class AdminController extends Controller
{
    public function __construct()
    {
        // Semua method di sini hanya untuk admin
        if (session('user_role') !== 'admin') abort(403);
    }

    // ── Dashboard lintas kantor ──────────────────────
    public function dashboard()
    {
        $kantors = Kantor::withCount([
            'barang as total_barang'       => fn($q) => $q->where('is_active', true),
            'barang as stok_menipis'       => fn($q) => $q->where('is_active', true)->whereRaw('stok_tersedia <= stok_minimum'),
            'permintaanBarang as permintaan_aktif' => fn($q) => $q->whereNotIn('status', ['selesai', 'dibatalkan', 'ditolak']),
        ])->where('is_active', true)->orderBy('nama')->get();

        $totalBarangGlobal    = Barang::where('is_active', true)->count();
        $totalUserAktif       = User::where('is_active', true)->count();
        $permintaanBulanIni   = PermintaanBarang::whereMonth('created_at', now()->month)->count();
        $stokMenipisGlobal    = Barang::where('is_active', true)->whereRaw('stok_tersedia <= stok_minimum')->count();

        return view('admin.dashboard', compact(
            'kantors', 'totalBarangGlobal', 'totalUserAktif',
            'permintaanBulanIni', 'stokMenipisGlobal'
        ));
    }

    // ── Manajemen Kantor ─────────────────────────────
    public function kantorIndex()
    {
        $kantors = Kantor::withCount('users')->orderBy('nama')->paginate(20);
        return view('admin.kantor.index', compact('kantors'));
    }

    public function kantorStore(Request $request)
    {
        $request->validate([
            'nama'    => 'required|string|max:100',
            'kode'    => 'required|string|unique:kantors,kode',
            'kota'    => 'required|string',
            'alamat'  => 'nullable|string',
        ]);

        $kantor = Kantor::create($request->only(['nama', 'kode', 'kota', 'alamat']) + ['is_active' => true]);
        AuditLog::catat('tambah_kantor', 'kantor', $kantor->id, $kantor->nama);

        return back()->with('toast', 'Kantor berhasil ditambahkan!');
    }

    public function kantorUpdate(Request $request, Kantor $kantor)
    {
        $request->validate([
            'nama'   => 'required|string|max:100',
            'kode'   => 'required|string|unique:kantors,kode,' . $kantor->id,
            'kota'   => 'required|string',
            'alamat' => 'nullable|string',
        ]);

        $kantor->update($request->only(['nama', 'kode', 'kota', 'alamat']) + ['is_active' => $request->boolean('is_active', true)]);
        AuditLog::catat('edit_kantor', 'kantor', $kantor->id, $kantor->nama);

        return back()->with('toast', 'Kantor berhasil diupdate!');
    }

    // ── Assign user ke kantor ────────────────────────
    public function assignUser(Request $request, Kantor $kantor)
    {
        $request->validate(['user_ids' => 'required|array', 'user_ids.*' => 'exists:users,id']);
        $kantor->users()->sync($request->user_ids);
        AuditLog::catat('assign_user_kantor', 'kantor', $kantor->id, implode(',', $request->user_ids));
        return back()->with('toast', 'User berhasil di-assign ke kantor!');
    }

    // ── Manajemen User global ────────────────────────
    public function userIndex(Request $request)
    {
        $query = User::query();
        if ($request->q)    $query->where(fn($w) => $w->where('nama', 'like', '%'.$request->q.'%')->orWhere('email', 'like', '%'.$request->q.'%'));
        if ($request->role) $query->where('role', $request->role);

        $users    = $query->orderBy('nama')->paginate(20);
        $kantors  = Kantor::orderBy('nama')->get();
        return view('admin.user.index', compact('users', 'kantors'));
    }

    public function userStore(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|in:admin,pimpinan,manajerial,operator_gudang',
            'jabatan'  => 'nullable|string',
            'no_hp'    => 'nullable|string',
        ]);

        $user = User::create([
            'nama'      => $request->nama,
            'email'     => $request->email,
            'password'  => password_hash($request->password, PASSWORD_BCRYPT),
            'role'      => $request->role,
            'jabatan'   => $request->jabatan,
            'no_hp'     => $request->no_hp,
            'is_active' => true,
        ]);

        // Assign ke kantor jika dipilih
        if ($request->kantor_ids) {
            $user->kantors()->sync($request->kantor_ids);
        }

        AuditLog::catat('tambah_user', 'user', $user->id, $user->nama);
        return back()->with('toast', 'User berhasil ditambahkan!');
    }

    public function userUpdate(Request $request, User $user)
    {
        $request->validate([
            'nama'    => 'required|string|max:100',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'role'    => 'required|in:admin,pimpinan,manajerial,operator_gudang',
            'jabatan' => 'nullable|string',
            'no_hp'   => 'nullable|string',
        ]);

        $data = $request->only(['nama', 'email', 'role', 'jabatan', 'no_hp']);
        $data['is_active'] = $request->boolean('is_active', true);
        if ($request->filled('password')) {
            $data['password'] = password_hash($request->password, PASSWORD_BCRYPT);
        }

        $user->update($data);

        if ($request->kantor_ids) {
            $user->kantors()->sync($request->kantor_ids);
        }

        AuditLog::catat('edit_user', 'user', $user->id, $user->nama);
        return back()->with('toast', 'User berhasil diupdate!');
    }

    public function userDestroy(User $user)
    {
        if ($user->id == session('user_id')) return back()->withErrors('Tidak bisa menonaktifkan akun sendiri.');
        $user->update(['is_active' => false]);
        AuditLog::catat('nonaktif_user', 'user', $user->id, $user->nama);
        return back()->with('toast', 'User dinonaktifkan.', 'warning');
    }

    // ── Audit Log global ─────────────────────────────
    public function auditIndex(Request $request)
    {
        $query = AuditLog::with('user')->latest();
        if ($request->modul)   $query->where('modul', $request->modul);
        if ($request->user_id) $query->where('user_id', $request->user_id);
        if ($request->dari)    $query->whereDate('created_at', '>=', $request->dari);
        if ($request->sampai)  $query->whereDate('created_at', '<=', $request->sampai);

        $logs  = $query->paginate(30);
        $users = User::orderBy('nama')->get();
        return view('admin.audit.index', compact('logs', 'users'));
    }
}