@extends('layouts.app')

@section('judul', 'Crisis Center')

@section('isi')
<style>
  .page-header-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin: 0 0 18px;
  }
  .page-subtitle {
    margin: 0;
    color: var(--text-secondary);
    font-size: 14.5px;
  }

  /* Filter Tabs */
  .tabs-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    border-bottom: 2px solid var(--border);
    padding-bottom: 0;
  }
  .tabs-bar .tab {
    margin-right: 0;
    border-radius: 8px 8px 0 0;
    padding: 9px 16px;
    font-size: 13.5px;
  }
  .table-card {
    margin-top: 0;
    border-top-left-radius: 0;
    border-top: none;
  }

  /* Detail Grid */
  .crisis-split {
    display: grid;
    grid-template-columns: 1.1fr 1fr;
    gap: 20px;
    margin-top: 16px;
  }
  .crisis-split .card {
    margin-top: 0;
  }
  .detail-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    padding-bottom: 14px;
    margin-bottom: 16px;
    border-bottom: 1px solid var(--border);
  }
  .detail-header h3 {
    margin: 0;
    padding: 0;
    border: none;
    font-size: 19px;
  }
  .detail-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px 16px;
    font-size: 13.5px;
    color: var(--text-secondary);
    margin-top: 4px;
  }
  .info-box {
    background: var(--bg-subtle);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 14px 16px;
    margin-bottom: 16px;
    font-size: 14.5px;
    line-height: 1.6;
  }
  .info-box-label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--text-muted);
    margin-bottom: 6px;
    display: block;
  }

  /* Radio Pills */
  .radio-pill-group {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 6px;
  }
  .radio-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: var(--bg-subtle);
    font-weight: 600;
    font-size: 13.5px;
    color: var(--text-main);
    cursor: pointer;
    margin: 0;
    transition: all 0.15s ease;
  }
  .radio-pill:hover {
    border-color: #93c5fd;
    background: #eff6ff;
  }
  .radio-pill input[type="radio"] {
    accent-color: var(--primary);
    margin: 0;
  }

  .action-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid var(--border);
  }
  .row-active {
    background: #eff6ff !important;
  }
  .empty-box {
    text-align: center;
    padding: 32px 20px;
    color: var(--text-muted);
    font-weight: 600;
  }

  @media (max-width: 960px) {
    .crisis-split { grid-template-columns: 1fr; }
  }
</style>

  <div class="page-header-bar">
    <p class="page-subtitle">Periksa laporan kendala dari mahasiswa maupun dosen, beri tindak lanjut, lalu perbarui statusnya.</p>
    <span class="badge st-Baru" style="padding:5px 12px; font-size:13px">
      {{ $jumlah['Baru'] ?? 0 }} laporan baru
    </span>
  </div>

  {{-- Filter status --}}
  <div class="tabs-bar">
    <a class="tab {{ !$filter ? 'aktif' : '' }}" href="{{ route('crisis.admin') }}">Semua ({{ $total }})</a>
    <a class="tab {{ $filter == 'Baru' ? 'aktif' : '' }}" href="{{ route('crisis.admin', ['status' => 'Baru']) }}">Baru ({{ $jumlah['Baru'] ?? 0 }})</a>
    <a class="tab {{ $filter == 'Diproses' ? 'aktif' : '' }}" href="{{ route('crisis.admin', ['status' => 'Diproses']) }}">Ditangani ({{ $jumlah['Diproses'] ?? 0 }})</a>
    <a class="tab {{ $filter == 'Selesai' ? 'aktif' : '' }}" href="{{ route('crisis.admin', ['status' => 'Selesai']) }}">Selesai ({{ $jumlah['Selesai'] ?? 0 }})</a>
    <a class="tab {{ $filter == 'Tidak Valid' ? 'aktif' : '' }}" href="{{ route('crisis.admin', ['status' => 'Tidak Valid']) }}">Tidak valid ({{ $jumlah['Tidak Valid'] ?? 0 }})</a>
  </div>

  {{-- Daftar laporan --}}
  <div class="card table-card">
    @if ($daftar->isEmpty())
      <div class="empty-box">Tidak ada laporan pada kategori ini.</div>
    @else
      <table>
        <thead>
          <tr>
            <th>Pelapor</th>
            <th>Kategori</th>
            <th>Tanggal</th>
            <th>Ringkasan</th>
            <th>Status</th>
            <th style="text-align:right">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($daftar as $l)
            <tr class="{{ $detail && $detail->idLaporan == $l->idLaporan ? 'row-active' : '' }}">
              <td>
                <strong>{{ $l->pelapor->nama }}</strong><br>
                <small style="color:var(--text-muted)">{{ $l->pelapor->role }}</small>
              </td>
              <td>{{ $l->kategori }}</td>
              <td style="white-space:nowrap">{{ $l->tanggalLapor->format('d/m/Y') }}</td>
              <td>{{ \Illuminate\Support\Str::limit($l->deskripsi, 55) }}</td>
              <td><span class="badge st-{{ $l->kelas_status }}">{{ $l->label_status }}</span></td>
              <td style="text-align:right; white-space:nowrap">
                <a class="btn {{ $detail && $detail->idLaporan == $l->idLaporan ? 'btn-primary' : 'btn-batal' }}"
                   style="padding:6px 12px; font-size:13px"
                   href="{{ route('crisis.admin', ['status' => $filter, 'id' => $l->idLaporan]) }}">
                  <i data-lucide="eye" style="width:14px;height:14px"></i> Tinjau
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>

  {{-- Detail laporan --}}
  @if ($detail)
    <div class="crisis-split" id="detail-laporan">
      {{-- Kolom Kiri: Informasi Laporan --}}
      <div class="card">
        <div class="detail-header">
          <div>
            <h3>{{ $detail->pelapor->nama }}</h3>
            <div class="detail-meta">
              <span><strong>Peran:</strong> {{ $detail->pelapor->role }}</span>
              <span><strong>Username:</strong> {{ $detail->pelapor->username }}</span>
              <span><strong>Waktu:</strong> {{ $detail->tanggalLapor->format('d/m/Y H:i') }}</span>
            </div>
          </div>
          <span class="badge st-{{ $detail->kelas_status }}">{{ $detail->label_status }}</span>
        </div>

        <div class="info-box">
          <span class="info-box-label">Kategori &amp; Deskripsi Kendala</span>
          <div style="margin-bottom:8px"><span class="badge st-Izin">{{ $detail->kategori }}</span></div>
          <div style="color:var(--text-main)">{{ $detail->deskripsi }}</div>
        </div>

        <div class="info-box" style="margin-bottom:0">
          <span class="info-box-label">Riwayat Penanganan</span>
          @if ($detail->tindakLanjut)
            <div>{!! nl2br(e($detail->tindakLanjut)) !!}</div>
          @else
            <span style="color:var(--text-muted); font-style:italic">Belum ada catatan tindak lanjut.</span>
          @endif
        </div>
      </div>

      {{-- Kolom Kanan: Form Tindak Lanjut --}}
      <div class="card">
        <h3>Form Tindak Lanjut</h3>

        @if ($detail->status === 'Tidak Valid')
          <div class="alert-error" style="margin-bottom:0">
            Laporan ini sudah ditandai <strong>Tidak Valid</strong> sehingga tidak dapat ditindaklanjuti lagi.
          </div>
        @else
          <form method="POST" action="{{ route('crisis.tindaklanjut', $detail->idLaporan) }}">
            @csrf
            <div class="row">
              <label>Ubah Status Laporan</label>
              <div class="radio-pill-group">
                @foreach (['Baru' => 'Baru', 'Diproses' => 'Ditangani', 'Selesai' => 'Selesai'] as $nilai => $label)
                  <label class="radio-pill">
                    <input type="radio" name="status" value="{{ $nilai }}"
                      @checked(old('status', $detail->status) == $nilai)>
                    <span>{{ $label }}</span>
                  </label>
                @endforeach
              </div>
            </div>

            <div class="row">
              <label for="tindakLanjut">Catatan Tindak Lanjut <span style="color:#ef4444">*</span></label>
              <textarea name="tindakLanjut" id="tindakLanjut" rows="4" maxlength="1000" required
                        placeholder="Tuliskan langkah penyelesaian atau tanggapan untuk pelapor...">{{ old('tindakLanjut') }}</textarea>
              <small style="display:block; margin-top:6px; color:var(--text-muted)">
                Pelapor akan otomatis menerima notifikasi setelah tindak lanjut disimpan.
              </small>
            </div>

            <div class="action-footer">
              <button type="submit" class="btn-primary">
                <i data-lucide="send" style="width:15px;height:15px"></i> Simpan Tindak Lanjut
              </button>
          </form>

              <form method="POST" action="{{ route('crisis.tidakvalid', $detail->idLaporan) }}" style="margin:0"
                    onsubmit="return confirm('Tandai laporan ini sebagai tidak valid?')">
                @csrf
                <button type="submit" class="btn-hapus">
                  <i data-lucide="x-circle" style="width:15px;height:15px"></i> Tandai Tidak Valid
                </button>
              </form>
            </div>
        @endif
      </div>
    </div>
  @endif
@endsection