@extends('layouts.app')

@section('judul', 'Rekap Kehadiran')

@section('isi')
<style>
  .page-subtitle {
    margin: 0 0 18px;
    color: var(--text-secondary);
    font-size: 14.5px;
  }

  /* Toolbar Filter & Ekspor */
  .toolbar-flex {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    flex-wrap: wrap;
    gap: 16px;
  }
  .filter-select-wrap {
    flex: 1;
    min-width: 260px;
  }
  .export-actions {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
  }

  /* KPI Grid 4 Kolom */
  .kpi-grid-4 {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 16px;
    margin: 18px 0;
  }
  .kpi-box {
    background: #ffffff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 20px;
    box-shadow: var(--shadow-sm);
  }
  .kpi-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12.5px;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.03em;
  }
  .kpi-icon {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    background: var(--primary-soft);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .kpi-val {
    font-size: 28px;
    font-weight: 800;
    color: var(--text-main);
    margin-top: 8px;
    line-height: 1.15;
  }

  /* Split Grid saat Detail Dipilih */
  .rekap-layout {
    display: grid;
    grid-template-columns: 1fr;
    gap: 18px;
    align-items: start;
  }
  .rekap-layout.has-detail {
    grid-template-columns: 1.45fr 1fr;
  }
  .row-active {
    background: #eff6ff !important;
  }

  /* Progress Cell */
  .prog-cell {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 140px;
  }
  .prog-cell .progress {
    flex: 1;
    margin: 0;
  }
  .progress-fill {
    width: var(--w, 0%);
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
    align-items: flex-start;
    gap: 12px;
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
  .pertemuan-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin: 12px 0 16px;
  }
  .pertemuan-badges .badge {
    padding: 5px 10px;
    font-size: 12.5px;
  }
  .legend-note {
    font-size: 12.5px;
    color: var(--text-muted);
    background: var(--bg-subtle);
    border: 1px solid var(--border);
    padding: 10px 12px;
    border-radius: 8px;
    margin: 0;
  }
  .empty-box {
    text-align: center;
    padding: 36px 20px;
    color: var(--text-muted);
    font-weight: 600;
  }

  /* Header Khusus Cetak PDF (tersembunyi di layar) */
  .print-only-header {
    display: none;
  }

  @media (max-width: 1024px) {
    .kpi-grid-4 { grid-template-columns: repeat(2, 1fr); }
    .rekap-layout,
    .rekap-layout.has-detail { grid-template-columns: 1fr; }
    .detail-card { position: static; }
  }
  @media (max-width: 560px) {
    .kpi-grid-4 { grid-template-columns: 1fr; }
  }

  /* Pengaturan Cetak PDF */
  @media print {
    .sidebar, .topbar, .no-print, .detail-card, .aksi-col {
      display: none !important;
    }
    .main-wrap, .content {
      margin: 0 !important;
      padding: 0 !important;
      width: 100% !important;
      max-width: 100% !important;
      background: #ffffff !important;
    }
    body {
      background: #ffffff !important;
      color: #0f172a !important;
    }
    .print-only-header {
      display: block;
      border-bottom: 2px solid #0f172a;
      padding-bottom: 12px;
      margin-bottom: 18px;
    }
    .print-only-header h2 {
      margin: 0 0 4px;
      font-size: 20px;
    }
    .print-only-header p {
      margin: 0;
      font-size: 13px;
      color: #334155;
    }
    .card, .kpi-box {
      box-shadow: none !important;
      border: 1px solid #cbd5e1 !important;
    }
    .kpi-grid-4 {
      grid-template-columns: repeat(4, 1fr) !important;
      gap: 10px !important;
    }
    .rekap-layout, .rekap-layout.has-detail {
      grid-template-columns: 1fr !important;
    }
  }
</style>

  <p class="page-subtitle no-print">Ringkasan kehadiran mahasiswa per perkuliahan beserta rincian status tiap pertemuan.</p>

  @if (!$perkuliahan)
    <div class="card" style="margin-top:0">
      <div class="empty-box">Belum ada perkuliahan yang Anda ampu.</div>
    </div>
  @else
    {{-- Kop Dokumen saat Cetak PDF --}}
    <div class="print-only-header">
      <h2>Laporan Rekap Kehadiran Mahasiswa — SIPPRES</h2>
      <p>
        Mata Kuliah: <strong>{{ $perkuliahan->kodeMatkul }} - {{ $perkuliahan->namaMatkul }} (Kelas {{ $perkuliahan->kelas }})</strong>
        &bull; Dosen: {{ Auth::user()->nama }} &bull; Dicetak: {{ now()->format('d/m/Y H:i') }}
      </p>
    </div>

    {{-- Toolbar Pilih Perkuliahan & Ekspor --}}
    <div class="card no-print" style="margin-top:0">
      <div class="toolbar-flex">
        <form method="GET" action="{{ route('rekap.dosen') }}" class="filter-select-wrap">
          <label for="perkuliahan">Pilih Mata Kuliah &amp; Kelas</label>
          <select name="perkuliahan" id="perkuliahan" onchange="this.form.submit()">
            @foreach ($daftar as $p)
              <option value="{{ $p->idPerkuliahan }}" @selected($perkuliahan->idPerkuliahan == $p->idPerkuliahan)>
                {{ $p->kodeMatkul }} - {{ $p->namaMatkul }} (Kelas {{ $p->kelas }})
              </option>
            @endforeach
          </select>
        </form>

        <div class="export-actions">
          <button type="button" class="btn-batal" onclick="window.print()">
            <i data-lucide="printer" style="width:15px;height:15px;color:var(--primary)"></i> Cetak / Simpan PDF
          </button>
          <a class="btn btn-primary" href="{{ route('rekap.ekspor', ['perkuliahan' => $perkuliahan->idPerkuliahan]) }}">
            <i data-lucide="file-spreadsheet" style="width:15px;height:15px"></i> Ekspor Excel (CSV)
          </a>
        </div>
      </div>
    </div>

    @if ($pertemuan->isEmpty() || $mahasiswa->isEmpty())
      <div class="card">
        <div class="empty-box">Data kehadiran mahasiswa belum tersedia pada mata kuliah ini.</div>
      </div>
    @else
      {{-- Kartu Ringkasan KPI --}}
      <div class="kpi-grid-4">
        <div class="kpi-box">
          <div class="kpi-head">
            <span>Total Mahasiswa</span>
            <div class="kpi-icon"><i data-lucide="users" style="width:17px;height:17px"></i></div>
          </div>
          <div class="kpi-val">{{ $ringkasan['totalMahasiswa'] }}</div>
        </div>

        <div class="kpi-box">
          <div class="kpi-head">
            <span>Rata-rata Kehadiran</span>
            <div class="kpi-icon"><i data-lucide="bar-chart-3" style="width:17px;height:17px"></i></div>
          </div>
          <div class="kpi-val">{{ $ringkasan['rataRata'] }}%</div>
        </div>

        <div class="kpi-box">
          <div class="kpi-head">
            <span>Total Izin &amp; Sakit</span>
            <div class="kpi-icon"><i data-lucide="file-text" style="width:17px;height:17px"></i></div>
          </div>
          <div class="kpi-val">{{ $ringkasan['izinSakit'] }}</div>
        </div>

        <div class="kpi-box">
          <div class="kpi-head">
            <span>Total Alpa</span>
            <div class="kpi-icon" style="background:var(--danger-bg); color:var(--danger)">
              <i data-lucide="alert-circle" style="width:17px;height:17px"></i>
            </div>
          </div>
          <div class="kpi-val">{{ $ringkasan['alpa'] }}</div>
        </div>
      </div>

      <div class="rekap-layout {{ $detail ? 'has-detail' : '' }}">
        {{-- Tabel Kehadiran per Mahasiswa --}}
        <div class="card" style="margin-top:0">
          <table>
            <thead>
              <tr>
                <th>Mahasiswa</th>
                <th style="text-align:center">Hadir</th>
                <th style="text-align:center">Izin</th>
                <th style="text-align:center">Sakit</th>
                <th style="text-align:center">Alpa</th>
                <th>Persentase</th>
                <th class="aksi-col" style="text-align:right">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($mahasiswa as $m)
                @php $h = $rekap[$m->idUser]; @endphp
                <tr class="{{ $detail && $detail->idUser == $m->idUser ? 'row-active' : '' }}">
                  <td>
                    <strong>{{ $m->nama }}</strong><br>
                    <small style="color:var(--text-muted)">{{ $m->username }}</small>
                  </td>
                  <td style="text-align:center; font-weight:700; color:var(--success)">{{ $h['Hadir'] }}</td>
                  <td style="text-align:center; font-weight:700; color:var(--primary)">{{ $h['Izin'] }}</td>
                  <td style="text-align:center; font-weight:700; color:var(--warning)">{{ $h['Sakit'] }}</td>
                  <td style="text-align:center; font-weight:700; color:var(--danger)">{{ $h['Alpa'] }}</td>
                  <td>
                    <div class="prog-cell">
                      <div class="progress">
                        <div class="progress-fill" data-w="{{ $h['persen'] }}"></div>
                      </div>
                      <strong style="font-size:13px; min-width:38px; text-align:right">{{ $h['persen'] }}%</strong>
                    </div>
                  </td>
                  <td class="aksi-col" style="text-align:right">
                    <a class="btn {{ $detail && $detail->idUser == $m->idUser ? 'btn-primary' : 'btn-batal' }}"
                       style="padding:6px 12px; font-size:13px"
                       href="{{ route('rekap.dosen', ['perkuliahan' => $perkuliahan->idPerkuliahan, 'mahasiswa' => $m->idUser]) }}">
                      Detail
                    </a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {{-- Detail per Pertemuan --}}
        @if ($detail)
          <div class="card detail-card">
            <div class="detail-header">
              <div>
                <h3>{{ $detail->nama }}</h3>
                <small style="color:var(--text-muted)">{{ $detail->username }} &bull; Kehadiran <strong>{{ $rekap[$detail->idUser]['persen'] }}%</strong></small>
              </div>
              <a class="btn btn-batal" style="padding:5px 10px; font-size:12.5px"
                 href="{{ route('rekap.dosen', ['perkuliahan' => $perkuliahan->idPerkuliahan]) }}">
                <i data-lucide="x" style="width:14px;height:14px"></i> Tutup
              </a>
            </div>

            <div class="pertemuan-badges">
              @foreach ($pertemuan as $s)
                @php $st = $matriks[$detail->idUser][$s->pertemuan]['status']; @endphp
                <span class="badge st-{{ $st }}">P{{ $s->pertemuan }}: {{ $st }}</span>
              @endforeach
            </div>

            <p class="legend-note">
              Status: <strong>Hadir</strong>, <strong>Izin</strong>, <strong>Sakit</strong>, <strong>Alpa</strong>.
              Label <strong>Belum</strong> menandakan sesi presensi masih berlangsung.
            </p>
          </div>
        @endif
      </div>
    @endif
  @endif

  <script>
    document.querySelectorAll('.progress-fill').forEach(function (el) {
      el.style.width = (el.dataset.w || 0) + '%';
    });
  </script>
@endsection