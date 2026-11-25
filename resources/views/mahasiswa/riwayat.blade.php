@extends('layouts.app')

@section('judul', 'Riwayat')

@section('isi')
<style>
  .page-subtitle {
    margin: 0 0 18px;
    color: var(--text-secondary);
    font-size: 14.5px;
  }

  /* Baris Tab & Filter Mata Kuliah */
  .riwayat-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    flex-wrap: wrap;
    gap: 12px;
    border-bottom: 2px solid var(--border);
  }
  .riwayat-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: -2px;
  }
  .riwayat-tabs .tab {
    margin-right: 0;
    border-radius: 8px 8px 0 0;
    padding: 9px 18px;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 7px;
  }
  .matkul-filter-form {
    display: flex;
    align-items: center;
    gap: 8px;
    padding-bottom: 8px;
  }
  .matkul-filter-label {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 13px;
    font-weight: 700;
    color: var(--text-secondary);
    white-space: nowrap;
  }
  .matkul-filter-select {
    min-width: 240px;
    max-width: 320px;
    padding: 7px 12px;
    font-size: 13.5px;
    font-weight: 600;
    border-radius: 8px;
    border: 1px solid var(--border);
    background: #ffffff;
    color: var(--text-main);
  }
  .riwayat-card {
    margin-top: 0;
    border-top-left-radius: 0;
    border-top: none;
  }

  /* Split Grid saat Detail Dipilih */
  .riwayat-layout {
    display: grid;
    grid-template-columns: 1fr;
    gap: 18px;
    align-items: start;
  }
  .riwayat-layout.has-detail {
    grid-template-columns: 1.35fr 1fr;
  }

  .row-active {
    background: #eff6ff !important;
  }

  /* Detail Panel */
  .detail-card {
    margin-top: 0;
    position: sticky;
    top: 84px;
    border-top: 4px solid var(--primary);
  }
  .detail-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 12px;
    margin-bottom: 14px;
    border-bottom: 1px solid var(--border);
  }
  .detail-header h3 {
    margin: 0;
    padding: 0;
    border: none;
    font-size: 18px;
  }
  .detail-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  .detail-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    padding-bottom: 10px;
    border-bottom: 1px dashed var(--border);
    font-size: 14px;
  }
  .detail-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
  }
  .detail-label {
    color: var(--text-muted);
    font-weight: 600;
    flex-shrink: 0;
  }
  .detail-val {
    color: var(--text-main);
    font-weight: 700;
    text-align: right;
  }
  .note-box {
    margin-top: 14px;
    background: var(--bg-subtle);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 12px 14px;
    font-size: 13.5px;
    line-height: 1.55;
  }
  .note-label {
    display: block;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--text-muted);
    margin-bottom: 4px;
  }
  .empty-box {
    text-align: center;
    padding: 40px 20px;
    color: var(--text-muted);
    font-weight: 600;
  }

  @media (max-width: 960px) {
    .riwayat-layout,
    .riwayat-layout.has-detail {
      grid-template-columns: 1fr;
    }
    .detail-card {
      position: static;
    }
  }
  @media (max-width: 680px) {
    .matkul-filter-form {
      width: 100%;
    }
    .matkul-filter-select {
      flex: 1;
      max-width: none;
    }
  }
</style>

  @php
    $matkulParam = !empty($filterMatkul) ? ['matkul' => $filterMatkul->idPerkuliahan] : [];
  @endphp

  <p class="page-subtitle">Catatan lengkap riwayat presensi perkuliahan dan status pengajuan izin ketidakhadiran Anda.</p>

  {{-- Toolbar: Tab Jenis Riwayat & Filter Mata Kuliah --}}
  <div class="riwayat-toolbar">
    <div class="riwayat-tabs">
      <a class="tab {{ $jenis == 'presensi' ? 'aktif' : '' }}"
         href="{{ route('riwayat', array_merge(['jenis' => 'presensi'], $matkulParam)) }}">
        <i data-lucide="check-square" style="width:15px;height:15px"></i> Presensi
      </a>
      <a class="tab {{ $jenis == 'izin' ? 'aktif' : '' }}"
         href="{{ route('riwayat', array_merge(['jenis' => 'izin'], $matkulParam)) }}">
        <i data-lucide="file-text" style="width:15px;height:15px"></i> Pengajuan Izin
      </a>
    </div>

    <form method="GET" action="{{ route('riwayat') }}" class="matkul-filter-form">
      <input type="hidden" name="jenis" value="{{ $jenis }}">
      <label for="filterMatkul" class="matkul-filter-label">
        <i data-lucide="filter" style="width:14px;height:14px;color:var(--primary)"></i> Mata Kuliah:
      </label>
      <select name="matkul" id="filterMatkul" class="matkul-filter-select" onchange="this.form.submit()">
        <option value="">Semua Mata Kuliah</option>
        @foreach ($daftarMatkul as $mk)
          <option value="{{ $mk->idPerkuliahan }}" @selected(!empty($filterMatkul) && $filterMatkul->idPerkuliahan == $mk->idPerkuliahan)>
            {{ $mk->namaMatkul }} (Kelas {{ $mk->kelas }})
          </option>
        @endforeach
      </select>
      @if (!empty($filterMatkul))
        <a href="{{ route('riwayat', ['jenis' => $jenis]) }}"
           class="btn btn-batal" style="padding:7px 11px; font-size:12.5px" title="Reset Filter">
          Reset
        </a>
      @endif
    </form>
  </div>

  <div class="riwayat-layout {{ $detail ? 'has-detail' : '' }}">
    {{-- Tabel Riwayat --}}
    <div class="card riwayat-card">
      @if ($daftar->isEmpty())
        <div class="empty-box">
          @if (!empty($filterMatkul))
            {{ $jenis == 'presensi' ? 'Belum ada riwayat presensi untuk mata kuliah ' . $filterMatkul->namaMatkul . '.' : 'Belum ada riwayat pengajuan izin untuk mata kuliah ' . $filterMatkul->namaMatkul . '.' }}
          @else
            {{ $jenis == 'presensi' ? 'Riwayat presensi belum tersedia.' : 'Riwayat pengajuan izin belum tersedia.' }}
          @endif
        </div>
      @elseif ($jenis == 'presensi')
        <table>
          <thead>
            <tr>
              <th>Mata Kuliah</th>
              <th style="text-align:center">Pertemuan</th>
              <th>Waktu Presensi</th>
              <th>Status</th>
              <th style="text-align:right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($daftar as $p)
              <tr class="{{ $detail && $detail->idPresensi == $p->idPresensi ? 'row-active' : '' }}">
                <td>
                  <strong>{{ $p->sesi->perkuliahan->namaMatkul }}</strong><br>
                  <small style="color:var(--text-muted)">Kelas {{ $p->sesi->perkuliahan->kelas }}</small>
                </td>
                <td style="text-align:center">
                  <span class="badge st-Izin">Ke-{{ $p->pertemuan }}</span>
                </td>
                <td style="white-space:nowrap">{{ $p->waktu->format('d/m/Y H:i') }}</td>
                <td><span class="badge st-{{ $p->status }}">{{ $p->status }}</span></td>
                <td style="text-align:right">
                  <a class="btn {{ $detail && $detail->idPresensi == $p->idPresensi ? 'btn-primary' : 'btn-batal' }}"
                     style="padding:5px 11px; font-size:12.5px"
                     href="{{ route('riwayat', array_merge(['jenis' => 'presensi', 'id' => $p->idPresensi], $matkulParam)) }}">
                    Detail
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <table>
          <thead>
            <tr>
              <th>Mata Kuliah</th>
              <th style="text-align:center">Pertemuan</th>
              <th>Tanggal Ajuan</th>
              <th>Status</th>
              <th style="text-align:right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($daftar as $z)
              <tr class="{{ $detail && $detail->idIzin == $z->idIzin ? 'row-active' : '' }}">
                <td>
                  <strong>{{ $z->perkuliahan->namaMatkul }}</strong><br>
                  <small style="color:var(--text-muted)">Kelas {{ $z->perkuliahan->kelas }}</small>
                </td>
                <td style="text-align:center">
                  <span class="badge st-Izin">Ke-{{ $z->pertemuan }}</span>
                </td>
                <td style="white-space:nowrap">{{ $z->tanggalPengajuan->format('d/m/Y H:i') }}</td>
                <td><span class="badge st-{{ $z->status }}">{{ $z->status }}</span></td>
                <td style="text-align:right">
                  <a class="btn {{ $detail && $detail->idIzin == $z->idIzin ? 'btn-primary' : 'btn-batal' }}"
                     style="padding:5px 11px; font-size:12.5px"
                     href="{{ route('riwayat', array_merge(['jenis' => 'izin', 'id' => $z->idIzin], $matkulParam)) }}">
                    Detail
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </div>

    {{-- Detail Riwayat --}}
    @if ($detail)
      <div class="card detail-card">
        <div class="detail-header">
          <h3>{{ $jenis == 'presensi' ? 'Detail Presensi' : 'Detail Pengajuan Izin' }}</h3>
          <a href="{{ route('riwayat', array_merge(['jenis' => $jenis], $matkulParam)) }}"
             class="btn btn-batal" style="padding:5px 10px; font-size:12.5px">
            <i data-lucide="x" style="width:14px;height:14px"></i> Tutup
          </a>
        </div>

        @if ($jenis == 'presensi')
          <div class="detail-list">
            <div class="detail-row">
              <span class="detail-label">Mata Kuliah</span>
              <span class="detail-val">{{ $detail->sesi->perkuliahan->namaMatkul }} (Kelas {{ $detail->sesi->perkuliahan->kelas }})</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Pertemuan</span>
              <span class="detail-val">Pertemuan ke-{{ $detail->pertemuan }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Waktu Tercatat</span>
              <span class="detail-val">{{ $detail->waktu->format('d/m/Y H:i') }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Status Kehadiran</span>
              <span class="detail-val"><span class="badge st-{{ $detail->status }}">{{ $detail->status }}</span></span>
            </div>
          </div>

          <div class="note-box">
            <span class="note-label">Keterangan</span>
            <div>{{ $detail->keterangan ?? 'Tidak ada catatan tambahan.' }}</div>
          </div>
        @else
          <div class="detail-list">
            <div class="detail-row">
              <span class="detail-label">Mata Kuliah</span>
              <span class="detail-val">{{ $detail->perkuliahan->namaMatkul }} (Kelas {{ $detail->perkuliahan->kelas }})</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Pertemuan</span>
              <span class="detail-val">Pertemuan ke-{{ $detail->pertemuan }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Waktu Pengajuan</span>
              <span class="detail-val">{{ $detail->tanggalPengajuan->format('d/m/Y H:i') }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Status Pengajuan</span>
              <span class="detail-val"><span class="badge st-{{ $detail->status }}">{{ $detail->status }}</span></span>
            </div>
            @if ($detail->fileBukti)
              <div class="detail-row">
                <span class="detail-label">Bukti Pendukung</span>
                <span class="detail-val">
                  <a class="btn btn-batal" style="padding:4px 10px; font-size:12.5px"
                     href="{{ route('izin.bukti', $detail->idIzin) }}" target="_blank">
                    <i data-lucide="paperclip" style="width:13px;height:13px;color:var(--primary)"></i> Lihat Bukti
                  </a>
                </span>
              </div>
            @endif
          </div>

          <div class="note-box">
            <span class="note-label">Alasan Ketidakhadiran</span>
            <div>{{ $detail->alasan }}</div>
          </div>
        @endif
      </div>
    @endif
  </div>
@endsection