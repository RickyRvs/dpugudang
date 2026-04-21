<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>@yield('title', 'Sistem Gudang') | PT. Dian Bangun Sejahtera</title>
<meta name="csrf-token" content="{{ csrf_token() }}"/>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<style>
/* ─── Reset ─────────────────────────────────────────── */
*, *::before, *::after {
  font-family: 'DM Sans', sans-serif;
  box-sizing: border-box;
  margin: 0; padding: 0;
}
html { height: 100%; }
body { height: 100%; }
h1,h2,h3,.font-head { font-family: 'Sora', sans-serif; }
.material-symbols-outlined {
  font-variation-settings: 'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;
  font-size: 20px; vertical-align: middle; line-height: 1;
}
.fill-icon { font-variation-settings: 'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24; }

/* ─── App Shell ─────────────────────────────────────── */
.app-shell { display: flex; height: 100vh; }

/* ─── Sidebar ────────────────────────────────────────── */
.sidebar {
  width: 220px; flex-shrink: 0; height: 100vh;
  background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
  display: flex; flex-direction: column; overflow: hidden;
}
nav::-webkit-scrollbar { width: 3px; }
nav::-webkit-scrollbar-thumb { background: rgba(249,115,22,.25); border-radius: 8px; }
.nav-item {
  display: flex; align-items: center; gap: 10px;
  padding: 8px 12px; border-radius: 10px;
  color: #64748b; font-size: 12.5px; font-weight: 500;
  transition: all .15s; border: 1px solid transparent; text-decoration: none;
}
.nav-item:hover { background: #fff7ed; color: #ea580c; border-color: #fed7aa; }
.nav-item.active {
  background: linear-gradient(135deg,#f97316,#ea580c);
  color: #fff; font-weight: 700;
  box-shadow: 0 4px 14px rgba(249,115,22,.3);
}
.nav-item.active .material-symbols-outlined { color: #fff; }
.nav-group-label {
  font-size: 10px; font-weight: 700; letter-spacing: .12em;
  text-transform: uppercase; color: #cbd5e1; padding: 12px 12px 4px;
}

/* ─── Main Column ───────────────────────────────────── */
.main-area { flex: 1; min-width: 0; height: 100vh; display: flex; flex-direction: column; overflow: hidden; }
.top-header {
  flex-shrink: 0; background: #fff; border-bottom: 1px solid #f1f5f9;
  padding: 10px 20px; display: flex; align-items: center;
  justify-content: space-between; box-shadow: 0 1px 4px rgba(0,0,0,.04); z-index: 10;
}
.page-content { flex: 1; overflow-y: auto; overflow-x: hidden; }
.page-content::-webkit-scrollbar { width: 5px; }
.page-content::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 8px; }

/* ─── Modal ─────────────────────────────────────────── */
.modal {
  display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(15,23,42,.65); z-index: 9999; overflow-y: auto; padding: 48px 20px;
}
.modal.open { display: flex; flex-direction: column; align-items: center; }
.modal > div { width: 100%; max-width: 560px; flex-shrink: 0; margin-left: 220px; }

/* ─── Toast ─────────────────────────────────────────── */
.toast {
  position: fixed; bottom: 24px; right: 24px; z-index: 99999;
  transform: translateY(80px); opacity: 0;
  transition: all .3s cubic-bezier(.34,1.56,.64,1); pointer-events: none;
}
.toast.show { transform: translateY(0); opacity: 1; pointer-events: auto; }

/* ─── Animations ────────────────────────────────────── */
@keyframes fadeUp { from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);} }
.fade-up { animation: fadeUp .2s ease forwards; }
@keyframes spin { to { transform: rotate(360deg); } }

/* ─── Buttons ───────────────────────────────────────── */
.btn-or {
  background: linear-gradient(135deg,#f97316,#ea580c); color: #fff;
  font-weight: 700; border-radius: 12px; padding: 9px 20px; font-size: 13px;
  box-shadow: 0 4px 14px rgba(249,115,22,.25); border: none; cursor: pointer;
  display: inline-flex; align-items: center; gap: 6px; text-decoration: none;
  transition: all .18s;
}
.btn-or:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(249,115,22,.35); color: #fff; }
.btn-ghost {
  background: #f1f5f9; color: #475569; font-weight: 600;
  border-radius: 12px; padding: 9px 20px; font-size: 13px;
  border: 1px solid #e2e8f0; cursor: pointer;
  display: inline-flex; align-items: center; gap: 6px; text-decoration: none; transition: all .15s;
}
.btn-ghost:hover { background: #e2e8f0; color: #334155; }
.btn-danger {
  background: #fef2f2; color: #991b1b; font-weight: 700; border-radius: 12px;
  padding: 9px 20px; font-size: 13px; border: 1px solid #fecaca; cursor: pointer;
  display: inline-flex; align-items: center; gap: 6px; text-decoration: none; transition: all .15s;
}
.btn-danger:hover { background: #fee2e2; }
.btn-green {
  background: linear-gradient(135deg,#22c55e,#16a34a); color: #fff; font-weight: 700;
  border-radius: 12px; padding: 9px 20px; font-size: 13px;
  border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
  text-decoration: none; transition: all .18s;
  box-shadow: 0 4px 14px rgba(34,197,94,.25);
}
.btn-green:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(34,197,94,.35); color: #fff; }

/* ─── Cards ─────────────────────────────────────────── */
.card { background:#fff; border-radius:16px; border:1px solid #f1f5f9; box-shadow:0 1px 4px rgba(0,0,0,.05); }
.card-stat { background:#fff; border-radius:16px; border:1px solid #f1f5f9; padding:20px; box-shadow:0 1px 4px rgba(0,0,0,.05); }

/* ─── Fields ────────────────────────────────────────── */
.field {
  width:100%; background:#f8fafc; border:1.5px solid #e2e8f0;
  border-radius:10px; padding:9px 12px; font-size:13px;
  transition:all .15s; outline:none; color:#0f172a;
}
.field:focus { border-color:#f97316; background:#fff; box-shadow:0 0 0 3px rgba(249,115,22,.1); }
select.field { cursor:pointer; }
textarea.field { resize:vertical; min-height:80px; line-height:1.5; }

/* ─── Badges ────────────────────────────────────────── */
.badge { padding:3px 10px; border-radius:999px; font-size:10px; font-weight:700; }
.badge-draft      { background:#f1f5f9; color:#64748b; border:1px solid #e2e8f0; }
.badge-diajukan   { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
.badge-disetujui  { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
.badge-ditolak    { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }
.badge-dikirim    { background:#fef9c3; color:#854d0e; border:1px solid #fef08a; }
.badge-diproses   { background:#fff7ed; color:#c2410c; border:1px solid #fed7aa; }
.badge-selesai    { background:#d1fae5; color:#065f46; border:1px solid #6ee7b7; }
.badge-dibatalkan { background:#f1f5f9; color:#94a3b8; border:1px solid #e2e8f0; }
.badge-masuk      { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
.badge-keluar     { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }

/* ─── Table ─────────────────────────────────────────── */
.tbl { width:100%; border-collapse:collapse; }
.tbl th { padding:10px 12px; text-align:left; font-size:10px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.07em; background:#f8fafc; border-bottom:1px solid #f1f5f9; }
.tbl td { padding:11px 12px; font-size:13px; border-bottom:1px solid #f8fafc; color:#334155; }
.tbl tr:hover td { background:#fff7ed; }
.tbl tr:last-child td { border-bottom:none; }

/* ─── Alert ─────────────────────────────────────────── */
.alert-success { background:#dcfce7; border:1px solid #bbf7d0; border-radius:11px; padding:11px 14px; font-size:13px; color:#166534; font-weight:600; display:flex; align-items:center; gap:8px; }
.alert-error   { background:#fef2f2; border:1px solid #fecaca; border-radius:11px; padding:11px 14px; font-size:13px; color:#991b1b; font-weight:600; display:flex; align-items:center; gap:8px; }
.alert-warning { background:#fefce8; border:1px solid #fef08a; border-radius:11px; padding:11px 14px; font-size:13px; color:#854d0e; font-weight:600; display:flex; align-items:center; gap:8px; }
.alert-info    { background:#eff6ff; border:1px solid #bfdbfe; border-radius:11px; padding:11px 14px; font-size:13px; color:#1e40af; font-weight:600; display:flex; align-items:center; gap:8px; }

/* ─── Progress ──────────────────────────────────────── */
.progress-track { height:6px; background:#f1f5f9; border-radius:999px; overflow:hidden; }
.progress-fill  { height:100%; border-radius:999px; background:linear-gradient(90deg,#f97316,#ea580c); }

/* ─── Role badges ───────────────────────────────────── */
.role-admin      { background: linear-gradient(135deg, #dc2626, #991b1b); }
.role-pimpinan   { background: linear-gradient(135deg, #7c3aed, #6d28d9); }
.role-manajerial { background: linear-gradient(135deg, #0891b2, #0e7490); }
.role-operator   { background: linear-gradient(135deg, #f97316, #ea580c); }

/* ─── Notif ─────────────────────────────────────────── */
.notif-dot { position:absolute; top:6px; right:6px; width:8px; height:8px; background:#ef4444; border-radius:50%; border:2px solid #fff; }
.notif-badge { position:absolute; top:4px; right:4px; background:#ef4444; color:#fff; border-radius:999px; font-size:9px; font-weight:800; padding:1px 5px; border:1.5px solid #fff; min-width:16px; text-align:center; }

/* ─── Empty state ───────────────────────────────────── */
.empty-state { padding:60px 20px; text-align:center; }
.empty-state .icon { width:64px; height:64px; border-radius:18px; background:#f8fafc; border:1px solid #f1f5f9; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; }
</style>
</head>
<body>
<div class="app-shell">

  <!-- ── SIDEBAR ── -->
  <aside class="sidebar">
    <div style="flex-shrink:0;padding:16px;border-bottom:1px solid rgba(249,115,22,.15);">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
        <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#f97316,#c2410c);display:flex;align-items:center;justify-content:center;font-family:'Sora',sans-serif;font-weight:800;font-size:12px;color:#fff;flex-shrink:0;box-shadow:0 4px 12px rgba(249,115,22,.4);">DBS</div>
        <div>
          <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:12.5px;color:#fff;line-height:1.2;">Dian Bangun Sejahtera</div>
          <div style="font-size:10px;color:rgba(249,115,22,.7);font-weight:600;text-transform:uppercase;letter-spacing:.08em;">Gudang v1</div>
        </div>
      </div>
      <!-- User info sidebar -->
      <div style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);border-radius:10px;padding:8px 10px;display:flex;align-items:center;gap:8px;">
        <div style="width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-family:'Sora',sans-serif;font-weight:800;font-size:11px;color:#fff;flex-shrink:0;
          @if(session('user_role') === 'admin') background:linear-gradient(135deg,#dc2626,#991b1b);
          @elseif(session('user_role') === 'pimpinan') background:linear-gradient(135deg,#7c3aed,#6d28d9);
          @elseif(session('user_role') === 'manajerial') background:linear-gradient(135deg,#0891b2,#0e7490);
          @else background:linear-gradient(135deg,#f97316,#ea580c); @endif">
          {{ strtoupper(substr(session('user_nama', 'U'), 0, 2)) }}
        </div>
        <div style="min-width:0;flex:1;">
          <div style="font-size:12px;font-weight:700;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ session('user_nama', 'User') }}</div>
          <div style="font-size:10px;color:#64748b;font-weight:500;text-transform:capitalize;">{{ str_replace('_', ' ', session('user_role', 'Operator')) }}</div>
        </div>
      </div>
    </div>

    {{-- ── NAV ── --}}
    <nav style="flex:1;overflow-y:auto;padding:10px 8px;display:flex;flex-direction:column;gap:1px;">
      <div class="nav-group-label">Menu Utama</div>
      <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active':'' }}">
        <span class="material-symbols-outlined fill-icon">dashboard</span> Dashboard
      </a>

      <a href="{{ route('permintaan.index') }}" class="nav-item {{ request()->routeIs('permintaan*') ? 'active':'' }}">
        <span class="material-symbols-outlined">inbox</span> Permintaan Barang
      </a>

      <a href="{{ route('stok.index') }}" class="nav-item {{ request()->routeIs('stok*') ? 'active':'' }}">
        <span class="material-symbols-outlined">inventory_2</span> Stok Gudang
      </a>

      <a href="{{ route('riwayat.index') }}" class="nav-item {{ request()->routeIs('riwayat*') ? 'active':'' }}">
        <span class="material-symbols-outlined">receipt_long</span> Riwayat Stok
      </a>

      @if(in_array(session('user_role'), ['operator_gudang', 'manajerial', 'admin']))
      <a href="{{ route('opname.index') }}" class="nav-item {{ request()->routeIs('opname*') ? 'active':'' }}">
        <span class="material-symbols-outlined">fact_check</span> Stock Opname
      </a>
      @endif

      <a href="{{ route('laporan.index') }}" class="nav-item {{ request()->routeIs('laporan*') ? 'active':'' }}">
        <span class="material-symbols-outlined">bar_chart</span> Laporan
      </a>

      {{-- Manajemen: pimpinan, manajerial, admin --}}
      @if(in_array(session('user_role'), ['pimpinan', 'manajerial', 'admin']))
      <div class="nav-group-label" style="margin-top:4px;">Manajemen</div>
      <a href="{{ route('barang.index') }}" class="nav-item {{ request()->routeIs('barang*') ? 'active':'' }}">
        <span class="material-symbols-outlined">category</span> Master Barang
      </a>
      <a href="{{ route('kategori.index') }}" class="nav-item {{ request()->routeIs('kategori*') ? 'active':'' }}">
        <span class="material-symbols-outlined">folder</span> Kategori
      </a>
      @endif

      {{-- Pimpinan: kelola user di kantornya --}}
      @if(session('user_role') === 'pimpinan')
      <a href="{{ route('user.index') }}" class="nav-item {{ request()->routeIs('user*') ? 'active':'' }}">
        <span class="material-symbols-outlined">manage_accounts</span> Manajemen User
      </a>
      <a href="{{ route('audit.index') }}" class="nav-item {{ request()->routeIs('audit*') ? 'active':'' }}">
        <span class="material-symbols-outlined">history</span> Audit Log
      </a>
      @endif

      {{-- ADMIN: menu lintas kantor --}}
      @if(session('user_role') === 'admin')
      <div class="nav-group-label" style="margin-top:4px;">Admin Panel</div>
      <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active':'' }}">
        <span class="material-symbols-outlined fill-icon">admin_panel_settings</span> Admin Dashboard
      </a>
      <a href="{{ route('admin.kantor.index') }}" class="nav-item {{ request()->routeIs('admin.kantor*') ? 'active':'' }}">
        <span class="material-symbols-outlined">warehouse</span> Kelola Kantor
      </a>
      <a href="{{ route('admin.user.index') }}" class="nav-item {{ request()->routeIs('admin.user*') ? 'active':'' }}">
        <span class="material-symbols-outlined">manage_accounts</span> Kelola User Global
      </a>
      <a href="{{ route('admin.audit.index') }}" class="nav-item {{ request()->routeIs('admin.audit*') ? 'active':'' }}">
        <span class="material-symbols-outlined">history</span> Audit Log Global
      </a>
      @endif
    </nav>

    <div style="flex-shrink:0;padding:8px;border-top:1px solid rgba(255,255,255,.08);">
      <a href="{{ route('profil.index') }}" class="nav-item {{ request()->routeIs('profil*') ? 'active':'' }}">
        <span class="material-symbols-outlined">person</span> Profil Saya
      </a>
      <a href="#" class="nav-item" onclick="event.preventDefault();document.getElementById('logout-form').submit();" style="color:#ef4444;margin-top:2px;">
        <span class="material-symbols-outlined" style="color:#ef4444;">logout</span> Keluar
      </a>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
      <div style="font-size:10px;color:#475569;text-align:center;margin-top:8px;">Sistem Gudang DBS v1.0</div>
    </div>
  </aside>

  <!-- ── MAIN AREA ── -->
  <div class="main-area">
    <header class="top-header">
      <div>
        <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:16px;color:#0f172a;">@yield('page_title', 'Dashboard')</div>
        <div style="font-size:11px;color:#94a3b8;margin-top:1px;">@yield('page_sub', 'PT. Dian Bangun Sejahtera')</div>
      </div>

      <div style="display:flex;align-items:center;gap:10px;">

        {{-- Badge kantor aktif --}}
        @if(session('user_role') === 'admin')
        <div style="display:flex;align-items:center;gap:6px;padding:5px 12px;background:linear-gradient(135deg,#fef2f2,#fee2e2);border:1px solid #fecaca;border-radius:999px;">
          <span class="material-symbols-outlined fill-icon" style="font-size:13px;color:#dc2626;">admin_panel_settings</span>
          <span style="font-size:11px;font-weight:700;color:#991b1b;">Semua Kantor</span>
        </div>
        @else
        <div style="display:flex;align-items:center;gap:6px;padding:5px 12px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:999px;">
          <span class="material-symbols-outlined fill-icon" style="font-size:13px;color:#64748b;">location_on</span>
          <span style="font-size:11px;font-weight:700;color:#475569;">{{ session('kantor_nama', '-') }}</span>
        </div>
        @endif

        {{-- Notifikasi --}}
        <div style="position:relative;">
          <button onclick="toggleNotif()" style="position:relative;width:36px;height:36px;border-radius:9px;background:#f8fafc;border:1px solid #e2e8f0;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#64748b;">
            <span class="material-symbols-outlined" style="font-size:18px;">notifications</span>
            @php $jumlahNotif = \App\Models\Notifikasi::where('user_id', session('user_id'))->where('dibaca', false)->count(); @endphp
            @if($jumlahNotif > 0)
            <span class="notif-badge">{{ $jumlahNotif > 9 ? '9+' : $jumlahNotif }}</span>
            @endif
          </button>
          <!-- Dropdown notif -->
          <div id="notif-dropdown" style="display:none;position:absolute;top:42px;right:0;width:300px;background:#fff;border-radius:14px;border:1px solid #f1f5f9;box-shadow:0 8px 32px rgba(0,0,0,.12);z-index:999;overflow:hidden;">
            <div style="padding:12px 14px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
              <span style="font-size:13px;font-weight:700;color:#0f172a;">Notifikasi</span>
              <a href="{{ route('notifikasi.read_all') }}" style="font-size:11px;color:#f97316;font-weight:600;text-decoration:none;">Tandai semua dibaca</a>
            </div>
            <div style="max-height:320px;overflow-y:auto;">
              @php $notifList = \App\Models\Notifikasi::where('user_id', session('user_id'))->latest()->take(8)->get(); @endphp
              @forelse($notifList as $notif)
              <a href="{{ $notif->url ?? '#' }}" style="display:block;padding:10px 14px;border-bottom:1px solid #f8fafc;text-decoration:none;background:{{ $notif->dibaca ? '#fff' : '#fff7ed' }};">
                <div style="font-size:12px;font-weight:700;color:#0f172a;">{{ $notif->judul }}</div>
                <div style="font-size:11px;color:#64748b;margin-top:2px;line-height:1.4;">{{ Str::limit($notif->pesan, 60) }}</div>
                <div style="font-size:10px;color:#94a3b8;margin-top:3px;">{{ $notif->created_at->diffForHumans() }}</div>
              </a>
              @empty
              <div style="padding:24px;text-align:center;font-size:12px;color:#94a3b8;">Tidak ada notifikasi</div>
              @endforelse
            </div>
          </div>
        </div>

        {{-- User avatar --}}
        <div style="display:flex;align-items:center;gap:8px;">
          <div style="width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-family:'Sora',sans-serif;font-weight:800;font-size:11px;color:#fff;
            @if(session('user_role') === 'admin') background:linear-gradient(135deg,#dc2626,#991b1b);
            @elseif(session('user_role') === 'pimpinan') background:linear-gradient(135deg,#7c3aed,#6d28d9);
            @elseif(session('user_role') === 'manajerial') background:linear-gradient(135deg,#0891b2,#0e7490);
            @else background:linear-gradient(135deg,#f97316,#ea580c); @endif">
            {{ strtoupper(substr(session('user_nama', 'U'), 0, 2)) }}
          </div>
          <div>
            <div style="font-size:12px;font-weight:700;color:#0f172a;">{{ session('user_nama', 'User') }}</div>
            <div style="font-size:10px;color:#94a3b8;text-transform:capitalize;">{{ str_replace('_', ' ', session('user_role', '')) }}</div>
          </div>
        </div>

      </div>
    </header>

    <main class="page-content fade-up">
      <!-- Flash messages -->
      @if(session('success'))
      <div style="padding:16px 20px 0;">
        <div class="alert-success">
          <span class="material-symbols-outlined fill-icon" style="font-size:16px;color:#16a34a;flex-shrink:0;">check_circle</span>
          {{ session('success') }}
        </div>
      </div>
      @endif
      @if(session('error'))
      <div style="padding:16px 20px 0;">
        <div class="alert-error">
          <span class="material-symbols-outlined fill-icon" style="font-size:16px;color:#ef4444;flex-shrink:0;">error</span>
          {{ session('error') }}
        </div>
      </div>
      @endif

      @yield('content')
    </main>
  </div>
</div>

<!-- Toast -->
<div class="toast" id="toast">
  <div style="background:#fff;border-radius:14px;padding:13px 16px;box-shadow:0 8px 32px rgba(0,0,0,.14);display:flex;align-items:center;gap:10px;border-left:3px solid #f97316;min-width:240px;">
    <span class="material-symbols-outlined fill-icon" id="toast-icon" style="color:#10b981;font-size:18px;">check_circle</span>
    <span id="toast-msg" style="font-size:13px;font-weight:600;color:#0f172a;"></span>
  </div>
</div>

<script>
function showToast(msg, type) {
  var toast = document.getElementById('toast');
  var icon  = document.getElementById('toast-icon');
  document.getElementById('toast-msg').textContent = msg;
  if (type==='delete')       { icon.textContent='delete';       icon.style.color='#ef4444'; }
  else if (type==='warning') { icon.textContent='warning';      icon.style.color='#f59e0b'; }
  else                       { icon.textContent='check_circle'; icon.style.color='#10b981'; }
  toast.classList.add('show');
  setTimeout(function(){ toast.classList.remove('show'); }, 3500);
}
function openModal(id) { var m=document.getElementById(id); if(m){ m.classList.add('open'); } }
function closeModal(id) { var m=document.getElementById(id); if(m){ m.classList.remove('open'); } }
document.addEventListener('click', function(e) {
  if (e.target.classList.contains('modal')) closeModal(e.target.id);
});
document.addEventListener('keydown', function(e) {
  if (e.key==='Escape') { var o=document.querySelector('.modal.open'); if(o) closeModal(o.id); }
});
function toggleNotif() {
  var d = document.getElementById('notif-dropdown');
  d.style.display = d.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', function(e) {
  var nb = document.getElementById('notif-dropdown');
  if (nb && !nb.contains(e.target) && !e.target.closest('[onclick="toggleNotif()"]')) {
    nb.style.display = 'none';
  }
});
@if(session('toast'))
showToast("{{ session('toast') }}", "{{ session('toast_type', 'success') }}");
@endif
</script>
@stack('scripts')
</body>
</html>