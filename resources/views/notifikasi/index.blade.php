@extends('layouts.app')

@section('judul', 'Notifikasi')

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

  /* Split Grid saat Detail Dibuka */
  .notif-layout {
    display: grid;
    grid-template-columns: 1fr;
    gap: 18px;
    align-items: start;
  }
  .notif-layout.has-detail {
    grid-template-columns: 1.25fr 1fr;
  }

  /* Daftar Item Notifikasi */
  .notif-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .notif-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 14px;
    padding: 14px 16px;
    border: 1px solid var(--border);
    border-radius: 9px;
    background: #ffffff;
    text-decoration: none;
    color: var(--text-main);
    transition: all 0.15s ease;
  }
  .notif-item:hover {
    border-color: #93c5fd;
    background: var(--bg-subtle);
    transform: translateY(-1px);
  }
  .notif-item.unread {
    background: #f8fafc;
    border-left: 4px solid var(--primary);
  }
  .notif-item.active {
    background: #eff6ff;
    border-color: var(--primary);
    border-left: 4px solid var(--primary);
    box-shadow: var(--shadow-sm);
  }
  .notif-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: var(--primary-soft);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 2px;
  }
  .notif-body {
    flex: 1;
    min-width: 0;
  }
  .notif-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 4px;
    flex-wrap: wrap;
  }
  .notif-title {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    font-size: 14.5px;
    color: var(--text-main);
  }
  .notif-time {
    font-size: 12.5px;
    color: var(--text-muted);
    white-space: nowrap;
  }
  .notif-excerpt {
    font-size: 13.5px;
    color: var(--text-secondary);
    line-height: 1.5;
  }

  /* Panel Detail Notifikasi */
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
  .detail-content {
    background: var(--bg-subtle);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 15px 16px;
    font-size: 14.5px;
    line-height: 1.65;
    color: var(--text-main);
    white-space: pre-line;
  }
  .detail-footer {
    margin-top: 16px;
    padding-top: 14px;
    border-top: 1px solid var(--border);
    display: flex;
    justify-content: flex-end;
  }
  .empty-box {
    text-align: center;
    padding: 40px 20px;
    color: var(--text-muted);
    font-weight: 600;
  }

  @media (max-width: 900px) {
    .notif-layout,
    .notif-layout.has-detail {
      grid-template-columns: 1fr;
    }
    .detail-card {
      position: static;
    }
  }
</style>

  <div class="page-header-bar">
    <p class="page-subtitle">Klik salah satu notifikasi untuk membaca pesan lengkapnya.</p>
    <span class="badge {{ $belumDibaca > 0 ? 'st-Baru' : 'st-Selesai' }}" style="padding:5px 12px; font-size:13px">
      {{ $belumDibaca }} belum dibaca
    </span>
  </div>

  <div class="notif-layout {{ $detail ? 'has-detail' : '' }}">
    {{-- Daftar Notifikasi --}}
    <div class="card" style="margin-top:0">
      @if ($daftar->isEmpty())
        <div class="empty-box">Belum terdapat notifikasi terbaru.</div>
      @else
        <div class="notif-list">
          @foreach ($daftar as $n)
            @php
              $isSelected = $detail && $detail->idNotifikasi == $n->idNotifikasi;
            @endphp
            <a class="notif-item {{ !$n->statusBaca ? 'unread' : '' }} {{ $isSelected ? 'active' : '' }}"
               href="{{ route('notifikasi', ['id' => $n->idNotifikasi]) }}">
              <div class="notif-icon">
                <i data-lucide="bell" style="width:17px;height:17px"></i>
              </div>

              <div class="notif-body">
                <div class="notif-top">
                  <span class="notif-title">
                    <span>{{ $n->jenis }}</span>
                    @unless ($n->statusBaca)
                      <span class="badge st-Baru">Baru</span>
                    @endunless
                  </span>
                  <span class="notif-time">{{ $n->waktu->format('d/m/Y H:i') }}</span>
                </div>
                <div class="notif-excerpt">{{ \Illuminate\Support\Str::limit($n->isi, 85) }}</div>
              </div>
            </a>
          @endforeach
        </div>
      @endif
    </div>

    {{-- Detail Notifikasi --}}
    @if ($detail)
      <div class="card detail-card">
        <div class="detail-header">
          <div>
            <h3>{{ $detail->jenis }}</h3>
            <span class="detail-meta">
              <i data-lucide="clock" style="width:13px;height:13px"></i>
              {{ $detail->waktu->format('d/m/Y H:i') }}
            </span>
          </div>
          <a href="{{ route('notifikasi') }}" class="btn btn-batal" style="padding:5px 10px; font-size:12.5px">
            <i data-lucide="x" style="width:14px;height:14px"></i> Tutup
          </a>
        </div>

        <div class="detail-content">{{ $detail->isi }}</div>

        @if ($detail->jenis === 'Izin' && Auth::user()->role === 'Mahasiswa')
          <div class="detail-footer">
            <a class="btn btn-primary" href="{{ route('riwayat', ['jenis' => 'izin']) }}">
              <i data-lucide="file-text" style="width:15px;height:15px"></i> Lihat Riwayat Izin
            </a>
          </div>
        @endif
      </div>
    @endif
  </div>
@endsection