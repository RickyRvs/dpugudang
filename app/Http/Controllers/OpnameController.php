<?php

namespace App\Http\Controllers;
 
use App\Models\Opname;
use App\Models\Barang;
use App\Models\RiwayatStok;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
 
class OpnameController extends Controller
{
    public function index()
    {
        $isAdmin = session('user_role') === 'admin';
        $query   = Opname::with('pembuat');
 
        if (!$isAdmin) $query->forKantor();
 
        $opname = $query->latest()->paginate(15);
        return view('opname.index', compact('opname', 'isAdmin'));
    }
 
    public function create()
    {
        // Barang hanya dari kantor aktif
        $barangList = Barang::forKantor()->where('is_active', true)->with('kategori')->orderBy('nama')->get();
        return view('opname.create', compact('barangList'));
    }
 
    public function store(Request $request)
    {
        $request->validate([
            'tanggal'                  => 'required|date',
            'keterangan'               => 'nullable|string',
            'items'                    => 'required|array|min:1',
            'items.*.barang_id'        => 'required|exists:barang,id',
            'items.*.stok_fisik'       => 'required|integer|min:0',
        ]);
 
        DB::transaction(function () use ($request) {
            $opname = Opname::create([
                'kantor_id'    => session('kantor_id'),
                'nomor_opname' => Opname::generateNomor(),
                'tanggal'      => $request->tanggal,
                'keterangan'   => $request->keterangan,
                'status'       => 'draft',
                'dibuat_oleh'  => session('user_id'),
            ]);
 
            foreach ($request->items as $item) {
                $barang = Barang::where('id', $item['barang_id'])
                    ->where('kantor_id', session('kantor_id'))
                    ->firstOrFail();
 
                \App\Models\DetailOpname::create([
                    'opname_id'   => $opname->id,
                    'barang_id'   => $barang->id,
                    'stok_sistem' => $barang->stok_tersedia,
                    'stok_fisik'  => $item['stok_fisik'],
                    'keterangan'  => $item['keterangan'] ?? null,
                ]);
            }
 
            AuditLog::catat('buat_opname', 'opname', $opname->id, $opname->nomor_opname);
        });
 
        return redirect()->route('opname.index')->with('toast', 'Stock opname berhasil dibuat!');
    }
 
    public function show(Opname $opname)
    {
        $this->authorizeOpname($opname);
        $opname->load(['pembuat', 'penyetuju', 'details.barang']);
        return view('opname.show', compact('opname'));
    }
 
    public function selesai(Request $request, Opname $opname)
    {
        $this->authorizeOpname($opname);
        if ($opname->status !== 'draft') abort(403);
 
        DB::transaction(function () use ($opname) {
            foreach ($opname->details as $detail) {
                $barang      = $detail->barang;
                $stokSebelum = $barang->stok_tersedia;
                $stokBaru    = $detail->stok_fisik;
 
                if ($stokSebelum !== $stokBaru) {
                    $barang->update(['stok_tersedia' => $stokBaru]);
 
                    RiwayatStok::create([
                        'kantor_id'    => $opname->kantor_id,
                        'barang_id'    => $barang->id,
                        'user_id'      => session('user_id'),
                        'tipe'         => 'opname',
                        'jumlah'       => abs($stokBaru - $stokSebelum),
                        'stok_sebelum' => $stokSebelum,
                        'stok_sesudah' => $stokBaru,
                        'referensi'    => $opname->nomor_opname,
                        'keterangan'   => 'Penyesuaian stock opname ' . $opname->nomor_opname,
                    ]);
                }
            }
 
            $opname->update([
                'status'         => 'selesai',
                'disetujui_oleh' => session('user_id'),
                'tgl_selesai'    => now(),
            ]);
 
            AuditLog::catat('selesai_opname', 'opname', $opname->id, $opname->nomor_opname);
        });
 
        return back()->with('toast', 'Stock opname diselesaikan & stok diperbarui!');
    }
 
    private function authorizeOpname(Opname $opname): void
    {
        if (session('user_role') === 'admin') return;
        if ($opname->kantor_id !== session('kantor_id')) abort(403, 'Akses ditolak.');
    }
}