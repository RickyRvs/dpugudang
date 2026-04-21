@extends('layouts.app')
@section('title', 'Detail Opname '.$opname->nomor_opname)
@section('page_title', 'Detail Stock Opname')
@section('page_sub', $opname->nomor_opname)

@section('content')
<div style="padding:20px;display:flex;flex-direction:column;gap:16px;max-width:920px;margin:0 auto;">

  {{-- Header --}}
  <div class="card" style="padding:20px 24px;">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
      <div>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
          <span class="badge {{ $opname->status === 'selesai' ? 'badge-selesai' : 'badge-draft' }}" style="font-size:11px;padding:4px 12px;">
            {{ $opname->status === 'selesai' ? '✅ Selesai' : '📋 Draft' }}
          </span>
        </div>
        <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:20px;color:#0f172a;">{{ $opname->nomor_opname }}</div>
        <div style="font-size:13px;color:#64748b;margin-top:4px;">Tanggal: {{ \Carbon\Carbon::parse($opname->tanggal)->format('d F Y') }}</div>
        @if($opname->keterangan)
        <div style="font-size:12px;color:#94a3b8;margin-top:2px;">{{ $opname->keterangan }}</div>
        @endif
      </div>
      <div style="text-align:right;">
        <div style="font-size:11px;color:#94a3b8;">Dibuat oleh</div>
        <div style="font-weight:700;color:#0f172a;">{{ $opname->pembuat->nama ?? '-' }}</div>
        <div style="font-size:11px;color:#94a3b8;">{{ $opname->created_at->format('d M Y H:i') }}</div>
        @if($opname->penyetuju)
        <div style="margin-top:6px;font-size:11px;color:#94a3b8;">Diselesaikan oleh</div>
        <div style="font-weight:700;color:#16a34a;">{{ $opname->penyetuju->nama }}</div>
        <div style="font-size:11px;color:#94a3b8;">{{ $opname->tgl_selesai?->format('d M Y H:i') }}</div>
        @endif
      </div>
    </div>
  </div>

  {{-- Ringkasan --}}
  @php
    $totalItems = $opname->details->count();
    $sesuai = $opname->details->filter(fn($d) => $d->stok_sistem === $d->stok_fisik)->count();
    $lebih = $opname->details->filter(fn($d) => $d->stok_fisik > $d->stok_sistem)->count();
    $kurang = $opname->details->filter(fn($d) => $d->stok_fisik < $d->stok_sistem)->count();
  @endphp
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;">
    <div class="card-stat" style="border-left:3px solid #3b82f6;">
      <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:6px;">Total Barang</div>
      <div style="font-family:'Sora',sans-serif;font-size:24px;font-weight:800;color:#0f172a;">{{ $totalItems }}</div>
    </div>
    <div class="card-stat" style="border-left:3px solid #22c55e;">
      <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:6px;">Sesuai</div>
      <div style="font-family:'Sora',sans-serif;font-size:24px;font-weight:800;color:#16a34a;">{{ $sesuai }}</div>
    </div>
    <div class="card-stat" style="border-left:3px solid #f97316;">
      <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:6px;">Lebih</div>
      <div style="font-family:'Sora',sans-serif;font-size:24px;font-weight:800;color:#f97316;">{{ $lebih }}</div>
    </div>
    <div class="card-stat" style="border-left:3px solid #ef4444;">
      <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:6px;">Kurang</div>
      <div style="font-family:'Sora',sans-serif;font-size:24px;font-weight:800;color:#dc2626;">{{ $kurang }}</div>
    </div>
  </div>

  {{-- Tabel detail --}}
  <div class="card">
    <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
      <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:13px;color:#0f172a;">Detail Pengecekan Barang</div>
      @if($opname->status === 'draft')
      <form method="POST" action="{{ route('opname.selesai', $opname->id) }}" onsubmit="return confirm('Selesaikan opname ini? Stok akan disesuaikan dengan hasil fisik.')">
        @csrf
        <button type="submit" class="btn-green" style="font-size:12px;padding:8px 16px;">
          <span class="material-symbols-outlined fill-icon" style="font-size:15px;">check_circle</span> Selesaikan & Update Stok
        </button>
      </form>
      @endif
    </div>
    <div style="overflow-x:auto;">
      <table class="tbl">
        <thead>
          <tr>
            <th>#</th>
            <th>Barang</th>
            <th style="text-align:center;">Stok Sistem</th>
            <th style="text-align:center;">Stok Fisik</th>
            <th style="text-align:center;">Selisih</th>
            <th>Keterangan</th>
          </tr>
        </thead>
        <tbody>
          @foreach($opname->details as $i => $det)
          @php
            $selisih = $det->stok_fisik - $det->stok_sistem;
            $selisihColor = $selisih > 0 ? '#16a34a' : ($selisih < 0 ? '#dc2626' : '#94a3b8');
          @endphp
          <tr>
            <td style="color:#94a3b8;font-size:11px;">{{ $i+1 }}</td>
            <td>
              <div style="font-weight:700;font-size:12.5px;color:#0f172a;">{{ $det->barang->nama ?? '-' }}</div>
              <div style="font-size:10px;color:#94a3b8;">{{ $det->barang->kode ?? '' }}</div>
            </td>
            <td style="text-align:center;font-size:14px;font-weight:700;color:#64748b;">{{ $det->stok_sistem }}</td>
            <td style="text-align:center;font-size:14px;font-weight:700;color:#0f172a;">{{ $det->stok_fisik }}</td>
            <td style="text-align:center;">
              <span style="font-weight:800;font-size:14px;color:{{ $selisihColor }};">
                {{ $selisih > 0 ? '+' : '' }}{{ $selisih }}
              </span>
            </td>
            <td style="font-size:11px;color:#64748b;">{{ $det->keterangan ?: '-' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <a href="{{ route('opname.index') }}" class="btn-ghost" style="align-self:flex-start;">
    <span class="material-symbols-outlined" style="font-size:16px;">arrow_back</span> Kembali
  </a>
</div>
@endsection