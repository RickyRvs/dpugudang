<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Login | Sistem Gudang DBS</title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<style>
*,*::before,*::after{font-family:'DM Sans',sans-serif;box-sizing:border-box;margin:0;padding:0;}
h1,h2,h3,.sora{font-family:'Sora',sans-serif;}
.material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;font-size:20px;vertical-align:middle;line-height:1;}
.fill-icon{font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24;}
html,body{min-height:100vh;background:#f1f0eb;}
.shell{display:flex;min-height:100vh;}
.panel-left{width:42%;flex-shrink:0;background:linear-gradient(160deg,#0f172a 0%,#1a2744 50%,#0f172a 100%);display:flex;flex-direction:column;justify-content:space-between;padding:44px 48px;position:relative;overflow:hidden;min-height:100vh;}
.panel-left::before{content:'';position:absolute;top:-100px;left:-100px;width:500px;height:500px;background:radial-gradient(circle,rgba(249,115,22,.12) 0%,transparent 70%);pointer-events:none;}
.dot-grid{position:absolute;inset:0;opacity:.025;background-image:radial-gradient(#f97316 1px,transparent 1px);background-size:28px 28px;pointer-events:none;}
.top-bar{position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,#f97316 40%,#ea580c 60%,transparent);}
.panel-right{flex:1;display:flex;flex-direction:column;justify-content:center;align-items:center;padding:40px 28px;position:relative;min-height:100vh;background:#f8f7f3;}
.form-wrap{width:100%;max-width:400px;position:relative;z-index:1;}
.login-card{background:#fff;border-radius:22px;padding:28px 26px;box-shadow:0 1px 3px rgba(0,0,0,.04),0 8px 24px rgba(0,0,0,.07),0 24px 48px rgba(0,0,0,.05);border:1px solid rgba(0,0,0,.06);}
.field-label{display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;}
.field{width:100%;background:#f8fafc;border:1.5px solid #e8eaf0;border-radius:11px;padding:11px 14px;font-size:13.5px;transition:all .15s;outline:none;color:#0f172a;font-family:'DM Sans',sans-serif;}
.field:focus{border-color:#f97316;background:#fff;box-shadow:0 0 0 3px rgba(249,115,22,.1);}
.btn-or{width:100%;background:linear-gradient(135deg,#f97316,#ea580c);color:#fff;font-weight:700;border-radius:12px;padding:13px 22px;font-size:14px;transition:all .18s;box-shadow:0 4px 16px rgba(249,115,22,.3);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;font-family:'DM Sans',sans-serif;}
.btn-or:hover{transform:translateY(-1px);box-shadow:0 8px 24px rgba(249,115,22,.4);}
.alert-error{background:#fef2f2;border:1px solid #fecaca;border-radius:11px;padding:11px 14px;margin-bottom:16px;font-size:12.5px;color:#991b1b;font-weight:600;display:flex;align-items:center;gap:8px;}
.stat-item .num{font-family:'Sora',sans-serif;font-size:30px;font-weight:800;color:#fff;line-height:1;margin-bottom:4px;}
.stat-item .lbl{font-size:10px;color:#475569;font-weight:700;text-transform:uppercase;letter-spacing:.08em;}
.loc-pill{padding:5px 14px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:999px;color:#94a3b8;font-size:11.5px;font-weight:600;}
@keyframes fadeUp{from{opacity:0;transform:translateY(12px);}to{opacity:1;transform:translateY(0);}}
.fade-up{animation:fadeUp .3s ease forwards;}
@media(max-width:900px){.panel-left{display:none;}.panel-right{justify-content:flex-start;padding:32px 20px 40px;}.form-wrap{max-width:100%;}}
</style>
</head>
<body>
<div class="shell">

  <section class="panel-left">
    <div class="dot-grid"></div>
    <div class="top-bar"></div>
    <div style="position:relative;z-index:1;">
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:52px;">
        <div style="width:46px;height:46px;border-radius:13px;background:linear-gradient(135deg,#f97316,#c2410c);display:flex;align-items:center;justify-content:center;font-family:'Sora',sans-serif;font-weight:800;font-size:14px;color:#fff;box-shadow:0 6px 24px rgba(249,115,22,.45);">DBS</div>
        <div>
          <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:15px;color:#fff;">Dian Bangun Sejahtera</div>
          <div style="font-size:10px;font-weight:700;color:rgba(249,115,22,.75);text-transform:uppercase;letter-spacing:.12em;">Sistem Gudang v1</div>
        </div>
      </div>
      <h1 style="font-weight:800;font-size:44px;color:#fff;line-height:1.1;margin-bottom:18px;letter-spacing:-.02em;">
        Kelola Gudang.<br/><span style="color:#f97316;">Efisien.</span><br/>Terpusat.
      </h1>
      <p style="color:#94a3b8;font-size:14px;line-height:1.75;max-width:320px;">Platform manajemen pergudangan terpadu untuk 4 kantor — dari permintaan hingga eksekusi stok, semua tercatat dan teraudit.</p>
      <div style="margin-top:32px;display:flex;flex-direction:column;gap:10px;">
        @foreach([['location_on','Kantor Pusat Pekanbaru & 3 Kantor Proyek'],['assignment_turned_in','Permintaan barang + approval pimpinan'],['inventory_2','Manajemen stok real-time per kantor'],['bar_chart','Laporan & audit log lengkap']] as [$ic,$tx])
        <div style="display:flex;align-items:center;gap:10px;">
          <div style="width:30px;height:30px;border-radius:8px;background:rgba(249,115,22,.15);border:1px solid rgba(249,115,22,.25);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <span class="material-symbols-outlined fill-icon" style="color:#f97316;font-size:15px;">{{ $ic }}</span>
          </div>
          <span style="font-size:13px;color:#94a3b8;font-weight:500;">{{ $tx }}</span>
        </div>
        @endforeach
      </div>
    </div>
    <div style="position:relative;z-index:1;">
      <div style="height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,.08),transparent);margin-bottom:24px;"></div>
      <div style="display:flex;gap:28px;margin-bottom:24px;">
        <div class="stat-item"><div class="num">4</div><div class="lbl">Kantor</div></div>
        <div class="stat-item"><div class="num">3</div><div class="lbl">Role Akses</div></div>
        <div class="stat-item"><div class="num">∞</div><div class="lbl">Riwayat</div></div>
      </div>
      <div style="display:flex;flex-wrap:wrap;gap:6px;">
        <span class="loc-pill">📍 Pekanbaru</span>
        <span class="loc-pill">📍 Tebet</span>
        <span class="loc-pill">📍 Surabaya</span>
        <span class="loc-pill">📍 Bekasi</span>
      </div>
    </div>
  </section>

  <section class="panel-right">
    <div class="form-wrap fade-up">
      <div class="login-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
          <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#f97316,#c2410c);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <span class="material-symbols-outlined fill-icon" style="color:#fff;font-size:18px;">warehouse</span>
          </div>
          <div>
            <h2 style="font-weight:800;font-size:18px;color:#0f172a;line-height:1.2;">Selamat Datang!</h2>
            <p style="font-size:12px;color:#94a3b8;margin-top:1px;">Masuk ke Sistem Gudang DBS.</p>
          </div>
        </div>

        @if($errors->any())
        <div class="alert-error">
          <span class="material-symbols-outlined fill-icon" style="font-size:16px;color:#ef4444;flex-shrink:0;">error</span>
          {{ $errors->first() }}
        </div>
        @endif
        @if(session('error'))
        <div class="alert-error">
          <span class="material-symbols-outlined fill-icon" style="font-size:16px;color:#ef4444;flex-shrink:0;">error</span>
          {{ session('error') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" style="display:flex;flex-direction:column;gap:14px;">
          @csrf
          <div>
            <label class="field-label">Email</label>
            <div style="position:relative;">
              <span class="material-symbols-outlined" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#cbd5e1;font-size:17px;">mail</span>
              <input type="email" name="email" placeholder="email@dbs.co.id" class="field" style="padding-left:40px;" value="{{ old('email') }}" required autofocus/>
            </div>
          </div>
          <div>
            <label class="field-label">Kata Sandi</label>
            <div style="position:relative;">
              <span class="material-symbols-outlined" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#cbd5e1;font-size:17px;">lock</span>
              <input id="pass-field" type="password" name="password" placeholder="Kata sandi" class="field" style="padding-left:40px;padding-right:42px;" required/>
              <button type="button" onclick="togglePass()" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);border:none;background:none;cursor:pointer;color:#cbd5e1;padding:4px;display:flex;align-items:center;">
                <span class="material-symbols-outlined" id="pass-eye" style="font-size:17px;">visibility</span>
              </button>
            </div>
          </div>

          <div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1px solid #bbf7d0;border-radius:10px;padding:10px 13px;font-size:11.5px;color:#166534;display:flex;flex-direction:column;gap:3px;">
            <div style="font-weight:700;margin-bottom:2px;">🔑 Demo Akun:</div>
            <div><b>Pimpinan (all):</b> pimpinan@dbs.co.id / pimpinan123</div>
            <div><b>Manajer Pusat:</b> manajer.pku@dbs.co.id / manajerial123</div>
            <div><b>Manajer Proyek:</b> manajerial@dbs.co.id / manajerial123</div>
            <div><b>Operator:</b> operator@dbs.co.id / operator123</div>
          </div>

          <button type="submit" class="btn-or">
            <span class="material-symbols-outlined fill-icon" style="font-size:18px;">login</span>
            Masuk ke Sistem
          </button>
        </form>
      </div>
      <p style="text-align:center;font-size:11px;color:#cbd5e1;margin-top:20px;">PT. Dian Bangun Sejahtera &copy; {{ date('Y') }} · Sistem Gudang v1.0</p>
    </div>
  </section>
</div>
<script>
function togglePass() {
  var f = document.getElementById('pass-field');
  var e = document.getElementById('pass-eye');
  f.type = f.type === 'password' ? 'text' : 'password';
  e.textContent = f.type === 'password' ? 'visibility' : 'visibility_off';
}
</script>
</body>
</html>