@extends('layouts.app')

@section('judul', 'Rekap dan Statistik')

@section('isi')
<style>
  .page-subtitle {
    margin: 0 0 18px;
    color: var(--text-secondary);
    font-size: 14.5px;
  }

  /* Filter Bar Ringkas */
  .filter-card {
    background: #ffffff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 20px;
    box-shadow: var(--shadow-sm);
  }
  .filter-row {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 12px;
  }
  .filter-row label {
    display: block;
    font-size: 12.5px;
    font-weight: 700;
    margin-bottom: 5px;
    color: var(--text-secondary);
  }
  .filter-row select,
  .filter-row input[type="month"] {
    width: 100%;
    padding: 8.5px 10px;
    font-size: 13.5px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-family: inherit;
    background: #fff;
    color: var(--text-main);
  }
  .filter-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 14px;
    padding-top: 14px;
    border-top: 1px solid var(--border);
  }
  .btn-group-inline {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
  }

  /* Header Periode */
  .section-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 24px 0 14px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--border);
  }
  .section-toolbar h3 {
    margin: 0;
    font-size: 17px;
    font-weight: 800;
    color: var(--text-main);
    display: flex;
    align-items: center;
    gap: 8px;
  }

  /* KPI Cards */
  .kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 14px;
  }
  .kpi-card {
    background: #ffffff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 20px;
    box-shadow: var(--shadow-sm);
  }
  .kpi-label {
    font-size: 13px;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.03em;
  }
  .kpi-value {
    font-size: 30px;
    font-weight: 800;
    color: var(--text-main);
    margin: 6px 0 4px;
    line-height: 1.15;
  }
  .kpi-note {
    font-size: 13px;
    color: var(--text-secondary);
  }

  /* Chart 2 Kolom */
  .split-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
    margin-top: 16px;
  }
  .split-grid .card {
    margin-top: 0;
  }

  /* Progress Bar Bersih */
  .metric-item {
    margin-bottom: 14px;
  }
  .metric-item:last-child {
    margin-bottom: 0;
  }
  .metric-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13.5px;
    font-weight: 600;
    margin-bottom: 6px;
  }
  .progress-fill {
    width: var(--w, 0%);
  }
  .fill-Hadir { background: #16a34a !important; }
  .fill-Izin { background: #2563eb !important; }
  .fill-Sakit { background: #d97706 !important; }
  .fill-Alpa { background: #dc2626 !important; }

  .card-summary-foot {
    margin-top: 14px;
    padding-top: 12px;
    border-top: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 14px;
  }

  .text-center { text-align: center; }
  .text-muted { color: var(--text-muted); }
  .empty-box {
    text-align: center;
    padding: 36px 20px;
    color: var(--text-muted);
    font-weight: 600;
  }

  @media (max-width: 1100px) {
    .filter-row { grid-template-columns: repeat(3, minmax(0, 1fr)); }
  }
  @media (max-width: 768px) {
    .filter-row { grid-template-columns: 1fr; }
    .split-grid { grid-template-columns: 1fr; }
  }
</style>

  <p class="page-subtitle">Pantau rekapitulasi presensi, pengajuan izin, dan laporan Crisis Center per periode.</p>

  {{-- Filter Ringkas 1 Baris --}}
  <div class="filter-card noprint">
    <form method="GET" action="{{ route('statistik.admin') }}">
      <div class="filter-row">
        <div>
          <label for="jenis">Jenis Data</label>
          <select name="jenis" id="jenis">
            @foreach (['Semua', 'Presensi', 'Izin', 'Crisis Center'] as $j)
              <option value="{{ $j }}" @selected($f['jenis'] == $j)>{{ $j }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label for="bulan">Periode Bulan</label>
          <input type="month" name="bulan" id="bulan" value="{{ $f['bulan'] }}">
        </div>
        <div>
          <label for="perkuliahan">Mata Kuliah</label>
          <select name="perkuliahan" id="perkuliahan">
            <option value="">Semua Mata Kuliah</option>
            @foreach ($daftarPerkuliahan as $p)
              <option value="{{ $p->idPerkuliahan }}" @selected($f['perkuliahan'] == $p->idPerkuliahan)>
                {{ $p->namaMatkul }} ({{ $p->kelas }})
              </option>
            @endforeach
          </select>
        </div>
        <div>
          <label for="dosen">Dosen Pengampu</label>
          <select name="dosen" id="dosen">
            <option value="">Semua Dosen</option>
            @foreach ($daftarDosen as $d)
              <option value="{{ $d->idUser }}" @selected($f['dosen'] == $d->idUser)>{{ $d->nama }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label for="status">Status Kehadiran</label>
          <select name="status" id="status">
            <option value="">Semua Status</option>
            @foreach (['Hadir', 'Izin', 'Sakit', 'Alpa'] as $st)
              <option value="{{ $st }}" @selected($f['status'] == $st)>{{ $st }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="filter-footer">
        <div class="btn-group-inline">
          <button type="submit" class="btn-primary">
            <i data-lucide="filter" style="width:15px;height:15px"></i> Terapkan Filter
          </button>
          @if (request()->anyFilled(['jenis', 'bulan', 'perkuliahan', 'dosen', 'status']))
            <a href="{{ route('statistik.admin') }}" class="btn btn-batal">Reset</a>
          @endif
        </div>

        <div class="btn-group-inline">
          <a class="btn btn-batal" href="{{ route('statistik.ekspor', request()->query()) }}">
            <i data-lucide="download" style="width:15px;height:15px;color:#16a34a"></i> Ekspor CSV
          </a>
          <button type="button" class="btn-batal" onclick="window.print()">
            <i data-lucide="printer" style="width:15px;height:15px;color:var(--primary)"></i> Cetak PDF
          </button>
        </div>
      </div>
    </form>
  </div>

  @if ($galat)
    <div class="alert-error" style="margin-top:16px">Terjadi kesalahan saat mengambil data, silakan coba kembali.</div>
  @else
    @php
      $pr = $data['presensi'];
      $iz = $data['izin'];
      $lp = $data['laporan'];
      $adaPresensi = $pr['total'] > 0;
      $adaIzin = $iz['total'] > 0;
      $adaLaporan = $lp['total'] > 0;
      $semuaKosong = match ($f['jenis']) {
        'Presensi'      => !$adaPresensi,
        'Izin'          => !$adaIzin,
        'Crisis Center' => !$adaLaporan,
        default         => !$adaPresensi && !$adaIzin && !$adaLaporan,
      };
    @endphp

    <div class="section-toolbar">
      <h3>
        <i data-lucide="calendar" style="width:18px;height:18px;color:var(--primary)"></i>
        Periode {{ $f['awal']->format('m/Y') }}
      </h3>
      <span class="badge st-Izin">{{ $f['jenis'] }}</span>
    </div>

    @if ($semuaKosong)
      <div class="card empty-box">Data belum tersedia pada periode ini.</div>
    @else
      {{-- Ringkasan Angka (KPI) --}}
      <div class="kpi-grid">
        @if (in_array($f['jenis'], ['Semua', 'Presensi']))
          <div class="kpi-card">
            <div class="kpi-label">Total Presensi</div>
            <div class="kpi-value">{{ $pr['tercatat'] }}</div>
            <div class="kpi-note">Dari {{ $pr['perMatkul']->count() }} perkuliahan</div>
          </div>

          <div class="kpi-card">
            <div class="kpi-label">Rata-rata Kehadiran</div>
            <div class="kpi-value">{{ $pr['rata'] }}%</div>
            <div class="kpi-note">
              @if ($pr['selisih'] === null)
                Belum ada pembanding bulan lalu
              @else
                <strong>{{ $pr['selisih'] >= 0 ? '+' : '' }}{{ $pr['selisih'] }}%</strong> dari bulan lalu
              @endif
            </div>
          </div>
        @endif

        @if (in_array($f['jenis'], ['Semua', 'Izin']))
          <div class="kpi-card">
            <div class="kpi-label">Pengajuan Izin</div>
            <div class="kpi-value">{{ $iz['total'] }}</div>
            <div class="kpi-note">{{ $iz['menunggu'] }} menunggu persetujuan</div>
          </div>
        @endif

        @if (in_array($f['jenis'], ['Semua', 'Crisis Center']))
          <div class="kpi-card">
            <div class="kpi-label">Laporan Kendala</div>
            <div class="kpi-value">{{ $lp['total'] }}</div>
            <div class="kpi-note">{{ $lp['belum'] }} belum ditangani</div>
          </div>
        @endif
      </div>

      {{-- Bagian Presensi --}}
      @if (in_array($f['jenis'], ['Semua', 'Presensi']) && $adaPresensi)
        <div class="split-grid">
          <div class="card">
            <h3>Kehadiran per Minggu</h3>
            @foreach ($pr['mingguan'] as $minggu => $persen)
              <div class="metric-item">
                <div class="metric-head">
                  <span>Minggu {{ $minggu }}</span>
                  <strong>{{ $persen }}%</strong>
                </div>
                <div class="progress">
                  <div class="progress-fill" data-w="{{ $persen }}"></div>
                </div>
              </div>
            @endforeach
            <div class="card-summary-foot">
              <span class="text-muted">Rata-rata Periode</span>
              <strong>{{ $pr['rata'] }}%</strong>
            </div>
          </div>

          <div class="card">
            <h3>Distribusi Status Kehadiran</h3>
            @foreach ($pr['perStatus'] as $status => $v)
              <div class="metric-item">
                <div class="metric-head">
                  <span class="badge st-{{ $status }}">{{ $status }}</span>
                  <span><strong>{{ $v['jumlah'] }}</strong> <span class="text-muted">({{ $v['persen'] }}%)</span></span>
                </div>
                <div class="progress">
                  <div class="progress-fill fill-{{ $status }}" data-w="{{ $v['persen'] }}"></div>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        <div class="card">
          <h3>Rekap per Perkuliahan</h3>
          <table>
            <thead>
              <tr>
                <th>Mata Kuliah</th>
                <th>Dosen</th>
                <th class="text-center">Pertemuan</th>
                <th class="text-center">Hadir</th>
                <th class="text-center">Izin</th>
                <th class="text-center">Sakit</th>
                <th class="text-center">Alpa</th>
                <th class="text-center">% Hadir</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($pr['perMatkul'] as $r)
                <tr>
                  <td><strong>{{ $r['perkuliahan']->namaMatkul }}</strong> <span class="text-muted">(Kelas {{ $r['perkuliahan']->kelas }})</span></td>
                  <td>{{ $r['perkuliahan']->dosen->nama ?? '-' }}</td>
                  <td class="text-center">{{ $r['pertemuan'] }}</td>
                  <td class="text-center">{{ $r['Hadir'] }}</td>
                  <td class="text-center">{{ $r['Izin'] }}</td>
                  <td class="text-center">{{ $r['Sakit'] }}</td>
                  <td class="text-center">{{ $r['Alpa'] }}</td>
                  <td class="text-center"><strong>{{ $r['persen'] }}%</strong></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="card">
          <h3>Detail Kehadiran {{ $f['status'] ? '(' . $f['status'] . ')' : '' }}</h3>
          @if ($pr['detail']->isEmpty())
            <p class="text-muted" style="margin:0">Tidak ada data untuk status ini.</p>
          @else
            <table>
              <thead>
                <tr>
                  <th>Mahasiswa</th>
                  <th>Mata Kuliah</th>
                  <th class="text-center">Pertemuan</th>
                  <th class="text-center">Tanggal</th>
                  <th class="text-center">Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($pr['detail']->take(100) as $r)
                  <tr>
                    <td><strong>{{ $r['mahasiswa']->nama }}</strong></td>
                    <td>{{ $r['perkuliahan']->namaMatkul }} <span class="text-muted">(Kelas {{ $r['perkuliahan']->kelas }})</span></td>
                    <td class="text-center">{{ $r['sesi']->pertemuan }}</td>
                    <td class="text-center">{{ $r['sesi']->waktuMulai->format('d/m/Y') }}</td>
                    <td class="text-center"><span class="badge st-{{ $r['status'] }}">{{ $r['status'] }}</span></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
            @if ($pr['detail']->count() > 100)
              <p class="text-muted" style="margin:12px 0 0; font-size:13px">
                Menampilkan 100 dari <strong>{{ $pr['detail']->count() }}</strong> baris. Gunakan tombol <strong>Ekspor CSV</strong> untuk data lengkap.
              </p>
            @endif
          @endif
        </div>
      @elseif (in_array($f['jenis'], ['Semua', 'Presensi']))
        <div class="card empty-box">Data presensi belum tersedia pada periode ini.</div>
      @endif

      {{-- Bagian Izin & Crisis Center (2 Kolom Sejajar jika Jenis == Semua) --}}
      @if ($f['jenis'] === 'Semua')
        <div class="split-grid">
      @endif

      @if (in_array($f['jenis'], ['Semua', 'Izin']))
        <div class="card">
          <h3>Pengajuan Izin</h3>
          @if ($adaIzin)
            @foreach ($iz['perStatus'] as $status => $v)
              <div class="metric-item">
                <div class="metric-head">
                  <span class="badge st-{{ $status }}">{{ $status }}</span>
                  <span><strong>{{ $v['jumlah'] }}</strong> <span class="text-muted">({{ $v['persen'] }}%)</span></span>
                </div>
                <div class="progress">
                  <div class="progress-fill" data-w="{{ $v['persen'] }}"></div>
                </div>
              </div>
            @endforeach
          @else
            <p class="text-muted" style="margin:0">Data izin belum tersedia pada periode ini.</p>
          @endif
        </div>
      @endif

      @if (in_array($f['jenis'], ['Semua', 'Crisis Center']))
        <div class="card">
          <h3>Laporan Crisis Center</h3>
          @if ($adaLaporan)
            @foreach ($lp['perKategori'] as $kategori => $v)
              <div class="metric-item">
                <div class="metric-head">
                  <span>{{ $kategori }}</span>
                  <span><strong>{{ $v['jumlah'] }}</strong> <span class="text-muted">({{ $v['persen'] }}%)</span></span>
                </div>
                <div class="progress">
                  <div class="progress-fill" data-w="{{ $v['persen'] }}"></div>
                </div>
              </div>
            @endforeach
            <div class="card-summary-foot" style="flex-wrap:wrap; gap:6px">
              <span class="badge st-Baru">Baru {{ $lp['perStatus']['Baru'] }}</span>
              <span class="badge st-Diproses">Ditangani {{ $lp['perStatus']['Diproses'] }}</span>
              <span class="badge st-Selesai">Selesai {{ $lp['perStatus']['Selesai'] }}</span>
              <span class="badge st-TidakValid">Tidak valid {{ $lp['perStatus']['Tidak Valid'] }}</span>
            </div>
          @else
            <p class="text-muted" style="margin:0">Data laporan belum tersedia pada periode ini.</p>
          @endif
        </div>
      @endif

      @if ($f['jenis'] === 'Semua')
        </div>
      @endif
    @endif
  @endif

<script>
  document.querySelectorAll('.progress-fill').forEach(function (el) {
    el.style.width = (el.dataset.w || 0) + '%';
  });
</script>
@endsection