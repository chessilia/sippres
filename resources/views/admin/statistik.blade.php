@extends('layouts.app')

@section('judul', 'Rekap dan Statistik')

@section('isi')
  <p>Pantau presensi, izin, dan laporan Crisis Center per periode.</p>

  {{-- Filter --}}
  <div class="card noprint">
    <form method="GET" action="{{ route('statistik.admin') }}">
      <div class="row">
        <label for="jenis">Jenis data</label>
        <select name="jenis" id="jenis">
          @foreach (['Semua', 'Presensi', 'Izin', 'Crisis Center'] as $j)
            <option value="{{ $j }}" @selected($f['jenis'] == $j)>{{ $j }}</option>
          @endforeach
        </select>
      </div>
      <div class="row">
        <label for="bulan">Periode (bulan)</label>
        <input type="month" name="bulan" id="bulan" value="{{ $f['bulan'] }}">
      </div>
      <div class="row">
        <label for="perkuliahan">Mata kuliah (presensi dan izin)</label>
        <select name="perkuliahan" id="perkuliahan">
          <option value="">Semua mata kuliah</option>
          @foreach ($daftarPerkuliahan as $p)
            <option value="{{ $p->idPerkuliahan }}" @selected($f['perkuliahan'] == $p->idPerkuliahan)>
              {{ $p->namaMatkul }} (Kelas {{ $p->kelas }})
            </option>
          @endforeach
        </select>
      </div>
      <div class="row">
        <label for="dosen">Dosen (presensi dan izin)</label>
        <select name="dosen" id="dosen">
          <option value="">Semua dosen</option>
          @foreach ($daftarDosen as $d)
            <option value="{{ $d->idUser }}" @selected($f['dosen'] == $d->idUser)>{{ $d->nama }}</option>
          @endforeach
        </select>
      </div>
      <div class="row">
        <label for="status">Status kehadiran (untuk detail presensi)</label>
        <select name="status" id="status">
          <option value="">Semua status</option>
          @foreach (['Hadir', 'Izin', 'Sakit', 'Alpa'] as $st)
            <option value="{{ $st }}" @selected($f['status'] == $st)>{{ $st }}</option>
          @endforeach
        </select>
      </div>

      <button type="submit" class="btn-primary">Terapkan</button>
      <a class="btn btn-batal" href="{{ route('statistik.ekspor', request()->query()) }}">Ekspor Excel (CSV)</a>
      <button type="button" class="btn-batal" onclick="window.print()">Cetak / Simpan PDF</button>
    </form>
  </div>

  @if ($galat)
    <div class="alert-error">Terjadi kesalahan saat mengambil data, silakan coba kembali</div>
  @else
    @php
      $pr = $data['presensi']; $iz = $data['izin']; $lp = $data['laporan'];
      $adaPresensi = $pr['total'] > 0; $adaIzin = $iz['total'] > 0; $adaLaporan = $lp['total'] > 0;
      $semuaKosong = match ($f['jenis']) {
        'Presensi' => !$adaPresensi, 'Izin' => !$adaIzin, 'Crisis Center' => !$adaLaporan,
        default => !$adaPresensi && !$adaIzin && !$adaLaporan,
      };
    @endphp

    <h3>Periode {{ $f['awal']->format('m/Y') }}</h3>

    @if ($semuaKosong)
      <div class="card"><p>Data belum tersedia</p></div>
    @else
      {{-- Kartu ringkasan --}}
      <div class="grid4">
        @if (in_array($f['jenis'], ['Semua', 'Presensi']))
          <div class="stat">Total presensi tercatat <b>{{ $pr['tercatat'] }}</b>
            <small>dari {{ $pr['perMatkul']->count() }} perkuliahan</small></div>
          <div class="stat">Rata-rata kehadiran <b>{{ $pr['rata'] }}%</b>
            <small>
              @if ($pr['selisih'] === null) belum ada pembanding bulan lalu
              @else {{ $pr['selisih'] >= 0 ? '+' : '' }}{{ $pr['selisih'] }}% dari bulan lalu @endif
            </small></div>
        @endif
        @if (in_array($f['jenis'], ['Semua', 'Izin']))
          <div class="stat">Pengajuan izin <b>{{ $iz['total'] }}</b>
            <small>{{ $iz['menunggu'] }} menunggu persetujuan</small></div>
        @endif
        @if (in_array($f['jenis'], ['Semua', 'Crisis Center']))
          <div class="stat">Laporan Crisis Center <b>{{ $lp['total'] }}</b>
            <small>{{ $lp['belum'] }} belum ditangani</small></div>
        @endif
      </div>

      {{-- Presensi --}}
      @if (in_array($f['jenis'], ['Semua', 'Presensi']) && $adaPresensi)
        <div class="card">
          <h3>Kehadiran per Minggu</h3>
          @foreach ($pr['mingguan'] as $minggu => $persen)
            <p>Minggu {{ $minggu }}: <progress value="{{ $persen }}" max="100"></progress> {{ $persen }}%</p>
          @endforeach
          <p><strong>Rata-rata: {{ $pr['rata'] }}%</strong></p>
        </div>

        <div class="card">
          <h3>Status Kehadiran</h3>
          @foreach ($pr['perStatus'] as $status => $v)
            <p><span class="badge st-{{ $status }}">{{ $status }}</span>
               <progress value="{{ $v['persen'] }}" max="100"></progress> {{ $v['jumlah'] }} ({{ $v['persen'] }}%)</p>
          @endforeach
        </div>

        <div class="card">
          <h3>Rekap per Perkuliahan</h3>
          <table style="width:100%; border-collapse:collapse;">
            <tr>
              <th style="text-align:left">Mata Kuliah</th>
              <th style="text-align:left">Dosen</th>
              <th>Pertemuan</th><th>Hadir</th><th>Izin</th><th>Sakit</th><th>Alpa</th><th>% Hadir</th>
            </tr>
            @foreach ($pr['perMatkul'] as $r)
              <tr>
                <td>{{ $r['perkuliahan']->namaMatkul }} ({{ $r['perkuliahan']->kelas }})</td>
                <td>{{ $r['perkuliahan']->dosen->nama ?? '-' }}</td>
                <td style="text-align:center">{{ $r['pertemuan'] }}</td>
                <td style="text-align:center">{{ $r['Hadir'] }}</td>
                <td style="text-align:center">{{ $r['Izin'] }}</td>
                <td style="text-align:center">{{ $r['Sakit'] }}</td>
                <td style="text-align:center">{{ $r['Alpa'] }}</td>
                <td style="text-align:center">{{ $r['persen'] }}%</td>
              </tr>
            @endforeach
          </table>
        </div>

        <div class="card">
          <h3>Detail Kehadiran {{ $f['status'] ? '(' . $f['status'] . ')' : '' }}</h3>
          @if ($pr['detail']->isEmpty())
            <p>Tidak ada data untuk status ini.</p>
          @else
            <table style="width:100%; border-collapse:collapse;">
              <tr>
                <th style="text-align:left">Mahasiswa</th>
                <th style="text-align:left">Mata Kuliah</th>
                <th>Pertemuan</th>
                <th>Tanggal</th>
                <th>Status</th>
              </tr>
              @foreach ($pr['detail']->take(100) as $r)
                <tr>
                  <td>{{ $r['mahasiswa']->nama }}</td>
                  <td>{{ $r['perkuliahan']->namaMatkul }} ({{ $r['perkuliahan']->kelas }})</td>
                  <td style="text-align:center">{{ $r['sesi']->pertemuan }}</td>
                  <td style="text-align:center">{{ $r['sesi']->waktuMulai->format('d/m/Y') }}</td>
                  <td style="text-align:center"><span class="badge st-{{ $r['status'] }}">{{ $r['status'] }}</span></td>
                </tr>
              @endforeach
            </table>
            @if ($pr['detail']->count() > 100)
              <p><small>Menampilkan 100 dari {{ $pr['detail']->count() }} baris. Gunakan ekspor untuk data lengkap.</small></p>
            @endif
          @endif
        </div>
      @elseif (in_array($f['jenis'], ['Semua', 'Presensi']))
        <div class="card"><p>Data presensi belum tersedia pada periode ini.</p></div>
      @endif

      {{-- Izin --}}
      @if (in_array($f['jenis'], ['Semua', 'Izin']))
        <div class="card">
          <h3>Pengajuan Izin</h3>
          @if ($adaIzin)
            @foreach ($iz['perStatus'] as $status => $v)
              <p><span class="badge st-{{ $status }}">{{ $status }}</span>
                 <progress value="{{ $v['persen'] }}" max="100"></progress> {{ $v['jumlah'] }} ({{ $v['persen'] }}%)</p>
            @endforeach
          @else
            <p>Data izin belum tersedia pada periode ini.</p>
          @endif
        </div>
      @endif

      {{-- Crisis Center --}}
      @if (in_array($f['jenis'], ['Semua', 'Crisis Center']))
        <div class="card">
          <h3>Laporan Crisis Center</h3>
          @if ($adaLaporan)
            @foreach ($lp['perKategori'] as $kategori => $v)
              <p>{{ $kategori }}: <progress value="{{ $v['persen'] }}" max="100"></progress> {{ $v['jumlah'] }} ({{ $v['persen'] }}%)</p>
            @endforeach
            <p><strong>Status laporan:</strong>
              <span class="badge st-Baru">Baru {{ $lp['perStatus']['Baru'] }}</span>
              <span class="badge st-Diproses">Ditangani {{ $lp['perStatus']['Diproses'] }}</span>
              <span class="badge st-Selesai">Selesai {{ $lp['perStatus']['Selesai'] }}</span>
              <span class="badge st-TidakValid">Tidak valid {{ $lp['perStatus']['Tidak Valid'] }}</span>
            </p>
          @else
            <p>Data laporan belum tersedia pada periode ini.</p>
          @endif
        </div>
      @endif
    @endif
  @endif
@endsection