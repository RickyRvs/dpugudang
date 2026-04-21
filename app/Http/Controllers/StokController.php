<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\RiwayatStok;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class StokController extends Controller
{
    public function index(Request $request)
    {
        $isAdmin = session('user_role') === 'admin';

        $query = Barang::with('kategori')->where('is_active', true);

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
        if ($request->kondisi === 'habis')    $query->where('stok_tersedia', 0);
        elseif ($request->kondisi === 'menipis') $query->whereRaw('stok_tersedia > 0 AND stok_tersedia <= stok_minimum');
        elseif ($request->kondisi === 'normal')  $query->whereRaw('stok_tersedia > stok_minimum');

        $barang       = $query->orderBy('nama')->paginate(20);
        $kategoriList = KategoriBarang::orderBy('nama')->get();
        $kantorList   = $isAdmin ? \App\Models\Kantor::where('is_active', true)->orderBy('nama')->get() : collect();

        // Summary stats — filter by kantor aktif (atau semua jika admin tidak filter)
        $statsBase = Barang::where('is_active', true);
        if (!$isAdmin) $statsBase->forKantor();
        elseif ($request->kantor_id) $statsBase->where('kantor_id', $request->kantor_id);

        $totalJenis  = (clone $statsBase)->count();
        $stokNormal  = (clone $statsBase)->whereRaw('stok_tersedia > stok_minimum')->count();
        $stokMenipis = (clone $statsBase)->whereRaw('stok_tersedia > 0 AND stok_tersedia <= stok_minimum')->count();
        $stokHabis   = (clone $statsBase)->where('stok_tersedia', 0)->count();

        return view('stok.index', compact(
            'barang', 'kategoriList', 'totalJenis', 'stokNormal',
            'stokMenipis', 'stokHabis', 'kantorList', 'isAdmin'
        ));
    }

    public function koreksi(Request $request)
    {
        $request->validate([
            'barang_id'  => 'required|exists:barang,id',
            'tipe'       => 'required|in:koreksi,opname',
            'stok_baru'  => 'required|integer|min:0',
            'keterangan' => 'required|string',
        ]);

        $barang = Barang::findOrFail($request->barang_id);

        // Cegah koreksi stok barang kantor lain (kecuali admin)
        if (session('user_role') !== 'admin' && $barang->kantor_id !== session('kantor_id')) {
            abort(403, 'Akses ditolak.');
        }

        $stokSebelum = $barang->stok_tersedia;
        $selisih     = $request->stok_baru - $stokSebelum;

        $barang->update(['stok_tersedia' => $request->stok_baru]);

        RiwayatStok::create([
            'kantor_id'    => $barang->kantor_id,
            'barang_id'    => $barang->id,
            'user_id'      => session('user_id'),
            'tipe'         => $request->tipe,
            'jumlah'       => abs($selisih),
            'stok_sebelum' => $stokSebelum,
            'stok_sesudah' => $request->stok_baru,
            'keterangan'   => $request->keterangan,
        ]);

        AuditLog::catat('koreksi_stok', 'stok', $barang->id,
            "Koreksi stok {$barang->nama}: {$stokSebelum} → {$request->stok_baru}");

        return back()->with('toast', 'Koreksi stok berhasil disimpan!');
    }
}