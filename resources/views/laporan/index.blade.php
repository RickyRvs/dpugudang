@extends('layouts.app')
@section('title', 'Laporan')
@section('page_title', 'Laporan')
@section('page_sub', 'Analisis stok & transaksi gudang')

@section('content')
<div style="padding:20px;display:flex;flex-direction:column;gap:16px;">

  {{-- Filter tanggal --}}
  <div class="card" style="padding:16px 20px;">
    <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
      <span style="font-size:12px;font-weight:700;color:#64748b;">Periode:</span>
      <div style="display:flex;align-items:center;gap:6px;">
        <input type="date" name="dari" value="{{ $dari }}"
          style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:12.5px;outline:none;background:#f8fafc;"/>
        <span style="color:#94a3b8;font-size:13px;">s/d</span>
        <input type="date" name="sampai" value="{{ $sampai }}"
          style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:12.5px;outline:none;background:#f8fafc;"/>
      </div>
      <button type="submit" class="btn-or" style="padding:8px 18px;font-size:12px;">Tampilkan</button>
      <div style="font-size:12px;color:#94a3b8;margin-left:auto;">
        {{ \Carbon\Carbon::parse($dari)->format('d M Y') }} — {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}
      </div>
    </form>
  </div>

  {{-- Stat cards --}}
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;">
    <div class="card-stat" style="border-left:3px solid #22c55e;">
      <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:8px;">Total Masuk</div>
      <div style="font-family:'Sora',sans-serif;font-size:26px;font-weight:800;color:#16a34a;">{{ number_format($totalMasuk) }}</div>
      <div style="font-size:11px;color:#94a3b8;margin-top:3px;">unit masuk ke gudang</div>
    </div>
    <div class="card-stat" style="border-left:3px solid #ef4444;">
      <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:8px;">Total Keluar</div>
      <div style="font-family:'Sora',sans-serif;font-size:26px;font-weight:800;color:#dc2626;">{{ number_format($totalKeluar) }}</div>
      <div style="font-size:11px;color:#94a3b8;margin-top:3px;">unit keluar dari gudang</div>
    </div>
    <div class="card-stat" style="border-left:3px solid #3b82f6;">
      <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:8px;">Total Permintaan</div>
      <div style="font-family:'Sora',sans-serif;font-size:26px;font-weight:800;color:#1d4ed8;">{{ $totalPermintaan }}</div>
      <div style="font-size:11px;color:#94a3b8;margin-top:3px;">permintaan masuk</div>
    </div>
    <div class="card-stat" style="border-left:3px solid #f97316;">
      <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:8px;">Permintaan Selesai</div>
      <div style="font-family:'Sora',sans-serif;font-size:26px;font-weight:800;color:#ea580c;">{{ $permintaanSelesai }}</div>
      <div style="font-size:11px;color:#94a3b8;margin-top:3px;">
    @if($totalPermintaan > 0)
  {{ round($permintaanSelesai / $totalPermintaan * 100) }}% completion rate
@endif
      </div>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
    {{-- Top barang keluar --}}
    <div class="card">
      <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;">
        <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:13px;color:#0f172a;">🔝 Top 10 Barang Keluar</div>
      </div>
      <div style="padding:12px;">
        @forelse($topBarangKeluar as $i => $item)
        @php $max = $topBarangKeluar->first()->total ?? 1; @endphp
        <div style="display:flex;align-items:center;gap:10px;padding:8px;margin-bottom:4px;">
          <div style="width:20px;height:20px;border-radius:6px;background:{{ $i < 3 ? 'linear-gradient(135deg,#f97316,#ea580c)' : '#f1f5f9' }};display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:{{ $i < 3 ? '#fff' : '#94a3b8' }};flex-shrink:0;">{{ $i+1 }}</div>
          <div style="flex:1;min-width:0;">
            <div style="font-size:12px;font-weight:700;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $item->barang->nama ?? '-' }}</div>
            <div class="progress-track" style="margin-top:4px;">
              <div class="progress-fill" style="width:{{ ($item->total / $max) * 100 }}%;background:#ef4444;"></div>
            </div>
          </div>
          <div style="font-family:'Sora',sans-serif;font-size:14px;font-weight:800;color:#dc2626;flex-shrink:0;">{{ number_format($item->total) }}</div>
        </div>
        @empty
        <div style="padding:24px;text-align:center;font-size:12px;color:#94a3b8;">Tidak ada data</div>
        @endforelse
      </div>
    </div>

    {{-- Top barang masuk --}}
    <div class="card">
      <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;">
        <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:13px;color:#0f172a;">📦 Top 10 Barang Masuk</div>
      </div>
      <div style="padding:12px;">
        @forelse($topBarangMasuk as $i => $item)
        @php $max = $topBarangMasuk->first()->total ?? 1; @endphp
        <div style="display:flex;align-items:center;gap:10px;padding:8px;margin-bottom:4px;">
          <div style="width:20px;height:20px;border-radius:6px;background:{{ $i < 3 ? 'linear-gradient(135deg,#22c55e,#16a34a)' : '#f1f5f9' }};display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:{{ $i < 3 ? '#fff' : '#94a3b8' }};flex-shrink:0;">{{ $i+1 }}</div>
          <div style="flex:1;min-width:0;">
            <div style="font-size:12px;font-weight:700;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $item->barang->nama ?? '-' }}</div>
            <div class="progress-track" style="margin-top:4px;">
              <div class="progress-fill" style="width:{{ ($item->total / $max) * 100 }}%;background:#22c55e;"></div>
            </div>
          </div>
          <div style="font-family:'Sora',sans-serif;font-size:14px;font-weight:800;color:#16a34a;flex-shrink:0;">{{ number_format($item->total) }}</div>
        </div>
        @empty
        <div style="padding:24px;text-align:center;font-size:12px;color:#94a3b8;">Tidak ada data</div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- Tren harian --}}
  <div class="card">
    <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;">
      <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:13px;color:#0f172a;">📈 Tren Transaksi Harian</div>
    </div>
    <div style="overflow-x:auto;">
      <table class="tbl">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th style="text-align:center;color:#16a34a;">Masuk</th>
            <th style="text-align:center;color:#dc2626;">Keluar</th>
            <th style="text-align:center;">Net</th>
          </tr>
        </thead>
        <tbody>
          @forelse($trenHarian as $tgl => $rows)
          @php
            $masuk = $rows->where('tipe','masuk')->first()?->total ?? 0;
            $keluar = $rows->where('tipe','keluar')->first()?->total ?? 0;
            $net = $masuk - $keluar;
          @endphp
          <tr>
            <td style="font-size:12px;">{{ \Carbon\Carbon::parse($tgl)->format('d M Y') }}</td>
            <td style="text-align:center;font-weight:700;color:#16a34a;">+{{ number_format($masuk) }}</td>
            <td style="text-align:center;font-weight:700;color:#dc2626;">-{{ number_format($keluar) }}</td>
            <td style="text-align:center;font-weight:800;color:{{ $net >= 0 ? '#16a34a' : '#dc2626' }};">
              {{ $net >= 0 ? '+' : '' }}{{ number_format($net) }}
            </td>
          </tr>
          @empty
          <tr><td colspan="4" style="text-align:center;padding:30px;color:#94a3b8;">Tidak ada transaksi di periode ini</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Stok menipis --}}
  @if($stokMenipis->count() > 0)
  <div class="card">
    <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;">
      <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:13px;color:#dc2626;">⚠️ Barang Perlu Reorder (Stok ≤ Minimum)</div>
    </div>
    <div style="overflow-x:auto;">
      <table class="tbl">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th style="text-align:center;">Stok Saat Ini</th>
            <th style="text-align:center;">Stok Minimum</th>
            <th style="text-align:center;">Kekurangan</th>
            <th>Harga Satuan</th>
          </tr>
        </thead>
        <tbody>
          @foreach($stokMenipis as $b)
          <tr>
            <td style="font-size:11px;font-weight:700;color:#64748b;">{{ $b->kode }}</td>
            <td style="font-weight:700;font-size:12.5px;color:#0f172a;">{{ $b->nama }}</td>
            <td style="font-size:12px;">{{ $b->kategori->nama ?? '-' }}</td>
            <td style="text-align:center;font-weight:800;color:{{ $b->stok_tersedia == 0 ? '#dc2626' : '#d97706' }};">{{ $b->stok_tersedia }}</td>
            <td style="text-align:center;font-size:12px;color:#94a3b8;">{{ $b->stok_minimum }}</td>
            <td style="text-align:center;font-weight:700;color:#dc2626;">{{ max(0, $b->stok_minimum - $b->stok_tersedia) }}</td>
            <td style="font-size:12px;">Rp {{ number_format($b->harga_satuan, 0, ',', '.') }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif
</div>
@endsection