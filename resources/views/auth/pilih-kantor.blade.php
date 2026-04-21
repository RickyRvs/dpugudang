<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Pilih Kantor | Sistem Gudang DBS</title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<style>
*,*::before,*::after{font-family:'DM Sans',sans-serif;box-sizing:border-box;margin:0;padding:0;}
h1,h2,h3{font-family:'Sora',sans-serif;}
.material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;font-size:20px;vertical-align:middle;line-height:1;}
.fill-icon{font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24;}
html,body{min-height:100vh;background:#f8f7f3;display:flex;align-items:center;justify-content:center;padding:24px;}
@keyframes fadeUp{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}
.fade-up{animation:fadeUp .35s ease forwards;}

.wrap{width:100%;max-width:520px;}
.header-card{background:linear-gradient(135deg,#0f172a,#1e293b);border-radius:20px;padding:28px 28px 24px;margin-bottom:16px;position:relative;overflow:hidden;}
.header-card::before{content:'';position:absolute;top:-60px;right:-60px;width:200px;height:200px;background:radial-gradient(circle,rgba(249,115,22,.18) 0%,transparent 70%);pointer-events:none;}

.kantor-card{background:#fff;border-radius:16px;border:2px solid #f1f5f9;padding:0;cursor:pointer;transition:all .2s;text-align:left;width:100%;display:block;text-decoration:none;overflow:hidden;margin-bottom:10px;}
.kantor-card:hover{border-color:#f97316;box-shadow:0 4px 20px rgba(249,115,22,.15);transform:translateY(-2px);}
.kantor-card:active{transform:translateY(0);}
.kantor-inner{display:flex;align-items:center;gap:16px;padding:16px 18px;}
.kantor-icon{width:48px;height:48px;border-radius:13px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-family:'Sora',sans-serif;font-weight:800;font-size:13px;color:#fff;}
.kantor-badge{display:inline-flex;align-items:center;gap:4px;padding:2px 10px;border-radius:999px;font-size:10px;font-weight:700;background:#fff7ed;color:#ea580c;border:1px solid #fed7aa;margin-top:3px;}

.btn-logout{background:none;border:1px solid rgba(255,255,255,.15);color:#94a3b8;font-size:12px;font-weight:600;border-radius:8px;padding:6px 14px;cursor:pointer;transition:all .15s;display:inline-flex;align-items:center;gap:6px;}
.btn-logout:hover{background:rgba(255,255,255,.08);color:#fff;}
</style>
</head>
<body>
<div class="wrap fade-up">

  <!-- Header -->
  <div class="header-card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
      <div style="display:flex;align-items:center;gap:10px;">
        <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#f97316,#c2410c);display:flex;align-items:center;justify-content:center;font-family:'Sora',sans-serif;font-weight:800;font-size:13px;color:#fff;">DBS</div>
        <div>
          <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:14px;color:#fff;">Dian Bangun Sejahtera</div>
          <div style="font-size:10px;color:rgba(249,115,22,.7);font-weight:600;text-transform:uppercase;letter-spacing:.08em;">Sistem Gudang v1</div>
        </div>
      </div>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-logout">
          <span class="material-symbols-outlined" style="font-size:15px;">logout</span> Keluar
        </button>
      </form>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
      <div style="width:42px;height:42px;border-radius:11px;background:linear-gradient(135deg,
        @if(session('user_role')==='pimpinan') #7c3aed,#6d28d9
        @elseif(session('user_role')==='manajerial') #0891b2,#0e7490
        @else #f97316,#ea580c @endif
      );display:flex;align-items:center;justify-content:center;font-family:'Sora',sans-serif;font-weight:800;font-size:14px;color:#fff;flex-shrink:0;">
        {{ strtoupper(substr(session('user_nama','U'),0,2)) }}
      </div>
      <div>
        <div style="font-size:13px;font-weight:700;color:#fff;">Halo, {{ session('user_nama') }}!</div>
        <div style="font-size:11px;color:#64748b;text-transform:capitalize;">{{ str_replace('_',' ',session('user_role')) }} · Pilih kantor untuk melanjutkan</div>
      </div>
    </div>
  </div>

  <!-- Label -->
  <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:10px;padding:0 2px;">
    Kantor yang dapat diakses ({{ count($kantors) }})
  </div>

  <!-- Daftar Kantor -->
  @php
    $colors = ['linear-gradient(135deg,#f97316,#ea580c)', 'linear-gradient(135deg,#0891b2,#0e7490)', 'linear-gradient(135deg,#7c3aed,#6d28d9)', 'linear-gradient(135deg,#16a34a,#15803d)'];
    $icons  = ['warehouse', 'business', 'location_city', 'corporate_fare'];
    $types  = ['Kantor Pusat', 'Kantor Proyek', 'Kantor Proyek', 'Kantor Proyek'];
  @endphp

  @foreach($kantors as $i => $kantor)
  <form method="POST" action="{{ route('pilih.kantor.post') }}" style="margin-bottom:10px;">
    @csrf
    <input type="hidden" name="kantor_id" value="{{ $kantor['id'] }}"/>
    <button type="submit" class="kantor-card" style="width:100%;">
      <div class="kantor-inner">
        <div class="kantor-icon" style="background:{{ $colors[$i % 4] }};">
          <span class="material-symbols-outlined fill-icon" style="font-size:22px;color:#fff;">{{ $icons[$i % 4] }}</span>
        </div>
        <div style="flex:1;min-width:0;">
          <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:14px;color:#0f172a;line-height:1.3;">{{ $kantor['nama'] }}</div>
          <div style="font-size:12px;color:#64748b;margin-top:2px;display:flex;align-items:center;gap:4px;">
            <span class="material-symbols-outlined fill-icon" style="font-size:13px;color:#94a3b8;">location_on</span>
            {{ $kantor['kota'] }}
          </div>
          @if(isset($kantor['alamat']))
          <div style="font-size:11px;color:#94a3b8;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:280px;">{{ $kantor['alamat'] }}</div>
          @endif
        </div>
        <div style="flex-shrink:0;">
          <span class="material-symbols-outlined" style="color:#cbd5e1;font-size:18px;">chevron_right</span>
        </div>
      </div>
    </button>
  </form>
  @endforeach

  <p style="text-align:center;font-size:11px;color:#cbd5e1;margin-top:20px;">PT. Dian Bangun Sejahtera &copy; {{ date('Y') }} · Sistem Gudang v1.0</p>
</div>
</body>
</html>