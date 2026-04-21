<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $permintaan->nomor_permintaan }} — Surat Permintaan Barang</title>
  <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Sora:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      --ink: #1a1a2e;
      --muted: #64748b;
      --light: #f8fafc;
      --border: #e2e8f0;
      --orange: #f97316;
      --green: #16a34a;
      --red: #dc2626;
      --blue: #1d4ed8;
      --yellow-bg: #fefce8;
      --yellow-border: #fde047;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'EB Garamond', serif;
      background: #eef2f7;
      color: var(--ink);
      min-height: 100vh;
    }

    /* ── ACTION BAR ─────────────────────────────── */
    .action-bar {
      background: #fff;
      border-bottom: 1px solid var(--border);
      padding: 10px 24px;
      display: flex;
      align-items: center;
      gap: 10px;
      position: sticky;
      top: 0;
      z-index: 200;
      box-shadow: 0 1px 4px rgba(0,0,0,.06);
    }
    .action-bar .title {
      font-family: 'Sora', sans-serif;
      font-weight: 700;
      font-size: 13px;
      color: var(--ink);
      flex: 1;
    }
    .action-bar .sub { font-size: 11px; color: var(--muted); font-weight: 400; }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 7px 16px;
      border-radius: 8px;
      font-family: 'Sora', sans-serif;
      font-size: 12px;
      font-weight: 600;
      cursor: pointer;
      border: none;
      text-decoration: none;
      transition: opacity .15s;
      white-space: nowrap;
    }
    .btn:hover { opacity: .85; }
    .btn-ghost { background: var(--light); color: #475569; border: 1px solid var(--border); }
    .btn-green { background: var(--green); color: #fff; }
    .btn-red   { background: var(--red);   color: #fff; }
    .btn-or    { background: var(--orange); color: #fff; }

    /* ── WRAP ──────────────────────────────────── */
    .wrap { max-width: 860px; margin: 32px auto 64px; padding: 0 20px; }

    /* ── APPROVAL PANEL ────────────────────────── */
    .approval-panel {
      background: var(--yellow-bg);
      border: 2px solid var(--yellow-border);
      border-radius: 14px;
      padding: 22px 24px;
      margin-bottom: 24px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }
    .approval-panel h3 {
      font-family: 'Sora', sans-serif;
      font-size: 13px;
      font-weight: 800;
      color: #854d0e;
      margin-bottom: 14px;
      grid-column: 1/-1;
    }
    .canvas-wrap { display: flex; flex-direction: column; gap: 8px; }
    .canvas-wrap label {
      font-family: 'Sora', sans-serif;
      font-size: 11px;
      font-weight: 700;
      color: #92400e;
      text-transform: uppercase;
      letter-spacing: .06em;
    }
    #ttd-canvas {
      background: #fffbeb;
      border: 1.5px dashed #f59e0b;
      border-radius: 8px;
      cursor: crosshair;
      display: block;
      width: 100%;
      touch-action: none;
    }
    .canvas-actions { display: flex; gap: 8px; align-items: center; }
    .canvas-hint { font-size: 11px; color: #92400e; font-style: italic; }

    .form-panel { display: flex; flex-direction: column; gap: 12px; }
    .form-panel label {
      font-family: 'Sora', sans-serif;
      font-size: 11px;
      font-weight: 700;
      color: #92400e;
      text-transform: uppercase;
      letter-spacing: .06em;
      display: block;
      margin-bottom: 4px;
    }
    .form-panel textarea, .form-panel input[type=number] {
      width: 100%;
      padding: 8px 10px;
      border: 1.5px solid #f59e0b;
      border-radius: 8px;
      font-size: 13px;
      font-family: 'Sora', sans-serif;
      background: #fffbeb;
      outline: none;
      resize: none;
      transition: border-color .15s;
    }
    .form-panel textarea:focus, .form-panel input[type=number]:focus {
      border-color: var(--orange);
    }
    .jumlah-row {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 8px 10px;
      background: #fff;
      border-radius: 8px;
      border: 1px solid var(--border);
      margin-bottom: 4px;
    }
    .jumlah-row .nama { flex: 1; font-size: 12.5px; font-weight: 600; color: var(--ink); }
    .jumlah-row .req { font-size: 11px; color: var(--muted); }
    .jumlah-row input {
      width: 64px !important;
      text-align: center;
      padding: 5px 6px !important;
    }
    .jumlah-row .sat { font-size: 11px; color: var(--muted); }

    .tolak-panel { grid-column: 1/-1; display: none; }
    .tolak-panel textarea { border-color: #fca5a5; background: #fff5f5; }
    .btn-row { display: flex; gap: 8px; grid-column: 1/-1; }

    /* ── INFO STRIP (for operator/manajerial) ─── */
    .info-strip {
      border-radius: 12px;
      padding: 14px 18px;
      margin-bottom: 20px;
      font-family: 'Sora', sans-serif;
      font-size: 13px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .info-strip.green { background: #f0fdf4; border: 1.5px solid #86efac; color: #166534; }
    .info-strip.blue  { background: #eff6ff; border: 1.5px solid #93c5fd; color: #1e40af; }

    /* ── LETTER ────────────────────────────────── */
    .letter {
      background: #fff;
      box-shadow: 0 4px 30px rgba(0,0,0,.10), 0 1px 4px rgba(0,0,0,.06);
      padding: 64px 72px;
      position: relative;
      overflow: hidden;
    }

    /* Watermark */
    .letter::before {
      content: attr(data-status);
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%,-50%) rotate(-35deg);
      font-family: 'Sora', sans-serif;
      font-size: 80px;
      font-weight: 900;
      letter-spacing: 6px;
      opacity: .04;
      color: #000;
      text-transform: uppercase;
      pointer-events: none;
      white-space: nowrap;
    }

    /* KOP */
    .kop {
      display: flex;
      align-items: center;
      gap: 18px;
      padding-bottom: 16px;
      border-bottom: 3px double #1a1a2e;
      margin-bottom: 28px;
    }
    .kop-logo {
      width: 72px;
      height: 72px;
      border-radius: 50%;
      background: linear-gradient(135deg,#f97316,#c2410c);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .kop-logo span {
      color: #fff;
      font-family: 'Sora', sans-serif;
      font-size: 30px;
      font-weight: 900;
    }
    .kop-text .kantor {
      font-family: 'Sora', sans-serif;
      font-weight: 800;
      font-size: 17px;
      text-transform: uppercase;
      letter-spacing: .04em;
    }
    .kop-text .alamat { font-size: 12px; color: var(--muted); margin-top: 2px; }

    /* JUDUL */
    .judul {
      text-align: center;
      margin-bottom: 24px;
    }
    .judul h2 {
      font-family: 'Sora', sans-serif;
      font-size: 14px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: .1em;
    }
    .judul .underline {
      width: 60px;
      height: 3px;
      background: var(--orange);
      margin: 8px auto 0;
      border-radius: 2px;
    }

    /* META */
    .meta-table { font-size: 13.5px; margin-bottom: 24px; border-collapse: collapse; }
    .meta-table td { padding: 3px 0; vertical-align: top; }
    .meta-table td:first-child { width: 150px; color: var(--muted); }
    .meta-table td:nth-child(2) { width: 12px; padding: 0 8px; color: var(--muted); }
    .meta-table td:last-child { font-weight: 600; }

    /* KEPADA */
    .kepada { font-size: 13.5px; margin-bottom: 24px; }
    .kepada .label { color: var(--muted); margin-bottom: 4px; }
    .kepada .nama { font-weight: 700; }

    /* BODY TEXT */
    .body-text {
      font-size: 14px;
      line-height: 1.9;
      margin-bottom: 16px;
      text-align: justify;
      text-indent: 2.5em;
    }
    .body-text.no-indent { text-indent: 0; }

    /* TABLE BARANG */
    .tbl-barang {
      width: 100%;
      border-collapse: collapse;
      font-size: 12.5px;
      margin: 20px 0 24px;
    }
    .tbl-barang thead tr {
      background: #f1f5f9;
    }
    .tbl-barang th {
      border: 1px solid #cbd5e1;
      padding: 9px 12px;
      font-family: 'Sora', sans-serif;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .05em;
      text-align: center;
      color: var(--muted);
    }
    .tbl-barang td {
      border: 1px solid #cbd5e1;
      padding: 9px 12px;
      vertical-align: middle;
    }
    .tbl-barang td:nth-child(1) { text-align: center; font-family: 'Sora', sans-serif; font-size: 11px; color: var(--muted); }
    .tbl-barang td:nth-child(3) { text-align: center; font-family: 'Sora', sans-serif; font-size: 11px; color: var(--muted); }
    .tbl-barang td.num { text-align: center; font-weight: 700; }
    .tbl-barang .nama-barang { font-weight: 700; font-size: 13px; }
    .tbl-barang .kode-barang { font-size: 10.5px; color: var(--muted); margin-top: 2px; font-family: 'Sora', monospace; }

    /* Badge inside table */
    .num-approved {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 700;
      font-family: 'Sora', sans-serif;
    }
    .num-approved.full  { background: #dcfce7; color: #166534; }
    .num-approved.partial { background: #fef9c3; color: #854d0e; }
    .num-approved.done  { background: #dbeafe; color: #1e40af; }

    /* CATATAN */
    .note-box {
      padding: 12px 16px;
      border-radius: 8px;
      font-size: 13px;
      margin-bottom: 12px;
    }
    .note-box.blue   { background: #eff6ff; border-left: 3px solid var(--blue); color: #1e3a8a; }
    .note-box.green  { background: #f0fdf4; border-left: 3px solid var(--green); color: #14532d; }
    .note-box .note-label {
      font-family: 'Sora', sans-serif;
      font-size: 10.5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .07em;
      margin-bottom: 4px;
    }

    /* TTD SECTION */
    .ttd-section {
      display: flex;
      justify-content: space-between;
      margin-top: 48px;
      gap: 20px;
    }
    .ttd-box { text-align: center; min-width: 180px; }
    .ttd-box .ttd-title {
      font-size: 13px;
      margin-bottom: 4px;
    }
    .ttd-box .ttd-place {
      font-size: 12px;
      color: var(--muted);
      margin-bottom: 60px;
    }
    .ttd-box img {
      display: block;
      max-height: 70px;
      max-width: 180px;
      margin: 0 auto 4px;
      object-fit: contain;
    }
    .ttd-box .empty-space { height: 70px; }
    .ttd-box .ttd-line {
      border-top: 1px solid var(--ink);
      padding-top: 6px;
    }
    .ttd-box .ttd-nama { font-weight: 700; font-size: 13.5px; }
    .ttd-box .ttd-role { font-size: 11.5px; color: var(--muted); text-transform: capitalize; }
    .ttd-box .ttd-tanggal { font-size: 11px; color: var(--muted); margin-top: 3px; }

    .ttd-pending {
      height: 70px;
      border: 1.5px dashed #cbd5e1;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .ttd-pending span { font-size: 11px; color: var(--muted); font-style: italic; }

    /* ── PRINT ────────────────────────────────── */
    @media print {
      .action-bar, .approval-panel, .info-strip, .no-print { display: none !important; }
      body { background: #fff; }
      .wrap { margin: 0; padding: 0; max-width: 100%; }
      .letter { box-shadow: none; padding: 40px 52px; }
    }
  </style>
</head>
<body>

{{-- ── ACTION BAR ──────────────────────────── --}}
<div class="action-bar no-print">
  <a href="{{ route('permintaan.show', $permintaan->id) }}" class="btn btn-ghost">← Kembali</a>
  <div class="title">
    Surat Permintaan Barang
    <span class="sub">/ {{ $permintaan->nomor_permintaan }}</span>
  </div>
  @php
    $statusLabel = ['draft'=>'Draft','diajukan'=>'Menunggu TTD','disetujui'=>'Disetujui','ditolak'=>'Ditolak','dikirim_operator'=>'Di Operator','diproses'=>'Diproses','selesai'=>'Selesai','dibatalkan'=>'Dibatalkan'];
    $statusColor = ['draft'=>'#94a3b8','diajukan'=>'#d97706','disetujui'=>'#16a34a','ditolak'=>'#dc2626','dikirim_operator'=>'#2563eb','diproses'=>'#7c3aed','selesai'=>'#16a34a','dibatalkan'=>'#9ca3af'];
  @endphp
  <span style="font-family:'Sora',sans-serif;font-size:11px;font-weight:700;padding:5px 14px;border-radius:999px;background:{{ $statusColor[$permintaan->status]??'#94a3b8' }}1a;color:{{ $statusColor[$permintaan->status]??'#94a3b8' }};border:1px solid {{ $statusColor[$permintaan->status]??'#94a3b8' }}33;">
    {{ $statusLabel[$permintaan->status] ?? $permintaan->status }}
  </span>
  <button onclick="window.print()" class="btn btn-ghost">🖨️ Cetak / PDF</button>
</div>

<div class="wrap">

  {{-- ── PANEL PIMPINAN: Tanda Tangan & Setujui ── --}}
  @if(session('user_role') === 'pimpinan' && $permintaan->status === 'diajukan')
  <div class="approval-panel">
    <h3>⏳ Surat ini membutuhkan Tanda Tangan dan Persetujuan Anda</h3>

    {{-- Canvas TTD --}}
    <div class="canvas-wrap">
      <label>Tanda Tangan Digital</label>
      <canvas id="ttd-canvas" width="400" height="130"></canvas>
      <div class="canvas-actions">
        <button type="button" class="btn btn-ghost" onclick="clearCanvas()" style="font-size:11px;padding:4px 12px;">↺ Hapus</button>
        <span class="canvas-hint">Tanda tangan di atas kotak menggunakan mouse / jari</span>
      </div>
    </div>

    {{-- Form Setujui --}}
    <div class="form-panel">
      <form method="POST" action="{{ route('permintaan.setujui', $permintaan->id) }}" id="form-setujui" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="ttd_canvas" id="ttd-canvas-data">

        <div>
          <label>Jumlah Disetujui per Item</label>
          @foreach($permintaan->details as $det)
          <div class="jumlah-row">
            <span class="nama">{{ $det->barang->nama }}</span>
            <span class="req">Diminta: {{ $det->jumlah_diminta }}</span>
            <input type="number" name="jumlah_disetujui[{{ $det->id }}]"
              value="{{ $det->jumlah_diminta }}" min="0" max="{{ $det->jumlah_diminta }}">
            <span class="sat">{{ $det->satuan }}</span>
          </div>
          @endforeach
        </div>

        <div>
          <label>Catatan Pimpinan (Opsional)</label>
          <textarea name="catatan_pimpinan" rows="2" placeholder="Catatan atau pesan untuk manajerial..."></textarea>
        </div>

        <button type="button" onclick="submitApproval()" class="btn btn-green" style="width:100%;justify-content:center;">
          ✅ Setujui & Tanda Tangani
        </button>
      </form>

      {{-- Tolak --}}
      <button type="button" class="btn btn-red" style="width:100%;justify-content:center;margin-top:4px;" onclick="toggleTolak()">
        ❌ Tolak Permintaan
      </button>
    </div>

    {{-- Panel Tolak (hidden) --}}
    <div class="tolak-panel" id="tolak-panel">
      <form method="POST" action="{{ route('permintaan.tolak', $permintaan->id) }}">
        @csrf
        <label style="font-family:'Sora',sans-serif;font-size:11px;font-weight:700;color:#991b1b;text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:6px;">
          Alasan Penolakan *
        </label>
        <textarea name="catatan_pimpinan" rows="3" required
          style="width:100%;padding:10px;border:1.5px solid #fca5a5;border-radius:8px;font-family:'Sora',sans-serif;font-size:13px;background:#fff5f5;outline:none;resize:none;margin-bottom:10px;"
          placeholder="Jelaskan alasan penolakan secara singkat..."></textarea>
        <div style="display:flex;gap:8px;">
          <button type="button" class="btn btn-ghost" onclick="toggleTolak()" style="flex:1;justify-content:center;">Batal</button>
          <button type="submit" class="btn btn-red" style="flex:1;justify-content:center;">Konfirmasi Tolak</button>
        </div>
      </form>
    </div>

  </div>
  @endif

  {{-- ── INFO STRIP untuk Operator ── --}}
  @if(session('user_role') === 'operator_gudang' && in_array($permintaan->status, ['dikirim_operator','diproses','selesai']))
  <div class="info-strip blue no-print">
    ℹ️ Ini adalah dokumen resmi permintaan barang yang telah <strong>disetujui dan ditandatangani</strong> oleh pimpinan. Gunakan sebagai acuan saat eksekusi stok.
  </div>
  @endif

  {{-- ── INFO STRIP Manajerial setelah disetujui ── --}}
  @if(session('user_role') === 'manajerial' && $permintaan->status === 'disetujui')
  <div class="info-strip green no-print">
    ✅ Surat telah <strong>disetujui dan ditandatangani</strong> pimpinan. Anda dapat meneruskannya ke Operator Gudang dari halaman detail.
  </div>
  @endif

  {{-- ══════════════════════════════════════════ --}}
  {{--                  THE LETTER               --}}
  {{-- ══════════════════════════════════════════ --}}
  @php
    $watermarkMap = ['selesai'=>'SELESAI','ditolak'=>'DITOLAK','dibatalkan'=>'BATAL'];
    $watermark = $watermarkMap[$permintaan->status] ?? '';
  @endphp
  <div class="letter" data-status="{{ $watermark }}">

    {{-- KOP SURAT --}}
    <div class="kop">
      <div class="kop-logo">
        <span>{{ strtoupper(substr($permintaan->kantor->nama ?? 'G', 0, 1)) }}</span>
      </div>
      <div class="kop-text">
        <div class="kantor">{{ strtoupper($permintaan->kantor->nama ?? config('app.name', 'Perusahaan')) }}</div>
        <div class="alamat">{{ $permintaan->kantor->alamat ?? 'Sistem Manajemen Gudang' }}</div>
      </div>
    </div>

    {{-- JUDUL --}}
    <div class="judul">
      <h2>Surat Permintaan {{ $permintaan->jenis === 'masuk' ? 'Pengadaan' : 'Pengeluaran' }} Barang</h2>
      <div class="underline"></div>
    </div>

    {{-- META INFO --}}
    <table class="meta-table">
      <tr>
        <td>Nomor Surat</td>
        <td>:</td>
        <td><strong>{{ $permintaan->nomor_permintaan }}</strong></td>
      </tr>
      <tr>
        <td>Tanggal Dibuat</td>
        <td>:</td>
        <td>{{ $permintaan->created_at->translatedFormat('d F Y') }}</td>
      </tr>
      <tr>
        <td>Perihal</td>
        <td>:</td>
        <td>Permintaan {{ $permintaan->jenis === 'masuk' ? 'Pengadaan' : 'Pengeluaran' }} Barang</td>
      </tr>
      <tr>
        <td>Tanggal Dibutuhkan</td>
        <td>:</td>
        <td>{{ \Carbon\Carbon::parse($permintaan->tanggal_dibutuhkan)->translatedFormat('d F Y') }}</td>
      </tr>
      @if($permintaan->departemen_tujuan)
      <tr>
        <td>Departemen / Tujuan</td>
        <td>:</td>
        <td>{{ $permintaan->departemen_tujuan }}</td>
      </tr>
      @endif
    </table>

    {{-- KEPADA --}}
    <div class="kepada">
      <div class="label">Kepada Yth.</div>
      <div class="nama">Pimpinan {{ $permintaan->kantor->nama ?? '' }}</div>
      <div style="font-size:13px;margin-top:2px;">di Tempat</div>
    </div>

    {{-- BODY --}}
    <div class="body-text">
      Dengan hormat, yang bertanda tangan di bawah ini:
    </div>

    <table class="meta-table" style="margin-left:2.5em;margin-bottom:16px;">
      <tr>
        <td>Nama</td>
        <td>:</td>
        <td><strong>{{ $permintaan->pembuat->nama }}</strong></td>
      </tr>
      <tr>
        <td>Jabatan</td>
        <td>:</td>
        <td>{{ ucwords(str_replace('_',' ',$permintaan->pembuat->role ?? '-')) }}</td>
      </tr>
      <tr>
        <td>Unit / Kantor</td>
        <td>:</td>
        <td>{{ $permintaan->kantor->nama ?? '-' }}</td>
      </tr>
    </table>

    <div class="body-text">
      dengan ini mengajukan permohonan
      <strong>{{ $permintaan->jenis === 'masuk' ? 'pengadaan / penerimaan' : 'pengeluaran / pemakaian' }} barang</strong>
      @if($permintaan->departemen_tujuan)
      untuk keperluan <strong>{{ $permintaan->departemen_tujuan }}</strong>
      @endif
      dengan rincian sebagaimana terlampir berikut ini:
    </div>

    {{-- TABLE BARANG --}}
    <table class="tbl-barang">
      <thead>
        <tr>
          <th width="5%">No.</th>
          <th style="text-align:left;">Nama Barang</th>
          <th width="11%">Kode</th>
          <th width="13%">Jml Diminta</th>
          @if(!in_array($permintaan->status, ['draft','diajukan']))
          <th width="13%">Jml Disetujui</th>
          @endif
          @if($permintaan->status === 'selesai')
          <th width="13%">Jml Dieksekusi</th>
          @endif
          <th width="9%">Satuan</th>
          <th style="text-align:left;">Keterangan</th>
        </tr>
      </thead>
      <tbody>
        @foreach($permintaan->details as $i => $det)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td>
            <div class="nama-barang">{{ $det->barang->nama ?? '-' }}</div>
            <div class="kode-barang">{{ $det->barang->kode ?? '' }}</div>
          </td>
          <td>{{ $det->barang->kode ?? '-' }}</td>
          <td class="num">{{ $det->jumlah_diminta }}</td>
          @if(!in_array($permintaan->status, ['draft','diajukan']))
          <td class="num">
            @if($det->jumlah_disetujui !== null)
              @php $cls = $det->jumlah_disetujui >= $det->jumlah_diminta ? 'full' : 'partial'; @endphp
              <span class="num-approved {{ $cls }}">{{ $det->jumlah_disetujui }}</span>
            @else
              <span style="color:#94a3b8;">—</span>
            @endif
          </td>
          @endif
          @if($permintaan->status === 'selesai')
          <td class="num">
            @if($det->jumlah_dieksekusi !== null)
              <span class="num-approved done">{{ $det->jumlah_dieksekusi }}</span>
            @else
              <span style="color:#94a3b8;">—</span>
            @endif
          </td>
          @endif
          <td class="num">{{ $det->satuan }}</td>
          <td style="font-size:12px;color:#64748b;">{{ $det->keterangan ?: '—' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    {{-- KEPERLUAN --}}
    <div class="body-text no-indent">
      <strong>Keperluan / Alasan Permintaan:</strong><br>
      {{ $permintaan->keperluan }}
    </div>

    @if($permintaan->catatan_manajerial)
    <div class="body-text no-indent">
      <strong>Catatan Tambahan:</strong><br>
      {{ $permintaan->catatan_manajerial }}
    </div>
    @endif

    <div class="body-text">
      Demikian surat permintaan ini kami sampaikan. Besar harapan kami agar permohonan ini dapat diproses dengan sebaik-baiknya. Atas perhatian dan persetujuan Bapak/Ibu Pimpinan, kami mengucapkan terima kasih.
    </div>

    {{-- CATATAN PIMPINAN --}}
    @if($permintaan->catatan_pimpinan)
    <div class="note-box blue">
      <div class="note-label">Catatan Pimpinan</div>
      {{ $permintaan->catatan_pimpinan }}
    </div>
    @endif

    {{-- CATATAN OPERATOR --}}
    @if($permintaan->catatan_operator)
    <div class="note-box green">
      <div class="note-label">Catatan Pelaksanaan Operator</div>
      {{ $permintaan->catatan_operator }}
    </div>
    @endif

    {{-- TTD SECTION --}}
    <div class="ttd-section">

      {{-- Pengaju (Manajerial) --}}
      <div class="ttd-box">
        <div class="ttd-title">Hormat kami,</div>
        <div class="ttd-place">{{ $permintaan->kantor->nama ?? '-' }}, {{ $permintaan->created_at->format('d M Y') }}</div>
        <div class="ttd-line">
          <div class="ttd-nama">{{ $permintaan->pembuat->nama }}</div>
          <div class="ttd-role">{{ ucwords(str_replace('_',' ',$permintaan->pembuat->role ?? '')) }}</div>
        </div>
      </div>

      {{-- Pimpinan --}}
      <div class="ttd-box">
        <div class="ttd-title">Menyetujui,</div>
        <div class="ttd-place">
          @if($permintaan->tgl_disetujui)
            {{ $permintaan->kantor->nama ?? '-' }}, {{ $permintaan->tgl_disetujui->format('d M Y') }}
          @else
            &nbsp;
          @endif
        </div>

        @if($permintaan->ttd_pimpinan)
          <img src="{{ asset('storage/'.$permintaan->ttd_pimpinan) }}" alt="TTD Pimpinan">
          <div class="ttd-line">
            <div class="ttd-nama">{{ $permintaan->nama_ttd_pimpinan ?? $permintaan->penyetuju->nama ?? '-' }}</div>
            <div class="ttd-role">Pimpinan</div>
            @if($permintaan->tgl_disetujui)
            <div class="ttd-tanggal">{{ $permintaan->tgl_disetujui->format('d M Y, H:i') }} WIB</div>
            @endif
          </div>
        @elseif($permintaan->status === 'ditolak')
          <div class="ttd-pending" style="border-color:#fca5a5;">
            <span style="color:#dc2626;">❌ Ditolak</span>
          </div>
          <div class="ttd-line">
            <div class="ttd-nama">{{ $permintaan->penyetuju->nama ?? '-' }}</div>
            <div class="ttd-role">Pimpinan</div>
          </div>
        @else
          <div class="ttd-pending">
            <span>Menunggu tanda tangan...</span>
          </div>
          <div class="ttd-line" style="color:#94a3b8;">
            <div class="ttd-nama" style="font-weight:400;">.....................................</div>
            <div class="ttd-role">Pimpinan</div>
          </div>
        @endif
      </div>

    </div>

  </div>{{-- /letter --}}
</div>{{-- /wrap --}}

<script>
// ── Canvas Signature ──────────────────────────────
const canvas = document.getElementById('ttd-canvas');
if (canvas) {
  const ctx = canvas.getContext('2d');
  let drawing = false;
  let lastX = 0, lastY = 0;

  // Scale canvas for retina/device pixel ratio
  const dpr = window.devicePixelRatio || 1;
  const rect = canvas.getBoundingClientRect();
  canvas.width  = rect.width  * dpr || 400;
  canvas.height = 130 * dpr;
  canvas.style.height = '130px';
  ctx.scale(dpr, dpr);

  ctx.strokeStyle = '#1e3a5f';
  ctx.lineWidth   = 2;
  ctx.lineCap     = 'round';
  ctx.lineJoin    = 'round';

  function getPos(e) {
    const r = canvas.getBoundingClientRect();
    if (e.touches) {
      return {
        x: e.touches[0].clientX - r.left,
        y: e.touches[0].clientY - r.top
      };
    }
    return { x: e.clientX - r.left, y: e.clientY - r.top };
  }

  canvas.addEventListener('mousedown', e => {
    drawing = true;
    const p = getPos(e);
    ctx.beginPath();
    ctx.moveTo(p.x, p.y);
    lastX = p.x; lastY = p.y;
  });
  canvas.addEventListener('mousemove', e => {
    if (!drawing) return;
    const p = getPos(e);
    ctx.lineTo(p.x, p.y);
    ctx.stroke();
  });
  canvas.addEventListener('mouseup',    () => drawing = false);
  canvas.addEventListener('mouseleave', () => drawing = false);

  canvas.addEventListener('touchstart', e => {
    e.preventDefault();
    drawing = true;
    const p = getPos(e);
    ctx.beginPath();
    ctx.moveTo(p.x, p.y);
  }, { passive: false });
  canvas.addEventListener('touchmove', e => {
    e.preventDefault();
    if (!drawing) return;
    const p = getPos(e);
    ctx.lineTo(p.x, p.y);
    ctx.stroke();
  }, { passive: false });
  canvas.addEventListener('touchend', () => drawing = false);
}

function clearCanvas() {
  const c = document.getElementById('ttd-canvas');
  if (!c) return;
  const dpr = window.devicePixelRatio || 1;
  c.getContext('2d').clearRect(0, 0, c.width / dpr, c.height / dpr);
}

function isCanvasBlank(canvas) {
  const blank = document.createElement('canvas');
  blank.width  = canvas.width;
  blank.height = canvas.height;
  return canvas.toDataURL() === blank.toDataURL();
}

function submitApproval() {
  const c = document.getElementById('ttd-canvas');
  if (c && !isCanvasBlank(c)) {
    document.getElementById('ttd-canvas-data').value = c.toDataURL('image/png');
  }
  document.getElementById('form-setujui').submit();
}

function toggleTolak() {
  const panel = document.getElementById('tolak-panel');
  panel.style.display = panel.style.display === 'block' ? 'none' : 'block';
}
</script>
</body>
</html>