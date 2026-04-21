<?php

namespace App\Http\Controllers;

use App\Models\KategoriBarang;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = KategoriBarang::withCount('barang')->orderBy('nama')->paginate(20);
        return view('kategori.index', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama'=>'required','kode'=>'required|unique:kategori_barang,kode','deskripsi'=>'nullable']);
        $k = KategoriBarang::create($request->only(['nama','kode','deskripsi']));
        AuditLog::catat('tambah_kategori','kategori',$k->id,$k->nama);
        return back()->with('toast','Kategori berhasil ditambahkan!');
    }

    public function update(Request $request, KategoriBarang $kategori)
    {
        $request->validate(['nama'=>'required','kode'=>'required|unique:kategori_barang,kode,'.$kategori->id,'deskripsi'=>'nullable']);
        $kategori->update($request->only(['nama','kode','deskripsi']));
        AuditLog::catat('edit_kategori','kategori',$kategori->id,$kategori->nama);
        return back()->with('toast','Kategori berhasil diupdate!');
    }

    public function destroy(KategoriBarang $kategori)
    {
        if ($kategori->barang()->count()) return back()->withErrors('Kategori masih memiliki barang, tidak bisa dihapus.');
        $kategori->delete();
        AuditLog::catat('hapus_kategori','kategori',$kategori->id,$kategori->nama);
        return back()->with('toast','Kategori dihapus.','delete');
    }
}