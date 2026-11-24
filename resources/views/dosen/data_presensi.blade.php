@extends('layouts.app')

@section('judul', 'Data Presensi')

@section('isi')
<style>
  .page-subtitle {
    margin: 0 0 18px;
    color: var(--text-secondary);
    font-size: 14.5px;
  }

  /* Filter Bar */
  .filter-grid {
    display: grid;
    grid-template-columns: 1.4fr 1fr;
    gap: 16px 20px;
  }

  /* Split Grid saat Detail Koreksi Dibuka */
  .koreksi-layout {
    display: grid;
    grid-template-columns: 1fr;
    gap: 18px;
    align-items: start;
    margin-top: 18px;
  }
  .koreksi-layout.has-detail {
    grid-template-columns: 1.35fr 1fr;
  }

  .row-active {
    background: #eff6ff !important;
  }

  /* Panel Koreksi */
  .detail-card {
    margin-top: 0;
    position: sticky;
    top: 84px;
    border-top: 4px solid var(--primary);
  }
  .detail-header {
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
  .detail-sub {
    font-size: 13.5px;
    color: var(--text-secondary);
  }

  /* Radio Status Pills */
  .status-pill-group {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
    margin-top: 6px;
  }
  .status-pill {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 9px 12px;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: var(--bg-subtle);
    font-weight: 600;
    font-size: 13.5px;
    color: var(--text-main);
    cursor: pointer;
    margin: 0;
    transition: all 0.15s ease;
  }
  .status-pill:hover {
    border-color: #93c5fd;
    background: #eff6ff;
  }
  .status-pill input[type="radio"] {
    accent-color: var(--primary);
    margin: 0;
  }

  .panel-footer {
    display: flex;
    justify-content: flex-end;
    align-items: center;
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
    .filter-grid {
      grid-template-columns: 1fr;
    }
    .koreksi-layout,
    .koreksi-layout.has-detail {
      grid-template-columns: 1fr;
    }
    .detail-card {
      position: static;
    }
  }
</style>

  <p class="page-subtitle">Pilih mata kuliah dan pertemuan, lalu klik <strong>Koreksi</strong> pada mahasiswa yang status kehadirannya perlu disesuaikan.</p>

  @if (!$perkuliahan)
    <div class="card" style="margin-top:0">
      <div class="empty-box">Belum ada perkuliahan yang Anda ampu.</div>
    </div>
  @else
    {{-- Pilihan perkuliahan dan pertemuan --}}
    <div class="card" style="margin-top:0">
      <form method="GET" action="{{ route('koreksi.dosen') }}">
        <div class="{{ $pertemuan->isNotEmpty() ? 'filter-grid' : '' }}">
          <div>
            <label for="perkuliahan">Mata Kuliah &amp; Kelas</label>
            <select name="perkuliahan" id="perkuliahan" onchange="this.form.submit()">
              @foreach ($daftar as $p)
                <option value="{{ $p->idPerkuliahan }}" @selected($perkuliahan->idPerkuliahan == $p->idPerkuliahan)>
                  {{ $p->kodeMatkul }} - {{ $p->namaMatkul }} (Kelas {{ $p->kelas }})
                </option>
              @endforeach
            </select>
          </div>

          @if ($pertemuan->isNotEmpty())
            <div>
              <label for="pertemuan">Sesi Pertemuan</label>
              <select name="pertemuan" id="pertemuan" onchange="this.form.submit()">
                @foreach ($pertemuan as $s)
                  <option value="{{ $s->pertemuan }}" @selected($nomor == $s->pertemuan)>
                    Pertemuan {{ $s->pertemuan }} — {{ $s->waktuMulai->format('d/m/Y H:i') }}
                  </option>
                @endforeach
              </select>
            </div>
          @endif
        </div>
      </form>
    </div>

    <div class="koreksi-layout {{ $detail ? 'has-detail' : '' }}">
      {{-- Tabel presensi --}}
      <div class="card" style="margin-top:0">
        @if ($pertemuan->isEmpty())
          <div class="empty-box">Data kehadiran belum tersedia. Silakan buat sesi presensi terlebih dahulu.</div>
        @else
          <table>
            <thead>
              <tr>
                <th>Mahasiswa</th>
                <th>Waktu</th>
                <th>Status</th>
                <th>Keterangan</th>
                <th style="text-align:right">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($mahasiswa as $m)
                @php $sel = $matriks[$m->idUser][$nomor]; @endphp
                <tr class="{{ $detail && $detail['mahasiswa']->idUser == $m->idUser ? 'row-active' : '' }}">
                  <td>
                    <strong>{{ $m->nama }}</strong><br>
                    <small style="color:var(--text-muted)">{{ $m->username }}</small>
                  </td>
                  <td style="white-space:nowrap; font-variant-numeric:tabular-nums">
                    {{ $sel['presensi'] ? $sel['presensi']->waktu->format('H:i') : '-' }}
                  </td>
                  <td>
                    <span class="badge st-{{ $sel['status'] }}">{{ $sel['status'] }}</span>
                  </td>
                  <td style="color:var(--text-secondary)">
                    {{ $sel['presensi']->keterangan ?? '-' }}
                  </td>
                  <td style="text-align:right">
                    <a class="btn {{ $detail && $detail['mahasiswa']->idUser == $m->idUser ? 'btn-primary' : 'btn-batal' }}"
                       style="padding:6px 12px; font-size:13px"
                       href="{{ route('koreksi.dosen', ['perkuliahan' => $perkuliahan->idPerkuliahan, 'pertemuan' => $nomor, 'mahasiswa' => $m->idUser]) }}">
                      <i data-lucide="edit-3" style="width:14px;height:14px"></i> Koreksi
                    </a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @endif
      </div>

      {{-- Panel koreksi --}}
      @if ($detail)
        <div class="card detail-card">
          <div class="detail-header">
            <h3>Koreksi Presensi</h3>
            <div class="detail-sub">
              <strong>{{ $detail['mahasiswa']->nama }}</strong> ({{ $detail['mahasiswa']->username }}) &bull; Pertemuan {{ $nomor }}
            </div>
          </div>

          <form method="POST" action="{{ route('koreksi.simpan') }}">
            @csrf
            <input type="hidden" name="idPerkuliahan" value="{{ $perkuliahan->idPerkuliahan }}">
            <input type="hidden" name="pertemuan" value="{{ $nomor }}">
            <input type="hidden" name="idMahasiswa" value="{{ $detail['mahasiswa']->idUser }}">

            <div class="row">
              <label>Status Kehadiran Baru</label>
              <div class="status-pill-group">
                @foreach (['Hadir', 'Izin', 'Sakit', 'Alpa'] as $st)
                  <label class="status-pill">
                    <input type="radio" name="status" value="{{ $st }}"
                      @checked(old('status', $detail['sel']['status']) == $st)>
                    <span>{{ $st }}</span>
                  </label>
                @endforeach
              </div>
            </div>

            <div class="row" style="margin-bottom:0">
              <label for="keterangan">Keterangan / Alasan Koreksi</label>
              <input type="text" name="keterangan" id="keterangan" maxlength="255" required
                     placeholder="Contoh: Kendala jaringan saat scan QR"
                     value="{{ old('keterangan', $detail['sel']['presensi']->keterangan ?? '') }}">
            </div>

            <div class="panel-footer">
              <button type="submit" name="aksi" value="Batal" class="btn-batal" formnovalidate>Batal</button>
              <button type="submit" name="aksi" value="Simpan" class="btn-primary">
                <i data-lucide="check" style="width:15px;height:15px"></i> Simpan Perubahan
              </button>
            </div>
          </form>
        </div>
      @endif
    </div>
  @endif
@endsection