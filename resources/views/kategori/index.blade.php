@extends('layouts.app')
@section('title', 'Kategori Barang')
@section('page_title', 'Kategori Barang')
@section('page_sub', 'Kelola kategori pengelompokan barang gudang')

@section('content')
<div style="padding:20px;display:flex;flex-direction:column;gap:16px;">

  <div style="display:flex;align-items:center;justify-content:space-between;">
    <div style="font-size:13px;color:#64748b;">{{ $kategori->total() }} kategori terdaftar</div>
    @if(in_array(session('user_role'), ['pimpinan','manajerial']))
    <button onclick="openModal('modal-tambah')" class="btn-or">
      <span class="material-symbols-outlined fill-icon" style="font-size:16px;">add</span> Tambah Kategori
    </button>
    @endif
  </div>

  <div class="card">
    <div style="overflow-x:auto;">
      <table class="tbl">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Nama Kategori</th>
            <th>Deskripsi</th>
            <th style="text-align:center;">Jumlah Barang</th>
            <th style="text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($kategori as $k)
          <tr>
            <td style="font-family:'Sora',sans-serif;font-size:11px;font-weight:700;color:#64748b;">{{ $k->kode }}</td>
            <td style="font-weight:700;font-size:13px;color:#0f172a;">{{ $k->nama }}</td>
            <td style="font-size:12px;color:#64748b;">{{ $k->deskripsi ?: '-' }}</td>
            <td style="text-align:center;">
              <span style="display:inline-block;padding:3px 12px;border-radius:999px;background:#eff6ff;color:#1d4ed8;font-size:11px;font-weight:700;border:1px solid #bfdbfe;">
                {{ $k->barang_count }} barang
              </span>
            </td>
            <td style="text-align:right;">
              @if(in_array(session('user_role'), ['pimpinan','manajerial']))
              <div style="display:flex;gap:6px;justify-content:flex-end;">
                <button onclick="openEditModal({{ $k->id }}, '{{ addslashes($k->nama) }}', '{{ addslashes($k->kode) }}', '{{ addslashes($k->deskripsi) }}')"
                  style="display:inline-flex;align-items:center;gap:3px;padding:5px 10px;border-radius:8px;background:#eff6ff;border:1px solid #bfdbfe;font-size:11px;font-weight:600;color:#1d4ed8;cursor:pointer;">
                  <span class="material-symbols-outlined" style="font-size:13px;">edit</span> Edit
                </button>
                @if($k->barang_count == 0)
                <form method="POST" action="{{ route('kategori.destroy', $k->id) }}" onsubmit="return confirm('Hapus kategori ini?')">
                  @csrf @method('DELETE')
                  <button type="submit" style="display:inline-flex;align-items:center;gap:3px;padding:5px 10px;border-radius:8px;background:#fef2f2;border:1px solid #fecaca;font-size:11px;font-weight:600;color:#991b1b;cursor:pointer;">
                    <span class="material-symbols-outlined" style="font-size:13px;">delete</span> Hapus
                  </button>
                </form>
                @endif
              </div>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="5">
            <div class="empty-state">
              <div class="icon"><span class="material-symbols-outlined" style="font-size:28px;color:#94a3b8;">folder</span></div>
              <div style="font-weight:700;color:#0f172a;margin-bottom:6px;">Belum ada kategori</div>
            </div>
          </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($kategori->hasPages())
    <div style="padding:14px 18px;border-top:1px solid #f1f5f9;">{{ $kategori->withQueryString()->links() }}</div>
    @endif
  </div>
</div>

{{-- Modal Tambah --}}
@if(in_array(session('user_role'), ['pimpinan','manajerial']))
<div class="modal" id="modal-tambah">
  <div>
    <div class="card" style="padding:24px;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
        <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:16px;color:#0f172a;">Tambah Kategori</div>
        <button onclick="closeModal('modal-tambah')" style="border:none;background:none;cursor:pointer;color:#94a3b8;font-size:20px;">✕</button>
      </div>
      <form method="POST" action="{{ route('kategori.store') }}">
        @csrf
        <div style="display:flex;flex-direction:column;gap:14px;">
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Kode Kategori *</label>
            <input type="text" name="kode" class="field" placeholder="Contoh: KAT-001" required/>
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Nama Kategori *</label>
            <input type="text" name="nama" class="field" placeholder="Nama kategori" required/>
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Deskripsi</label>
            <textarea name="deskripsi" class="field" placeholder="Deskripsi kategori (opsional)..."></textarea>
          </div>
        </div>
        <div style="display:flex;gap:8px;margin-top:18px;">
          <button type="button" onclick="closeModal('modal-tambah')" class="btn-ghost" style="flex:1;justify-content:center;">Batal</button>
          <button type="submit" class="btn-or" style="flex:1;justify-content:center;">
            <span class="material-symbols-outlined fill-icon" style="font-size:16px;">save</span> Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Edit --}}
<div class="modal" id="modal-edit">
  <div>
    <div class="card" style="padding:24px;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
        <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:16px;color:#0f172a;">Edit Kategori</div>
        <button onclick="closeModal('modal-edit')" style="border:none;background:none;cursor:pointer;color:#94a3b8;font-size:20px;">✕</button>
      </div>
      <form method="POST" id="form-edit-kategori">
        @csrf @method('PUT')
        <div style="display:flex;flex-direction:column;gap:14px;">
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Kode Kategori *</label>
            <input type="text" name="kode" id="edit-kode" class="field" required/>
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Nama Kategori *</label>
            <input type="text" name="nama" id="edit-nama" class="field" required/>
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Deskripsi</label>
            <textarea name="deskripsi" id="edit-deskripsi" class="field"></textarea>
          </div>
        </div>
        <div style="display:flex;gap:8px;margin-top:18px;">
          <button type="button" onclick="closeModal('modal-edit')" class="btn-ghost" style="flex:1;justify-content:center;">Batal</button>
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
function openEditModal(id, nama, kode, deskripsi) {
  document.getElementById('edit-nama').value = nama;
  document.getElementById('edit-kode').value = kode;
  document.getElementById('edit-deskripsi').value = deskripsi;
  document.getElementById('form-edit-kategori').action = '/kategori/' + id;
  openModal('modal-edit');
}
</script>
@endpush
@endif
@endsection