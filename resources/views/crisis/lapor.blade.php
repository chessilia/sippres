@extends('layouts.app')

@section('judul', 'Lapor Kendala')

@section('isi')
<style>
  /* Kunci lebar viewport agar tidak bergeser saat scrollbar halaman muncul */
  html {
    scrollbar-gutter: stable;
  }

  .page-subtitle {
    margin: 0 0 18px;
    color: var(--text-secondary);
    font-size: 14.5px;
  }

  /* Grid Utama: Form Lapor & Riwayat Laporan */
  .crisis-user-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1.25fr);
    gap: 18px;
    align-items: start;
  }
  .crisis-user-grid .card {
    margin-top: 0;
  }

  /* Upload Foto */
  .upload-box {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 12px 14px;
    border: 1.5px dashed #cbd5e1;
    border-radius: 8px;
    background: var(--bg-subtle);
    transition: border-color 0.15s ease, background-color 0.15s ease;
  }
  .upload-box:hover {
    border-color: var(--primary);
    background: #f8fafc;
  }
  .upload-box.has-error {
    border-color: #ef4444;
    background: #fef2f2;
  }
  .upload-box input[type="file"] {
    font-size: 13px;
    color: var(--text-secondary);
    padding: 0;
    border: none;
    background: transparent;
  }
  .foto-error-msg {
    display: none;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    font-weight: 600;
    color: #dc2626;
    margin-top: 2px;
  }
  .foto-preview-wrap {
    display: none;
    align-items: center;
    gap: 12px;
    margin-top: 4px;
    padding-top: 8px;
    border-top: 1px solid var(--border);
  }
  .foto-preview-wrap img {
    width: 72px;
    height: 72px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid var(--border);
  }

  .form-footer-bar {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 8px;
    margin-top: 16px;
    padding-top: 14px;
    border-top: 1px solid var(--border);
  }

  /* Daftar Laporan Saya */
  .report-list-hint {
    font-size: 12.5px;
    color: var(--text-muted);
    margin: -6px 0 12px;
    display: flex;
    align-items: center;
    gap: 5px;
  }
  .report-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .report-item {
    display: flex;
    justify-content: space-between;
    align-items: stretch;
    gap: 12px;
    padding: 13px 15px;
    border: 1px solid var(--border);
    border-left: 4px solid transparent;
    border-radius: 9px;
    background: var(--bg-subtle);
    text-decoration: none;
    color: var(--text-main);
    transition: background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
  }
  .report-item:hover {
    background: #ffffff;
    border-color: #93c5fd;
    border-left-color: #93c5fd;
    box-shadow: var(--shadow-sm);
  }
  .report-item.active {
    background: #eff6ff;
    border-color: #93c5fd;
    border-left-color: var(--primary);
  }
  .report-top {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 5px;
    flex-wrap: wrap;
  }
  .report-cat {
    font-weight: 700;
    font-size: 14.5px;
    color: var(--text-main);
  }
  .report-desc {
    font-size: 13.5px;
    color: var(--text-secondary);
    line-height: 1.45;
  }
  .report-right {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: flex-end;
    gap: 8px;
    flex-shrink: 0;
  }
  .report-date {
    font-size: 12.5px;
    color: var(--text-muted);
    white-space: nowrap;
  }
  .report-cta {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    min-width: 178px;
    font-size: 12px;
    font-weight: 700;
    color: var(--primary);
    background: #ffffff;
    border: 1px solid #bfdbfe;
    padding: 4px 10px;
    border-radius: 6px;
    white-space: nowrap;
    transition: all 0.15s ease;
  }
  .report-item:hover .report-cta {
    background: var(--primary);
    border-color: var(--primary);
    color: #ffffff;
  }
  .has-reply-tag {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11.5px;
    font-weight: 700;
    color: #15803d;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    padding: 1px 7px;
    border-radius: 99px;
  }

  /* Panel Detail Laporan */
  .detail-card {
    margin-top: 18px;
    border-top: 4px solid var(--primary);
    scroll-margin-top: 84px;
  }
  .detail-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    padding-bottom: 12px;
    margin-bottom: 16px;
    border-bottom: 1px solid var(--border);
  }
  .detail-header h3 {
    margin: 0 0 4px;
    padding: 0;
    border: none;
    font-size: 18px;
  }
  .detail-meta {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 13px;
    color: var(--text-muted);
  }
  .detail-split {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    gap: 16px;
  }
  .detail-box {
    background: var(--bg-subtle);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 14px 16px;
    font-size: 14px;
    line-height: 1.6;
  }
  .detail-box.tindak-lanjut {
    background: #eff6ff;
    border-color: #bfdbfe;
  }
  .box-label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--text-muted);
    margin-bottom: 6px;
  }
  .detail-box.tindak-lanjut .box-label {
    color: var(--primary-hover);
  }
  .detail-foto {
    margin-top: 12px;
    padding-top: 10px;
    border-top: 1px dashed var(--border);
  }
  .detail-foto img {
    max-width: 100%;
    max-height: 240px;
    border-radius: 8px;
    border: 1px solid var(--border);
    display: block;
    margin-top: 6px;
  }
  .empty-box {
    text-align: center;
    padding: 32px 16px;
    color: var(--text-muted);
    font-weight: 600;
    font-size: 14px;
  }

  @media (max-width: 960px) {
    .crisis-user-grid {
      grid-template-columns: 1fr;
    }
    .detail-split {
      grid-template-columns: 1fr;
    }
  }
  @media (max-width: 540px) {
    .report-item {
      flex-direction: column;
    }
    .report-right {
      flex-direction: row;
      width: 100%;
      align-items: center;
    }
  }
</style>

  <p class="page-subtitle">Laporkan kendala teknis atau akademik yang Anda alami. Laporan akan segera ditindaklanjuti oleh admin.</p>

  <div class="crisis-user-grid">
    {{-- Form Lapor Kendala --}}
    <div class="card">
      <h3>Form Lapor Kendala</h3>
      <form method="POST" action="{{ route('crisis.kirim') }}" enctype="multipart/form-data">
        @csrf

        <div class="row">
          <label for="kategori">Kategori Kendala</label>
          <select name="kategori" id="kategori" required>
            @foreach ($kategori as $k)
              <option value="{{ $k }}" @selected(old('kategori') == $k)>{{ $k }}</option>
            @endforeach
          </select>
        </div>

        <div class="row">
          <label for="deskripsi">Deskripsi Kendala</label>
          <textarea name="deskripsi" id="deskripsi" rows="4" maxlength="1000" required
                    placeholder="Jelaskan kendala secara rinci (mata kuliah, waktu kejadian, pesan error, dll.)...">{{ old('deskripsi') }}</textarea>
        </div>

        <div class="row" style="margin-bottom:0">
          <label for="foto">Lampiran Foto / Screenshot <span style="font-weight:400; color:var(--text-muted)">(opsional, maks. 5MB)</span></label>
          <div class="upload-box" id="uploadBox">
            <input type="file" name="foto" id="foto" accept="image/png,image/jpeg,image/webp" onchange="previewFotoLaporan(this)">
            <div class="foto-error-msg" id="fotoErrorMsg">
              <i data-lucide="alert-circle" style="width:14px;height:14px;flex-shrink:0"></i>
              <span id="fotoErrorText">Ukuran foto melebihi batas maksimal 5 MB.</span>
            </div>
            <div class="foto-preview-wrap" id="fotoPreviewWrap">
              <img id="fotoPreviewImg" src="" alt="Preview lampiran">
              <div style="font-size:12.5px; color:var(--text-secondary)">
                <strong id="fotoPreviewName" style="display:block; color:var(--text-main)"></strong>
                <span id="fotoPreviewSize" style="font-size:11.5px; color:var(--text-muted)"></span><br>
                <button type="button" onclick="hapusFotoLaporan()" style="margin-top:4px; background:none; border:none; padding:0; color:#dc2626; font-size:12px; font-weight:600; cursor:pointer">
                  Hapus foto
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="form-footer-bar">
          <button type="submit" name="aksi" value="Batal" class="btn-batal" formnovalidate>Batal</button>
          <button type="submit" name="aksi" value="Submit" class="btn-primary">
            <i data-lucide="send" style="width:15px;height:15px"></i> Submit Laporan
          </button>
        </div>
      </form>
    </div>

    {{-- Laporan Saya --}}
    <div class="card">
      <div style="display:flex; justify-content:space-between; align-items:center; padding-bottom:12px; margin-bottom:14px; border-bottom:1px solid var(--border)">
        <h3 style="margin:0; padding:0; border:none">Laporan Saya</h3>
        <span style="font-size:13px; font-weight:600; color:var(--text-muted)">{{ $daftar->count() }} laporan</span>
      </div>

      @if ($daftar->isEmpty())
        <div class="empty-box">Belum ada laporan kendala yang Anda kirimkan.</div>
      @else
        <div class="report-list-hint">
          <i data-lucide="mouse-pointer-click" style="width:14px;height:14px;color:var(--primary)"></i>
          <span>Pilih salah satu laporan di bawah untuk melihat detail dan riwayat penanganan admin.</span>
        </div>

        <div class="report-list">
          @foreach ($daftar as $l)
            @php
              $sedangDibuka = $detail && $detail->idLaporan == $l->idLaporan;
            @endphp
            <a class="report-item {{ $sedangDibuka ? 'active' : '' }}"
               href="{{ $sedangDibuka ? route('crisis.pengguna') : route('crisis.pengguna', ['id' => $l->idLaporan]) . '#detailLaporan' }}">
              <div style="min-width:0">
                <div class="report-top">
                  <span class="badge st-{{ $l->kelas_status }}">{{ $l->label_status }}</span>
                  <span class="report-cat">{{ $l->kategori }}</span>
                  @if ($l->foto)
                    <span class="badge st-Izin" style="font-size:11px; padding:1px 7px">
                      <i data-lucide="image" style="width:11px;height:11px"></i> Foto
                    </span>
                  @endif
                  @if ($l->tindakLanjut)
                    <span class="has-reply-tag">
                      <i data-lucide="check-circle-2" style="width:11px;height:11px"></i> Ada Penanganan
                    </span>
                  @endif
                </div>
                <div class="report-desc">{{ \Illuminate\Support\Str::limit($l->deskripsi, 75) }}</div>
              </div>

              <div class="report-right">
                <span class="report-date">{{ $l->tanggalLapor->format('d/m/Y') }}</span>
                <span class="report-cta">
                  @if ($sedangDibuka)
                    <i data-lucide="chevron-up" style="width:13px;height:13px"></i>
                    <span>Tutup Detail</span>
                  @else
                    <span>Lihat Detail &amp; Penanganan</span>
                    <i data-lucide="chevron-down" style="width:13px;height:13px"></i>
                  @endif
                </span>
              </div>
            </a>
          @endforeach
        </div>
      @endif
    </div>
  </div>

  {{-- Detail Laporan Terpilih --}}
  @if ($detail)
    <div class="card detail-card" id="detailLaporan">
      <div class="detail-header">
        <div>
          <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap">
            <h3>{{ $detail->kategori }}</h3>
            <span class="badge st-{{ $detail->kelas_status }}">{{ $detail->label_status }}</span>
          </div>
          <span class="detail-meta" style="margin-top:4px">
            <i data-lucide="clock" style="width:13px;height:13px"></i>
            Dilaporkan pada {{ $detail->tanggalLapor->format('d/m/Y H:i') }}
          </span>
        </div>
      </div>

      <div class="detail-split">
        <div class="detail-box">
          <span class="box-label">
            <i data-lucide="file-text" style="width:13px;height:13px"></i> Deskripsi Kendala
          </span>
          <div>{{ $detail->deskripsi }}</div>

          @if ($detail->foto)
            <div class="detail-foto">
              <span class="box-label" style="margin-bottom:4px">
                <i data-lucide="image" style="width:13px;height:13px"></i> Lampiran Foto
              </span>
              <a href="{{ asset('storage/' . $detail->foto) }}" target="_blank" title="Klik untuk memperbesar">
                <img src="{{ asset('storage/' . $detail->foto) }}" alt="Lampiran Kendala">
              </a>
            </div>
          @endif
        </div>

        <div class="detail-box {{ $detail->tindakLanjut ? 'tindak-lanjut' : '' }}">
          <span class="box-label">
            <i data-lucide="shield-check" style="width:13px;height:13px"></i> Riwayat Penanganan Admin
          </span>
          @if ($detail->tindakLanjut)
            <div>{!! nl2br(e($detail->tindakLanjut)) !!}</div>
          @else
            <div style="color:var(--text-muted); font-style:italic">Belum ada tindak lanjut dari admin.</div>
          @endif
        </div>
      </div>
    </div>
  @endif

<script>
  const MAX_FOTO_BYTES = 5 * 1024 * 1024; // 5 MB

  function previewFotoLaporan(input) {
    const box       = document.getElementById('uploadBox');
    const wrap      = document.getElementById('fotoPreviewWrap');
    const img       = document.getElementById('fotoPreviewImg');
    const name      = document.getElementById('fotoPreviewName');
    const sizeLabel = document.getElementById('fotoPreviewSize');
    const errWrap   = document.getElementById('fotoErrorMsg');
    const errText   = document.getElementById('fotoErrorText');

    errWrap.style.display = 'none';
    box.classList.remove('has-error');

    if (input.files && input.files[0]) {
      const file = input.files[0];
      const sizeMB = (file.size / (1024 * 1024)).toFixed(2);

      if (file.size > MAX_FOTO_BYTES) {
        input.value = '';
        wrap.style.display = 'none';
        img.src = '';
        box.classList.add('has-error');
        errText.textContent = 'Ukuran foto (' + sizeMB + ' MB) terlalu besar. Maksimal 5 MB.';
        errWrap.style.display = 'flex';
        return;
      }

      name.textContent = file.name;
      sizeLabel.textContent = sizeMB + ' MB';
      const reader = new FileReader();
      reader.onload = function (e) {
        img.src = e.target.result;
        wrap.style.display = 'flex';
      };
      reader.readAsDataURL(file);
    } else {
      wrap.style.display = 'none';
    }
  }

  function hapusFotoLaporan() {
    const input = document.getElementById('foto');
    input.value = '';
    document.getElementById('fotoPreviewWrap').style.display = 'none';
    document.getElementById('fotoPreviewImg').src = '';
    document.getElementById('fotoErrorMsg').style.display = 'none';
    document.getElementById('uploadBox').classList.remove('has-error');
  }
</script>
@endsection