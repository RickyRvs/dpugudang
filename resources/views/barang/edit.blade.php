@extends('layouts.app')
@section('title', 'Edit Barang')
@section('page_title', 'Edit Barang')
@section('page_sub', $barang->kode . ' — ' . $barang->nama)

@section('content')
<div style="padding:20px;">
  <div style="max-width:760px;margin:0 auto;">
    <form method="POST" action="{{ route('barang.update', $barang->id) }}" enctype="multipart/form-data">
      @csrf @method('PUT')

      <div class="card" style="padding:24px;margin-bottom:16px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid #f1f5f9;">
          <div style="width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);display:flex;align-items:center;justify-content:center;">
            <span class="material-symbols-outlined fill-icon" style="color:#fff;font-size:17px;">edit</span>
          </div>
          <div>
            <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:14px;color:#0f172a;">Edit Barang</div>
            <div style="font-size:11px;color:#94a3b8;">Update data barang gudang</div>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Kode Barang *</label>
            <input type="text" name="kode" class="field" value="{{ old('kode', $barang->kode) }}" required/>
            @error('kode')<div style="font-size:11px;color:#ef4444;margin-top:4px;">{{ $message }}</div>@enderror
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Nama Barang *</label>
            <input type="text" name="nama" class="field" value="{{ old('nama', $barang->nama) }}" required/>
            @error('nama')<div style="font-size:11px;color:#ef4444;margin-top:4px;">{{ $message }}</div>@enderror
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Kategori *</label>
            <select name="kategori_id" class="field" required>
              @foreach($kategoriList as $k)
              <option value="{{ $k->id }}" {{ old('kategori_id', $barang->kategori_id)==$k->id?'selected':'' }}>{{ $k->nama }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Satuan *</label>
            <input type="text" name="satuan" class="field" value="{{ old('satuan', $barang->satuan) }}" required/>
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Stok Minimum *</label>
            <input type="number" name="stok_minimum" class="field" value="{{ old('stok_minimum', $barang->stok_minimum) }}" min="0" required/>
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Harga Satuan (Rp) *</label>
            <input type="number" name="harga_satuan" class="field" value="{{ old('harga_satuan', $barang->harga_satuan) }}" min="0" step="100" required/>
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Lokasi Rak</label>
            <input type="text" name="lokasi_rak" class="field" value="{{ old('lokasi_rak', $barang->lokasi_rak) }}" placeholder="Contoh: A-01"/>
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Stok Saat Ini</label>
            <div style="padding:9px 12px;background:#f1f5f9;border-radius:10px;font-size:13px;font-weight:700;color:#0f172a;">
              {{ $barang->stok_tersedia }} {{ $barang->satuan }}
              <span style="font-size:11px;color:#94a3b8;font-weight:400;">(ubah via koreksi stok)</span>
            </div>
          </div>
          <div style="grid-column:1/-1;">
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Deskripsi</label>
            <textarea name="deskripsi" class="field">{{ old('deskripsi', $barang->deskripsi) }}</textarea>
          </div>
          <div style="grid-column:1/-1;">
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Foto Barang</label>
            @if($barang->foto)
            <div style="margin-bottom:8px;">
              <img src="{{ asset('storage/'.$barang->foto) }}" alt="Foto" style="height:80px;border-radius:8px;border:1px solid #e2e8f0;"/>
              <div style="font-size:11px;color:#94a3b8;margin-top:4px;">Upload baru untuk mengganti</div>
            </div>
            @endif
            <input type="file" name="foto" accept="image/*" class="field" style="padding:7px;"/>
          </div>
          <div>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
              <input type="checkbox" name="is_active" value="1" {{ old('is_active', $barang->is_active) ? 'checked' : '' }} style="width:16px;height:16px;accent-color:#f97316;"/>
              <span style="font-size:13px;font-weight:600;color:#0f172a;">Barang Aktif</span>
            </label>
          </div>
        </div>
      </div>

      <div style="display:flex;gap:10px;justify-content:flex-end;">
        <a href="{{ route('barang.index') }}" class="btn-ghost">
          <span class="material-symbols-outlined" style="font-size:16px;">arrow_back</span> Batal
        </a>
        <button type="submit" class="btn-or">
          <span class="material-symbols-outlined fill-icon" style="font-size:16px;">save</span> Update Barang
        </button>
      </div>
    </form>
  </div>
</div>
@endsection