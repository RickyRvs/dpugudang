@extends('layouts.app')
@section('title', 'Buat Permintaan Barang')
@section('page_title', 'Buat Permintaan Barang')
@section('page_sub', 'Ajukan permintaan barang masuk atau keluar gudang')

@section('content')
<div style="padding:20px;">
  <div style="max-width:860px;margin:0 auto;">

    <form method="POST" action="{{ route('permintaan.store') }}" id="form-permintaan">
      @csrf

      {{-- Step 1: Info Permintaan --}}
      <div class="card" style="padding:24px;margin-bottom:16px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid #f1f5f9;">
          <div style="width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,#f97316,#ea580c);display:flex;align-items:center;justify-content:center;">
            <span class="material-symbols-outlined fill-icon" style="color:#fff;font-size:17px;">assignment</span>
          </div>
          <div>
            <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:14px;color:#0f172a;">Informasi Permintaan</div>
            <div style="font-size:11px;color:#94a3b8;">Isi detail permintaan barang</div>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Jenis Permintaan *</label>
            <select name="jenis" class="field" required onchange="updateJenisUI(this.value)">
              <option value="">-- Pilih Jenis --</option>
              <option value="masuk" {{ old('jenis')==='masuk'?'selected':'' }}>↑ Barang Masuk (ke Gudang)</option>
              <option value="keluar" {{ old('jenis')==='keluar'?'selected':'' }}>↓ Barang Keluar (dari Gudang)</option>
            </select>
            @error('jenis')<div style="font-size:11px;color:#ef4444;margin-top:4px;">{{ $message }}</div>@enderror
          </div>

          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Tanggal Dibutuhkan *</label>
            <input type="date" name="tanggal_dibutuhkan" class="field" required value="{{ old('tanggal_dibutuhkan', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}"/>
            @error('tanggal_dibutuhkan')<div style="font-size:11px;color:#ef4444;margin-top:4px;">{{ $message }}</div>@enderror
          </div>

          <div id="wrap-departemen" style="{{ old('jenis','') !== 'keluar' ? 'display:none;' : '' }}">
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Departemen / Tujuan</label>
            <input type="text" name="departemen_tujuan" class="field" placeholder="Contoh: Divisi Operasional, Proyek A" value="{{ old('departemen_tujuan') }}"/>
          </div>

          <div style="grid-column:1/-1;">
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Keperluan / Alasan *</label>
            <textarea name="keperluan" class="field" placeholder="Jelaskan keperluan permintaan ini secara singkat..." required>{{ old('keperluan') }}</textarea>
            @error('keperluan')<div style="font-size:11px;color:#ef4444;margin-top:4px;">{{ $message }}</div>@enderror
          </div>

          <div style="grid-column:1/-1;">
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Catatan Tambahan</label>
            <textarea name="catatan_manajerial" class="field" placeholder="Catatan untuk pimpinan / operator (opsional)...">{{ old('catatan_manajerial') }}</textarea>
          </div>
        </div>
      </div>

      {{-- Step 2: Daftar Barang --}}
      <div class="card" style="padding:24px;margin-bottom:16px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid #f1f5f9;">
          <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);display:flex;align-items:center;justify-content:center;">
              <span class="material-symbols-outlined fill-icon" style="color:#fff;font-size:17px;">list</span>
            </div>
            <div>
              <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:14px;color:#0f172a;">Daftar Barang</div>
              <div style="font-size:11px;color:#94a3b8;">Tambahkan barang yang diminta</div>
            </div>
          </div>
          <button type="button" onclick="tambahBaris()" class="btn-or" style="font-size:12px;padding:7px 14px;">
            <span class="material-symbols-outlined fill-icon" style="font-size:15px;">add</span> Tambah Barang
          </button>
        </div>

        <div id="tabel-barang">
          <table class="tbl" style="margin-bottom:8px;">
            <thead>
              <tr>
                <th style="width:40%;">Barang</th>
                <th style="width:20%;">Jumlah</th>
                <th style="width:15%;">Satuan</th>
                <th style="width:20%;">Keterangan</th>
                <th style="width:5%;"></th>
              </tr>
            </thead>
            <tbody id="tbody-barang">
              @if(old('items'))
                @foreach(old('items') as $i => $item)
                <tr id="row-{{ $i }}">
                  <td>
                    <select name="items[{{ $i }}][barang_id]" class="field barang-select" required onchange="autoSatuan(this, {{ $i }})">
                      <option value="">-- Pilih Barang --</option>
                      @foreach($barangList as $b)
                      <option value="{{ $b->id }}" data-satuan="{{ $b->satuan }}" data-stok="{{ $b->stok_tersedia }}" {{ $item['barang_id'] == $b->id ? 'selected':'' }}>
                        {{ $b->kode }} - {{ $b->nama }} (Stok: {{ $b->stok_tersedia }})
                      </option>
                      @endforeach
                    </select>
                  </td>
                  <td><input type="number" name="items[{{ $i }}][jumlah]" class="field" min="1" value="{{ $item['jumlah'] ?? 1 }}" required/></td>
                  <td><input type="text" name="items[{{ $i }}][satuan]" class="field" id="satuan-{{ $i }}" value="{{ $item['satuan'] ?? '' }}" placeholder="pcs"/></td>
                  <td><input type="text" name="items[{{ $i }}][keterangan]" class="field" value="{{ $item['keterangan'] ?? '' }}" placeholder="Opsional"/></td>
                  <td><button type="button" onclick="hapusBaris({{ $i }})" style="width:28px;height:28px;border-radius:8px;background:#fef2f2;border:1px solid #fecaca;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#ef4444;"><span class="material-symbols-outlined" style="font-size:15px;">delete</span></button></td>
                </tr>
                @endforeach
              @endif
            </tbody>
          </table>
          <div id="empty-barang" style="padding:30px;text-align:center;color:#94a3b8;font-size:13px;display:{{ old('items') ? 'none' : 'block' }};">
            <span class="material-symbols-outlined" style="font-size:32px;display:block;margin-bottom:8px;color:#e2e8f0;">add_shopping_cart</span>
            Klik "Tambah Barang" untuk menambahkan item
          </div>
        </div>
      </div>

      {{-- Tombol --}}
      <div style="display:flex;gap:10px;justify-content:flex-end;">
        <a href="{{ route('permintaan.index') }}" class="btn-ghost">
          <span class="material-symbols-outlined" style="font-size:16px;">arrow_back</span> Batal
        </a>
        <button type="submit" name="action" value="draft" class="btn-ghost">
          <span class="material-symbols-outlined" style="font-size:16px;">save</span> Simpan Draft
        </button>
        <button type="submit" name="action" value="ajukan" class="btn-or">
          <span class="material-symbols-outlined fill-icon" style="font-size:16px;">send</span> Ajukan ke Pimpinan
        </button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
var rowCount = {{ old('items') ? count(old('items')) : 0 }};

{{-- Hindari arrow function fn() di dalam @json — parse dulu di PHP lalu encode --}}
@php
$barangJson = $barangList->map(function($b) {
    return [
        'id'     => $b->id,
        'kode'   => $b->kode,
        'nama'   => $b->nama,
        'satuan' => $b->satuan,
        'stok'   => $b->stok_tersedia,
    ];
})->values()->toArray();
@endphp
var barangData = {!! json_encode($barangJson) !!};

function tambahBaris() {
  var i = rowCount++;
  document.getElementById('empty-barang').style.display = 'none';
  var opts = '<option value="">-- Pilih Barang --</option>';
  barangData.forEach(function(b) {
    opts += '<option value="'+b.id+'" data-satuan="'+b.satuan+'" data-stok="'+b.stok+'">';
    opts += b.kode+' - '+b.nama+' (Stok: '+b.stok+')';
    opts += '</option>';
  });
  var row = document.createElement('tr');
  row.id = 'row-'+i;
  row.innerHTML = '<td><select name="items['+i+'][barang_id]" class="field barang-select" required onchange="autoSatuan(this,'+i+')">'+opts+'</select></td>'
    +'<td><input type="number" name="items['+i+'][jumlah]" class="field" min="1" value="1" required/></td>'
    +'<td><input type="text" name="items['+i+'][satuan]" class="field" id="satuan-'+i+'" placeholder="pcs"/></td>'
    +'<td><input type="text" name="items['+i+'][keterangan]" class="field" placeholder="Opsional"/></td>'
    +'<td><button type="button" onclick="hapusBaris('+i+')" style="width:28px;height:28px;border-radius:8px;background:#fef2f2;border:1px solid #fecaca;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#ef4444;"><span class="material-symbols-outlined" style="font-size:15px;">delete</span></button></td>';
  document.getElementById('tbody-barang').appendChild(row);
}

function hapusBaris(i) {
  var row = document.getElementById('row-'+i);
  if (row) row.remove();
  if (document.getElementById('tbody-barang').children.length === 0) {
    document.getElementById('empty-barang').style.display = 'block';
  }
}

function autoSatuan(sel, i) {
  var opt = sel.options[sel.selectedIndex];
  var satuan = opt.dataset.satuan || '';
  var el = document.getElementById('satuan-'+i);
  if (el) el.value = satuan;
}

function updateJenisUI(val) {
  document.getElementById('wrap-departemen').style.display = val === 'keluar' ? 'block' : 'none';
}

document.getElementById('form-permintaan').addEventListener('submit', function(e) {
  var rows = document.getElementById('tbody-barang').children.length;
  if (rows === 0) { e.preventDefault(); alert('Tambahkan minimal 1 barang!'); }
});
</script>
@endpush
@endsection