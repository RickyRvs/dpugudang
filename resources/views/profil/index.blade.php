@extends('layouts.app')
@section('title', 'Profil Saya')
@section('page_title', 'Profil Saya')
@section('page_sub', 'Kelola informasi akun dan tanda tangan digital')

@section('content')
<div style="padding:20px;">
  <div style="max-width:680px;margin:0 auto;display:flex;flex-direction:column;gap:16px;">

    {{-- Profil card --}}
    <div class="card" style="padding:24px;">
      <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid #f1f5f9;">
        @php
          $roleStyle = $user->role === 'pimpinan' ? 'linear-gradient(135deg,#7c3aed,#6d28d9)' : ($user->role === 'manajerial' ? 'linear-gradient(135deg,#0891b2,#0e7490)' : 'linear-gradient(135deg,#f97316,#ea580c)');
        @endphp
        <div style="width:64px;height:64px;border-radius:18px;background:{{ $roleStyle }};display:flex;align-items:center;justify-content:center;font-family:'Sora',sans-serif;font-weight:800;font-size:22px;color:#fff;flex-shrink:0;box-shadow:0 8px 24px rgba(0,0,0,.15);">
          {{ strtoupper(substr($user->nama, 0, 2)) }}
        </div>
        <div>
          <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:20px;color:#0f172a;">{{ $user->nama }}</div>
          <div style="font-size:12px;color:#64748b;">{{ $user->email }}</div>
          <span style="display:inline-block;margin-top:6px;padding:3px 12px;border-radius:999px;font-size:11px;font-weight:700;color:#fff;background:{{ $roleStyle }};">
            {{ str_replace('_', ' ', ucfirst($user->role)) }}
          </span>
        </div>
      </div>

      <form method="POST" action="{{ route('profil.update') }}" enctype="multipart/form-data">
        @csrf

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
          <div style="grid-column:1/-1;">
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Nama Lengkap *</label>
            <input type="text" name="nama" class="field" value="{{ old('nama', $user->nama) }}" required/>
            @error('nama')<div style="font-size:11px;color:#ef4444;margin-top:4px;">{{ $message }}</div>@enderror
          </div>

          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Email</label>
            <div style="padding:9px 12px;background:#f1f5f9;border-radius:10px;font-size:13px;color:#64748b;">{{ $user->email }}</div>
          </div>

          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Jabatan</label>
            <input type="text" name="jabatan" class="field" value="{{ old('jabatan', $user->jabatan) }}" placeholder="Jabatan Anda"/>
          </div>

          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">No HP</label>
            <input type="text" name="no_hp" class="field" value="{{ old('no_hp', $user->no_hp) }}" placeholder="08xx"/>
          </div>
        </div>

        {{-- TTD --}}
        @if($user->role === 'pimpinan')
        <div style="margin-top:20px;padding-top:20px;border-top:1px solid #f1f5f9;">
          <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:13px;color:#0f172a;margin-bottom:14px;">Tanda Tangan Digital</div>
          @if($user->ttd_path)
          <div style="margin-bottom:10px;">
            <div style="font-size:11px;color:#94a3b8;margin-bottom:6px;">TTD Saat Ini:</div>
            <img src="{{ asset('storage/'.$user->ttd_path) }}" alt="TTD" style="max-height:80px;border:1.5px solid #e2e8f0;border-radius:10px;padding:8px;background:#fff;"/>
          </div>
          @endif
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Upload TTD Baru</label>
            <input type="file" name="ttd" accept="image/*" class="field" style="padding:7px;"/>
            <div style="font-size:11px;color:#94a3b8;margin-top:4px;">Format: JPG/PNG, transparent background lebih baik</div>
          </div>
        </div>
        @endif

        {{-- Ganti Password --}}
        <div style="margin-top:20px;padding-top:20px;border-top:1px solid #f1f5f9;">
          <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:13px;color:#0f172a;margin-bottom:14px;">Ganti Password</div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
            <div>
              <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Password Baru</label>
              <input type="password" name="password" class="field" placeholder="Kosongkan jika tidak diubah" minlength="6"/>
              @error('password')<div style="font-size:11px;color:#ef4444;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
            <div>
              <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Konfirmasi Password</label>
              <input type="password" name="password_confirmation" class="field" placeholder="Ulangi password baru"/>
              @error('password_confirmation')<div style="font-size:11px;color:#ef4444;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>

        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px;">
          <button type="submit" class="btn-or">
            <span class="material-symbols-outlined fill-icon" style="font-size:16px;">save</span> Simpan Perubahan
          </button>
        </div>
      </form>
    </div>

    {{-- Info akun --}}
    <div class="card" style="padding:16px 20px;">
      <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.07em;margin-bottom:12px;">Informasi Akun</div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
        @foreach([
          ['Role', str_replace('_',' ', ucfirst($user->role))],
          ['Status', $user->is_active ? 'Aktif' : 'Nonaktif'],
          ['Bergabung', $user->created_at->format('d M Y')],
          ['Terakhir Update', $user->updated_at->format('d M Y H:i')],
        ] as [$k,$v])
        <div style="background:#f8fafc;border-radius:10px;padding:10px 12px;">
          <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.07em;margin-bottom:3px;">{{ $k }}</div>
          <div style="font-size:13px;font-weight:700;color:#0f172a;">{{ $v }}</div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endsection