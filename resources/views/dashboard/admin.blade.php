@extends('layouts.app')

@section('judul', 'Dashboard Admin')

@section('isi')
<style>
  /* Hero Banner Admin */
  .admin-banner {
    background: linear-gradient(135deg, var(--primary) 0%, #1d4ed8 100%);
    color: #ffffff;
    border-radius: var(--radius);
    padding: 24px 28px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    box-shadow: 0 6px 18px rgba(37, 99, 235, 0.22);
    margin-bottom: 18px;
  }
  .admin-banner h3 {
    margin: 0 0 6px;
    font-size: 22px;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -0.3px;
  }
  .admin-banner p {
    margin: 0;
    color: rgba(255, 255, 255, 0.92);
    font-size: 14.5px;
  }
  .banner-actions {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
  }
  .btn-banner-white {
    background: #ffffff;
    color: var(--primary);
    padding: 10px 18px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 14px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.12);
    transition: all 0.15s ease;
  }
  .btn-banner-white:hover {
    background: #f8fafc;
    transform: translateY(-1px);
  }
  .btn-banner-glass {
    background: rgba(255, 255, 255, 0.16);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.3);
    padding: 10px 18px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 14px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.15s ease;
  }
  .btn-banner-glass:hover {
    background: rgba(255, 255, 255, 0.25);
  }

  /* KPI Grid Admin */
  .kpi-grid-admin {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px;
    margin-bottom: 18px;
  }
  .kpi-box {
    background: #ffffff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 20px;
    box-shadow: var(--shadow-sm);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.15s ease;
  }
  .kpi-box:hover {
    border-color: #93c5fd;
    box-shadow: var(--shadow-md);
  }
  .kpi-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13px;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.03em;
  }
  .kpi-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: var(--primary-soft);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .kpi-val {
    font-size: 30px;
    font-weight: 800;
    color: var(--text-main);
    margin: 10px 0 6px;
    line-height: 1.15;
  }
  .kpi-sub {
    font-size: 13px;
    color: var(--text-secondary);
    margin-top: 6px;
  }
  .role-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 8px;
  }
  .role-chip {
    font-size: 12px;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 6px;
    background: var(--bg-subtle);
    border: 1px solid var(--border);
    color: var(--text-secondary);
  }
  .kpi-link {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 13.5px;
    font-weight: 700;
    color: var(--primary);
    text-decoration: none;
    margin-top: 14px;
    padding-top: 12px;
    border-top: 1px solid var(--border);
  }
  .kpi-link:hover {
    text-decoration: underline;
  }
  .progress-fill {
    width: var(--w, 0%);
  }

  /* Card Header & Table */
  .card-head-flex {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 12px;
    margin-bottom: 12px;
    border-bottom: 1px solid var(--border);
  }
  .card-head-flex h3 {
    margin: 0;
    padding: 0;
    border: none;
    font-size: 18px;
  }
  .empty-state-sm {
    text-align: center;
    padding: 32px 16px;
    color: var(--text-muted);
    font-size: 14px;
    font-weight: 600;
  }

  @media (max-width: 960px) {
    .kpi-grid-admin { grid-template-columns: 1fr; }
  }
</style>

  {{-- Banner Selamat Datang --}}
  <div class="admin-banner">
    <div>
      <h3>Hi, {{ Auth::user()->nama }}!</h3>
      <p>
        @if ($laporanBaru > 0)
          Terdapat <strong>{{ $laporanBaru }} laporan kendala baru</strong> yang menunggu penanganan Anda.
        @else
          Semua laporan kendala sudah ditindaklanjuti. Sistem berjalan normal.
        @endif
      </p>
    </div>
    <div class="banner-actions">
      <a class="btn-banner-white" href="{{ route('perkuliahan.daftar') }}">
        <i data-lucide="book-open" style="width:16px;height:16px"></i> Kelola Perkuliahan
      </a>
      <a class="btn-banner-glass" href="{{ route('pengguna.daftar', ['tambah' => 1]) }}">
        <i data-lucide="user-plus" style="width:16px;height:16px"></i> Tambah Pengguna
      </a>
    </div>
  </div>

  {{-- Kartu Ringkasan KPI --}}
  <div class="kpi-grid-admin">
    {{-- Crisis Center --}}
    <div class="kpi-box">
      <div>
        <div class="kpi-head">
          <span>Crisis Center</span>
          <div class="kpi-icon"><i data-lucide="life-buoy" style="width:18px;height:18px"></i></div>
        </div>
        <div class="kpi-val">{{ $laporanBaru }} <span style="font-size:16px; font-weight:700; color:var(--text-muted)">baru</span></div>
        @php $persenTindak = round(($ditindak / max($totalLaporan, 1)) * 100); @endphp
        <div class="progress">
          <div class="progress-fill" data-w="{{ $persenTindak }}"></div>
        </div>
        <div class="kpi-sub">{{ $ditindak }} dari {{ $totalLaporan }} laporan sudah ditindaklanjuti</div>
      </div>
      <a class="kpi-link" href="{{ route('crisis.admin') }}">
        Tangani laporan <i data-lucide="arrow-right" style="width:14px;height:14px"></i>
      </a>
    </div>

    {{-- Total Pengguna --}}
    <div class="kpi-box">
      <div>
        <div class="kpi-head">
          <span>Total Pengguna</span>
          <div class="kpi-icon"><i data-lucide="users" style="width:18px;height:18px"></i></div>
        </div>
        <div class="kpi-val">{{ $pengguna->sum() }} <span style="font-size:16px; font-weight:700; color:var(--text-muted)">akun</span></div>
        <div class="role-chips">
          <span class="role-chip">{{ $pengguna['Mahasiswa'] ?? 0 }} Mahasiswa</span>
          <span class="role-chip">{{ $pengguna['Dosen'] ?? 0 }} Dosen</span>
          <span class="role-chip">{{ $pengguna['Admin'] ?? 0 }} Admin</span>
        </div>
      </div>
      <a class="kpi-link" href="{{ route('pengguna.daftar') }}">
        Kelola pengguna <i data-lucide="arrow-right" style="width:14px;height:14px"></i>
      </a>
    </div>

    {{-- Rata-rata Kehadiran --}}
    <div class="kpi-box">
      <div>
        <div class="kpi-head">
          <span>Rata-rata Kehadiran</span>
          <div class="kpi-icon"><i data-lucide="pie-chart" style="width:18px;height:18px"></i></div>
        </div>
        <div class="kpi-val">{{ $rata === null ? '-' : $rata . '%' }}</div>
        @if ($rata !== null)
          <div class="progress">
            <div class="progress-fill" data-w="{{ $rata }}"></div>
          </div>
          <div class="kpi-sub">Akumulasi kehadiran seluruh perkuliahan</div>
        @else
          <div class="kpi-sub">Belum ada data presensi tercatat</div>
        @endif
      </div>
      <a class="kpi-link" href="{{ route('statistik.admin') }}">
        Lihat rekap &amp; statistik <i data-lucide="arrow-right" style="width:14px;height:14px"></i>
      </a>
    </div>
  </div>

  {{-- Laporan Crisis Center Terbaru --}}
  <div class="card" style="margin-top:0">
    <div class="card-head-flex">
      <h3>Laporan Crisis Center Terbaru</h3>
      <a href="{{ route('crisis.admin') }}" style="font-size:13px; font-weight:700; color:var(--primary); text-decoration:none">
        Lihat Semua Laporan
      </a>
    </div>

    @if ($terbaru->isEmpty())
      <div class="empty-state-sm">Belum ada laporan kendala yang masuk.</div>
    @else
      <table>
        <thead>
          <tr>
            <th>Pelapor</th>
            <th>Kategori</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th style="text-align:right">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($terbaru as $l)
            <tr>
              <td>
                <strong>{{ $l->pelapor->nama }}</strong><br>
                <small style="color:var(--text-muted)">{{ $l->pelapor->role }}</small>
              </td>
              <td>{{ $l->kategori }}</td>
              <td style="white-space:nowrap">{{ $l->tanggalLapor->format('d/m/Y') }}</td>
              <td><span class="badge st-{{ $l->kelas_status }}">{{ $l->label_status }}</span></td>
              <td style="text-align:right">
                <a class="btn btn-batal" style="padding:6px 12px; font-size:13px"
                   href="{{ route('crisis.admin', ['id' => $l->idLaporan]) }}">
                  <i data-lucide="eye" style="width:14px;height:14px"></i> Tangani
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>

  <script>
    document.querySelectorAll('.progress-fill').forEach(function (el) {
      el.style.width = (el.dataset.w || 0) + '%';
    });
  </script>
@endsection