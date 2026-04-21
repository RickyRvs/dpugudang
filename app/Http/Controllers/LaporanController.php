<?php

namespace App\Http\Controllers;
 
use App\Models\PermintaanBarang;
use App\Models\RiwayatStok;
use App\Models\Barang;
use Illuminate\Http\Request;
 
class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $isAdmin = session('user_role') === 'admin';
        $dari    = $request->dari ?? now()->startOfMonth()->format('Y-m-d');
        $sampai  = $request->sampai ?? now()->format('Y-m-d');
        $range   = [$dari . ' 00:00:00', $sampai . ' 23:59:59'];
 
        $riwayatBase    = RiwayatStok::whereBetween('created_at', $range);
        $permintaanBase = PermintaanBarang::whereBetween('created_at', $range);
        $barangBase     = Barang::where('is_active', true);
 
        // Scope kantor
        if (!$isAdmin) {
            $riwayatBase->forKantor();
            $permintaanBase->forKantor();
            $barangBase->forKantor();
        } elseif ($request->kantor_id) {
            $riwayatBase->where('kantor_id', $request->kantor_id);
            $permintaanBase->where('kantor_id', $request->kantor_id);
            $barangBase->where('kantor_id', $request->kantor_id);
        }
 
        $totalMasuk        = (clone $riwayatBase)->where('tipe', 'masuk')->sum('jumlah');
        $totalKeluar       = (clone $riwayatBase)->where('tipe', 'keluar')->sum('jumlah');
        $totalPermintaan   = (clone $permintaanBase)->count();
        $permintaanSelesai = (clone $permintaanBase)->where('status', 'selesai')->count();
 
        $topBarangKeluar = (clone $riwayatBase)->where('tipe', 'keluar')
            ->selectRaw('barang_id, SUM(jumlah) as total')
            ->groupBy('barang_id')->orderByDesc('total')->take(10)->with('barang')->get();
 
        $topBarangMasuk = (clone $riwayatBase)->where('tipe', 'masuk')
            ->selectRaw('barang_id, SUM(jumlah) as total')
            ->groupBy('barang_id')->orderByDesc('total')->take(10)->with('barang')->get();
 
        $trenHarian = (clone $riwayatBase)
            ->selectRaw('DATE(created_at) as tgl, tipe, SUM(jumlah) as total')
            ->groupBy('tgl', 'tipe')->orderBy('tgl')->get()->groupBy('tgl');
 
        $stokMenipis = (clone $barangBase)->whereRaw('stok_tersedia <= stok_minimum')->orderBy('stok_tersedia')->get();
 
        $kantorList = $isAdmin ? \App\Models\Kantor::where('is_active', true)->orderBy('nama')->get() : collect();
 
        return view('laporan.index', compact(
            'dari', 'sampai', 'totalMasuk', 'totalKeluar', 'totalPermintaan',
            'permintaanSelesai', 'topBarangKeluar', 'topBarangMasuk', 'trenHarian',
            'stokMenipis', 'kantorList', 'isAdmin'
        ));
    }
}