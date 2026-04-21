<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\PermintaanBarang;
use App\Models\DetailPermintaan;
use App\Models\RiwayatStok;
use App\Models\AuditLog;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermintaanController extends Controller
{
    public function index(Request $request)
    {
        $isAdmin = session('user_role') === 'admin';
        $role    = session('user_role');

        $query = PermintaanBarang::with('pembuat', 'kantor')->latest();

        // ── Isolasi per kantor ──────────────────────────
        if ($isAdmin) {
            // Admin bisa filter by kantor atau lihat semua
            if ($request->kantor_id) $query->where('kantor_id', $request->kantor_id);
        } else {
            // Semua role selain admin hanya lihat kantor aktif
            $query->forKantor();

            // Filter tambahan per role
            if ($role === 'manajerial') {
                $query->where('dibuat_oleh', session('user_id'));
            } elseif ($role === 'operator_gudang') {
                $query->whereIn('status', ['dikirim_operator', 'diproses', 'selesai']);
            }
        }

        // ── Filter tab & search ─────────────────────────
        $filter = $request->get('filter', 'semua');
        match($filter) {
            'diajukan'          => $query->where('status', 'diajukan'),
            'disetujui'         => $query->where('status', 'disetujui'),
            'dikirim_operator'  => $query->where('status', 'dikirim_operator'),
            'selesai'           => $query->where('status', 'selesai'),
            default             => null,
        };

        if ($request->q) {
            $q = $request->q;
            $query->where(fn($w) =>
                $w->where('nomor_permintaan', 'like', "%$q%")->orWhere('keperluan', 'like', "%$q%")
            );
        }
        if ($request->jenis) $query->where('jenis', $request->jenis);

        $permintaan = $query->paginate(15);
        $kantorList = $isAdmin ? \App\Models\Kantor::where('is_active', true)->orderBy('nama')->get() : collect();

        return view('permintaan.index', compact('permintaan', 'kantorList', 'isAdmin'));
    }

    public function create()
    {
        $this->requireRole(['manajerial']);
        // Barang yang bisa dipilih hanya milik kantor aktif
        $barangList = Barang::forKantor()->where('is_active', true)->with('kategori')->orderBy('nama')->get();
        return view('permintaan.create', compact('barangList'));
    }

    public function store(Request $request)
    {
        $this->requireRole(['manajerial']);
        $request->validate([
            'jenis'              => 'required|in:masuk,keluar',
            'keperluan'          => 'required|string',
            'tanggal_dibutuhkan' => 'required|date',
            'items'              => 'required|array|min:1',
            'items.*.barang_id'  => 'required|exists:barang,id',
            'items.*.jumlah'     => 'required|integer|min:1',
            'items.*.satuan'     => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            $status = $request->action === 'ajukan' ? 'diajukan' : 'draft';

            $permintaan = PermintaanBarang::create([
                'kantor_id'          => session('kantor_id'), // ← kantor pengaju
                'nomor_permintaan'   => PermintaanBarang::generateNomor($request->jenis),
                'jenis'              => $request->jenis,
                'keperluan'          => $request->keperluan,
                'departemen_tujuan'  => $request->departemen_tujuan,
                'tanggal_dibutuhkan' => $request->tanggal_dibutuhkan,
                'catatan_manajerial' => $request->catatan_manajerial,
                'status'             => $status,
                'dibuat_oleh'        => session('user_id'),
                'tgl_diajukan'       => $status === 'diajukan' ? now() : null,
            ]);

            foreach ($request->items as $item) {
                // Validasi: barang harus milik kantor aktif
                $barang = Barang::where('id', $item['barang_id'])
                    ->where('kantor_id', session('kantor_id'))
                    ->firstOrFail();

                DetailPermintaan::create([
                    'permintaan_id'  => $permintaan->id,
                    'barang_id'      => $barang->id,
                    'jumlah_diminta' => $item['jumlah'],
                    'satuan'         => $item['satuan'] ?? $barang->satuan,
                    'keterangan'     => $item['keterangan'] ?? null,
                ]);
            }

            AuditLog::catat('buat_permintaan', 'permintaan', $permintaan->id, $permintaan->nomor_permintaan);

            if ($status === 'diajukan') {
                // Notif pimpinan yang memiliki akses ke kantor ini
                $kantor = \App\Models\Kantor::find(session('kantor_id'));
                $kantor?->users()->where('role', 'pimpinan')->where('is_active', true)
                    ->each(function ($p) use ($permintaan) {
                        Notifikasi::create([
                            'user_id'       => $p->id,
                            'judul'         => 'Permintaan Baru Menunggu Approval',
                            'pesan'         => "Permintaan {$permintaan->nomor_permintaan} dari " . session('user_nama') . " membutuhkan persetujuan Anda.",
                            'tipe'          => 'permintaan_baru',
                            'url'           => route('permintaan.show', $permintaan->id),
                            'permintaan_id' => $permintaan->id,
                        ]);
                    });
            }
        });

        return redirect()->route('permintaan.index')->with('toast', 'Permintaan berhasil dibuat!');
    }

    public function show(PermintaanBarang $permintaan)
    {
        $this->authorizePermintaan($permintaan);
        $permintaan->load(['pembuat', 'penyetuju', 'pelaksana', 'details.barang', 'kantor']);
        return view('permintaan.show', compact('permintaan'));
    }

    public function surat(PermintaanBarang $permintaan)
{
    $this->authorizePermintaan($permintaan);
 
    // Draft belum bisa dilihat sebagai surat
    if ($permintaan->status === 'draft') {
        abort(403, 'Surat belum tersedia. Ajukan permintaan terlebih dahulu.');
    }
 
    $permintaan->load(['pembuat', 'penyetuju', 'pelaksana', 'details.barang', 'kantor']);
 
    return view('permintaan.surat', compact('permintaan'));
}

    public function ajukan(PermintaanBarang $permintaan)
    {
        $this->requireRole(['manajerial']);
        $this->authorizePermintaan($permintaan);
        if ($permintaan->status !== 'draft' || $permintaan->dibuat_oleh != session('user_id')) abort(403);

        $permintaan->update(['status' => 'diajukan', 'tgl_diajukan' => now()]);
        AuditLog::catat('ajukan_permintaan', 'permintaan', $permintaan->id, $permintaan->nomor_permintaan);

        // Notif pimpinan di kantor yang sama
        $kantor = \App\Models\Kantor::find($permintaan->kantor_id);
        $kantor?->users()->where('role', 'pimpinan')->where('is_active', true)
            ->each(function ($p) use ($permintaan) {
                Notifikasi::create([
                    'user_id'       => $p->id,
                    'judul'         => 'Permintaan Baru Menunggu Approval',
                    'pesan'         => "Permintaan {$permintaan->nomor_permintaan} membutuhkan persetujuan Anda.",
                    'tipe'          => 'permintaan_baru',
                    'url'           => route('permintaan.show', $permintaan->id),
                    'permintaan_id' => $permintaan->id,
                ]);
            });

        return back()->with('toast', 'Permintaan telah diajukan ke Pimpinan.');
    }

    public function setujui(Request $request, PermintaanBarang $permintaan)
{
    $this->requireRole(['pimpinan']);
    $this->authorizePermintaan($permintaan);
    if ($permintaan->status !== 'diajukan') abort(403);
 
    $request->validate([
        'catatan_pimpinan' => 'nullable|string',
        'ttd'              => 'nullable|file|image|max:2048',
        'ttd_canvas'       => 'nullable|string', // base64 dari canvas
    ]);
 
    // ── Proses TTD ──────────────────────────────────────────────────
    $ttdPath = null;
 
    if ($request->hasFile('ttd')) {
        // Upload file biasa
        $ttdPath = $request->file('ttd')->store('ttd', 'public');
 
    } elseif ($request->filled('ttd_canvas')) {
        // Canvas base64 → simpan sebagai PNG
        $base64  = preg_replace('#^data:image/\w+;base64,#i', '', $request->ttd_canvas);
        $imgData = base64_decode($base64);
 
        if ($imgData !== false && strlen($imgData) > 100) { // validasi minimal ada data
            $filename = 'ttd/pimpinan_' . uniqid('', true) . '.png';
            \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $imgData);
            $ttdPath  = $filename;
        }
    }
 
    DB::transaction(function () use ($request, $permintaan, $ttdPath) {
 
        // Update jumlah disetujui per item
        if ($request->jumlah_disetujui) {
            foreach ($request->jumlah_disetujui as $detailId => $jumlah) {
                DetailPermintaan::where('id', $detailId)
                    ->where('permintaan_id', $permintaan->id)
                    ->update(['jumlah_disetujui' => max(0, (int) $jumlah)]);
            }
        }
 
        $permintaan->update([
            'status'            => 'disetujui',
            'disetujui_oleh'    => session('user_id'),
            'tgl_disetujui'     => now(),
            'catatan_pimpinan'  => $request->catatan_pimpinan,
            'ttd_pimpinan'      => $ttdPath,
            'nama_ttd_pimpinan' => session('user_nama'),
        ]);
 
        AuditLog::catat('setujui_permintaan', 'permintaan', $permintaan->id, $permintaan->nomor_permintaan);
 
        // Notifikasi ke pengaju
        Notifikasi::create([
            'user_id'       => $permintaan->dibuat_oleh,
            'judul'         => 'Permintaan Disetujui & Ditandatangani',
            'pesan'         => "Permintaan {$permintaan->nomor_permintaan} telah disetujui oleh "
                               . session('user_nama') . ". Silakan teruskan ke Operator Gudang.",
            'tipe'          => 'disetujui',
            'url'           => route('permintaan.show', $permintaan->id),
            'permintaan_id' => $permintaan->id,
        ]);
    });
 
    // Redirect ke halaman surat agar pimpinan bisa lihat hasil TTD-nya
    return redirect()
        ->route('permintaan.surat', $permintaan->id)
        ->with('toast', 'Permintaan berhasil disetujui dan ditandatangani!');
}

    public function tolak(Request $request, PermintaanBarang $permintaan)
    {
        $this->requireRole(['pimpinan']);
        $this->authorizePermintaan($permintaan);
        if ($permintaan->status !== 'diajukan') abort(403);
        $request->validate(['catatan_pimpinan' => 'required|string']);

        $permintaan->update([
            'status'           => 'ditolak',
            'disetujui_oleh'   => session('user_id'),
            'tgl_disetujui'    => now(),
            'catatan_pimpinan' => $request->catatan_pimpinan,
        ]);
        AuditLog::catat('tolak_permintaan', 'permintaan', $permintaan->id, $permintaan->nomor_permintaan);

        Notifikasi::create([
            'user_id'       => $permintaan->dibuat_oleh,
            'judul'         => 'Permintaan Ditolak',
            'pesan'         => "Permintaan {$permintaan->nomor_permintaan} ditolak. Alasan: {$request->catatan_pimpinan}",
            'tipe'          => 'ditolak',
            'url'           => route('permintaan.show', $permintaan->id),
            'permintaan_id' => $permintaan->id,
        ]);

        return back()->with('toast', 'Permintaan telah ditolak.', 'warning');
    }

    public function kirimOperator(PermintaanBarang $permintaan)
    {
        $this->requireRole(['manajerial']);
        $this->authorizePermintaan($permintaan);
        if ($permintaan->status !== 'disetujui') abort(403);

        $permintaan->update(['status' => 'dikirim_operator', 'tgl_dikirim_operator' => now()]);
        AuditLog::catat('kirim_operator', 'permintaan', $permintaan->id, $permintaan->nomor_permintaan);

        // Notif operator yang terdaftar di kantor yang sama
        $kantor = \App\Models\Kantor::find($permintaan->kantor_id);
        $kantor?->users()->where('role', 'operator_gudang')->where('is_active', true)
            ->each(function ($op) use ($permintaan) {
                Notifikasi::create([
                    'user_id'       => $op->id,
                    'judul'         => 'Tugas Baru dari Manajerial',
                    'pesan'         => "Permintaan {$permintaan->nomor_permintaan} dikirim untuk dieksekusi.",
                    'tipe'          => 'dikirim_operator',
                    'url'           => route('permintaan.show', $permintaan->id),
                    'permintaan_id' => $permintaan->id,
                ]);
            });

        return back()->with('toast', 'Permintaan berhasil dikirim ke Operator Gudang.');
    }

    public function eksekusi(Request $request, PermintaanBarang $permintaan)
    {
        $this->requireRole(['operator_gudang']);
        $this->authorizePermintaan($permintaan);
        if (!in_array($permintaan->status, ['dikirim_operator', 'diproses'])) abort(403);
        $request->validate(['jumlah_eksekusi' => 'required|array', 'catatan_operator' => 'nullable|string']);

        DB::transaction(function () use ($request, $permintaan) {
            foreach ($request->jumlah_eksekusi as $detailId => $jumlah) {
                $detail = DetailPermintaan::where('id', $detailId)
                    ->where('permintaan_id', $permintaan->id)->first();
                if (!$detail) continue;

                $jumlah = max(0, (int) $jumlah);
                $detail->update(['jumlah_dieksekusi' => $jumlah]);

                $barang      = Barang::find($detail->barang_id);
                $stokSebelum = $barang->stok_tersedia;
                $stokSesudah = $permintaan->jenis === 'masuk'
                    ? $stokSebelum + $jumlah
                    : max(0, $stokSebelum - $jumlah);

                $barang->update(['stok_tersedia' => $stokSesudah]);

                RiwayatStok::create([
                    'kantor_id'     => $permintaan->kantor_id, // ← kantor dari permintaan
                    'barang_id'     => $barang->id,
                    'permintaan_id' => $permintaan->id,
                    'user_id'       => session('user_id'),
                    'tipe'          => $permintaan->jenis,
                    'jumlah'        => $jumlah,
                    'stok_sebelum'  => $stokSebelum,
                    'stok_sesudah'  => $stokSesudah,
                    'referensi'     => $permintaan->nomor_permintaan,
                    'keterangan'    => $request->catatan_operator,
                ]);
            }

            $permintaan->update([
                'status'           => 'selesai',
                'dieksekusi_oleh'  => session('user_id'),
                'tgl_selesai'      => now(),
                'catatan_operator' => $request->catatan_operator,
            ]);

            AuditLog::catat('eksekusi_stok', 'permintaan', $permintaan->id, $permintaan->nomor_permintaan);

            Notifikasi::create([
                'user_id'       => $permintaan->dibuat_oleh,
                'judul'         => 'Permintaan Selesai Dieksekusi',
                'pesan'         => "Permintaan {$permintaan->nomor_permintaan} telah dieksekusi oleh operator.",
                'tipe'          => 'selesai',
                'url'           => route('permintaan.show', $permintaan->id),
                'permintaan_id' => $permintaan->id,
            ]);
        });

        return back()->with('toast', 'Stok berhasil diupdate!');
    }

    public function batal(Request $request, PermintaanBarang $permintaan)
    {
        $this->requireRole(['manajerial']);
        $this->authorizePermintaan($permintaan);
        if (!in_array($permintaan->status, ['draft', 'diajukan']) || $permintaan->dibuat_oleh != session('user_id')) abort(403);

        $permintaan->update(['status' => 'dibatalkan']);
        AuditLog::catat('batal_permintaan', 'permintaan', $permintaan->id, $permintaan->nomor_permintaan);
        return back()->with('toast', 'Permintaan dibatalkan.', 'warning');
    }

    // ── Helpers ──────────────────────────────────────────
    private function requireRole(array $roles): void
    {
        // Admin bisa akses semua
        if (session('user_role') === 'admin') return;
        if (!in_array(session('user_role'), $roles)) abort(403, 'Akses ditolak.');
    }

    private function authorizePermintaan(PermintaanBarang $permintaan): void
    {
        // Admin bypass
        if (session('user_role') === 'admin') return;
        // Permintaan harus dari kantor yang sama dengan session aktif
        if ($permintaan->kantor_id !== session('kantor_id')) abort(403, 'Akses ditolak.');
    }
}