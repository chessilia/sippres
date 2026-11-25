@extends('layouts.app')

@section('judul', 'Jadwal Perkuliahan')

@section('isi')
<style>
  .page-subtitle {
    margin: 0 0 18px;
    color: var(--text-secondary);
    font-size: 14.5px;
  }

  /* Day Tabs */
  .days-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    border-bottom: 2px solid var(--border);
  }
  .days-tabs .tab {
    margin-right: 0;
    border-radius: 8px 8px 0 0;
    padding: 9px 18px;
    font-size: 14px;
  }
  .jadwal-card {
    margin-top: 0;
    border-top-left-radius: 0;
    border-top: none;
  }

  /* Split Grid saat Detail Dipilih */
  .jadwal-layout {
    display: grid;
    grid-template-columns: 1fr;
    gap: 18px;
    align-items: start;
  }
  .jadwal-layout.has-detail {
    grid-template-columns: 1.2fr 1.05fr;
  }

  /* Schedule Item Card */
  .schedule-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    padding: 16px 18px;
    border: 1px solid var(--border);
    border-radius: 10px;
    background: var(--bg-subtle);
    text-decoration: none;
    color: var(--text-main);
    margin-bottom: 12px;
    transition: all 0.15s ease;
  }
  .schedule-item:last-child {
    margin-bottom: 0;
  }
  .schedule-item:hover {
    background: #ffffff;
    border-color: #93c5fd;
    box-shadow: var(--shadow-sm);
    transform: translateY(-1px);
  }
  .schedule-item.active {
    background: #eff6ff;
    border-color: var(--primary);
    border-left: 4px solid var(--primary);
  }
  .schedule-badges {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 7px;
  }
  .time-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    font-weight: 700;
    color: var(--primary);
    background: var(--primary-soft);
    padding: 3px 10px;
    border-radius: 6px;
  }
  .pertemuan-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12.5px;
    font-weight: 700;
    color: #334155;
    background: #e2e8f0;
    padding: 3px 10px;
    border-radius: 6px;
  }
  .peserta-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12.5px;
    font-weight: 700;
    color: #15803d;
    background: #dcfce7;
    padding: 3px 10px;
    border-radius: 6px;
  }
  .matkul-title {
    font-size: 16px;
    font-weight: 800;
    color: var(--text-main);
    margin-bottom: 4px;
  }
  .matkul-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 6px 16px;
    font-size: 13.5px;
    color: var(--text-secondary);
  }
  .matkul-meta span {
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }

  /* Tombol Aksi di Sisi Kanan Kartu */
  .card-action-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 8px;
    border: 1px solid var(--border);
    background: #ffffff;
    color: var(--text-main);
    font-size: 13px;
    font-weight: 700;
    white-space: nowrap;
    flex-shrink: 0;
    transition: all 0.15s ease;
  }
  .schedule-item:hover .card-action-pill {
    border-color: var(--primary);
    color: var(--primary);
    background: #eff6ff;
  }
  .schedule-item.active .card-action-pill {
    border-color: var(--primary);
    background: var(--primary);
    color: #ffffff;
  }

  /* Mini Rekap Kehadiran di Kartu (Mahasiswa) */
  .mini-rekap-bar {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px dashed var(--border);
  }
  .rekap-chip {
    font-size: 12px;
    font-weight: 700;
    padding: 2px 9px;
    border-radius: 99px;
    background: #ffffff;
    border: 1px solid var(--border);
    color: var(--text-secondary);
  }
  .rekap-chip.hadir { background: #f0fdf4; border-color: #bbf7d0; color: #15803d; }
  .rekap-chip.izin  { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }
  .rekap-chip.sakit { background: #fefce8; border-color: #fef08a; color: #a16207; }
  .rekap-chip.alpa  { background: #fef2f2; border-color: #fecaca; color: #b91c1c; }

  /* Detail Panel */
  .detail-card {
    margin-top: 0;
    position: sticky;
    top: 84px;
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
    gap: 10px;
  }
  .detail-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    padding-bottom: 8px;
    border-bottom: 1px dashed var(--border);
    font-size: 13.5px;
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

  /* Daftar Peserta Kelas + Persentase Kehadiran (Dosen) */
  .peserta-section {
    margin-top: 16px;
    padding-top: 14px;
    border-top: 1px solid var(--border);
  }
  .peserta-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
  }
  .peserta-section-title {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    font-weight: 800;
    color: var(--text-main);
  }
  .peserta-list-box {
    max-height: 320px;
    overflow-y: auto;
    border: 1px solid var(--border);
    border-radius: 9px;
    background: #ffffff;
  }
  .peserta-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 10px 14px;
    border-bottom: 1px solid var(--border);
  }
  .peserta-item:nth-child(even) {
    background: var(--bg-subtle);
  }
  .peserta-item:last-child {
    border-bottom: none;
  }
  .peserta-left {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
  }
  .peserta-num {
    width: 24px;
    height: 24px;
    border-radius: 99px;
    background: #e2e8f0;
    color: #334155;
    font-size: 11.5px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .peserta-name {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--text-main);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .persen-badge {
    font-size: 12.5px;
    font-weight: 800;
    padding: 3px 11px;
    border-radius: 99px;
    border: 1px solid transparent;
    white-space: nowrap;
    flex-shrink: 0;
  }
  .persen-badge.aman {
    background: #f0fdf4;
    border-color: #bbf7d0;
    color: #15803d;
  }
  .persen-badge.waspada {
    background: #fefce8;
    border-color: #fef08a;
    color: #a16207;
  }
  .persen-badge.kurang {
    background: #fef2f2;
    border-color: #fecaca;
    color: #b91c1c;
  }
  .persen-badge.netral {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #475569;
  }

  /* Kotak Statistik Kehadiran di Panel Detail (Mahasiswa) */
  .rekap-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    margin-top: 16px;
    padding-top: 14px;
    border-top: 1px solid var(--border);
  }
  .rekap-box {
    text-align: center;
    padding: 10px 6px;
    border-radius: 8px;
    border: 1px solid var(--border);
    background: var(--bg-subtle);
  }
  .rekap-box .num {
    font-size: 18px;
    font-weight: 800;
    line-height: 1.2;
  }
  .rekap-box .lbl {
    font-size: 11.5px;
    font-weight: 700;
    margin-top: 2px;
  }
  .rekap-box.hadir { background: #f0fdf4; border-color: #bbf7d0; color: #15803d; }
  .rekap-box.izin  { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }
  .rekap-box.sakit { background: #fefce8; border-color: #fef08a; color: #a16207; }
  .rekap-box.alpa  { background: #fef2f2; border-color: #fecaca; color: #b91c1c; }

  .btn-riwayat-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    width: 100%;
    margin-top: 16px;
    padding: 10px 16px;
    border-radius: 8px;
    border: 1px solid var(--primary);
    background: var(--primary);
    color: #ffffff;
    font-size: 13.5px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.15s ease;
  }
  .btn-riwayat-link:hover {
    background: var(--primary-hover);
    border-color: var(--primary-hover);
  }

  .empty-box {
    text-align: center;
    padding: 36px 20px;
    color: var(--text-muted);
    font-weight: 600;
  }

  @media (max-width: 960px) {
    .jadwal-layout,
    .jadwal-layout.has-detail {
      grid-template-columns: 1fr;
    }
    .detail-card {
      position: static;
    }
  }
  @media (max-width: 600px) {
    .schedule-item {
      flex-direction: column;
      align-items: stretch;
    }
    .card-action-pill {
      justify-content: center;
    }
  }
</style>

  <p class="page-subtitle">
    @if (Auth::user()->role === 'Dosen')
      Pilih hari untuk melihat jadwal mengajar, lalu klik <strong>Lihat Detail</strong> untuk memantau persentase kehadiran peserta kelas.
    @else
      Pilih hari untuk melihat daftar perkuliahan, lalu klik <strong>Lihat Detail</strong> untuk memantau rekap &amp; riwayatnya.
    @endif
  </p>

  {{-- Pemilih Hari --}}
  <div class="days-tabs">
    @foreach (['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
      <a class="tab {{ $hari == $h ? 'aktif' : '' }}" href="{{ route('jadwal', ['hari' => $h]) }}">{{ $h }}</a>
    @endforeach
  </div>

  <div class="jadwal-layout {{ $detail ? 'has-detail' : '' }}">
    {{-- Daftar Jadwal Hari Terpilih --}}
    <div class="card jadwal-card">
      @if ($daftarJadwal->isEmpty())
        <div class="empty-box">Jadwal perkuliahan belum tersedia.</div>
      @elseif ($jadwalHari->isEmpty())
        <div class="empty-box">Tidak ada perkuliahan pada hari <strong>{{ $hari }}</strong>.</div>
      @else
        @foreach ($jadwalHari as $j)
          @php $isSelected = $detail && $detail->idPerkuliahan == $j->idPerkuliahan; @endphp
          <a class="schedule-item {{ $isSelected ? 'active' : '' }}"
             href="{{ route('jadwal', ['hari' => $hari, 'id' => $j->idPerkuliahan]) }}">
            <div style="flex:1">
              <div class="schedule-badges">
                <span class="time-pill">
                  <i data-lucide="clock" style="width:13px;height:13px"></i>
                  {{ substr($j->jamMulai, 0, 5) }} - {{ substr($j->jamSelesai, 0, 5) }}
                </span>
                <span class="pertemuan-pill">
                  <i data-lucide="book-open" style="width:13px;height:13px"></i>
                  {{ $j->totalPertemuan > 0 ? 'Pertemuan ke-' . $j->totalPertemuan : 'Belum ada pertemuan' }}
                </span>
                @if (isset($j->jumlahPeserta))
                  <span class="peserta-pill">
                    <i data-lucide="users" style="width:13px;height:13px"></i>
                    {{ $j->jumlahPeserta }} Mahasiswa
                  </span>
                @endif
              </div>

              <div class="matkul-title">{{ $j->namaMatkul }}</div>
              <div class="matkul-meta">
                <span><i data-lucide="layers" style="width:14px;height:14px"></i> Kelas {{ $j->kelas }}</span>
                <span><i data-lucide="map-pin" style="width:14px;height:14px"></i> Ruang {{ $j->ruang }}</span>
                @if ($j->dosen)
                  <span><i data-lucide="user" style="width:14px;height:14px"></i> {{ $j->dosen->nama }}</span>
                @endif
              </div>

              @if (isset($j->rekapKehadiran))
                <div class="mini-rekap-bar">
                  <span class="rekap-chip hadir">Hadir: {{ $j->rekapKehadiran['Hadir'] }}</span>
                  <span class="rekap-chip izin">Izin: {{ $j->rekapKehadiran['Izin'] }}</span>
                  <span class="rekap-chip sakit">Sakit: {{ $j->rekapKehadiran['Sakit'] }}</span>
                  <span class="rekap-chip alpa">Alpa: {{ $j->rekapKehadiran['Alpa'] }}</span>
                </div>
              @endif
            </div>

            <span class="card-action-pill">
              @if ($isSelected)
                <i data-lucide="eye" style="width:14px;height:14px"></i> Sedang Dilihat
              @else
                Lihat Detail <i data-lucide="arrow-right" style="width:14px;height:14px"></i>
              @endif
            </span>
          </a>
        @endforeach
      @endif
    </div>

    {{-- Detail Perkuliahan --}}
    @if ($detail)
      <div class="card detail-card">
        <div class="detail-header">
          <h3>Detail Perkuliahan</h3>
          <a href="{{ route('jadwal', ['hari' => $hari]) }}" class="btn btn-batal" style="padding:5px 10px; font-size:12.5px">
            <i data-lucide="x" style="width:14px;height:14px"></i> Tutup
          </a>
        </div>

        <div class="detail-list">
          <div class="detail-row">
            <span class="detail-label">Kode Mata Kuliah</span>
            <span class="detail-val"><span class="badge st-Izin">{{ $detail->kodeMatkul }}</span></span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Mata Kuliah</span>
            <span class="detail-val">{{ $detail->namaMatkul }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Kelas</span>
            <span class="detail-val">Kelas {{ $detail->kelas }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Hari &amp; Waktu</span>
            <span class="detail-val">{{ $detail->hari }}, {{ substr($detail->jamMulai, 0, 5) }} - {{ substr($detail->jamSelesai, 0, 5) }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Ruang Kuliah</span>
            <span class="detail-val">{{ $detail->ruang }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Dosen Pengampu</span>
            <span class="detail-val">{{ $detail->dosen->nama ?? '-' }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Pertemuan Terlaksana</span>
            <span class="detail-val">{{ $detail->totalPertemuan > 0 ? 'Pertemuan ke-' . $detail->totalPertemuan : 'Belum ada pertemuan' }}</span>
          </div>
          @if (isset($detail->jumlahPeserta))
            <div class="detail-row">
              <span class="detail-label">Jumlah Peserta</span>
              <span class="detail-val">{{ $detail->jumlahPeserta }} Mahasiswa</span>
            </div>
          @endif
        </div>

        {{-- Daftar Peserta Kelas & Persentase Kehadiran (Khusus Dosen) --}}
        @if (isset($detail->daftarPeserta))
          <div class="peserta-section">
            <div class="peserta-section-header">
              <span class="peserta-section-title">
                <i data-lucide="users" style="width:15px;height:15px;color:var(--primary)"></i>
                Peserta Kelas ({{ $detail->jumlahPeserta }})
              </span>
              <span style="font-size:12px; font-weight:700; color:var(--text-muted)">Persentase Kehadiran</span>
            </div>

            @if ($detail->daftarPeserta->isEmpty())
              <div style="padding:16px; text-align:center; font-size:13px; color:var(--text-muted); background:var(--bg-subtle); border:1px solid var(--border); border-radius:8px">
                Belum ada mahasiswa yang terdaftar pada kelas ini.
              </div>
            @else
              <div class="peserta-list-box">
                @foreach ($detail->daftarPeserta as $idx => $mhs)
                  @php
                    $persen = $mhs->persentaseHadir ?? 0;
                    if ($mhs->persentaseHadir === null) {
                        $kelasPersen = 'netral';
                    } elseif ($persen >= 75) {
                        $kelasPersen = 'aman';
                    } elseif ($persen >= 50) {
                        $kelasPersen = 'waspada';
                    } else {
                        $kelasPersen = 'kurang';
                    }
                  @endphp
                  <div class="peserta-item">
                    <div class="peserta-left">
                      <span class="peserta-num">{{ $idx + 1 }}</span>
                      <span class="peserta-name">{{ $mhs->nama }}</span>
                    </div>
                    <span class="persen-badge {{ $kelasPersen }}">{{ $persen }}%</span>
                  </div>
                @endforeach
              </div>
            @endif
          </div>
        @endif

        {{-- Statistik & Riwayat (Khusus Mahasiswa) --}}
        @if (isset($detail->rekapKehadiran))
          <div class="rekap-grid">
            <div class="rekap-box hadir">
              <div class="num">{{ $detail->rekapKehadiran['Hadir'] }}</div>
              <div class="lbl">Hadir</div>
            </div>
            <div class="rekap-box izin">
              <div class="num">{{ $detail->rekapKehadiran['Izin'] }}</div>
              <div class="lbl">Izin</div>
            </div>
            <div class="rekap-box sakit">
              <div class="num">{{ $detail->rekapKehadiran['Sakit'] }}</div>
              <div class="lbl">Sakit</div>
            </div>
            <div class="rekap-box alpa">
              <div class="num">{{ $detail->rekapKehadiran['Alpa'] }}</div>
              <div class="lbl">Alpa</div>
            </div>
          </div>

          <a href="{{ route('riwayat', ['jenis' => 'presensi', 'matkul' => $detail->idPerkuliahan]) }}" class="btn-riwayat-link">
            <i data-lucide="history" style="width:15px;height:15px"></i> Lihat Riwayat Mata Kuliah Ini
          </a>
        @endif
      </div>
    @endif
  </div>
@endsection