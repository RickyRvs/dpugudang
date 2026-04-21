@extends('layouts.app')
@section('title', 'Audit Log')
@section('page_title', 'Audit Log')
@section('page_sub', 'Rekam jejak semua aktivitas sistem')

@section('content')
<div style="padding:20px;display:flex;flex-direction:column;gap:16px;">

  <div class="card">
    <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;">
      <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
        <select name="modul" onchange="this.form.submit()" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:12.5px;outline:none;background:#f8fafc;cursor:pointer;">
          <option value="">Semua Modul</option>
          @foreach(['auth','barang','kategori','permintaan','stok','opname','user'] as $m)
          <option value="{{ $m }}" {{ request('modul')===$m?'selected':'' }}>{{ ucfirst($m) }}</option>
          @endforeach
        </select>
        <select name="user_id" onchange="this.form.submit()" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:12.5px;outline:none;background:#f8fafc;cursor:pointer;">
          <option value="">Semua User</option>
          @foreach($users as $u)
          <option value="{{ $u->id }}" {{ request('user_id')==$u->id?'selected':'' }}>{{ $u->nama }}</option>
          @endforeach
        </select>
        <input type="date" name="dari" value="{{ request('dari') }}" onchange="this.form.submit()"
          style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:12.5px;outline:none;background:#f8fafc;"/>
        <input type="date" name="sampai" value="{{ request('sampai') }}" onchange="this.form.submit()"
          style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:12.5px;outline:none;background:#f8fafc;"/>
        @if(request()->hasAny(['modul','user_id','dari','sampai']))
        <a href="{{ route('audit.index') }}" class="btn-ghost" style="font-size:12px;padding:7px 14px;">Reset</a>
        @endif
        <div style="font-size:12px;color:#94a3b8;font-weight:600;margin-left:auto;">{{ $logs->total() }} log</div>
      </form>
    </div>

    <div style="overflow-x:auto;">
      <table class="tbl">
        <thead>
          <tr>
            <th>Waktu</th>
            <th>User</th>
            <th>Aksi</th>
            <th>Modul</th>
            <th>Detail</th>
            <th>IP Address</th>
          </tr>
        </thead>
        <tbody>
          @forelse($logs as $log)
          @php
            $modulColor = match($log->modul) {
              'auth' => ['#7c3aed','#f3e8ff'],
              'barang' => ['#f97316','#fff7ed'],
              'permintaan' => ['#1d4ed8','#eff6ff'],
              'stok' => ['#16a34a','#f0fdf4'],
              'user' => ['#dc2626','#fef2f2'],
              default => ['#64748b','#f8fafc'],
            };
          @endphp
          <tr>
            <td style="font-size:11px;color:#94a3b8;white-space:nowrap;">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
            <td>
              <div style="font-weight:700;font-size:12px;color:#0f172a;">{{ $log->user->nama ?? 'System' }}</div>
              <div style="font-size:10px;color:#94a3b8;text-transform:capitalize;">{{ str_replace('_',' ', $log->user->role ?? '') }}</div>
            </td>
            <td>
              <code style="font-size:11px;background:#f1f5f9;padding:2px 8px;border-radius:5px;color:#0f172a;font-family:monospace;">{{ $log->aksi }}</code>
            </td>
            <td>
              <span style="display:inline-block;padding:2px 9px;border-radius:999px;font-size:10px;font-weight:700;background:{{ $modulColor[1] }};color:{{ $modulColor[0] }};">
                {{ strtoupper($log->modul) }}
              </span>
            </td>
            <td style="font-size:12px;color:#334155;max-width:200px;">{{ Str::limit($log->detail, 70) ?: '-' }}</td>
            <td style="font-size:11px;color:#94a3b8;font-family:monospace;">{{ $log->ip_address }}</td>
          </tr>
          @empty
          <tr><td colspan="6">
            <div class="empty-state">
              <div class="icon"><span class="material-symbols-outlined" style="font-size:28px;color:#94a3b8;">history</span></div>
              <div style="font-weight:700;color:#0f172a;margin-bottom:6px;">Belum ada log aktivitas</div>
            </div>
          </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($logs->hasPages())
    <div style="padding:14px 18px;border-top:1px solid #f1f5f9;">{{ $logs->withQueryString()->links() }}</div>
    @endif
  </div>
</div>
@endsection