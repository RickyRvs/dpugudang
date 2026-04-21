<?php

namespace App\Http\Controllers;
 
use App\Models\Barang;
use App\Models\PermintaanBarang;
use App\Models\RiwayatStok;
 
class DashboardController extends Controller
{
    public function index()
    {
        $isAdmin = session('user_role') === 'admin';
 
        // Semua query scope ke kantor aktif kecuali admin
        $barangBase     = Barang::where('is_active', true);
        $permintaanBase = PermintaanBarang::query();
        $riwayatBase    = RiwayatStok::query();
 
        if (!$isAdmin) {
            $barangBase->forKantor();
            $permintaanBase->forKantor();
            $riwayatBase->forKantor();
        }
 
        $totalBarang        = (clone $barangBase)->count();
        $permintaanAktif    = (clone $permintaanBase)->whereNotIn('status', ['selesai', 'dibatalkan', 'ditolak'])->count();
        $transaksibulanIni  = (clone $riwayatBase)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $stokMenipis        = (clone $barangBase)->whereRaw('stok_tersedia <= stok_minimum')->count();
        $permintaanTerbaru  = (clone $permintaanBase)->with('pembuat', 'kantor')->latest()->take(10)->get();
        $barangMenipis      = (clone $barangBase)->whereRaw('stok_tersedia <= stok_minimum')->orderBy('stok_tersedia')->take(6)->get();
        $permintaanMenunggu = (clone $permintaanBase)->where('status', 'diajukan')->count();
        $tugasOperator      = (clone $permintaanBase)->whereIn('status', ['dikirim_operator', 'diproses'])->count();
 
        return view('dashboard.index', compact(
            'totalBarang', 'permintaanAktif', 'transaksibulanIni', 'stokMenipis',
            'permintaanTerbaru', 'barangMenipis', 'permintaanMenunggu', 'tugasOperator',
            'isAdmin'
        ));
    }
}