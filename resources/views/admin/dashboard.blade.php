@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('page_title', 'Admin Dashboard')
@section('page_sub', 'Ringkasan semua kantor — akses penuh lintas kantor')

@section('content')
<div style="padding:20px;display:flex;flex-direction:column;gap:20px;">

  {{-- Banner admin --}}
  <div style="background:linear-gradient(135deg,#7f1d1d,#991b1b);border-radius:20px;padding:20px 24px;display:flex;align-items:center;gap:14px;">
    <div style="width:46px;height:46px;border-radius:13px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <span class="material-symbols-outlined fill-icon" style="color:#fff;font-size:24px;">admin_panel_settings</span>
    </div>
    <div>
      <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:16px;color:#fff;">Mode Administrator</div>
      <div style="font-size:12px;color:rgba(255,255,255,.65);margin-top:2px;">Anda memiliki akses penuh ke seluruh data semua kantor. Gunakan dengan bijak.</div>
    </div>
  </div>

  {{-- Global stats --}}
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;">
    <div class="card-stat" style="border-left:3px solid #dc2626;">
      <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;margin-bottom:8px;">Total Barang (Global)</div>
      <div style="font-family:'Sora',sans-serif;font-size:28px;font-weight:800;color:#0f172a;">{{ $totalBarangGlobal }}</div>
      <div style="font-size:11px;color:#94a3b8;margin-top:3px;">semua kantor</div>
    </div>
    <div class="card-stat" style="border-left:3px solid #7c3aed;">
      <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;margin-bottom:8px;">User Aktif</div>
      <div style="font-family:'Sora',sans-serif;font-size:28px;font-weight:800;color:#0f172a;">{{ $totalUserAktif }}</div>
      <div style="font-size:11px;color:#94a3b8;margin-top:3px;">seluruh sistem</div>
    </div>
    <div class="card-stat" style="border-left:3px solid #0891b2;">
      <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;margin-bottom:8px;">Permintaan Bulan Ini</div>
      <div style="font-family:'Sora',sans-serif;font-size:28px;font-weight:800;color:#0f172a;">{{ $permintaanBulanIni }}</div>
      <div style="font-size:11px;color:#94a3b8;margin-top:3px;">semua kantor</div>
    </div>
    <div class="card-stat" style="border-left:3px solid #ef4444;">
      <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;margin-bottom:8px;">Stok Menipis (Global)</div>
      <div style="font-family:'Sora',sans-serif;font-size:28px;font-weight:800;color:#dc2626;">{{ $stokMenipisGlobal }}</div>
      <div style="font-size:11px;color:#94a3b8;margin-top:3px;">seluruh sistem</div>
    </div>
  </div>

  {{-- Per-kantor breakdown --}}
  <div>
    <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:15px;color:#0f172a;margin-bottom:12px;">Kondisi Per Kantor</div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;">
      @foreach($kantors as $kantor)
      <div class="card" style="padding:18px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
          <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#dc2626,#991b1b);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <span class="material-symbols-outlined fill-icon" style="color:#fff;font-size:18px;">warehouse</span>
          </div>
          <div>
            <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:13px;color:#0f172a;">{{ $kantor->nama }}</div>
            <div style="font-size:11px;color:#94a3b8;">{{ $kantor->kota }}</div>
          </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;text-align:center;">
          <div style="background:#f8fafc;border-radius:8px;padding:8px;">
            <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:18px;color:#0f172a;">{{ $kantor->total_barang }}</div>
            <div style="font-size:9px;color:#94a3b8;font-weight:700;text-transform:uppercase;">Barang</div>
          </div>
          <div style="background:{{ $kantor->stok_menipis > 0 ? '#fef2f2' : '#f0fdf4' }};border-radius:8px;padding:8px;">
            <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:18px;color:{{ $kantor->stok_menipis > 0 ? '#dc2626' : '#16a34a' }};">{{ $kantor->stok_menipis }}</div>
            <div style="font-size:9px;color:#94a3b8;font-weight:700;text-transform:uppercase;">Menipis</div>
          </div>
          <div style="background:#eff6ff;border-radius:8px;padding:8px;">
            <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:18px;color:#1d4ed8;">{{ $kantor->permintaan_aktif }}</div>
            <div style="font-size:9px;color:#94a3b8;font-weight:700;text-transform:uppercase;">Aktif</div>
          </div>
        </div>
        <div style="margin-top:12px;display:flex;gap:6px;">
          <a href="{{ route('stok.index') }}?kantor_id={{ $kantor->id }}"
            style="flex:1;text-align:center;padding:6px;border-radius:8px;background:#f8fafc;border:1px solid #e2e8f0;font-size:11px;font-weight:600;color:#475569;text-decoration:none;">
            Lihat Stok
          </a>
          <a href="{{ route('laporan.index') }}?kantor_id={{ $kantor->id }}"
            style="flex:1;text-align:center;padding:6px;border-radius:8px;background:#f8fafc;border:1px solid #e2e8f0;font-size:11px;font-weight:600;color:#475569;text-decoration:none;">
            Laporan
          </a>
        </div>
      </div>
      @endforeach
    </div>
  </div>

  {{-- Quick links --}}
  <div style="display:flex;gap:10px;flex-wrap:wrap;">
    <a href="{{ route('admin.kantor.index') }}" class="btn-or" style="font-size:12px;">
      <span class="material-symbols-outlined fill-icon" style="font-size:15px;">warehouse</span> Kelola Kantor
    </a>
    <a href="{{ route('admin.user.index') }}" class="btn-or" style="font-size:12px;">
      <span class="material-symbols-outlined fill-icon" style="font-size:15px;">manage_accounts</span> Kelola User
    </a>
    <a href="{{ route('admin.audit.index') }}" class="btn-ghost" style="font-size:12px;">
      <span class="material-symbols-outlined" style="font-size:15px;">history</span> Audit Log Global
    </a>
  </div>
</div>
@endsection