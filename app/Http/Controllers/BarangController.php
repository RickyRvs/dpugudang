<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\RiwayatStok;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    // Hanya admin yang bisa lihat semua kantor sekaligus.
    // Role lain hanya lihat barang milik kantor aktif mereka.

    public function index(Request $request)
    {
        $isAdmin = session('user_role') === 'admin';

        $query = Barang::with('kategori');

        // Admin bisa filter by kantor, role lain otomatis terkunci ke kantor aktif
        if ($isAdmin) {
            if ($request->kantor_id) $query->where('kantor_id', $request->kantor_id);
        } else {
            $query->forKantor();
        }

        if ($request->q) {
            $q = $request->q;
            $query->where(fn($w) => $w->where('nama', 'like', "%$q%")->orWhere('kode', 'like', "%$q%"));
        }
        if ($request->kategori) $query->where('kategori_id', $request->kategori);

        $barang       = $query->orderBy('nama')->paginate(20);
        $kategoriList = KategoriBarang::orderBy('nama')->get();

        // Admin butuh list kantor untuk filter dropdown
        $kantorList = $isAdmin ? \App\Models\Kantor::where('is_active', true)->orderBy('nama')->get() : collect();

        return view('barang.index', compact('barang', 'kategoriList', 'kantorList', 'isAdmin'));
    }

    public function create()
    {
        $kategoriList = KategoriBarang::orderBy('nama')->get();
        return view('barang.create', compact('kategoriList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode'          => 'required|unique:barang,kode',
            'nama'          => 'required|string',
            'kategori_id'   => 'required|exists:kategori_barang,id',
            'satuan'        => 'required|string',
            'stok_tersedia' => 'required|integer|min:0',
            'stok_minimum'  => 'required|integer|min:0',
            'harga_satuan'  => 'required|numeric|min:0',
        ]);

        $data = $request->only(['kode','nama','kategori_id','satuan','deskripsi','stok_tersedia','stok_minimum','lokasi_rak','harga_satuan']);
        $data['is_active']  = $request->boolean('is_active', true);
        $data['kantor_id']  = session('kantor_id'); // barang milik kantor yang sedang aktif

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('barang', 'public');
        }

        $barang = Barang::create($data);
        AuditLog::catat('tambah_barang', 'barang', $barang->id, $barang->nama);

        return redirect()->route('barang.index')->with('toast', 'Barang berhasil ditambahkan!');
    }

    public function show(Barang $barang)
    {
        $this->authorizeBarang($barang);

        $riwayat = RiwayatStok::where('barang_id', $barang->id)
            ->with('user', 'permintaan')
            ->latest()
            ->paginate(20);

        return view('barang.show', compact('barang', 'riwayat'));
    }

    public function edit(Barang $barang)
    {
        $this->authorizeBarang($barang);
        $kategoriList = KategoriBarang::orderBy('nama')->get();
        return view('barang.edit', compact('barang', 'kategoriList'));
    }

    public function update(Request $request, Barang $barang)
    {
        $this->authorizeBarang($barang);

        $request->validate([
            'kode'         => 'required|unique:barang,kode,' . $barang->id,
            'nama'         => 'required|string',
            'kategori_id'  => 'required|exists:kategori_barang,id',
            'satuan'       => 'required|string',
            'stok_minimum' => 'required|integer|min:0',
            'harga_satuan' => 'required|numeric|min:0',
        ]);

        $data = $request->only(['kode','nama','kategori_id','satuan','deskripsi','stok_minimum','lokasi_rak','harga_satuan']);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('barang', 'public');
        }

        $barang->update($data);
        AuditLog::catat('edit_barang', 'barang', $barang->id, $barang->nama);

        return redirect()->route('barang.index')->with('toast', 'Barang berhasil diupdate!');
    }

    public function destroy(Barang $barang)
    {
        $this->authorizeBarang($barang);
        $barang->update(['is_active' => false]);
        AuditLog::catat('nonaktif_barang', 'barang', $barang->id, $barang->nama);
        return back()->with('toast', 'Barang dinonaktifkan.', 'warning');
    }

    // ── Pastikan user hanya bisa akses barang kantornya sendiri ──
    private function authorizeBarang(Barang $barang): void
    {
        if (session('user_role') === 'admin') return; // admin bypass
        if ($barang->kantor_id !== session('kantor_id')) abort(403, 'Akses ditolak.');
    }
}