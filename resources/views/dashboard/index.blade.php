@extends('layouts.app')
@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_sub', 'Ringkasan sistem gudang PT. Dian Pilar Utama')

@section('content')
<div style="padding:20px;display:flex;flex-direction:column;gap:20px;">

  {{-- Selamat datang --}}
  <div style="background:linear-gradient(135deg,#0f172a,#1e3a5f);border-radius:20px;padding:24px 28px;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-40px;right:-40px;width:200px;height:200px;background:radial-gradient(circle,rgba(249,115,22,.15),transparent);pointer-events:none;"></div>
    <div style="position:relative;z-index:1;">
      <div style="font-size:12px;color:rgba(249,115,22,.8);font-weight:700;text-transform:uppercase;letter-spacing:.1em;margin-bottom:6px;">
        {{ now()->format('l, d F Y') }}
      </div>
      <h2 style="font-family:'Sora',sans-serif;font-weight:800;font-size:22px;color:#fff;margin-bottom:6px;">
        Selamat datang, {{ session('user_nama') }}! 👋
      </h2>
      <p style="font-size:13px;color:#94a3b8;">
        @if(session('user_role') === 'pimpinan') Anda memiliki <b style="color:#fbbf24;">{{ $permintaanMenunggu }}</b> permintaan menunggu persetujuan Anda.
        @elseif(session('user_role') === 'manajerial') Kelola permintaan barang dan pantau stok gudang dari sini.
        @else Ada <b style="color:#fbbf24;">{{ $tugasOperator }}</b> tugas yang perlu Anda eksekusi hari ini.
        @endif
      </p>
    </div>
  </div>

  {{-- Stat cards --}}
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;">
    <div class="card-stat" style="border-left:3px solid #f97316;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
        <span style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;">Total Barang</span>
        <div style="width:32px;height:32px;border-radius:9px;background:#fff7ed;display:flex;align-items:center;justify-content:center;">
          <span class="material-symbols-outlined fill-icon" style="font-size:17px;color:#f97316;">inventory_2</span>
        </div>
      </div>
      <div style="font-family:'Sora',sans-serif;font-size:28px;font-weight:800;color:#0f172a;">{{ $totalBarang }}</div>
      <div style="font-size:11px;color:#94a3b8;margin-top:4px;">jenis barang terdaftar</div>
    </div>

    <div class="card-stat" style="border-left:3px solid #3b82f6;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
        <span style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;">Permintaan Aktif</span>
        <div style="width:32px;height:32px;border-radius:9px;background:#eff6ff;display:flex;align-items:center;justify-content:center;">
          <span class="material-symbols-outlined fill-icon" style="font-size:17px;color:#3b82f6;">inbox</span>
        </div>
      </div>
      <div style="font-family:'Sora',sans-serif;font-size:28px;font-weight:800;color:#0f172a;">{{ $permintaanAktif }}</div>
      <div style="font-size:11px;color:#94a3b8;margin-top:4px;">sedang dalam proses</div>
    </div>

    <div class="card-stat" style="border-left:3px solid #22c55e;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
        <span style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;">Transaksi Bulan Ini</span>
        <div style="width:32px;height:32px;border-radius:9px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;">
          <span class="material-symbols-outlined fill-icon" style="font-size:17px;color:#22c55e;">receipt_long</span>
        </div>
      </div>
      <div style="font-family:'Sora',sans-serif;font-size:28px;font-weight:800;color:#0f172a;">{{ $transaksibulanIni }}</div>
      <div style="font-size:11px;color:#94a3b8;margin-top:4px;">riwayat stok bulan ini</div>
    </div>

    <div class="card-stat" style="border-left:3px solid #ef4444;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
        <span style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;">Stok Menipis</span>
        <div style="width:32px;height:32px;border-radius:9px;background:#fef2f2;display:flex;align-items:center;justify-content:center;">
          <span class="material-symbols-outlined fill-icon" style="font-size:17px;color:#ef4444;">warning</span>
        </div>
      </div>
      <div style="font-family:'Sora',sans-serif;font-size:28px;font-weight:800;color:#0f172a;">{{ $stokMenipis }}</div>
      <div style="font-size:11px;color:#94a3b8;margin-top:4px;">barang di bawah minimum</div>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 360px;gap:16px;">

    {{-- Permintaan terbaru --}}
    <div class="card">
      <div style="padding:16px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
        <div>
          <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:14px;color:#0f172a;">Permintaan Terbaru</div>
          <div style="font-size:11px;color:#94a3b8;">10 permintaan terakhir</div>
        </div>
        <a href="{{ route('permintaan.index') }}" class="btn-ghost" style="font-size:12px;padding:7px 14px;">Lihat Semua</a>
      </div>
      <div style="overflow-x:auto;">
        <table class="tbl">
          <thead>
            <tr>
              <th>Nomor</th>
              <th>Jenis</th>
              <th>Keperluan</th>
              <th>Pengaju</th>
              <th>Status</th>
              <th>Tanggal</th>
            </tr>
          </thead>
          <tbody>
            @forelse($permintaanTerbaru as $p)
            <tr>
              <td>
                <a href="{{ route('permintaan.show', $p->id) }}" style="font-weight:700;color:#f97316;text-decoration:none;font-size:12px;">
                  {{ $p->nomor_permintaan }}
                </a>
              </td>
              <td>
                <span class="badge {{ $p->jenis === 'masuk' ? 'badge-masuk' : 'badge-keluar' }}">
                  {{ $p->jenis === 'masuk' ? '↑ Masuk' : '↓ Keluar' }}
                </span>
              </td>
              <td style="max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $p->keperluan }}</td>
              <td>{{ $p->pembuat->nama ?? '-' }}</td>
              <td>
                @php
                $badgeMap = [
                  'draft'=>'badge-draft','diajukan'=>'badge-diajukan','disetujui'=>'badge-disetujui',
                  'ditolak'=>'badge-ditolak','dikirim_operator'=>'badge-dikirim',
                  'diproses'=>'badge-diproses','selesai'=>'badge-selesai','dibatalkan'=>'badge-dibatalkan'
                ];
                $labelMap = [
                  'draft'=>'Draft','diajukan'=>'Diajukan','disetujui'=>'Disetujui',
                  'ditolak'=>'Ditolak','dikirim_operator'=>'Dikirim Operator',
                  'diproses'=>'Diproses','selesai'=>'Selesai','dibatalkan'=>'Dibatalkan'
                ];
                @endphp
                <span class="badge {{ $badgeMap[$p->status] ?? 'badge-draft' }}">{{ $labelMap[$p->status] ?? $p->status }}</span>
              </td>
              <td style="color:#94a3b8;font-size:11px;">{{ $p->created_at->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;padding:40px;color:#94a3b8;">Belum ada permintaan</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Sidebar right --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

      {{-- Stok menipis --}}
      <div class="card">
        <div style="padding:14px 16px;border-bottom:1px solid #f1f5f9;">
          <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:13px;color:#0f172a;">⚠️ Stok Menipis</div>
        </div>
        <div style="padding:8px;">
          @forelse($barangMenipis as $b)
          <div style="display:flex;align-items:center;gap:10px;padding:8px;border-radius:10px;background:#fef2f2;margin-bottom:6px;">
            <div style="flex:1;min-width:0;">
              <div style="font-size:12px;font-weight:700;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $b->nama }}</div>
              <div style="font-size:11px;color:#94a3b8;">{{ $b->kode }}</div>
            </div>
            <div style="text-align:right;flex-shrink:0;">
              <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:16px;color:#ef4444;">{{ $b->stok_tersedia }}</div>
              <div style="font-size:10px;color:#94a3b8;">/ min {{ $b->stok_minimum }}</div>
            </div>
          </div>
          @empty
          <div style="padding:20px;text-align:center;font-size:12px;color:#94a3b8;">Semua stok aman ✓</div>
          @endforelse
        </div>
      </div>

      {{-- Quick actions --}}
      <div class="card" style="padding:14px;">
        <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:13px;color:#0f172a;margin-bottom:10px;">Aksi Cepat</div>
        <div style="display:flex;flex-direction:column;gap:6px;">
          @if(session('user_role') === 'manajerial')
          <a href="{{ route('permintaan.create') }}" class="btn-or" style="width:100%;justify-content:center;">
            <span class="material-symbols-outlined fill-icon" style="font-size:16px;">add</span> Buat Permintaan
          </a>
          @endif
          @if(session('user_role') === 'operator_gudang')
          <a href="{{ route('permintaan.index') }}?filter=dikirim_operator" class="btn-or" style="width:100%;justify-content:center;">
            <span class="material-symbols-outlined fill-icon" style="font-size:16px;">task_alt</span> Tugas Saya
          </a>
          @endif
          @if(session('user_role') === 'pimpinan')
          <a href="{{ route('permintaan.index') }}?filter=diajukan" class="btn-or" style="width:100%;justify-content:center;">
            <span class="material-symbols-outlined fill-icon" style="font-size:16px;">approval</span> Menunggu Approval
          </a>
          @endif
          <a href="{{ route('stok.index') }}" class="btn-ghost" style="width:100%;justify-content:center;">
            <span class="material-symbols-outlined" style="font-size:16px;">inventory_2</span> Cek Stok
          </a>
          <a href="{{ route('laporan.index') }}" class="btn-ghost" style="width:100%;justify-content:center;">
            <span class="material-symbols-outlined" style="font-size:16px;">bar_chart</span> Laporan
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection