@extends('layouts.app')
@section('title', 'Detail Permintaan '.$permintaan->nomor_permintaan)
@section('page_title', 'Detail Permintaan')
@section('page_sub', $permintaan->nomor_permintaan)

@section('content')
@php
$badgeMap = ['draft'=>'badge-draft','diajukan'=>'badge-diajukan','disetujui'=>'badge-disetujui','ditolak'=>'badge-ditolak','dikirim_operator'=>'badge-dikirim','diproses'=>'badge-diproses','selesai'=>'badge-selesai','dibatalkan'=>'badge-dibatalkan'];
$labelMap = ['draft'=>'Draft','diajukan'=>'Menunggu Approval Pimpinan','disetujui'=>'Disetujui Pimpinan','ditolak'=>'Ditolak Pimpinan','dikirim_operator'=>'Dikirim ke Operator','diproses'=>'Sedang Diproses Operator','selesai'=>'Selesai','dibatalkan'=>'Dibatalkan'];
@endphp
<div style="padding:20px;display:flex;flex-direction:column;gap:16px;max-width:900px;margin:0 auto;">

  {{-- Header card --}}
  <div class="card" style="padding:20px 24px;">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
      <div>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
          <span class="badge {{ $badgeMap[$permintaan->status] ?? 'badge-draft' }}" style="font-size:11px;padding:4px 12px;">
            {{ $labelMap[$permintaan->status] ?? $permintaan->status }}
          </span>
          <span class="badge {{ $permintaan->jenis === 'masuk' ? 'badge-masuk' : 'badge-keluar' }}">
            {{ $permintaan->jenis === 'masuk' ? '↑ Barang Masuk' : '↓ Barang Keluar' }}
          </span>
        </div>
        <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:20px;color:#0f172a;">{{ $permintaan->nomor_permintaan }}</div>
        <div style="font-size:13px;color:#64748b;margin-top:4px;">{{ $permintaan->keperluan }}</div>
      </div>
      <div style="text-align:right;">
        <div style="font-size:11px;color:#94a3b8;">Dibuat oleh</div>
        <div style="font-weight:700;color:#0f172a;">{{ $permintaan->pembuat->nama }}</div>
        <div style="font-size:11px;color:#94a3b8;">{{ $permintaan->created_at->format('d M Y H:i') }}</div>
      </div>
    </div>

    {{-- Timeline alur --}}
    <div style="margin-top:20px;padding-top:16px;border-top:1px solid #f1f5f9;">
      <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.07em;margin-bottom:12px;">Alur Proses</div>
      <div style="display:flex;align-items:center;gap:0;">
        @php
        $steps = [
          ['key'=>['draft','diajukan','disetujui','ditolak','dikirim_operator','diproses','selesai'],'label'=>'Dibuat','sub'=>$permintaan->created_at->format('d/m/Y'),'icon'=>'edit_document'],
          ['key'=>['diajukan','disetujui','ditolak','dikirim_operator','diproses','selesai'],'label'=>'Diajukan','sub'=>$permintaan->tgl_diajukan ? \Carbon\Carbon::parse($permintaan->tgl_diajukan)->format('d/m/Y'):'','icon'=>'send'],
          ['key'=>['disetujui','dikirim_operator','diproses','selesai'],'label'=>'Disetujui','sub'=>$permintaan->tgl_disetujui ? \Carbon\Carbon::parse($permintaan->tgl_disetujui)->format('d/m/Y'):'','icon'=>'verified','danger'=>$permintaan->status==='ditolak'],
          ['key'=>['dikirim_operator','diproses','selesai'],'label'=>'Ke Operator','sub'=>$permintaan->tgl_dikirim_operator ? \Carbon\Carbon::parse($permintaan->tgl_dikirim_operator)->format('d/m/Y'):'','icon'=>'local_shipping'],
          ['key'=>['selesai'],'label'=>'Selesai','sub'=>$permintaan->tgl_selesai ? \Carbon\Carbon::parse($permintaan->tgl_selesai)->format('d/m/Y'):'','icon'=>'check_circle'],
        ];
        $statusOrder = ['draft','diajukan','disetujui','ditolak','dikirim_operator','diproses','selesai'];
        $currentIdx = array_search($permintaan->status, $statusOrder);
        @endphp
        @foreach($steps as $sidx => $step)
        @php $done = in_array($permintaan->status, $step['key']); @endphp
        <div style="display:flex;flex-direction:column;align-items:center;flex:1;">
          <div style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;
            {{ $done ? 'background:linear-gradient(135deg,#22c55e,#16a34a);box-shadow:0 2px 8px rgba(34,197,94,.3);' : 'background:#f1f5f9;border:2px solid #e2e8f0;' }}
            {{ (isset($step['danger']) && $step['danger']) ? 'background:linear-gradient(135deg,#ef4444,#dc2626)!important;' : '' }}">
            <span class="material-symbols-outlined fill-icon" style="font-size:17px;color:{{ $done ? '#fff' : '#94a3b8' }};">{{ $step['icon'] }}</span>
          </div>
          <div style="font-size:10px;font-weight:700;color:{{ $done ? '#16a34a' : '#94a3b8' }};margin-top:5px;text-align:center;">{{ $step['label'] }}</div>
          @if($step['sub'])<div style="font-size:9px;color:#94a3b8;text-align:center;">{{ $step['sub'] }}</div>@endif
        </div>
        @if(!$loop->last)
        <div style="height:2px;flex:1;background:{{ $done ? 'linear-gradient(90deg,#22c55e,#16a34a)':'#e2e8f0' }};margin-bottom:20px;"></div>
        @endif
        @endforeach
      </div>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 320px;gap:16px;">
    {{-- Daftar barang --}}
    <div class="card">
      <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;">
        <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:13px;color:#0f172a;">Daftar Barang Diminta</div>
      </div>
      <table class="tbl">
        <thead>
          <tr>
            <th>#</th>
            <th>Barang</th>
            <th>Diminta</th>
            <th>Disetujui</th>
            <th>Dieksekusi</th>
            <th>Keterangan</th>
          </tr>
        </thead>
        <tbody>
          @foreach($permintaan->details as $i => $det)
          <tr>
            <td style="color:#94a3b8;font-size:11px;">{{ $i+1 }}</td>
            <td>
              <div style="font-weight:700;font-size:12.5px;color:#0f172a;">{{ $det->barang->nama ?? '-' }}</div>
              <div style="font-size:10px;color:#94a3b8;">{{ $det->barang->kode ?? '' }}</div>
            </td>
            <td><b>{{ $det->jumlah_diminta }}</b> {{ $det->satuan }}</td>
            <td>
              @if($det->jumlah_disetujui !== null)
              <b style="color:{{ $det->jumlah_disetujui < $det->jumlah_diminta ? '#f59e0b':'#16a34a' }}">{{ $det->jumlah_disetujui }}</b> {{ $det->satuan }}
              @else <span style="color:#94a3b8;">-</span> @endif
            </td>
            <td>
              @if($det->jumlah_dieksekusi !== null)
              <b style="color:#16a34a;">{{ $det->jumlah_dieksekusi }}</b> {{ $det->satuan }}
              @else <span style="color:#94a3b8;">-</span> @endif
            </td>
            <td style="font-size:11px;color:#94a3b8;">{{ $det->keterangan ?: '-' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Panel aksi & info --}}
    <div style="display:flex;flex-direction:column;gap:12px;">

      {{-- Info --}}
      <div class="card" style="padding:16px;">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.07em;margin-bottom:10px;">Informasi</div>
        <div style="display:flex;flex-direction:column;gap:8px;">
          @foreach([
            ['Pengaju',$permintaan->pembuat->nama??'-'],
            ['Tgl Dibutuhkan',\Carbon\Carbon::parse($permintaan->tanggal_dibutuhkan)->format('d M Y')],
            ['Departemen Tujuan',$permintaan->departemen_tujuan??'-'],
            ['Catatan Manajerial',$permintaan->catatan_manajerial??'-'],
          ] as [$k,$v])
          <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;">
            <span style="font-size:11px;color:#94a3b8;flex-shrink:0;">{{ $k }}</span>
            <span style="font-size:12px;font-weight:600;color:#0f172a;text-align:right;">{{ $v }}</span>
          </div>
          @endforeach
        </div>
        @if($permintaan->catatan_pimpinan)
        <div style="margin-top:12px;padding:10px;background:#eff6ff;border-radius:8px;border-left:3px solid #3b82f6;">
          <div style="font-size:10px;font-weight:700;color:#1e40af;margin-bottom:3px;">Catatan Pimpinan</div>
          <div style="font-size:12px;color:#1e40af;">{{ $permintaan->catatan_pimpinan }}</div>
          @if($permintaan->ttd_pimpinan)
          <div style="margin-top:8px;">
            <div style="font-size:10px;color:#64748b;margin-bottom:4px;">TTD:</div>
            <img src="{{ asset('storage/'.$permintaan->ttd_pimpinan) }}" alt="TTD Pimpinan" style="max-height:60px;border:1px solid #bfdbfe;border-radius:6px;padding:4px;background:#fff;"/>
          </div>
          @endif
        </div>
        @endif
        @if($permintaan->catatan_operator)
        <div style="margin-top:8px;padding:10px;background:#f0fdf4;border-radius:8px;border-left:3px solid #22c55e;">
          <div style="font-size:10px;font-weight:700;color:#166534;margin-bottom:3px;">Catatan Operator</div>
          <div style="font-size:12px;color:#166534;">{{ $permintaan->catatan_operator }}</div>
        </div>
        @endif
      </div>

      {{-- ===== AKSI PIMPINAN ===== --}}
      @if(session('user_role') === 'pimpinan' && $permintaan->status === 'diajukan')
      <div class="card" style="padding:16px;border:2px solid #fef08a;background:#fefce8;">
        <div style="font-weight:700;font-size:13px;color:#854d0e;margin-bottom:12px;">⏳ Menunggu Persetujuan Anda</div>
        <button type="button" onclick="openModal('modal-setujui')" class="btn-green" style="width:100%;justify-content:center;margin-bottom:8px;">
          <span class="material-symbols-outlined fill-icon" style="font-size:16px;">verified</span> Setujui Permintaan
        </button>
        <button type="button" onclick="openModal('modal-tolak')" class="btn-danger" style="width:100%;justify-content:center;">
          <span class="material-symbols-outlined" style="font-size:16px;">cancel</span> Tolak Permintaan
        </button>
      </div>
      @endif

      {{-- ===== AKSI MANAJERIAL: kirim ke operator ===== --}}
      @if(session('user_role') === 'manajerial' && $permintaan->status === 'disetujui')
      <div class="card" style="padding:16px;border:2px solid #bbf7d0;background:#f0fdf4;">
        <div style="font-weight:700;font-size:13px;color:#166534;margin-bottom:4px;">✅ Disetujui Pimpinan</div>
        <div style="font-size:12px;color:#64748b;margin-bottom:12px;">Kirimkan ke Operator Gudang untuk dieksekusi.</div>
        <form method="POST" action="{{ route('permintaan.kirim_operator', $permintaan->id) }}">
          @csrf
          <button type="submit" class="btn-or" style="width:100%;justify-content:center;">
            <span class="material-symbols-outlined fill-icon" style="font-size:16px;">local_shipping</span> Kirim ke Operator
          </button>
        </form>
      </div>
      @endif

      {{-- ===== AKSI MANAJERIAL: batal/ajukan saat draft ===== --}}
      @if(session('user_role') === 'manajerial' && $permintaan->status === 'draft' && $permintaan->dibuat_oleh === session('user_id'))
      <div class="card" style="padding:16px;">
        <form method="POST" action="{{ route('permintaan.ajukan', $permintaan->id) }}" style="margin-bottom:8px;">
          @csrf
          <button type="submit" class="btn-or" style="width:100%;justify-content:center;">
            <span class="material-symbols-outlined fill-icon" style="font-size:16px;">send</span> Ajukan ke Pimpinan
          </button>
        </form>
        <form method="POST" action="{{ route('permintaan.batal', $permintaan->id) }}">
          @csrf @method('PATCH')
          <button type="submit" class="btn-danger" style="width:100%;justify-content:center;" onclick="return confirm('Batalkan permintaan ini?')">
            <span class="material-symbols-outlined" style="font-size:16px;">close</span> Batalkan
          </button>
        </form>
      </div>
      @endif

      {{-- ===== AKSI OPERATOR ===== --}}
      @if(session('user_role') === 'operator_gudang' && in_array($permintaan->status, ['dikirim_operator','diproses']))
      <div class="card" style="padding:16px;border:2px solid #fed7aa;background:#fff7ed;">
        <div style="font-weight:700;font-size:13px;color:#c2410c;margin-bottom:4px;">📦 Tugas Anda</div>
        <div style="font-size:12px;color:#64748b;margin-bottom:12px;">Cek barang & eksekusi stok sesuai permintaan yang sudah disetujui pimpinan.</div>
        <button type="button" onclick="openModal('modal-eksekusi')" class="btn-or" style="width:100%;justify-content:center;">
          <span class="material-symbols-outlined fill-icon" style="font-size:16px;">task_alt</span> Eksekusi & Update Stok
        </button>
      </div>
      @endif

      {{-- Back --}}
      <a href="{{ route('permintaan.index') }}" class="btn-ghost" style="justify-content:center;">
        <span class="material-symbols-outlined" style="font-size:16px;">arrow_back</span> Kembali
      </a>
    </div>
  </div>
</div>

{{-- Modal Setujui --}}
@if(session('user_role') === 'pimpinan' && $permintaan->status === 'diajukan')
<div class="modal" id="modal-setujui">
  <div>
    <div class="card" style="padding:24px;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
        <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:16px;color:#0f172a;">✅ Setujui Permintaan</div>
        <button onclick="closeModal('modal-setujui')" style="border:none;background:none;cursor:pointer;color:#94a3b8;font-size:20px;">✕</button>
      </div>
      <form method="POST" action="{{ route('permintaan.setujui', $permintaan->id) }}" enctype="multipart/form-data">
        @csrf
        <div style="margin-bottom:14px;">
          <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Catatan (Opsional)</label>
          <textarea name="catatan_pimpinan" class="field" placeholder="Catatan atau pesan untuk manajerial..."></textarea>
        </div>
        {{-- Jumlah override per item --}}
        <div style="margin-bottom:14px;">
          <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px;">Jumlah Disetujui (per item)</label>
          @foreach($permintaan->details as $det)
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;padding:10px;background:#f8fafc;border-radius:10px;">
            <div style="flex:1;font-size:12.5px;font-weight:600;color:#0f172a;">{{ $det->barang->nama }}</div>
            <div style="font-size:11px;color:#94a3b8;">Diminta: {{ $det->jumlah_diminta }}</div>
            <input type="number" name="jumlah_disetujui[{{ $det->id }}]" value="{{ $det->jumlah_diminta }}" min="0" max="{{ $det->jumlah_diminta }}"
              style="width:80px;padding:7px 10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;text-align:center;outline:none;"/>
            <span style="font-size:11px;color:#64748b;">{{ $det->satuan }}</span>
          </div>
          @endforeach
        </div>
        <div style="margin-bottom:16px;">
          <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Upload TTD (opsional)</label>
          <input type="file" name="ttd" accept="image/*" class="field" style="padding:7px;"/>
          <div style="font-size:11px;color:#94a3b8;margin-top:4px;">Format: JPG/PNG, max 2MB</div>
        </div>
        <div style="display:flex;gap:8px;">
          <button type="button" onclick="closeModal('modal-setujui')" class="btn-ghost" style="flex:1;justify-content:center;">Batal</button>
          <button type="submit" class="btn-green" style="flex:1;justify-content:center;">
            <span class="material-symbols-outlined fill-icon" style="font-size:16px;">verified</span> Setujui
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Tolak --}}
<div class="modal" id="modal-tolak">
  <div>
    <div class="card" style="padding:24px;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
        <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:16px;color:#991b1b;">❌ Tolak Permintaan</div>
        <button onclick="closeModal('modal-tolak')" style="border:none;background:none;cursor:pointer;color:#94a3b8;font-size:20px;">✕</button>
      </div>
      <form method="POST" action="{{ route('permintaan.tolak', $permintaan->id) }}">
        @csrf
        <div style="margin-bottom:14px;">
          <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Alasan Penolakan *</label>
          <textarea name="catatan_pimpinan" class="field" placeholder="Jelaskan alasan penolakan..." required></textarea>
        </div>
        <div style="display:flex;gap:8px;">
          <button type="button" onclick="closeModal('modal-tolak')" class="btn-ghost" style="flex:1;justify-content:center;">Batal</button>
          <button type="submit" class="btn-danger" style="flex:1;justify-content:center;">
            <span class="material-symbols-outlined" style="font-size:16px;">cancel</span> Tolak
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif

{{-- Modal Eksekusi Operator --}}
@if(session('user_role') === 'operator_gudang' && in_array($permintaan->status, ['dikirim_operator','diproses']))
<div class="modal" id="modal-eksekusi">
  <div style="max-width:620px;">
    <div class="card" style="padding:24px;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
        <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:16px;color:#0f172a;">📦 Eksekusi Stok</div>
        <button onclick="closeModal('modal-eksekusi')" style="border:none;background:none;cursor:pointer;color:#94a3b8;font-size:20px;">✕</button>
      </div>
      <div class="alert-info" style="margin-bottom:14px;">
        <span class="material-symbols-outlined fill-icon" style="font-size:16px;color:#1d4ed8;flex-shrink:0;">info</span>
        Pastikan barang sudah dicek secara fisik sebelum eksekusi. Stok akan otomatis {{ $permintaan->jenis === 'masuk' ? 'bertambah' : 'berkurang' }}.
      </div>
      <form method="POST" action="{{ route('permintaan.eksekusi', $permintaan->id) }}">
        @csrf
        @foreach($permintaan->details as $det)
        <div style="padding:12px;background:#f8fafc;border-radius:10px;margin-bottom:8px;">
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <div style="flex:1;">
              <div style="font-size:13px;font-weight:700;color:#0f172a;">{{ $det->barang->nama }}</div>
              <div style="font-size:11px;color:#94a3b8;">Stok saat ini: <b>{{ $det->barang->stok_tersedia }}</b> {{ $det->satuan }}</div>
            </div>
            <div style="text-align:right;">
              <div style="font-size:11px;color:#94a3b8;">Disetujui</div>
              <div style="font-weight:800;font-size:15px;color:#0f172a;">{{ $det->jumlah_disetujui ?? $det->jumlah_diminta }} {{ $det->satuan }}</div>
            </div>
          </div>
          <div style="display:flex;align-items:center;gap:8px;">
            <label style="font-size:11px;font-weight:700;color:#94a3b8;flex-shrink:0;">Jumlah aktual:</label>
            <input type="number" name="jumlah_eksekusi[{{ $det->id }}]" value="{{ $det->jumlah_disetujui ?? $det->jumlah_diminta }}" min="0"
              style="flex:1;padding:7px 10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;"/>
            <span style="font-size:11px;color:#64748b;">{{ $det->satuan }}</span>
          </div>
        </div>
        @endforeach
        <div style="margin-top:14px;">
          <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Catatan Operator</label>
          <textarea name="catatan_operator" class="field" placeholder="Catatan pelaksanaan, kondisi barang, dll..."></textarea>
        </div>
        <div style="display:flex;gap:8px;margin-top:16px;">
          <button type="button" onclick="closeModal('modal-eksekusi')" class="btn-ghost" style="flex:1;justify-content:center;">Batal</button>
          <button type="submit" class="btn-or" style="flex:1;justify-content:center;">
            <span class="material-symbols-outlined fill-icon" style="font-size:16px;">task_alt</span> Konfirmasi Eksekusi
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif

@endsection