@extends('layouts.app')
@section('title', 'Stock Opname')
@section('page_title', 'Stock Opname')
@section('page_sub', 'Pengecekan dan penyesuaian fisik stok gudang')

@section('content')
<div style="padding:20px;display:flex;flex-direction:column;gap:16px;">

  <div style="display:flex;align-items:center;justify-content:space-between;">
    <div style="font-size:13px;color:#64748b;">{{ $opname->total() }} record opname</div>
    <a href="{{ route('opname.create') }}" class="btn-or">
      <span class="material-symbols-outlined fill-icon" style="font-size:16px;">add</span> Buat Opname Baru
    </a>
  </div>

  <div class="card">
    <div style="overflow-x:auto;">
      <table class="tbl">
        <thead>
          <tr>
            <th>Nomor Opname</th>
            <th>Tanggal</th>
            <th>Dibuat Oleh</th>
            <th>Keterangan</th>
            <th style="text-align:center;">Status</th>
            <th>Tgl Selesai</th>
            <th style="text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($opname as $o)
          <tr>
            <td>
              <a href="{{ route('opname.show', $o->id) }}" style="font-weight:700;color:#f97316;text-decoration:none;font-size:12px;">{{ $o->nomor_opname }}</a>
            </td>
            <td style="font-size:12px;">{{ \Carbon\Carbon::parse($o->tanggal)->format('d/m/Y') }}</td>
            <td style="font-size:12px;">{{ $o->pembuat->nama ?? '-' }}</td>
            <td style="font-size:12px;color:#64748b;max-width:200px;">{{ Str::limit($o->keterangan, 60) ?: '-' }}</td>
            <td style="text-align:center;">
              <span class="badge {{ $o->status === 'selesai' ? 'badge-selesai' : 'badge-draft' }}">
                {{ $o->status === 'selesai' ? 'Selesai' : 'Draft' }}
              </span>
            </td>
            <td style="font-size:11px;color:#94a3b8;">
              {{ $o->tgl_selesai ? \Carbon\Carbon::parse($o->tgl_selesai)->format('d/m/Y H:i') : '-' }}
            </td>
            <td style="text-align:right;">
              <a href="{{ route('opname.show', $o->id) }}" style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:8px;background:#f8fafc;border:1px solid #e2e8f0;font-size:12px;font-weight:600;color:#475569;text-decoration:none;">
                <span class="material-symbols-outlined" style="font-size:14px;">open_in_new</span> Detail
              </a>
            </td>
          </tr>
          @empty
          <tr><td colspan="7">
            <div class="empty-state">
              <div class="icon"><span class="material-symbols-outlined" style="font-size:28px;color:#94a3b8;">fact_check</span></div>
              <div style="font-weight:700;color:#0f172a;margin-bottom:6px;">Belum ada opname</div>
              <a href="{{ route('opname.create') }}" class="btn-or" style="display:inline-flex;margin-top:10px;">Buat Opname Pertama</a>
            </div>
          </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($opname->hasPages())
    <div style="padding:14px 18px;border-top:1px solid #f1f5f9;">{{ $opname->withQueryString()->links() }}</div>
    @endif
  </div>
</div>
@endsection