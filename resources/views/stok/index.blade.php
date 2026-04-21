@extends('layouts.app')
@section('title', 'Stok Gudang')
@section('page_title', 'Stok Gudang')
@section('page_sub', 'Data stok real-time seluruh barang di gudang')

@section('content')
<div style="padding:20px;display:flex;flex-direction:column;gap:16px;">

  {{-- Summary cards --}}
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;">
    <div class="card-stat" style="border-left:3px solid #3b82f6;">
      <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;margin-bottom:8px;">Total Jenis</div>
      <div style="font-family:'Sora',sans-serif;font-size:26px;font-weight:800;color:#0f172a;">{{ $totalJenis }}</div>
      <div style="font-size:11px;color:#94a3b8;margin-top:3px;">jenis barang</div>
    </div>
    <div class="card-stat" style="border-left:3px solid #22c55e;">
      <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;margin-bottom:8px;">Stok Normal</div>
      <div style="font-family:'Sora',sans-serif;font-size:26px;font-weight:800;color:#16a34a;">{{ $stokNormal }}</div>
      <div style="font-size:11px;color:#94a3b8;margin-top:3px;">di atas minimum</div>
    </div>
    <div class="card-stat" style="border-left:3px solid #f59e0b;">
      <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;margin-bottom:8px;">Stok Menipis</div>
      <div style="font-family:'Sora',sans-serif;font-size:26px;font-weight:800;color:#d97706;">{{ $stokMenipis }}</div>
      <div style="font-size:11px;color:#94a3b8;margin-top:3px;">di bawah minimum</div>
    </div>
    <div class="card-stat" style="border-left:3px solid #ef4444;">
      <div style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;margin-bottom:8px;">Stok Habis</div>
      <div style="font-family:'Sora',sans-serif;font-size:26px;font-weight:800;color:#dc2626;">{{ $stokHabis }}</div>
      <div style="font-size:11px;color:#94a3b8;margin-top:3px;">stok = 0</div>
    </div>
  </div>

  {{-- Table stok --}}
  <div class="card">
    <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:12px;">
      <form method="GET" style="display:flex;gap:8px;flex:1;flex-wrap:wrap;">
        <div style="position:relative;flex:1;max-width:280px;">
          <span class="material-symbols-outlined" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:16px;">search</span>
          <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, kode barang..."
            style="width:100%;padding:8px 12px 8px 34px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:12.5px;outline:none;background:#f8fafc;"/>
        </div>
        <select name="kategori" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:12.5px;outline:none;background:#f8fafc;cursor:pointer;">
          <option value="">Semua Kategori</option>
          @foreach($kategoriList as $k)
          <option value="{{ $k->id }}" {{ request('kategori')==$k->id?'selected':'' }}>{{ $k->nama }}</option>
          @endforeach
        </select>
        <select name="kondisi" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:12.5px;outline:none;background:#f8fafc;cursor:pointer;">
          <option value="">Semua Kondisi</option>
          <option value="normal" {{ request('kondisi')==='normal'?'selected':'' }}>Normal</option>
          <option value="menipis" {{ request('kondisi')==='menipis'?'selected':'' }}>Menipis</option>
          <option value="habis" {{ request('kondisi')==='habis'?'selected':'' }}>Habis</option>
        </select>
        <button type="submit" class="btn-or" style="padding:8px 16px;font-size:12px;">Cari</button>
      </form>
      @if(in_array(session('user_role'), ['manajerial','operator_gudang']))
      <button onclick="openModal('modal-koreksi')" class="btn-ghost" style="font-size:12px;padding:7px 14px;flex-shrink:0;">
        <span class="material-symbols-outlined" style="font-size:14px;">tune</span> Koreksi Stok
      </button>
      @endif
    </div>
    <div style="overflow-x:auto;">
      <table class="tbl">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Lokasi Rak</th>
            <th style="text-align:center;">Stok</th>
            <th style="text-align:center;">Minimum</th>
            <th style="text-align:center;">Status</th>
            <th>Harga Satuan</th>
            <th style="text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($barang as $b)
          @php
          $kondisi = $b->stok_tersedia == 0 ? 'habis' : ($b->stok_tersedia <= $b->stok_minimum ? 'menipis' : 'normal');
          $kondisiColor = $kondisi === 'habis' ? '#dc2626' : ($kondisi === 'menipis' ? '#d97706' : '#16a34a');
          $kondisiBg = $kondisi === 'habis' ? '#fef2f2' : ($kondisi === 'menipis' ? '#fefce8' : '#f0fdf4');
          @endphp
          <tr>
            <td style="font-family:'Sora',sans-serif;font-size:11px;font-weight:700;color:#64748b;">{{ $b->kode }}</td>
            <td>
              <div style="font-weight:700;font-size:12.5px;color:#0f172a;">{{ $b->nama }}</div>
              <div style="font-size:10px;color:#94a3b8;">{{ $b->kategori->nama ?? '-' }}</div>
            </td>
            <td style="font-size:12px;">{{ $b->kategori->nama ?? '-' }}</td>
            <td style="font-size:12px;color:#64748b;">{{ $b->lokasi_rak ?: '-' }}</td>
            <td style="text-align:center;">
              <div style="font-family:'Sora',sans-serif;font-size:18px;font-weight:800;color:{{ $kondisiColor }};">{{ $b->stok_tersedia }}</div>
              <div style="font-size:10px;color:#94a3b8;">{{ $b->satuan }}</div>
            </td>
            <td style="text-align:center;font-size:12px;color:#94a3b8;">{{ $b->stok_minimum }}</td>
            <td style="text-align:center;">
              <span style="display:inline-block;padding:3px 10px;border-radius:999px;font-size:10px;font-weight:700;background:{{ $kondisiBg }};color:{{ $kondisiColor }};">
                {{ $kondisi === 'habis' ? '⛔ Habis' : ($kondisi === 'menipis' ? '⚠️ Menipis' : '✅ Normal') }}
              </span>
              {{-- Progress bar --}}
              <div class="progress-track" style="margin-top:4px;width:80px;margin-left:auto;margin-right:auto;">
                @php $pct = $b->stok_minimum > 0 ? min(100, ($b->stok_tersedia / ($b->stok_minimum * 3)) * 100) : ($b->stok_tersedia > 0 ? 100 : 0); @endphp
                <div class="progress-fill" style="width:{{ $pct }}%;background:{{ $kondisiColor }};"></div>
              </div>
            </td>
            <td style="font-size:12px;">Rp {{ number_format($b->harga_satuan, 0, ',', '.') }}</td>
            <td style="text-align:right;">
              <a href="{{ route('barang.show', $b->id) }}" style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;border-radius:8px;background:#f8fafc;border:1px solid #e2e8f0;font-size:11px;font-weight:600;color:#475569;text-decoration:none;">
                <span class="material-symbols-outlined" style="font-size:13px;">history</span> Riwayat
              </a>
            </td>
          </tr>
          @empty
          <tr><td colspan="9">
            <div class="empty-state">
              <div class="icon"><span class="material-symbols-outlined" style="font-size:28px;color:#94a3b8;">inventory_2</span></div>
              <div style="font-weight:700;color:#0f172a;margin-bottom:6px;">Belum ada barang</div>
              <div style="font-size:12px;color:#94a3b8;">Tambahkan barang di menu Master Barang</div>
            </div>
          </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($barang->hasPages())
    <div style="padding:14px 18px;border-top:1px solid #f1f5f9;">
      {{ $barang->withQueryString()->links() }}
    </div>
    @endif
  </div>
</div>

{{-- Modal Koreksi Stok --}}
@if(in_array(session('user_role'), ['manajerial','operator_gudang']))
<div class="modal" id="modal-koreksi">
  <div>
    <div class="card" style="padding:24px;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
        <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:16px;color:#0f172a;">Koreksi Stok Manual</div>
        <button onclick="closeModal('modal-koreksi')" style="border:none;background:none;cursor:pointer;color:#94a3b8;font-size:20px;">✕</button>
      </div>
      <div class="alert-warning" style="margin-bottom:14px;">
        <span class="material-symbols-outlined fill-icon" style="font-size:16px;color:#d97706;flex-shrink:0;">warning</span>
        Koreksi stok akan dicatat di riwayat & audit log. Gunakan hanya untuk penyesuaian.
      </div>
      <form method="POST" action="{{ route('stok.koreksi') }}">
        @csrf
        <div style="margin-bottom:14px;">
          <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Barang *</label>
          <select name="barang_id" class="field" required>
            <option value="">-- Pilih Barang --</option>
            @foreach($barang as $b)
            <option value="{{ $b->id }}">{{ $b->kode }} - {{ $b->nama }} (Stok: {{ $b->stok_tersedia }})</option>
            @endforeach
          </select>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Tipe Koreksi *</label>
            <select name="tipe" class="field" required>
              <option value="koreksi">Koreksi</option>
              <option value="opname">Hasil Opname</option>
            </select>
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Stok Baru *</label>
            <input type="number" name="stok_baru" class="field" min="0" required placeholder="Jumlah stok yang benar"/>
          </div>
        </div>
        <div style="margin-bottom:16px;">
          <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Keterangan *</label>
          <textarea name="keterangan" class="field" required placeholder="Alasan koreksi stok..."></textarea>
        </div>
        <div style="display:flex;gap:8px;">
          <button type="button" onclick="closeModal('modal-koreksi')" class="btn-ghost" style="flex:1;justify-content:center;">Batal</button>
          <button type="submit" class="btn-or" style="flex:1;justify-content:center;">
            <span class="material-symbols-outlined fill-icon" style="font-size:16px;">save</span> Simpan Koreksi
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif
@endsection