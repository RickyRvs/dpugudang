<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Kantor;
use App\Models\KategoriBarang;
use App\Models\Barang;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Kantor ──────────────────────────────────────────────
        $kPusat    = Kantor::create(['kode' => 'PKU', 'nama' => 'Kantor Pusat Pekanbaru',  'kota' => 'Pekanbaru', 'alamat' => 'Jl. Sudirman No. 1, Pekanbaru, Riau',          'is_active' => true]);
        $kTebet    = Kantor::create(['kode' => 'TBT', 'nama' => 'Kantor Proyek Tebet',     'kota' => 'Jakarta',   'alamat' => 'Jl. Tebet Raya No. 45, Tebet, Jakarta Selatan', 'is_active' => true]);
        $kSurabaya = Kantor::create(['kode' => 'SBY', 'nama' => 'Kantor Proyek Surabaya',  'kota' => 'Surabaya',  'alamat' => 'Jl. Pemuda No. 17, Surabaya, Jawa Timur',       'is_active' => true]);
        $kBekasi   = Kantor::create(['kode' => 'BKS', 'nama' => 'Kantor Proyek Bekasi',    'kota' => 'Bekasi',    'alamat' => 'Jl. Ahmad Yani No. 88, Bekasi, Jawa Barat',     'is_active' => true]);

        // ── Users ────────────────────────────────────────────────

        // ADMIN: superuser lintas kantor, tidak terikat kantor tertentu
        User::create([
            'nama'      => 'Super Admin',
            'email'     => 'admin@dbs.co.id',
            'password'  => Hash::make('admin123'),
            'role'      => 'admin',
            'jabatan'   => 'System Administrator',
            'is_active' => true,
        ]);
        // Admin tidak di-attach ke kantor manapun (akses semua via role)

        // Pimpinan: akses semua kantor
        $pimpinan = User::create([
            'nama'      => 'Direktur Utama',
            'email'     => 'pimpinan@dbs.co.id',
            'password'  => Hash::make('pimpinan123'),
            'role'      => 'pimpinan',
            'jabatan'   => 'Direktur Utama',
            'is_active' => true,
        ]);
        $pimpinan->kantors()->attach([$kPusat->id, $kTebet->id, $kSurabaya->id, $kBekasi->id]);

        // Manajer Pusat: akses kantor pusat saja
        $manajerPusat = User::create([
            'nama'      => 'Manajer Gudang Pekanbaru',
            'email'     => 'manajer.pku@dbs.co.id',
            'password'  => Hash::make('manajerial123'),
            'role'      => 'manajerial',
            'jabatan'   => 'Manajer Gudang',
            'is_active' => true,
        ]);
        $manajerPusat->kantors()->attach([$kPusat->id]);

        // Manajer Proyek: akses 3 kantor proyek
        $manajerProyek = User::create([
            'nama'      => 'Manajer Proyek',
            'email'     => 'manajerial@dbs.co.id',
            'password'  => Hash::make('manajerial123'),
            'role'      => 'manajerial',
            'jabatan'   => 'Manajer Proyek',
            'is_active' => true,
        ]);
        $manajerProyek->kantors()->attach([$kTebet->id, $kSurabaya->id, $kBekasi->id]);

        // Operator Pusat
        $opPusat = User::create([
            'nama'      => 'Operator Gudang Pekanbaru',
            'email'     => 'operator.pku@dbs.co.id',
            'password'  => Hash::make('operator123'),
            'role'      => 'operator_gudang',
            'jabatan'   => 'Staff Gudang',
            'is_active' => true,
        ]);
        $opPusat->kantors()->attach([$kPusat->id]);

        // Operator Tebet
        $opTebet = User::create([
            'nama'      => 'Operator Gudang Tebet',
            'email'     => 'operator@dbs.co.id',
            'password'  => Hash::make('operator123'),
            'role'      => 'operator_gudang',
            'jabatan'   => 'Staff Gudang',
            'is_active' => true,
        ]);
        $opTebet->kantors()->attach([$kTebet->id]);

        // ── Kategori ─────────────────────────────────────────────
        $k1 = KategoriBarang::create(['nama' => 'Alat Tulis Kantor', 'kode' => 'ATK', 'deskripsi' => 'Perlengkapan kantor']);
        $k2 = KategoriBarang::create(['nama' => 'Elektronik',        'kode' => 'ELK', 'deskripsi' => 'Peralatan elektronik']);
        $k3 = KategoriBarang::create(['nama' => 'Bahan Bangunan',    'kode' => 'BHB', 'deskripsi' => 'Material konstruksi']);
        $k4 = KategoriBarang::create(['nama' => 'Kebersihan',        'kode' => 'KBR', 'deskripsi' => 'Perlengkapan kebersihan']);

        // ── Barang (dengan kantor_id) ─────────────────────────────
        // Kantor Pusat Pekanbaru
        Barang::create(['kode' => 'ATK-001', 'nama' => 'Kertas A4 80gr',       'kategori_id' => $k1->id, 'kantor_id' => $kPusat->id,    'satuan' => 'rim',    'stok_tersedia' => 25, 'stok_minimum' => 10, 'harga_satuan' => 55000,  'lokasi_rak' => 'A-01', 'is_active' => true]);
        Barang::create(['kode' => 'ATK-002', 'nama' => 'Pulpen Ballpoint',      'kategori_id' => $k1->id, 'kantor_id' => $kPusat->id,    'satuan' => 'lusin',  'stok_tersedia' => 8,  'stok_minimum' => 5,  'harga_satuan' => 24000,  'lokasi_rak' => 'A-02', 'is_active' => true]);
        Barang::create(['kode' => 'ATK-003', 'nama' => 'Tinta Printer Hitam',   'kategori_id' => $k1->id, 'kantor_id' => $kPusat->id,    'satuan' => 'botol',  'stok_tersedia' => 3,  'stok_minimum' => 5,  'harga_satuan' => 85000,  'lokasi_rak' => 'A-03', 'is_active' => true]);
        Barang::create(['kode' => 'ELK-001', 'nama' => 'Kabel LAN Cat6',        'kategori_id' => $k2->id, 'kantor_id' => $kPusat->id,    'satuan' => 'meter',  'stok_tersedia' => 0,  'stok_minimum' => 20, 'harga_satuan' => 8000,   'lokasi_rak' => 'B-01', 'is_active' => true]);
        Barang::create(['kode' => 'ELK-002', 'nama' => 'Stop Kontak 3 Lubang',  'kategori_id' => $k2->id, 'kantor_id' => $kPusat->id,    'satuan' => 'pcs',    'stok_tersedia' => 15, 'stok_minimum' => 5,  'harga_satuan' => 35000,  'lokasi_rak' => 'B-02', 'is_active' => true]);
        Barang::create(['kode' => 'BHB-001', 'nama' => 'Cat Tembok Putih 5kg',  'kategori_id' => $k3->id, 'kantor_id' => $kPusat->id,    'satuan' => 'kaleng', 'stok_tersedia' => 12, 'stok_minimum' => 4,  'harga_satuan' => 120000, 'lokasi_rak' => 'C-01', 'is_active' => true]);
        Barang::create(['kode' => 'KBR-001', 'nama' => 'Sabun Cuci Tangan',     'kategori_id' => $k4->id, 'kantor_id' => $kPusat->id,    'satuan' => 'botol',  'stok_tersedia' => 30, 'stok_minimum' => 10, 'harga_satuan' => 18000,  'lokasi_rak' => 'D-01', 'is_active' => true]);
        Barang::create(['kode' => 'KBR-002', 'nama' => 'Lap Pel Lantai',        'kategori_id' => $k4->id, 'kantor_id' => $kPusat->id,    'satuan' => 'pcs',    'stok_tersedia' => 6,  'stok_minimum' => 3,  'harga_satuan' => 25000,  'lokasi_rak' => 'D-02', 'is_active' => true]);

        // Kantor Proyek Tebet
        Barang::create(['kode' => 'ATK-001', 'nama' => 'Kertas A4 80gr',       'kategori_id' => $k1->id, 'kantor_id' => $kTebet->id,    'satuan' => 'rim',    'stok_tersedia' => 10, 'stok_minimum' => 5,  'harga_satuan' => 55000,  'lokasi_rak' => 'A-01', 'is_active' => true]);
        Barang::create(['kode' => 'ATK-002', 'nama' => 'Pulpen Ballpoint',      'kategori_id' => $k1->id, 'kantor_id' => $kTebet->id,    'satuan' => 'lusin',  'stok_tersedia' => 4,  'stok_minimum' => 3,  'harga_satuan' => 24000,  'lokasi_rak' => 'A-02', 'is_active' => true]);
        Barang::create(['kode' => 'BHB-001', 'nama' => 'Cat Tembok Putih 5kg',  'kategori_id' => $k3->id, 'kantor_id' => $kTebet->id,    'satuan' => 'kaleng', 'stok_tersedia' => 20, 'stok_minimum' => 8,  'harga_satuan' => 120000, 'lokasi_rak' => 'C-01', 'is_active' => true]);
        Barang::create(['kode' => 'KBR-001', 'nama' => 'Sabun Cuci Tangan',     'kategori_id' => $k4->id, 'kantor_id' => $kTebet->id,    'satuan' => 'botol',  'stok_tersedia' => 15, 'stok_minimum' => 5,  'harga_satuan' => 18000,  'lokasi_rak' => 'D-01', 'is_active' => true]);

        // Kantor Proyek Surabaya
        Barang::create(['kode' => 'ATK-001', 'nama' => 'Kertas A4 80gr',       'kategori_id' => $k1->id, 'kantor_id' => $kSurabaya->id, 'satuan' => 'rim',    'stok_tersedia' => 5,  'stok_minimum' => 5,  'harga_satuan' => 55000,  'lokasi_rak' => 'A-01', 'is_active' => true]);
        Barang::create(['kode' => 'ELK-001', 'nama' => 'Kabel LAN Cat6',        'kategori_id' => $k2->id, 'kantor_id' => $kSurabaya->id, 'satuan' => 'meter',  'stok_tersedia' => 50, 'stok_minimum' => 20, 'harga_satuan' => 8000,   'lokasi_rak' => 'B-01', 'is_active' => true]);
        Barang::create(['kode' => 'BHB-001', 'nama' => 'Cat Tembok Putih 5kg',  'kategori_id' => $k3->id, 'kantor_id' => $kSurabaya->id, 'satuan' => 'kaleng', 'stok_tersedia' => 8,  'stok_minimum' => 4,  'harga_satuan' => 120000, 'lokasi_rak' => 'C-01', 'is_active' => true]);
        Barang::create(['kode' => 'KBR-002', 'nama' => 'Lap Pel Lantai',        'kategori_id' => $k4->id, 'kantor_id' => $kSurabaya->id, 'satuan' => 'pcs',    'stok_tersedia' => 4,  'stok_minimum' => 3,  'harga_satuan' => 25000,  'lokasi_rak' => 'D-02', 'is_active' => true]);

        // Kantor Proyek Bekasi
        Barang::create(['kode' => 'ATK-001', 'nama' => 'Kertas A4 80gr',       'kategori_id' => $k1->id, 'kantor_id' => $kBekasi->id,   'satuan' => 'rim',    'stok_tersedia' => 7,  'stok_minimum' => 5,  'harga_satuan' => 55000,  'lokasi_rak' => 'A-01', 'is_active' => true]);
        Barang::create(['kode' => 'ATK-003', 'nama' => 'Tinta Printer Hitam',   'kategori_id' => $k1->id, 'kantor_id' => $kBekasi->id,   'satuan' => 'botol',  'stok_tersedia' => 2,  'stok_minimum' => 5,  'harga_satuan' => 85000,  'lokasi_rak' => 'A-03', 'is_active' => true]);
        Barang::create(['kode' => 'ELK-002', 'nama' => 'Stop Kontak 3 Lubang',  'kategori_id' => $k2->id, 'kantor_id' => $kBekasi->id,   'satuan' => 'pcs',    'stok_tersedia' => 10, 'stok_minimum' => 5,  'harga_satuan' => 35000,  'lokasi_rak' => 'B-02', 'is_active' => true]);
        Barang::create(['kode' => 'KBR-001', 'nama' => 'Sabun Cuci Tangan',     'kategori_id' => $k4->id, 'kantor_id' => $kBekasi->id,   'satuan' => 'botol',  'stok_tersedia' => 20, 'stok_minimum' => 10, 'harga_satuan' => 18000,  'lokasi_rak' => 'D-01', 'is_active' => true]);
    }
}