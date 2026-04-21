<?php

namespace App\Http\Controllers;
 
use App\Models\RiwayatStok;
use App\Models\Barang;
use Illuminate\Http\Request;
 
class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $isAdmin = session('user_role') === 'admin';
 
        $query = RiwayatStok::with(['barang', 'user', 'permintaan'])->latest();
 
        if (!$isAdmin) {
            $query->forKantor();
        } elseif ($request->kantor_id) {
            $query->where('kantor_id', $request->kantor_id);
        }
 
        if ($request->barang_id) $query->where('barang_id', $request->barang_id);
        if ($request->tipe)      $query->where('tipe', $request->tipe);
        if ($request->dari)      $query->whereDate('created_at', '>=', $request->dari);
        if ($request->sampai)    $query->whereDate('created_at', '<=', $request->sampai);
 
        $riwayat    = $query->paginate(25);
        $barangList = $isAdmin
            ? Barang::where('is_active', true)->orderBy('nama')->get()
            : Barang::forKantor()->where('is_active', true)->orderBy('nama')->get();
 
        $kantorList = $isAdmin ? \App\Models\Kantor::where('is_active', true)->orderBy('nama')->get() : collect();
 
        return view('riwayat.index', compact('riwayat', 'barangList', 'kantorList', 'isAdmin'));
    }
}