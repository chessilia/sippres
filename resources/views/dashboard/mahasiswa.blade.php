@extends('layouts.app')

@section('judul', 'Dashboard Mahasiswa')

@section('isi')
<style>
  /* Hero Banner Mahasiswa */
  .welcome-banner {
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
  .welcome-banner h3 {
    margin: 0 0 6px;
    font-size: 22px;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -0.3px;
  }
  .welcome-banner p {
    margin: 0;
    color: rgba(255, 255, 255, 0.92);
    font-size: 14.5px;
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

  /* Sesi Presensi Aktif */
  .active-session-card {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-left: 5px solid var(--primary);
    border-radius: var(--radius);
    padding: 18px 22px;
    margin-bottom: 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    box-shadow: var(--shadow-sm);
  }
  .session-top-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 6px;
    flex-wrap: wrap;
  }
  .timer-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #ffffff;
    border: 1px solid #bfdbfe;
    color: var(--primary-hover);
    padding: 2px 10px;
    border-radius: 99px;
    font-size: 13px;
    font-weight: 700;
  }
  .active-session-card h4 {
    margin: 0 0 5px;
    font-size: 18px;
    font-weight: 800;
    color: var(--text-main);
  }
  .session-sub-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px 16px;
    font-size: 13.5px;
    color: var(--text-secondary);
  }
  .session-sub-meta span {
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }
  .already-attended-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #ecfdf5;
    color: var(--success);
    border: 1px solid #a7f3d0;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 13.5px;
    font-weight: 700;
  }

  /* KPI Grid 4 Kolom */
  .kpi-grid-4 {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 16px;
    margin-bottom: 18px;
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
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .kpi-val {
    font-size: 30px;
    font-weight: 800;
    color: var(--text-main);
    margin-top: 8px;
    line-height: 1.15;
  }

  /* Split Bawah: Kuliah Berikutnya & Akses Cepat */
  .mhs-split-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 18px;
    align-items: start;
  }
  .mhs-split-grid .card {
    margin-top: 0;
  }
  .card-head-flex {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 12px;
    margin-bottom: 14px;
    border-bottom: 1px solid var(--border);
  }
  .card-head-flex h3 {
    margin: 0;
    padding: 0;
    border: none;
    font-size: 18px;
  }

  /* Item Jadwal Berikutnya */
  .next-class-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 13px 15px;
    border: 1px solid var(--border);
    border-radius: 9px;
    background: var(--bg-subtle);
    margin-bottom: 10px;
  }
  .next-class-row:last-child {
    margin-bottom: 0;
  }
  .day-time-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    font-weight: 700;
    color: var(--primary);
    background: var(--primary-soft);
    padding: 3px 10px;
    border-radius: 6px;
    margin-bottom: 5px;
  }
  .class-title {
    font-weight: 700;
    font-size: 15px;
    color: var(--text-main);
  }
  .class-sub {
    font-size: 13px;
    color: var(--text-secondary);
    margin-top: 2px;
    display: flex;
    align-items: center;
    gap: 5px;
  }

  /* Quick Action Links */
  .quick-action-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .quick-action-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 13px 15px;
    border: 1px solid var(--border);
    border-radius: 9px;
    background: var(--bg-subtle);
    text-decoration: none;
    color: var(--text-main);
    transition: all 0.15s ease;
  }
  .quick-action-item:hover {
    background: #ffffff;
    border-color: #93c5fd;
    box-shadow: var(--shadow-sm);
    transform: translateY(-1px);
  }
  .qa-left {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .qa-icon {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    background: var(--primary-soft);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .qa-title {
    font-weight: 700;
    font-size: 14px;
    color: var(--text-main);
  }
  .qa-desc {
    font-size: 12.5px;
    color: var(--text-muted);
    margin-top: 2px;
  }
  .empty-box {
    text-align: center;
    padding: 30px 16px;
    color: var(--text-muted);
    font-size: 14px;
    font-weight: 600;
  }

  @media (max-width: 960px) {
    .kpi-grid-4 { grid-template-columns: repeat(2, 1fr); }
    .mhs-split-grid { grid-template-columns: 1fr; }
  }
  @media (max-width: 520px) {
    .kpi-grid-4 { grid-template-columns: 1fr; }
  }
</style>

  {{-- Banner Selamat Datang --}}
  <div class="welcome-banner">
    <div>
      <h3>Hi, {{ Auth::user()->nama }}!</h3>
      <p>
        @if ($presensiHariIni > 0)
          Kamu sudah melakukan presensi hari ini sebanyak <strong>{{ $presensiHariIni }} kali</strong>.
        @else
          Belum ada catatan presensi hari ini. Pastikan pindai QR Code tepat waktu!
        @endif
      </p>
    </div>
    <a class="btn-banner-white" href="{{ route('presensi.mahasiswa') }}">
      <i data-lucide="scan-line" style="width:17px;height:17px"></i> Pindai QR Code
    </a>
  </div>

  {{-- Sesi Presensi Aktif --}}
  @forelse ($sesiAktif as $s)
    <div class="active-session-card">
      <div>
        <div class="session-top-meta">
          <span class="badge st-Selesai">Sesi Aktif</span>
          <span class="timer-pill">
            <i data-lucide="clock" style="width:13px;height:13px"></i>
            Sisa waktu: <span class="sisa" data-akhir="{{ $s->waktuBerakhir()->timestamp * 1000 }}">--:--</span>
          </span>
        </div>
        <h4>{{ $s->perkuliahan->namaMatkul }} (Kelas {{ $s->perkuliahan->kelas }})</h4>
        <div class="session-sub-meta">
          <span><i data-lucide="map-pin" style="width:14px;height:14px"></i> Ruang {{ $s->perkuliahan->ruang }}</span>
          <span><i data-lucide="play-circle" style="width:14px;height:14px"></i> Mulai {{ $s->waktuMulai->format('H:i') }}</span>
          <span><i data-lucide="hourglass" style="width:14px;height:14px"></i> Batas {{ $s->waktuBerakhir()->format('H:i') }}</span>
        </div>
      </div>

      @if ($s->sudahPresensi)
        <span class="already-attended-pill">
          <i data-lucide="check-circle-2" style="width:16px;height:16px"></i> Sudah Presensi
        </span>
      @else
        <a class="btn btn-primary" href="{{ route('presensi.mahasiswa') }}">
          <i data-lucide="qr-code" style="width:16px;height:16px"></i> Pindai QR Code
        </a>
      @endif
    </div>
  @empty
    <div class="card" style="margin-top:0; margin-bottom:18px; display:flex; align-items:center; gap:10px; color:var(--text-secondary)">
      <i data-lucide="info" style="width:18px;height:18px;color:var(--primary);flex-shrink:0"></i>
      <span>Tidak ada sesi presensi yang sedang dibuka saat ini.</span>
    </div>
  @endforelse

  {{-- Ringkasan Kehadiran --}}
  <div class="kpi-grid-4">
    <div class="kpi-box">
      <div class="kpi-head">
        <span>Hadir</span>
        <div class="kpi-icon" style="background:var(--success-bg); color:var(--success)">
          <i data-lucide="check-circle" style="width:17px;height:17px"></i>
        </div>
      </div>
      <div class="kpi-val">{{ $ringkasan['Hadir'] }}</div>
    </div>

    <div class="kpi-box">
      <div class="kpi-head">
        <span>Izin</span>
        <div class="kpi-icon" style="background:var(--primary-soft); color:var(--primary)">
          <i data-lucide="file-check" style="width:17px;height:17px"></i>
        </div>
      </div>
      <div class="kpi-val">{{ $ringkasan['Izin'] }}</div>
    </div>

    <div class="kpi-box">
      <div class="kpi-head">
        <span>Sakit</span>
        <div class="kpi-icon" style="background:var(--warning-bg); color:var(--warning)">
          <i data-lucide="heart-pulse" style="width:17px;height:17px"></i>
        </div>
      </div>
      <div class="kpi-val">{{ $ringkasan['Sakit'] }}</div>
    </div>

    <div class="kpi-box">
      <div class="kpi-head">
        <span>Alpa</span>
        <div class="kpi-icon" style="background:var(--danger-bg); color:var(--danger)">
          <i data-lucide="x-circle" style="width:17px;height:17px"></i>
        </div>
      </div>
      <div class="kpi-val">{{ $ringkasan['Alpa'] }}</div>
    </div>
  </div>

  {{-- Split Bawah: Kuliah Berikutnya & Layanan Cepat --}}
  <div class="mhs-split-grid">
    {{-- Kuliah Berikutnya --}}
    <div class="card">
      <div class="card-head-flex">
        <h3>Kuliah Berikutnya</h3>
        <a href="{{ route('jadwal') }}" style="font-size:13px; font-weight:700; color:var(--primary); text-decoration:none">
          Lihat Semua Jadwal
        </a>
      </div>

      @forelse ($berikutnya as $p)
        <div class="next-class-row">
          <div>
            <div class="day-time-chip">
              <i data-lucide="calendar-clock" style="width:13px;height:13px"></i>
              {{ $p->selisihHari === 0 ? 'Hari ini' : $p->hari }}, {{ substr($p->jamMulai, 0, 5) }} - {{ substr($p->jamSelesai, 0, 5) }}
            </div>
            <div class="class-title">{{ $p->namaMatkul }}</div>
            <div class="class-sub">
              <i data-lucide="map-pin" style="width:13px;height:13px"></i> Ruang {{ $p->ruang }}
            </div>
          </div>
          <span class="badge {{ $p->aktif ? 'st-Selesai' : 'st-Belum' }}">
            {{ $p->aktif ? 'Aktif' : 'Nanti' }}
          </span>
        </div>
      @empty
        <div class="empty-box">Belum ada jadwal perkuliahan terdaftar.</div>
      @endforelse
    </div>

    {{-- Layanan Cepat --}}
    <div class="card">
      <div class="card-head-flex">
        <h3>Layanan Mahasiswa</h3>
      </div>

      <div class="quick-action-list">
        <a class="quick-action-item" href="{{ route('izin.mahasiswa') }}">
          <div class="qa-left">
            <div class="qa-icon">
              <i data-lucide="file-plus-2" style="width:18px;height:18px"></i>
            </div>
            <div>
              <div class="qa-title">Ajukan Izin / Sakit</div>
              <div class="qa-desc">Kirim surat izin ketidakhadiran ke dosen</div>
            </div>
          </div>
          <i data-lucide="chevron-right" style="width:16px;height:16px;color:var(--text-muted)"></i>
        </a>

        <a class="quick-action-item" href="{{ route('riwayat') }}">
          <div class="qa-left">
            <div class="qa-icon">
              <i data-lucide="history" style="width:18px;height:18px"></i>
            </div>
            <div>
              <div class="qa-title">Riwayat Kehadiran</div>
              <div class="qa-desc">Cek rekap presensi dan status pengajuan izin</div>
            </div>
          </div>
          <i data-lucide="chevron-right" style="width:16px;height:16px;color:var(--text-muted)"></i>
        </a>

        <a class="quick-action-item" href="{{ route('crisis.pengguna') }}">
          <div class="qa-left">
            <div class="qa-icon" style="background:var(--warning-bg); color:var(--warning)">
              <i data-lucide="life-buoy" style="width:18px;height:18px"></i>
            </div>
            <div>
              <div class="qa-title">Lapor Kendala</div>
              <div class="qa-desc">Hubungi admin jika terjadi masalah presensi</div>
            </div>
          </div>
          <i data-lucide="chevron-right" style="width:16px;height:16px;color:var(--text-muted)"></i>
        </a>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    function hitungMundur() {
      document.querySelectorAll('.sisa').forEach(function (el) {
        var sisa = Math.floor((Number(el.dataset.akhir) - Date.now()) / 1000);
        if (sisa <= 0) { location.reload(); return; }
        el.textContent = String(Math.floor(sisa / 60)).padStart(2, '0') + ':' + String(sisa % 60).padStart(2, '0');
      });
    }
    hitungMundur();
    setInterval(hitungMundur, 1000);
  </script>
@endpush