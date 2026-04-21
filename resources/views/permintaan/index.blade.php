@extends('layouts.app')
@section('title', 'Permintaan Barang')
@section('page_title', 'Permintaan Barang')
@section('page_sub', 'Alur pengajuan, persetujuan & eksekusi stok')

@section('content')
<div style="padding:20px;display:flex;flex-direction:column;gap:16px;">

  {{-- Header + Filter --}}
  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
      @foreach(['semua'=>'Semua','diajukan'=>'Menunggu Approval','disetujui'=>'Disetujui','dikirim_operator'=>'Tugas Operator','selesai'=>'Selesai'] as $val=>$lbl)
      <a href="{{ route('permintaan.index') }}?filter={{ $val }}"
         style="padding:6px 14px;border-radius:999px;font-size:11.5px;font-weight:700;text-decoration:none;border:1px solid {{ request('filter',$val==='semua'?'semua':null) === $val ? '#f97316':'#e2e8f0' }};background:{{ request('filter',$val==='semua'?'semua':null) === $val ? '#fff7ed':'#fff' }};color:{{ request('filter',$val==='semua'?'semua':null) === $val ? '#f97316':'#64748b' }};">
        {{ $lbl }}
      </a>
      @endforeach
    </div>
    @if(session('user_role') === 'manajerial')
    <a href="{{ route('permintaan.create') }}" class="btn-or">
      <span class="material-symbols-outlined fill-icon" style="font-size:16px;">add</span>
      Buat Permintaan
    </a>
    @endif
  </div>

  {{-- Table --}}
  <div class="card">
    <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:12px;">
      <form method="GET" action="{{ route('permintaan.index') }}" style="display:flex;gap:8px;flex:1;">
        <input type="hidden" name="filter" value="{{ request('filter','semua') }}"/>
        <div style="position:relative;flex:1;max-width:300px;">
          <span class="material-symbols-outlined" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:16px;">search</span>
          <input type="text" name="q" placeholder="Cari nomor, keperluan..." value="{{ request('q') }}"
            style="width:100%;padding:8px 12px 8px 34px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:12.5px;outline:none;background:#f8fafc;"
            onchange="this.form.submit()"/>
        </div>
        <select name="jenis" onchange="this.form.submit()" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:12.5px;outline:none;background:#f8fafc;cursor:pointer;">
          <option value="">Semua Jenis</option>
          <option value="masuk" {{ request('jenis')==='masuk'?'selected':'' }}>Barang Masuk</option>
          <option value="keluar" {{ request('jenis')==='keluar'?'selected':'' }}>Barang Keluar</option>
        </select>
      </form>
      <div style="font-size:12px;color:#94a3b8;font-weight:600;">{{ $permintaan->total() }} data</div>
    </div>
    <div style="overflow-x:auto;">
      <table class="tbl">
        <thead>
          <tr>
            <th>Nomor</th>
            <th>Jenis</th>
            <th>Keperluan</th>
            <th>Tgl Butuh</th>
            <th>Pengaju</th>
            <th>Status</th>
            <th>Dibuat</th>
            <th style="text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($permintaan as $p)
          @php
          $badgeMap = ['draft'=>'badge-draft','diajukan'=>'badge-diajukan','disetujui'=>'badge-disetujui','ditolak'=>'badge-ditolak','dikirim_operator'=>'badge-dikirim','diproses'=>'badge-diproses','selesai'=>'badge-selesai','dibatalkan'=>'badge-dibatalkan'];
          $labelMap = ['draft'=>'Draft','diajukan'=>'Menunggu Approval','disetujui'=>'Disetujui','ditolak'=>'Ditolak','dikirim_operator'=>'Dikirim Operator','diproses'=>'Diproses','selesai'=>'Selesai','dibatalkan'=>'Dibatalkan'];
          @endphp
          <tr>
            <td>
              <a href="{{ route('permintaan.show', $p->id) }}" style="font-weight:700;color:#f97316;text-decoration:none;font-size:12px;">{{ $p->nomor_permintaan }}</a>
              <div style="font-size:10px;color:#94a3b8;">{{ $p->details->count() }} item</div>
            </td>
            <td>
              <span class="badge {{ $p->jenis === 'masuk' ? 'badge-masuk' : 'badge-keluar' }}">
                {{ $p->jenis === 'masuk' ? '↑ Masuk' : '↓ Keluar' }}
              </span>
            </td>
            <td style="max-width:200px;">
              <div style="font-weight:600;font-size:12.5px;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $p->keperluan }}</div>
              @if($p->departemen_tujuan)<div style="font-size:11px;color:#94a3b8;">→ {{ $p->departemen_tujuan }}</div>@endif
            </td>
            <td style="font-size:12px;">{{ \Carbon\Carbon::parse($p->tanggal_dibutuhkan)->format('d/m/Y') }}</td>
            <td>
              <div style="font-size:12px;font-weight:600;color:#0f172a;">{{ $p->pembuat->nama ?? '-' }}</div>
              <div style="font-size:10px;color:#94a3b8;text-transform:capitalize;">{{ str_replace('_',' ',$p->pembuat->role ?? '') }}</div>
            </td>
            <td><span class="badge {{ $badgeMap[$p->status] ?? 'badge-draft' }}">{{ $labelMap[$p->status] ?? $p->status }}</span></td>
            <td style="color:#94a3b8;font-size:11px;">{{ $p->created_at->format('d/m/Y') }}</td>
            <td style="text-align:right;">
              <a href="{{ route('permintaan.show', $p->id) }}" style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border-radius:8px;background:#f8fafc;border:1px solid #e2e8f0;font-size:12px;font-weight:600;color:#475569;text-decoration:none;">
                <span class="material-symbols-outlined" style="font-size:14px;">open_in_new</span> Detail
              </a>
            </td>
          </tr>
          @empty
          <tr><td colspan="8">
            <div class="empty-state">
              <div class="icon"><span class="material-symbols-outlined" style="font-size:28px;color:#94a3b8;">inbox</span></div>
              <div style="font-weight:700;font-size:14px;color:#0f172a;margin-bottom:6px;">Belum ada permintaan</div>
              <div style="font-size:12px;color:#94a3b8;">
                @if(session('user_role') === 'manajerial')
                <a href="{{ route('permintaan.create') }}" class="btn-or" style="margin-top:12px;display:inline-flex;">Buat Permintaan Pertama</a>
                @else
                Belum ada permintaan yang masuk.
                @endif
              </div>
            </div>
          </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($permintaan->hasPages())
    <div style="padding:14px 18px;border-top:1px solid #f1f5f9;">
      {{ $permintaan->withQueryString()->links() }}
    </div>
    @endif
  </div>
</div>
@endsection