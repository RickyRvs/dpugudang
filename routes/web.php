<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermintaanController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\OpnameController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\AdminController;

// ── Auth ────────────────────────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/pilih-kantor', [AuthController::class, 'showPilihKantor'])->name('pilih.kantor');
Route::post('/pilih-kantor', [AuthController::class, 'pilihKantor'])->name('pilih.kantor.post');

// ── Protected routes ────────────────────────────────────────────────────────
Route::middleware(['auth.session'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Permintaan Barang
Route::get('/permintaan',                              [PermintaanController::class, 'index'])->name('permintaan.index');
Route::get('/permintaan/create',                       [PermintaanController::class, 'create'])->name('permintaan.create');
Route::post('/permintaan',                             [PermintaanController::class, 'store'])->name('permintaan.store');
Route::get('/permintaan/{permintaan}',                 [PermintaanController::class, 'show'])->name('permintaan.show');
Route::get('/permintaan/{permintaan}/surat',           [PermintaanController::class, 'surat'])->name('permintaan.surat'); // ← BARU
Route::post('/permintaan/{permintaan}/ajukan',         [PermintaanController::class, 'ajukan'])->name('permintaan.ajukan');
Route::post('/permintaan/{permintaan}/setujui',        [PermintaanController::class, 'setujui'])->name('permintaan.setujui');
Route::post('/permintaan/{permintaan}/tolak',          [PermintaanController::class, 'tolak'])->name('permintaan.tolak');
Route::post('/permintaan/{permintaan}/kirim-operator', [PermintaanController::class, 'kirimOperator'])->name('permintaan.kirim_operator');
Route::post('/permintaan/{permintaan}/eksekusi',       [PermintaanController::class, 'eksekusi'])->name('permintaan.eksekusi');
Route::patch('/permintaan/{permintaan}/batal',         [PermintaanController::class, 'batal'])->name('permintaan.batal');
Route::post('/permintaan/{permintaan}/ttd-manajerial', [PermintaanController::class, 'ttdManajerial'])->name('permintaan.ttd_manajerial');
 
    // Stok
    Route::get('/stok', [StokController::class, 'index'])->name('stok.index');
    Route::post('/stok/koreksi', [StokController::class, 'koreksi'])->name('stok.koreksi');

    // Riwayat
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

    // Opname
    Route::get('/opname', [OpnameController::class, 'index'])->name('opname.index');
    Route::get('/opname/create', [OpnameController::class, 'create'])->name('opname.create');
    Route::post('/opname', [OpnameController::class, 'store'])->name('opname.store');
    Route::get('/opname/{opname}', [OpnameController::class, 'show'])->name('opname.show');
    Route::post('/opname/{opname}/selesai', [OpnameController::class, 'selesai'])->name('opname.selesai');

    // Master Barang
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
    Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
    Route::get('/barang/{barang}', [BarangController::class, 'show'])->name('barang.show');
    Route::get('/barang/{barang}/edit', [BarangController::class, 'edit'])->name('barang.edit');
    Route::put('/barang/{barang}', [BarangController::class, 'update'])->name('barang.update');
    Route::delete('/barang/{barang}', [BarangController::class, 'destroy'])->name('barang.destroy');

    // Kategori — kategori bersifat global (shared semua kantor), tidak per-kantor
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
    Route::put('/kategori/{kategori}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');

    // User Management (pimpinan — hanya user di kantornya sendiri)
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::post('/user', [UserController::class, 'store'])->name('user.store');
    Route::put('/user/{user}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/{user}', [UserController::class, 'destroy'])->name('user.destroy');

    // Audit Log (pimpinan — hanya audit kantornya)
    Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');

    // Profil
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
    Route::post('/profil', [ProfilController::class, 'update'])->name('profil.update');

    // Notifikasi
    Route::get('/notifikasi/read-all', [NotifikasiController::class, 'readAll'])->name('notifikasi.read_all');

    // ── ADMIN routes (lintas kantor) ─────────────────────────────────────────
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Manajemen Kantor
        Route::get('/kantor', [AdminController::class, 'kantorIndex'])->name('kantor.index');
        Route::post('/kantor', [AdminController::class, 'kantorStore'])->name('kantor.store');
        Route::put('/kantor/{kantor}', [AdminController::class, 'kantorUpdate'])->name('kantor.update');
        Route::post('/kantor/{kantor}/assign-user', [AdminController::class, 'assignUser'])->name('kantor.assign_user');

        // Manajemen User global
        Route::get('/user', [AdminController::class, 'userIndex'])->name('user.index');
        Route::post('/user', [AdminController::class, 'userStore'])->name('user.store');
        Route::put('/user/{user}', [AdminController::class, 'userUpdate'])->name('user.update');
        Route::delete('/user/{user}', [AdminController::class, 'userDestroy'])->name('user.destroy');

        // Audit log global
        Route::get('/audit', [AdminController::class, 'auditIndex'])->name('audit.index');
    });
});