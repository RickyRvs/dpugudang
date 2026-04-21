@extends('layouts.app')
@section('title', 'Master Barang')
@section('page_title', 'Master Barang')
@section('page_sub', 'Kelola data barang gudang PT. Dian Pilar Utama')

@section('content')
<div style="padding:20px;display:flex;flex-direction:column;gap:16px;">

  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
    <div style="font-size:13px;color:#64748b;">{{ $barang->total() }} barang terdaftar</div>
    @if(in_array(session('user_role'), ['pimpinan','manajerial']))
    <a href="{{ route('barang.create') }}" class="btn-or">
      <span class="material-symbols-outlined fill-icon" style="font-size:16px;">add</span> Tambah Barang
    </a>
    @endif
  </div>

  <div class="card">
    <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;">
      <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;">
        <div style="position:relative;flex:1;min-width:200px;max-width:300px;">
          <span class="material-symbols-outlined" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:16px;">search</span>
          <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, kode barang..."
            style="width:100%;padding:8px 12px 8px 34px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:12.5px;outline:none;background:#f8fafc;"/>
        </div>
        <select name="kategori" onchange="this.form.submit()" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:12.5px;outline:none;background:#f8fafc;cursor:pointer;">
          <option value="">Semua Kategori</option>
          @foreach($kategoriList as $k)
          <option value="{{ $k->id }}" {{ request('kategori')==$k->id?'selected':'' }}>{{ $k->nama }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn-or" style="padding:8px 16px;font-size:12px;">Cari</button>
      </form>
    </div>

    <div style="overflow-x:auto;">
      <table class="tbl">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th style="text-align:center;">Stok</th>
            <th style="text-align:center;">Min. Stok</th>
            <th>Satuan</th>
            <th>Harga Satuan</th>
            <th>Lokasi Rak</th>
            <th>Status</th>
            <th style="text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($barang as $b)
          @php
            $kondisi = $b->stok_tersedia == 0 ? 'habis' : ($b->stok_tersedia <= $b->stok_minimum ? 'menipis' : 'normal');
            $kondisiColor = $kondisi === 'habis' ? '#dc2626' : ($kondisi === 'menipis' ? '#d97706' : '#16a34a');
          @endphp
          <tr>
            <td style="font-family:'Sora',sans-serif;font-size:11px;font-weight:700;color:#64748b;">{{ $b->kode }}</td>
            <td>
              <div style="font-weight:700;font-size:12.5px;color:#0f172a;">{{ $b->nama }}</div>
              @if($b->deskripsi)<div style="font-size:10px;color:#94a3b8;margin-top:1px;">{{ Str::limit($b->deskripsi, 50) }}</div>@endif
            </td>
            <td style="font-size:12px;">{{ $b->kategori->nama ?? '-' }}</td>
            <td style="text-align:center;">
              <span style="font-family:'Sora',sans-serif;font-size:16px;font-weight:800;color:{{ $kondisiColor }};">{{ $b->stok_tersedia }}</span>
            </td>
            <td style="text-align:center;font-size:12px;color:#94a3b8;">{{ $b->stok_minimum }}</td>
            <td style="font-size:12px;">{{ $b->satuan }}</td>
            <td style="font-size:12px;">Rp {{ number_format($b->harga_satuan, 0, ',', '.') }}</td>
            <td style="font-size:12px;color:#64748b;">{{ $b->lokasi_rak ?: '-' }}</td>
            <td>
              <span style="display:inline-block;padding:3px 9px;border-radius:999px;font-size:10px;font-weight:700;
                {{ $b->is_active ? 'background:#dcfce7;color:#166534;border:1px solid #bbf7d0;' : 'background:#f1f5f9;color:#94a3b8;border:1px solid #e2e8f0;' }}">
                {{ $b->is_active ? 'Aktif' : 'Nonaktif' }}
              </span>
            </td>
            <td style="text-align:right;">
              <div style="display:flex;gap:6px;justify-content:flex-end;">
                <a href="{{ route('barang.show', $b->id) }}" style="display:inline-flex;align-items:center;gap:3px;padding:5px 10px;border-radius:8px;background:#f8fafc;border:1px solid #e2e8f0;font-size:11px;font-weight:600;color:#475569;text-decoration:none;">
                  <span class="material-symbols-outlined" style="font-size:13px;">visibility</span>
                </a>
                @if(in_array(session('user_role'), ['pimpinan','manajerial']))
                <a href="{{ route('barang.edit', $b->id) }}" style="display:inline-flex;align-items:center;gap:3px;padding:5px 10px;border-radius:8px;background:#eff6ff;border:1px solid #bfdbfe;font-size:11px;font-weight:600;color:#1d4ed8;text-decoration:none;">
                  <span class="material-symbols-outlined" style="font-size:13px;">edit</span>
                </a>
                @if($b->is_active)
                <form method="POST" action="{{ route('barang.destroy', $b->id) }}" onsubmit="return confirm('Nonaktifkan barang ini?')">
                  @csrf @method('DELETE')
                  <button type="submit" style="display:inline-flex;align-items:center;padding:5px 10px;border-radius:8px;background:#fef2f2;border:1px solid #fecaca;font-size:11px;font-weight:600;color:#991b1b;cursor:pointer;">
                    <span class="material-symbols-outlined" style="font-size:13px;">block</span>
                  </button>
                </form>
                @endif
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="10">
            <div class="empty-state">
              <div class="icon"><span class="material-symbols-outlined" style="font-size:28px;color:#94a3b8;">inventory_2</span></div>
              <div style="font-weight:700;color:#0f172a;margin-bottom:6px;">Belum ada barang</div>
              @if(in_array(session('user_role'), ['pimpinan','manajerial']))
              <a href="{{ route('barang.create') }}" class="btn-or" style="display:inline-flex;margin-top:10px;">Tambah Barang Pertama</a>
              @endif
            </div>
          </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($barang->hasPages())
    <div style="padding:14px 18px;border-top:1px solid #f1f5f9;">{{ $barang->withQueryString()->links() }}</div>
    @endif
  </div>
</div>
@endsection