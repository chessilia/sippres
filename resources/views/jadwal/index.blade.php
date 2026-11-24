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
    grid-template-columns: 1.25fr 1fr;
  }

  /* Schedule Item Card */
  .schedule-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    padding: 15px 18px;
    border: 1px solid var(--border);
    border-radius: 9px;
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
  .time-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 700;
    color: var(--primary);
    background: var(--primary-soft);
    padding: 3px 10px;
    border-radius: 6px;
    margin-bottom: 6px;
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
  .empty-box {
    text-align: center;
    padding: 36px 20px;
    color: var(--text-muted);
    font-weight: 600;
  }

  @media (max-width: 900px) {
    .jadwal-layout,
    .jadwal-layout.has-detail {
      grid-template-columns: 1fr;
    }
    .detail-card {
      position: static;
    }
  }
</style>

  <p class="page-subtitle">Pilih hari untuk melihat daftar perkuliahan, lalu klik mata kuliah untuk melihat detailnya.</p>

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
          <a class="schedule-item {{ $detail && $detail->idPerkuliahan == $j->idPerkuliahan ? 'active' : '' }}"
             href="{{ route('jadwal', ['hari' => $hari, 'id' => $j->idPerkuliahan]) }}">
            <div>
              <div class="time-pill">
                <i data-lucide="clock" style="width:13px;height:13px"></i>
                {{ substr($j->jamMulai, 0, 5) }} - {{ substr($j->jamSelesai, 0, 5) }}
              </div>
              <div class="matkul-title">{{ $j->namaMatkul }}</div>
              <div class="matkul-meta">
                <span><i data-lucide="layers" style="width:14px;height:14px"></i> Kelas {{ $j->kelas }}</span>
                <span><i data-lucide="map-pin" style="width:14px;height:14px"></i> Ruang {{ $j->ruang }}</span>
                @if ($j->dosen)
                  <span><i data-lucide="user" style="width:14px;height:14px"></i> {{ $j->dosen->nama }}</span>
                @endif
              </div>
            </div>
            <i data-lucide="chevron-right" style="width:18px;height:18px;color:var(--text-muted);flex-shrink:0"></i>
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
        </div>
      </div>
    @endif
  </div>
@endsection