@extends('layouts.app')

@section('judul', 'Dashboard Dosen')

@section('isi')
<style>
  /* Hero Banner Dosen */
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
    color: rgba(255, 255, 255, 0.9);
    font-size: 14.5px;
  }
  .btn-banner {
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
  .btn-banner:hover {
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
    margin-bottom: 16px;
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
    margin: 0 0 4px;
    font-size: 18px;
    font-weight: 800;
    color: var(--text-main);
  }
  .session-sub-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 14px;
    font-size: 13.5px;
    color: var(--text-secondary);
  }
  .session-sub-meta span {
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }

  /* KPI Grid */
  .kpi-grid-dosen {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 16px;
    margin-bottom: 18px;
  }
  .kpi-box {
    background: #ffffff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 20px;
    box-shadow: var(--shadow-sm);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
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
    font-size: 30px;
    font-weight: 800;
    color: var(--text-main);
    margin: 8px 0 10px;
    line-height: 1.15;
  }
  .kpi-link {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 13.5px;
    font-weight: 700;
    color: var(--primary);
    text-decoration: none;
    margin-top: 8px;
  }
  .kpi-link:hover {
    text-decoration: underline;
  }
  .progress-fill {
    width: var(--w, 0%);
  }

  /* Split 2 Kolom Bawah */
  .dosen-split-grid {
    display: grid;
    grid-template-columns: 1.15fr 1fr;
    gap: 18px;
  }
  .dosen-split-grid .card {
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

  /* Item List */
  .list-item-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: var(--bg-subtle);
    margin-bottom: 10px;
  }
  .list-item-row:last-child {
    margin-bottom: 0;
  }
  .time-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12.5px;
    font-weight: 700;
    color: var(--primary);
    background: var(--primary-soft);
    padding: 3px 9px;
    border-radius: 6px;
    margin-bottom: 4px;
  }
  .item-title {
    font-weight: 700;
    font-size: 14.5px;
    color: var(--text-main);
  }
  .item-sub {
    font-size: 13px;
    color: var(--text-secondary);
    margin-top: 2px;
  }
  .empty-state-sm {
    text-align: center;
    padding: 28px 16px;
    color: var(--text-muted);
    font-size: 14px;
    font-weight: 600;
  }

  @media (max-width: 960px) {
    .dosen-split-grid { grid-template-columns: 1fr; }
  }
</style>

  {{-- Banner Selamat Datang --}}
  <div class="welcome-banner">
    <div>
      <h3>Hi, {{ Auth::user()->nama }}!</h3>
      <p>
        @if ($jadwalHariIni->isEmpty())
          Tidak ada jadwal mengajar hari ini. Selamat beraktivitas!
        @else
          Anda memiliki <strong>{{ $jadwalHariIni->count() }} perkuliahan</strong> yang dijadwalkan hari ini.
        @endif
      </p>
    </div>
    <a class="btn-banner" href="{{ route('presensi.dosen') }}">
      <i data-lucide="qr-code" style="width:17px;height:17px"></i> Kelola Presensi QR
    </a>
  </div>

  {{-- Sesi presensi aktif --}}
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
          <span><i data-lucide="users" style="width:14px;height:14px"></i> Hadir: <strong>{{ $s->hadir }} / {{ $s->total }}</strong> mahasiswa</span>
        </div>
      </div>
      <a class="btn btn-primary" href="{{ route('presensi.dosen') }}">
        <i data-lucide="settings-2" style="width:15px;height:15px"></i> Kelola Sesi
      </a>
    </div>
  @empty
    <div class="card" style="margin-top:0; margin-bottom:18px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px">
      <div style="display:flex; align-items:center; gap:10px; color:var(--text-secondary)">
        <i data-lucide="info" style="width:18px;height:18px;color:var(--primary)"></i>
        <span>Belum ada sesi presensi yang sedang dibuka saat ini.</span>
      </div>
      <a class="btn btn-primary" href="{{ route('presensi.dosen') }}">
        <i data-lucide="plus-circle" style="width:15px;height:15px"></i> Buat Sesi Presensi
      </a>
    </div>
  @endforelse

  {{-- Ringkasan Statistik --}}
  <div class="kpi-grid-dosen">
    <div class="kpi-box">
      <div>
        <div class="kpi-head">
          <span>Izin Menunggu</span>
          <div class="kpi-icon"><i data-lucide="file-check-2" style="width:17px;height:17px"></i></div>
        </div>
        <div class="kpi-val">{{ $izinMenunggu->count() }}</div>
      </div>
      <a class="kpi-link" href="{{ route('izin.dosen') }}">
        Proses pengajuan <i data-lucide="arrow-right" style="width:14px;height:14px"></i>
      </a>
    </div>

    <div class="kpi-box">
      <div>
        <div class="kpi-head">
          <span>Rata-rata Kehadiran</span>
          <div class="kpi-icon"><i data-lucide="bar-chart-3" style="width:17px;height:17px"></i></div>
        </div>
        <div class="kpi-val">{{ $rata === null ? '-' : $rata . '%' }}</div>
        @if ($rata !== null)
          <div class="progress">
            <div class="progress-fill" data-w="{{ $rata }}"></div>
          </div>
        @endif
      </div>
      <a class="kpi-link" href="{{ route('rekap.dosen') }}">
        Lihat rekap lengkap <i data-lucide="arrow-right" style="width:14px;height:14px"></i>
      </a>
    </div>

    <div class="kpi-box">
      <div>
        <div class="kpi-head">
          <span>Jadwal Hari Ini</span>
          <div class="kpi-icon"><i data-lucide="calendar" style="width:17px;height:17px"></i></div>
        </div>
        <div class="kpi-val">{{ $jadwalHariIni->count() }} <span style="font-size:15px; font-weight:600; color:var(--text-muted)">kelas</span></div>
      </div>
      <a class="kpi-link" href="{{ route('jadwal') }}">
        Semua jadwal mengajar <i data-lucide="arrow-right" style="width:14px;height:14px"></i>
      </a>
    </div>
  </div>

  {{-- Grid Bawah: Jadwal & Pengajuan Izin --}}
  <div class="dosen-split-grid">
    {{-- Jadwal hari ini --}}
    <div class="card">
      <div class="card-head-flex">
        <h3>Jadwal Mengajar Hari Ini</h3>
        <a href="{{ route('jadwal') }}" style="font-size:13px; font-weight:700; color:var(--primary); text-decoration:none">Lihat Semua</a>
      </div>

      @forelse ($jadwalHariIni as $p)
        <div class="list-item-row">
          <div>
            <div class="time-chip">
              <i data-lucide="clock" style="width:12px;height:12px"></i>
              {{ substr($p->jamMulai, 0, 5) }} - {{ substr($p->jamSelesai, 0, 5) }}
            </div>
            <div class="item-title">{{ $p->namaMatkul }} (Kelas {{ $p->kelas }})</div>
            <div class="item-sub">Ruang {{ $p->ruang }}</div>
          </div>
          <span class="badge {{ $p->sesiAktif ? 'st-Selesai' : 'st-Belum' }}">
            {{ $p->sesiAktif ? 'Sesi Aktif' : 'Belum Dibuka' }}
          </span>
        </div>
      @empty
        <div class="empty-state-sm">Tidak ada jadwal mengajar hari ini.</div>
      @endforelse
    </div>

    {{-- Pengajuan izin terbaru --}}
    <div class="card">
      <div class="card-head-flex">
        <h3>Pengajuan Izin Terbaru</h3>
        <a href="{{ route('izin.dosen') }}" style="font-size:13px; font-weight:700; color:var(--primary); text-decoration:none">Semua Izin</a>
      </div>

      @forelse ($izinMenunggu->take(3) as $z)
        <div class="list-item-row">
          <div style="min-width:0">
            <div class="item-title">{{ $z->mahasiswa->nama }}</div>
            <div class="item-sub">{{ $z->perkuliahan->namaMatkul }} &bull; Pertemuan {{ $z->pertemuan }}</div>
            <div class="item-sub" style="color:var(--text-muted); margin-top:4px">{{ \Illuminate\Support\Str::limit($z->alasan, 75) }}</div>
          </div>
          <a class="btn btn-batal" style="padding:6px 12px; font-size:13px; flex-shrink:0"
             href="{{ route('izin.dosen', ['id' => $z->idIzin]) }}">
            Tinjau
          </a>
        </div>
      @empty
        <div class="empty-state-sm">Tidak ada pengajuan izin yang menunggu.</div>
      @endforelse
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    document.querySelectorAll('.progress-fill').forEach(function (el) {
      el.style.width = (el.dataset.w || 0) + '%';
    });

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