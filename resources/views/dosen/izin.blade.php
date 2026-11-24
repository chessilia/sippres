@extends('layouts.app')

@section('judul', 'Pengajuan Izin')

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

  /* Split Grid saat Detail Dipilih */
  .izin-layout {
    display: grid;
    grid-template-columns: 1fr;
    gap: 18px;
    align-items: start;
  }
  .izin-layout.has-detail {
    grid-template-columns: 1.2fr 1fr;
  }

  .row-active {
    background: #eff6ff !important;
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
  .detail-sub {
    font-size: 13px;
    color: var(--text-muted);
    margin-top: 3px;
  }

  .detail-list {
    display: flex;
    flex-direction: column;
    gap: 11px;
    margin-bottom: 16px;
  }
  .detail-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    padding-bottom: 9px;
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

  .alasan-box {
    background: var(--bg-subtle);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 13px 15px;
    margin-bottom: 16px;
    font-size: 14px;
    line-height: 1.6;
  }
  .alasan-label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--text-muted);
    margin-bottom: 5px;
  }

  .decision-footer {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 18px;
    padding-top: 16px;
    border-top: 1px solid var(--border);
  }
  .empty-box {
    text-align: center;
    padding: 36px 20px;
    color: var(--text-muted);
    font-weight: 600;
  }

  @media (max-width: 960px) {
    .izin-layout,
    .izin-layout.has-detail {
      grid-template-columns: 1fr;
    }
    .detail-card {
      position: static;
    }
  }
</style>

  <div class="page-header-bar">
    <p class="page-subtitle">Periksa pengajuan izin mahasiswa pada mata kuliah yang Anda ampu, lalu setujui atau tolak.</p>
    <span class="badge st-Menunggu" style="padding:5px 12px; font-size:13px">
      {{ $jumlahMenunggu }} menunggu persetujuan
    </span>
  </div>

  <div class="izin-layout {{ $detail ? 'has-detail' : '' }}">
    {{-- Daftar Pengajuan --}}
    <div class="card" style="margin-top:0">
      @if ($daftar->isEmpty())
        <div class="empty-box">Belum ada pengajuan izin pada mata kuliah yang Anda ampu.</div>
      @else
        <table>
          <thead>
            <tr>
              <th>Mahasiswa</th>
              <th>Mata Kuliah</th>
              <th style="text-align:center">Pertemuan</th>
              <th>Status</th>
              <th style="text-align:right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($daftar as $z)
              <tr class="{{ $detail && $detail->idIzin == $z->idIzin ? 'row-active' : '' }}">
                <td>
                  <strong>{{ $z->mahasiswa->nama }}</strong><br>
                  <small style="color:var(--text-muted)">{{ $z->mahasiswa->username }}</small>
                </td>
                <td>
                  {{ $z->perkuliahan->namaMatkul }}<br>
                  <small style="color:var(--text-muted)">Kelas {{ $z->perkuliahan->kelas }}</small>
                </td>
                <td style="text-align:center">
                  <span class="badge st-Izin">Ke-{{ $z->pertemuan }}</span>
                </td>
                <td><span class="badge st-{{ $z->status }}">{{ $z->status }}</span></td>
                <td style="text-align:right">
                  <a class="btn {{ $detail && $detail->idIzin == $z->idIzin ? 'btn-primary' : 'btn-batal' }}"
                     style="padding:6px 12px; font-size:13px"
                     href="{{ route('izin.dosen', ['id' => $z->idIzin]) }}">
                    <i data-lucide="eye" style="width:14px;height:14px"></i> Tinjau
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </div>

    {{-- Detail Pengajuan --}}
    @if ($detail)
      <div class="card detail-card">
        <div class="detail-header">
          <div>
            <h3>{{ $detail->mahasiswa->nama }}</h3>
            <div class="detail-sub">{{ $detail->mahasiswa->username }} &bull; {{ $detail->mahasiswa->email }}</div>
          </div>
          <span class="badge st-{{ $detail->status }}">{{ $detail->status }}</span>
        </div>

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
            <span class="detail-label">Bukti Pendukung</span>
            <span class="detail-val">
              @if ($detail->fileBukti)
                <a class="btn btn-batal" style="padding:4px 10px; font-size:12.5px"
                   href="{{ route('izin.bukti', $detail->idIzin) }}" target="_blank">
                  <i data-lucide="paperclip" style="width:13px;height:13px;color:var(--primary)"></i> Lihat Lampiran
                </a>
              @else
                <span style="color:var(--text-muted); font-weight:500">Tidak dilampirkan</span>
              @endif
            </span>
          </div>
        </div>

        <div class="alasan-box">
          <span class="alasan-label">Alasan Ketidakhadiran</span>
          <div>{{ $detail->alasan }}</div>
        </div>

        @if ($detail->status === 'Menunggu')
          <div class="decision-footer">
            <form method="POST" action="{{ route('izin.proses', $detail->idIzin) }}" style="margin:0"
                  onsubmit="return confirm('Tolak pengajuan izin ini?')">
              @csrf
              <input type="hidden" name="keputusan" value="Tolak">
              <button type="submit" class="btn-hapus">
                <i data-lucide="x" style="width:15px;height:15px"></i> Tolak Pengajuan
              </button>
            </form>

            <form method="POST" action="{{ route('izin.proses', $detail->idIzin) }}" style="margin:0"
                  onsubmit="return confirm('Setujui pengajuan izin ini?')">
              @csrf
              <input type="hidden" name="keputusan" value="Setujui">
              <button type="submit" class="btn-primary">
                <i data-lucide="check" style="width:15px;height:15px"></i> Setujui Pengajuan
              </button>
            </form>
          </div>
        @else
          <div style="padding:10px 14px; background:var(--bg-subtle); border:1px solid var(--border); border-radius:8px; font-size:13.5px; color:var(--text-muted); text-align:center">
            Pengajuan izin ini sudah diproses dengan status <strong>{{ $detail->status }}</strong>.
          </div>
        @endif
      </div>
    @endif
  </div>
@endsection