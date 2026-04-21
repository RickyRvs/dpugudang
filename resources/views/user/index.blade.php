@extends('layouts.app')
@section('title', 'Manajemen User')
@section('page_title', 'Manajemen User')
@section('page_sub', 'Kelola akun pengguna sistem gudang')

@section('content')
<div style="padding:20px;display:flex;flex-direction:column;gap:16px;">

  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
    <form method="GET" style="display:flex;gap:8px;flex:1;max-width:480px;">
      <div style="position:relative;flex:1;">
        <span class="material-symbols-outlined" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:16px;">search</span>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau email..."
          style="width:100%;padding:8px 12px 8px 34px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:12.5px;outline:none;background:#f8fafc;"/>
      </div>
      <select name="role" onchange="this.form.submit()" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:12.5px;outline:none;background:#f8fafc;cursor:pointer;">
        <option value="">Semua Role</option>
        <option value="pimpinan" {{ request('role')==='pimpinan'?'selected':'' }}>Pimpinan</option>
        <option value="manajerial" {{ request('role')==='manajerial'?'selected':'' }}>Manajerial</option>
        <option value="operator_gudang" {{ request('role')==='operator_gudang'?'selected':'' }}>Operator Gudang</option>
      </select>
      <button type="submit" class="btn-or" style="padding:8px 16px;font-size:12px;">Cari</button>
    </form>
    <button onclick="openModal('modal-tambah-user')" class="btn-or">
      <span class="material-symbols-outlined fill-icon" style="font-size:16px;">person_add</span> Tambah User
    </button>
  </div>

  <div class="card">
    <div style="overflow-x:auto;">
      <table class="tbl">
        <thead>
          <tr>
            <th>User</th>
            <th>Email</th>
            <th>Role</th>
            <th>Jabatan</th>
            <th>No HP</th>
            <th style="text-align:center;">Status</th>
            <th>Bergabung</th>
            <th style="text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $u)
          @php
            $roleStyle = $u->role === 'pimpinan' ? 'role-pimpinan' : ($u->role === 'manajerial' ? 'role-manajerial' : 'role-operator');
          @endphp
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:8px;">
                <div style="width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-family:'Sora',sans-serif;font-weight:800;font-size:11px;color:#fff;flex-shrink:0;" class="{{ $roleStyle }}">
                  {{ strtoupper(substr($u->nama, 0, 2)) }}
                </div>
                <div>
                  <div style="font-weight:700;font-size:12.5px;color:#0f172a;">{{ $u->nama }}</div>
                  @if($u->id == session('user_id'))<div style="font-size:10px;color:#f97316;font-weight:700;">← Anda</div>@endif
                </div>
              </div>
            </td>
            <td style="font-size:12px;color:#64748b;">{{ $u->email }}</td>
            <td>
              <span style="display:inline-block;padding:3px 10px;border-radius:999px;font-size:10px;font-weight:700;color:#fff;" class="{{ $roleStyle }}">
                {{ str_replace('_',' ', ucfirst($u->role)) }}
              </span>
            </td>
            <td style="font-size:12px;color:#64748b;">{{ $u->jabatan ?: '-' }}</td>
            <td style="font-size:12px;color:#64748b;">{{ $u->no_hp ?: '-' }}</td>
            <td style="text-align:center;">
              <span style="display:inline-block;padding:3px 9px;border-radius:999px;font-size:10px;font-weight:700;
                {{ $u->is_active ? 'background:#dcfce7;color:#166534;border:1px solid #bbf7d0;' : 'background:#f1f5f9;color:#94a3b8;border:1px solid #e2e8f0;' }}">
                {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
              </span>
            </td>
            <td style="font-size:11px;color:#94a3b8;">{{ $u->created_at->format('d/m/Y') }}</td>
            <td style="text-align:right;">
              <div style="display:flex;gap:6px;justify-content:flex-end;">
                <button onclick="openEditUser({{ $u->id }}, '{{ addslashes($u->nama) }}', '{{ $u->email }}', '{{ $u->role }}', '{{ addslashes($u->jabatan) }}', '{{ $u->no_hp }}', {{ $u->is_active ? 'true' : 'false' }})"
                  style="display:inline-flex;align-items:center;gap:3px;padding:5px 10px;border-radius:8px;background:#eff6ff;border:1px solid #bfdbfe;font-size:11px;font-weight:600;color:#1d4ed8;cursor:pointer;">
                  <span class="material-symbols-outlined" style="font-size:13px;">edit</span> Edit
                </button>
                @if($u->id != session('user_id') && $u->is_active)
                <form method="POST" action="{{ route('user.destroy', $u->id) }}" onsubmit="return confirm('Nonaktifkan user ini?')">
                  @csrf @method('DELETE')
                  <button type="submit" style="display:inline-flex;align-items:center;gap:3px;padding:5px 10px;border-radius:8px;background:#fef2f2;border:1px solid #fecaca;font-size:11px;font-weight:600;color:#991b1b;cursor:pointer;">
                    <span class="material-symbols-outlined" style="font-size:13px;">block</span>
                  </button>
                </form>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="8">
            <div class="empty-state">
              <div class="icon"><span class="material-symbols-outlined" style="font-size:28px;color:#94a3b8;">manage_accounts</span></div>
              <div style="font-weight:700;color:#0f172a;margin-bottom:6px;">Tidak ada user</div>
            </div>
          </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($users->hasPages())
    <div style="padding:14px 18px;border-top:1px solid #f1f5f9;">{{ $users->withQueryString()->links() }}</div>
    @endif
  </div>
</div>

{{-- Modal Tambah User --}}
<div class="modal" id="modal-tambah-user">
  <div>
    <div class="card" style="padding:24px;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
        <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:16px;color:#0f172a;">Tambah User Baru</div>
        <button onclick="closeModal('modal-tambah-user')" style="border:none;background:none;cursor:pointer;color:#94a3b8;font-size:20px;">✕</button>
      </div>
      <form method="POST" action="{{ route('user.store') }}">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
          <div style="grid-column:1/-1;">
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Nama Lengkap *</label>
            <input type="text" name="nama" class="field" placeholder="Nama lengkap" required/>
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Email *</label>
            <input type="email" name="email" class="field" placeholder="email@domain.com" required/>
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Role *</label>
            <select name="role" class="field" required>
              <option value="">-- Pilih Role --</option>
              <option value="pimpinan">Pimpinan</option>
              <option value="manajerial">Manajerial</option>
              <option value="operator_gudang">Operator Gudang</option>
            </select>
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Password *</label>
            <input type="password" name="password" class="field" placeholder="Min. 6 karakter" required minlength="6"/>
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Jabatan</label>
            <input type="text" name="jabatan" class="field" placeholder="Jabatan (opsional)"/>
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">No HP</label>
            <input type="text" name="no_hp" class="field" placeholder="08xx"/>
          </div>
        </div>
        <div style="display:flex;gap:8px;margin-top:18px;">
          <button type="button" onclick="closeModal('modal-tambah-user')" class="btn-ghost" style="flex:1;justify-content:center;">Batal</button>
          <button type="submit" class="btn-or" style="flex:1;justify-content:center;">
            <span class="material-symbols-outlined fill-icon" style="font-size:16px;">person_add</span> Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Edit User --}}
<div class="modal" id="modal-edit-user">
  <div>
    <div class="card" style="padding:24px;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
        <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:16px;color:#0f172a;">Edit User</div>
        <button onclick="closeModal('modal-edit-user')" style="border:none;background:none;cursor:pointer;color:#94a3b8;font-size:20px;">✕</button>
      </div>
      <form method="POST" id="form-edit-user">
        @csrf @method('PUT')
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
          <div style="grid-column:1/-1;">
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Nama Lengkap *</label>
            <input type="text" name="nama" id="eu-nama" class="field" required/>
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Email *</label>
            <input type="email" name="email" id="eu-email" class="field" required/>
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Role *</label>
            <select name="role" id="eu-role" class="field" required>
              <option value="pimpinan">Pimpinan</option>
              <option value="manajerial">Manajerial</option>
              <option value="operator_gudang">Operator Gudang</option>
            </select>
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Jabatan</label>
            <input type="text" name="jabatan" id="eu-jabatan" class="field"/>
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">No HP</label>
            <input type="text" name="no_hp" id="eu-nohp" class="field"/>
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Password Baru</label>
            <input type="password" name="password" class="field" placeholder="Kosongkan jika tidak diubah" minlength="6"/>
          </div>
          <div style="grid-column:1/-1;">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
              <input type="checkbox" name="is_active" id="eu-active" value="1" style="width:16px;height:16px;accent-color:#f97316;"/>
              <span style="font-size:13px;font-weight:600;color:#0f172a;">User Aktif</span>
            </label>
          </div>
        </div>
        <div style="display:flex;gap:8px;margin-top:18px;">
          <button type="button" onclick="closeModal('modal-edit-user')" class="btn-ghost" style="flex:1;justify-content:center;">Batal</button>
          <button type="submit" class="btn-or" style="flex:1;justify-content:center;">
            <span class="material-symbols-outlined fill-icon" style="font-size:16px;">save</span> Update
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
function openEditUser(id, nama, email, role, jabatan, nohp, isActive) {
  document.getElementById('eu-nama').value = nama;
  document.getElementById('eu-email').value = email;
  document.getElementById('eu-role').value = role;
  document.getElementById('eu-jabatan').value = jabatan;
  document.getElementById('eu-nohp').value = nohp;
  document.getElementById('eu-active').checked = isActive;
  document.getElementById('form-edit-user').action = '/user/' + id;
  openModal('modal-edit-user');
}
</script>
@endpush
@endsection