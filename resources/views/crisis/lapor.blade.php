@extends('layouts.app')

@section('judul', 'Lapor Kendala')

@section('isi')
<style>
  .page-subtitle {
    margin: 0 0 18px;
    color: var(--text-secondary);
    font-size: 14.5px;
  }

  /* Grid Utama: Form Lapor & Riwayat Laporan */
  .crisis-user-grid {
    display: grid;
    grid-template-columns: 1fr 1.25fr;
    gap: 18px;
    align-items: start;
  }
  .crisis-user-grid .card {
    margin-top: 0;
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
  .report-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .report-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    padding: 13px 15px;
    border: 1px solid var(--border);
    border-radius: 9px;
    background: var(--bg-subtle);
    text-decoration: none;
    color: var(--text-main);
    transition: all 0.15s ease;
  }
  .report-item:hover {
    background: #ffffff;
    border-color: #93c5fd;
    box-shadow: var(--shadow-sm);
    transform: translateY(-1px);
  }
  .report-item.active {
    background: #eff6ff;
    border-color: var(--primary);
    border-left: 4px solid var(--primary);
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
  .report-date {
    font-size: 12.5px;
    color: var(--text-muted);
    white-space: nowrap;
    flex-shrink: 0;
  }

  /* Panel Detail Laporan */
  .detail-card {
    margin-top: 18px;
    border-top: 4px solid var(--primary);
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
    grid-template-columns: 1fr 1fr;
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
</style>

  <p class="page-subtitle">Laporkan kendala teknis atau akademik yang Anda alami. Laporan akan segera ditindaklanjuti oleh admin.</p>

  <div class="crisis-user-grid">
    {{-- Form Lapor Kendala --}}
    <div class="card">
      <h3>Form Lapor Kendala</h3>
      <form method="POST" action="{{ route('crisis.kirim') }}">
        @csrf

        <div class="row">
          <label for="kategori">Kategori Kendala</label>
          <select name="kategori" id="kategori" required>
            @foreach ($kategori as $k)
              <option value="{{ $k }}" @selected(old('kategori') == $k)>{{ $k }}</option>
            @endforeach
          </select>
        </div>

        <div class="row" style="margin-bottom:0">
          <label for="deskripsi">Deskripsi Kendala <span style="font-weight:400; color:var(--text-muted)">(min. 10 karakter)</span></label>
          <textarea name="deskripsi" id="deskripsi" rows="4" maxlength="1000" required
                    placeholder="Jelaskan kendala secara rinci (mata kuliah, waktu kejadian, pesan error, dll.)...">{{ old('deskripsi') }}</textarea>
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
        <div class="report-list">
          @foreach ($daftar as $l)
            <a class="report-item {{ $detail && $detail->idLaporan == $l->idLaporan ? 'active' : '' }}"
               href="{{ route('crisis.pengguna', ['id' => $l->idLaporan]) }}">
              <div style="min-width:0">
                <div class="report-top">
                  <span class="badge st-{{ $l->kelas_status }}">{{ $l->label_status }}</span>
                  <span class="report-cat">{{ $l->kategori }}</span>
                </div>
                <div class="report-desc">{{ \Illuminate\Support\Str::limit($l->deskripsi, 75) }}</div>
              </div>
              <span class="report-date">{{ $l->tanggalLapor->format('d/m/Y') }}</span>
            </a>
          @endforeach
        </div>
      @endif
    </div>
  </div>

  {{-- Detail Laporan Terpilih --}}
  @if ($detail)
    <div class="card detail-card">
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

        <a href="{{ route('crisis.pengguna') }}" class="btn btn-batal" style="padding:5px 10px; font-size:12.5px">
          <i data-lucide="x" style="width:14px;height:14px"></i> Tutup
        </a>
      </div>

      <div class="detail-split">
        <div class="detail-box">
          <span class="box-label">
            <i data-lucide="file-text" style="width:13px;height:13px"></i> Deskripsi Kendala
          </span>
          <div>{{ $detail->deskripsi }}</div>
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
@endsection