@extends('layouts.app')
@section('title', $barang->nama)
@section('page_title', $barang->nama)
@section('page_sub', 'Detail barang & riwayat stok')

@section('content')
<div style="padding:20px;display:flex;flex-direction:column;gap:16px;max-width:960px;margin:0 auto;">

  <div style="display:grid;grid-template-columns:1fr 320px;gap:16px;">
    {{-- Info utama --}}
    <div class="card" style="padding:24px;">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid #f1f5f9;">
        <div>
          <div style="font-size:11px;color:#94a3b8;margin-bottom:4px;">{{ $barang->kode }}</div>
          <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:22px;color:#0f172a;">{{ $barang->nama }}</div>
          <div style="font-size:12px;color:#64748b;margin-top:4px;">{{ $barang->kategori->nama ?? '-' }}</div>
        </div>
        @if($barang->foto)
        <img src="{{ asset('storage/'.$barang->foto) }}" alt="Foto" style="width:80px;height:80px;object-fit:cover;border-radius:12px;border:1px solid #f1f5f9;flex-shrink:0;"/>
        @else
        <div style="width:80px;height:80px;border-radius:12px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          <span class="material-symbols-outlined" style="font-size:32px;color:#cbd5e1;">inventory_2</span>
        </div>
        @endif
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        @foreach([
          ['Satuan', $barang->satuan],
          ['Lokasi Rak', $barang->lokasi_rak ?: '-'],
          ['Harga Satuan', 'Rp '.number_format($barang->harga_satuan, 0, ',', '.')],
          ['Status', $barang->is_active ? 'Aktif' : 'Nonaktif'],
        ] as [$k,$v])
        <div style="background:#f8fafc;border-radius:10px;padding:12px;">
          <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.07em;margin-bottom:4px;">{{ $k }}</div>
          <div style="font-size:13px;font-weight:700;color:#0f172a;">{{ $v }}</div>
        </div>
        @endforeach
      </div>

      @if($barang->deskripsi)
      <div style="margin-top:14px;padding:12px;background:#f8fafc;border-radius:10px;">
        <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.07em;margin-bottom:4px;">Deskripsi</div>
        <div style="font-size:13px;color:#334155;line-height:1.6;">{{ $barang->deskripsi }}</div>
      </div>
      @endif

      @if(in_array(session('user_role'), ['pimpinan','manajerial']))
      <div style="margin-top:16px;display:flex;gap:8px;">
        <a href="{{ route('barang.edit', $barang->id) }}" class="btn-or" style="font-size:12px;padding:8px 16px;">
          <span class="material-symbols-outlined fill-icon" style="font-size:14px;">edit</span> Edit
        </a>
        <a href="{{ route('barang.index') }}" class="btn-ghost" style="font-size:12px;padding:8px 16px;">
          <span class="material-symbols-outlined" style="font-size:14px;">arrow_back</span> Kembali
        </a>
      </div>
      @else
      <div style="margin-top:16px;">
        <a href="{{ route('barang.index') }}" class="btn-ghost" style="font-size:12px;padding:8px 16px;">
          <span class="material-symbols-outlined" style="font-size:14px;">arrow_back</span> Kembali
        </a>
      </div>
      @endif
    </div>

    {{-- Stok info --}}
    <div style="display:flex;flex-direction:column;gap:12px;">
      @php
        $kondisi = $barang->stok_tersedia == 0 ? 'habis' : ($barang->stok_tersedia <= $barang->stok_minimum ? 'menipis' : 'normal');
        $kondisiColor = $kondisi === 'habis' ? '#dc2626' : ($kondisi === 'menipis' ? '#d97706' : '#16a34a');
        $kondisiBg = $kondisi === 'habis' ? '#fef2f2' : ($kondisi === 'menipis' ? '#fefce8' : '#f0fdf4');
      @endphp
      <div class="card" style="padding:20px;border:2px solid {{ $kondisiColor }};background:{{ $kondisiBg }};">
        <div style="font-size:11px;font-weight:700;color:{{ $kondisiColor }};text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px;">Stok Saat Ini</div>
        <div style="font-family:'Sora',sans-serif;font-size:48px;font-weight:800;color:{{ $kondisiColor }};line-height:1;">{{ $barang->stok_tersedia }}</div>
        <div style="font-size:13px;color:{{ $kondisiColor }};font-weight:600;margin-top:4px;">{{ $barang->satuan }}</div>
        <div style="margin-top:12px;padding-top:12px;border-top:1px solid rgba(0,0,0,.08);">
          <div style="font-size:11px;color:#64748b;">Stok minimum: <b>{{ $barang->stok_minimum }}</b> {{ $barang->satuan }}</div>
          <div class="progress-track" style="margin-top:8px;">
            @php $pct = $barang->stok_minimum > 0 ? min(100, ($barang->stok_tersedia / ($barang->stok_minimum * 3)) * 100) : ($barang->stok_tersedia > 0 ? 100 : 0); @endphp
            <div class="progress-fill" style="width:{{ $pct }}%;background:{{ $kondisiColor }};"></div>
          </div>
        </div>
        <div style="margin-top:10px;">
          <span style="padding:4px 12px;border-radius:999px;font-size:11px;font-weight:700;background:{{ $kondisiColor }};color:#fff;">
            {{ $kondisi === 'habis' ? '⛔ Stok Habis' : ($kondisi === 'menipis' ? '⚠️ Stok Menipis' : '✅ Stok Normal') }}
          </span>
        </div>
      </div>

      <div class="card" style="padding:16px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.07em;margin-bottom:10px;">Nilai Stok</div>
        <div style="font-family:'Sora',sans-serif;font-size:20px;font-weight:800;color:#0f172a;">
          Rp {{ number_format($barang->stok_tersedia * $barang->harga_satuan, 0, ',', '.') }}
        </div>
        <div style="font-size:11px;color:#94a3b8;margin-top:3px;">{{ $barang->stok_tersedia }} × Rp {{ number_format($barang->harga_satuan, 0, ',', '.') }}</div>
      </div>
    </div>
  </div>

  {{-- Riwayat Stok --}}
  <div class="card">
    <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;">
      <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:13px;color:#0f172a;">Riwayat Stok</div>
      <div style="font-size:11px;color:#94a3b8;">20 transaksi terakhir</div>
    </div>
    <div style="overflow-x:auto;">
      <table class="tbl">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Tipe</th>
            <th>Jumlah</th>
            <th>Stok Sebelum</th>
            <th>Stok Sesudah</th>
            <th>Referensi</th>
            <th>Oleh</th>
            <th>Keterangan</th>
          </tr>
        </thead>
        <tbody>
          @forelse($riwayat as $r)
          @php
            $tipeColor = in_array($r->tipe, ['masuk']) ? '#16a34a' : (in_array($r->tipe, ['keluar']) ? '#dc2626' : '#d97706');
            $tipeBg = in_array($r->tipe, ['masuk']) ? '#dcfce7' : (in_array($r->tipe, ['keluar']) ? '#fef2f2' : '#fefce8');
          @endphp
          <tr>
            <td style="font-size:11px;color:#94a3b8;">{{ $r->created_at->format('d/m/Y H:i') }}</td>
            <td>
              <span style="display:inline-block;padding:2px 9px;border-radius:999px;font-size:10px;font-weight:700;background:{{ $tipeBg }};color:{{ $tipeColor }};">
                {{ strtoupper($r->tipe) }}
              </span>
            </td>
            <td style="font-weight:700;color:{{ $tipeColor }};">
              {{ in_array($r->tipe, ['masuk']) ? '+' : (in_array($r->tipe, ['keluar']) ? '-' : '~') }}{{ $r->jumlah }}
            </td>
            <td style="font-size:12px;">{{ $r->stok_sebelum }}</td>
            <td style="font-size:12px;font-weight:700;">{{ $r->stok_sesudah }}</td>
            <td style="font-size:11px;color:#64748b;">{{ $r->referensi ?: '-' }}</td>
            <td style="font-size:12px;">{{ $r->user->nama ?? '-' }}</td>
            <td style="font-size:11px;color:#64748b;max-width:180px;">{{ Str::limit($r->keterangan, 60) ?: '-' }}</td>
          </tr>
          @empty
          <tr><td colspan="8" style="text-align:center;padding:40px;color:#94a3b8;">Belum ada riwayat stok</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($riwayat->hasPages())
    <div style="padding:14px 18px;border-top:1px solid #f1f5f9;">{{ $riwayat->withQueryString()->links() }}</div>
    @endif
  </div>
</div>
@endsection