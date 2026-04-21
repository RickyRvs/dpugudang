@extends('layouts.app')
@section('title', 'Buat Stock Opname')
@section('page_title', 'Buat Stock Opname')
@section('page_sub', 'Pengecekan stok fisik vs sistem')

@section('content')
<div style="padding:20px;">
  <div style="max-width:900px;margin:0 auto;">
    <form method="POST" action="{{ route('opname.store') }}" id="form-opname">
      @csrf

      <div class="card" style="padding:24px;margin-bottom:16px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid #f1f5f9;">
          <div style="width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,#f97316,#ea580c);display:flex;align-items:center;justify-content:center;">
            <span class="material-symbols-outlined fill-icon" style="color:#fff;font-size:17px;">fact_check</span>
          </div>
          <div>
            <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:14px;color:#0f172a;">Informasi Opname</div>
          </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Tanggal Opname *</label>
            <input type="date" name="tanggal" class="field" value="{{ old('tanggal', date('Y-m-d')) }}" required/>
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Keterangan</label>
            <input type="text" name="keterangan" class="field" value="{{ old('keterangan') }}" placeholder="Keterangan opname (opsional)"/>
          </div>
        </div>
      </div>

      <div class="card" style="padding:24px;margin-bottom:16px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid #f1f5f9;">
          <div>
            <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:14px;color:#0f172a;">Daftar Barang yang Dicek</div>
            <div style="font-size:11px;color:#94a3b8;">Masukkan stok fisik hasil penghitungan</div>
          </div>
          <button type="button" onclick="tambahBarang()" class="btn-or" style="font-size:12px;padding:7px 14px;">
            <span class="material-symbols-outlined fill-icon" style="font-size:15px;">add</span> Tambah Barang
          </button>
        </div>

        <div class="alert-info" style="margin-bottom:14px;">
          <span class="material-symbols-outlined fill-icon" style="font-size:16px;color:#1d4ed8;flex-shrink:0;">info</span>
          Stok sistem diambil otomatis. Isi stok fisik sesuai hasil penghitungan aktual di gudang.
        </div>

        <table class="tbl">
          <thead>
            <tr>
              <th style="width:35%;">Barang</th>
              <th style="width:15%;text-align:center;">Stok Sistem</th>
              <th style="width:15%;text-align:center;">Stok Fisik *</th>
              <th style="width:25%;">Keterangan</th>
              <th style="width:10%;"></th>
            </tr>
          </thead>
          <tbody id="tbody-opname"></tbody>
        </table>
        <div id="empty-opname" style="padding:30px;text-align:center;color:#94a3b8;font-size:13px;">
          <span class="material-symbols-outlined" style="font-size:32px;display:block;margin-bottom:8px;color:#e2e8f0;">add_task</span>
          Klik "Tambah Barang" untuk memulai
        </div>
      </div>

      <div style="display:flex;gap:10px;justify-content:flex-end;">
        <a href="{{ route('opname.index') }}" class="btn-ghost">
          <span class="material-symbols-outlined" style="font-size:16px;">arrow_back</span> Batal
        </a>
        <button type="submit" class="btn-or">
          <span class="material-symbols-outlined fill-icon" style="font-size:16px;">save</span> Simpan Opname
        </button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
var rowCount = 0;

@php
$barangJson = $barangList->map(function($b) {
    return [
        'id'     => $b->id,
        'kode'   => $b->kode,
        'nama'   => $b->nama,
        'stok'   => $b->stok_tersedia,
        'satuan' => $b->satuan,
    ];
})->values()->toArray();
@endphp
var barangData = {!! json_encode($barangJson) !!};

function tambahBarang() {
  document.getElementById('empty-opname').style.display = 'none';
  var i = rowCount++;
  var opts = '<option value="">-- Pilih Barang --</option>';
  barangData.forEach(function(b) {
    opts += '<option value="'+b.id+'" data-stok="'+b.stok+'" data-satuan="'+b.satuan+'">'+b.kode+' - '+b.nama+'</option>';
  });
  var row = document.createElement('tr');
  row.id = 'opname-row-'+i;
  row.innerHTML =
    '<td><select name="items['+i+'][barang_id]" class="field" required onchange="isiStok(this,'+i+')">'+opts+'</select></td>'
    +'<td style="text-align:center;"><span id="stok-sistem-'+i+'" style="font-family:\'Sora\',sans-serif;font-size:15px;font-weight:700;color:#94a3b8;">-</span></td>'
    +'<td><input type="number" name="items['+i+'][stok_fisik]" class="field" id="stok-fisik-'+i+'" min="0" required placeholder="0" style="text-align:center;"/></td>'
    +'<td><input type="text" name="items['+i+'][keterangan]" class="field" placeholder="Catatan..."/></td>'
    +'<td><button type="button" onclick="hapusRow('+i+')" style="width:28px;height:28px;border-radius:8px;background:#fef2f2;border:1px solid #fecaca;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#ef4444;"><span class="material-symbols-outlined" style="font-size:15px;">delete</span></button></td>';
  document.getElementById('tbody-opname').appendChild(row);
}

function isiStok(sel, i) {
  var opt = sel.options[sel.selectedIndex];
  var stok = opt.dataset.stok || '-';
  document.getElementById('stok-sistem-'+i).textContent = stok;
  document.getElementById('stok-fisik-'+i).value = stok;
}

function hapusRow(i) {
  var row = document.getElementById('opname-row-'+i);
  if (row) row.remove();
  if (document.getElementById('tbody-opname').children.length === 0) {
    document.getElementById('empty-opname').style.display = 'block';
  }
}

document.getElementById('form-opname').addEventListener('submit', function(e) {
  if (document.getElementById('tbody-opname').children.length === 0) {
    e.preventDefault();
    alert('Tambahkan minimal 1 barang untuk dicek!');
  }
});
</script>
@endpush
@endsection