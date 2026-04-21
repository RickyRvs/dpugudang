@extends('layouts.app')
@section('title', 'Riwayat Stok')
@section('page_title', 'Riwayat Stok')
@section('page_sub', 'Semua transaksi mutasi stok gudang')

@section('content')
<div style="padding:20px;display:flex;flex-direction:column;gap:16px;">

  <div class="card">
    <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;">
      <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
        <div style="position:relative;flex:1;min-width:180px;max-width:280px;">
          <span class="material-symbols-outlined" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:16px;">search</span>
          <select name="barang_id" onchange="this.form.submit()" style="width:100%;padding:8px 12px 8px 34px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:12.5px;outline:none;background:#f8fafc;cursor:pointer;">
            <option value="">Semua Barang</option>
            @foreach($barangList as $b)
            <option value="{{ $b->id }}" {{ request('barang_id')==$b->id?'selected':'' }}>{{ $b->nama }}</option>
            @endforeach
          </select>
        </div>
        <select name="tipe" onchange="this.form.submit()" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:12.5px;outline:none;background:#f8fafc;cursor:pointer;">
          <option value="">Semua Tipe</option>
          <option value="masuk" {{ request('tipe')==='masuk'?'selected':'' }}>Masuk</option>
          <option value="keluar" {{ request('tipe')==='keluar'?'selected':'' }}>Keluar</option>
          <option value="koreksi" {{ request('tipe')==='koreksi'?'selected':'' }}>Koreksi</option>
          <option value="opname" {{ request('tipe')==='opname'?'selected':'' }}>Opname</option>
        </select>
        <input type="date" name="dari" value="{{ request('dari') }}" onchange="this.form.submit()"
          style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:12.5px;outline:none;background:#f8fafc;"/>
        <input type="date" name="sampai" value="{{ request('sampai') }}" onchange="this.form.submit()"
          style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:12.5px;outline:none;background:#f8fafc;"/>
        @if(request()->hasAny(['barang_id','tipe','dari','sampai']))
        <a href="{{ route('riwayat.index') }}" class="btn-ghost" style="font-size:12px;padding:7px 14px;">Reset</a>
        @endif
        <div style="font-size:12px;color:#94a3b8;font-weight:600;margin-left:auto;">{{ $riwayat->total() }} data</div>
      </form>
    </div>

    <div style="overflow-x:auto;">
      <table class="tbl">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Tipe</th>
            <th>Barang</th>
            <th style="text-align:center;">Jumlah</th>
            <th style="text-align:center;">Stok Sebelum</th>
            <th style="text-align:center;">Stok Sesudah</th>
            <th>Referensi</th>
            <th>Oleh</th>
            <th>Keterangan</th>
          </tr>
        </thead>
        <tbody>
          @forelse($riwayat as $r)
          @php
            $isPlus = in_array($r->tipe, ['masuk']);
            $isMinus = in_array($r->tipe, ['keluar']);
            $tipeColor = $isPlus ? '#16a34a' : ($isMinus ? '#dc2626' : '#d97706');
            $tipeBg = $isPlus ? '#dcfce7' : ($isMinus ? '#fef2f2' : '#fefce8');
          @endphp
          <tr>
            <td style="font-size:11px;color:#94a3b8;white-space:nowrap;">{{ $r->created_at->format('d/m/Y H:i') }}</td>
            <td>
              <span style="display:inline-block;padding:2px 9px;border-radius:999px;font-size:10px;font-weight:700;background:{{ $tipeBg }};color:{{ $tipeColor }};">
                {{ strtoupper($r->tipe) }}
              </span>
            </td>
            <td>
              <div style="font-weight:700;font-size:12.5px;color:#0f172a;">{{ $r->barang->nama ?? '-' }}</div>
              <div style="font-size:10px;color:#94a3b8;">{{ $r->barang->kode ?? '' }}</div>
            </td>
            <td style="text-align:center;font-family:'Sora',sans-serif;font-weight:800;font-size:15px;color:{{ $tipeColor }};">
              {{ $isPlus ? '+' : ($isMinus ? '-' : '~') }}{{ $r->jumlah }}
            </td>
            <td style="text-align:center;font-size:12px;color:#64748b;">{{ $r->stok_sebelum }}</td>
            <td style="text-align:center;font-size:13px;font-weight:700;color:#0f172a;">{{ $r->stok_sesudah }}</td>
            <td style="font-size:11px;">
              @if($r->permintaan)
              <a href="{{ route('permintaan.show', $r->permintaan_id) }}" style="color:#f97316;text-decoration:none;font-weight:600;">{{ $r->referensi }}</a>
              @else
              <span style="color:#64748b;">{{ $r->referensi ?: '-' }}</span>
              @endif
            </td>
            <td style="font-size:12px;">{{ $r->user->nama ?? '-' }}</td>
            <td style="font-size:11px;color:#64748b;max-width:160px;">{{ Str::limit($r->keterangan, 50) ?: '-' }}</td>
          </tr>
          @empty
          <tr><td colspan="9">
            <div class="empty-state">
              <div class="icon"><span class="material-symbols-outlined" style="font-size:28px;color:#94a3b8;">receipt_long</span></div>
              <div style="font-weight:700;color:#0f172a;margin-bottom:6px;">Belum ada riwayat stok</div>
            </div>
          </td></tr>
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