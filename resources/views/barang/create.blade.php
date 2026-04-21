@extends('layouts.app')
@section('title', 'Tambah Barang')
@section('page_title', 'Tambah Barang')
@section('page_sub', 'Daftarkan barang baru ke gudang')

@section('content')
<div style="padding:20px;">
  <div style="max-width:760px;margin:0 auto;">
    <form method="POST" action="{{ route('barang.store') }}" enctype="multipart/form-data">
      @csrf

      <div class="card" style="padding:24px;margin-bottom:16px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid #f1f5f9;">
          <div style="width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,#f97316,#ea580c);display:flex;align-items:center;justify-content:center;">
            <span class="material-symbols-outlined fill-icon" style="color:#fff;font-size:17px;">inventory_2</span>
          </div>
          <div>
            <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:14px;color:#0f172a;">Informasi Barang</div>
            <div style="font-size:11px;color:#94a3b8;">Isi data barang dengan lengkap</div>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Kode Barang *</label>
            <input type="text" name="kode" class="field" value="{{ old('kode') }}" placeholder="Contoh: BRG-001" required/>
            @error('kode')<div style="font-size:11px;color:#ef4444;margin-top:4px;">{{ $message }}</div>@enderror
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Nama Barang *</label>
            <input type="text" name="nama" class="field" value="{{ old('nama') }}" placeholder="Nama barang" required/>
            @error('nama')<div style="font-size:11px;color:#ef4444;margin-top:4px;">{{ $message }}</div>@enderror
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Kategori *</label>
            <select name="kategori_id" class="field" required>
              <option value="">-- Pilih Kategori --</option>
              @foreach($kategoriList as $k)
              <option value="{{ $k->id }}" {{ old('kategori_id')==$k->id?'selected':'' }}>{{ $k->nama }}</option>
              @endforeach
            </select>
            @error('kategori_id')<div style="font-size:11px;color:#ef4444;margin-top:4px;">{{ $message }}</div>@enderror
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Satuan *</label>
            <input type="text" name="satuan" class="field" value="{{ old('satuan') }}" placeholder="pcs, kg, liter, roll..." required/>
            @error('satuan')<div style="font-size:11px;color:#ef4444;margin-top:4px;">{{ $message }}</div>@enderror
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Stok Awal *</label>
            <input type="number" name="stok_tersedia" class="field" value="{{ old('stok_tersedia', 0) }}" min="0" required/>
            @error('stok_tersedia')<div style="font-size:11px;color:#ef4444;margin-top:4px;">{{ $message }}</div>@enderror
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Stok Minimum *</label>
            <input type="number" name="stok_minimum" class="field" value="{{ old('stok_minimum', 5) }}" min="0" required/>
            @error('stok_minimum')<div style="font-size:11px;color:#ef4444;margin-top:4px;">{{ $message }}</div>@enderror
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Harga Satuan (Rp) *</label>
            <input type="number" name="harga_satuan" class="field" value="{{ old('harga_satuan', 0) }}" min="0" step="100" required/>
            @error('harga_satuan')<div style="font-size:11px;color:#ef4444;margin-top:4px;">{{ $message }}</div>@enderror
          </div>
          <div>
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Lokasi Rak</label>
            <input type="text" name="lokasi_rak" class="field" value="{{ old('lokasi_rak') }}" placeholder="Contoh: A-01, B-03"/>
          </div>
          <div style="grid-column:1/-1;">
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Deskripsi</label>
            <textarea name="deskripsi" class="field" placeholder="Deskripsi barang (opsional)...">{{ old('deskripsi') }}</textarea>
          </div>
          <div style="grid-column:1/-1;">
            <label style="display:block;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Foto Barang</label>
            <input type="file" name="foto" accept="image/*" class="field" style="padding:7px;"/>
            <div style="font-size:11px;color:#94a3b8;margin-top:4px;">Format: JPG/PNG, max 2MB (opsional)</div>
          </div>
          <div>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
              <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} style="width:16px;height:16px;accent-color:#f97316;"/>
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
          <span class="material-symbols-outlined fill-icon" style="font-size:16px;">save</span> Simpan Barang
        </button>
      </div>
    </form>
  </div>
</div>
@endsection