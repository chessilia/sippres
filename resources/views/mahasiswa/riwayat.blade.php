@extends('layouts.app')

@section('judul', 'Riwayat')

@section('isi')
  <p>Catatan kehadiran dan status pengajuan izinmu.</p>

  <div>
    <a class="tab {{ $jenis == 'presensi' ? 'aktif' : '' }}" href="{{ route('riwayat', ['jenis' => 'presensi']) }}">Presensi</a>
    <a class="tab {{ $jenis == 'izin' ? 'aktif' : '' }}" href="{{ route('riwayat', ['jenis' => 'izin']) }}">Pengajuan Izin</a>
  </div>

  <div class="card" style="margin-top:0">
    @if ($daftar->isEmpty())
      <p>Riwayat belum tersedia.</p>
    @elseif ($jenis == 'presensi')
      <table style="width:100%; border-collapse:collapse;">
        <tr>
          <th style="text-align:left">Mata Kuliah</th>
          <th style="text-align:left">Pertemuan</th>
          <th style="text-align:left">Tanggal</th>
          <th style="text-align:left">Status</th>
          <th></th>
        </tr>
        @foreach ($daftar as $p)
          <tr>
            <td>{{ $p->sesi->perkuliahan->namaMatkul }}</td>
            <td>{{ $p->pertemuan }}</td>
            <td>{{ $p->waktu->format('d/m/Y H:i') }}</td>
            <td><span class="badge st-{{ $p->status }}">{{ $p->status }}</span></td>
            <td><a href="{{ route('riwayat', ['jenis' => 'presensi', 'id' => $p->idPresensi]) }}">Detail</a></td>
          </tr>
        @endforeach
      </table>
    @else
      <table style="width:100%; border-collapse:collapse;">
        <tr>
          <th style="text-align:left">Mata Kuliah</th>
          <th style="text-align:left">Pertemuan</th>
          <th style="text-align:left">Tanggal</th>
          <th style="text-align:left">Status</th>
          <th></th>
        </tr>
        @foreach ($daftar as $z)
          <tr>
            <td>{{ $z->perkuliahan->namaMatkul }}</td>
            <td>{{ $z->pertemuan }}</td>
            <td>{{ $z->tanggalPengajuan->format('d/m/Y H:i') }}</td>
            <td><span class="badge st-{{ $z->status }}">{{ $z->status }}</span></td>
            <td><a href="{{ route('riwayat', ['jenis' => 'izin', 'id' => $z->idIzin]) }}">Detail</a></td>
          </tr>
        @endforeach
      </table>
    @endif
  </div>

  {{-- Detail riwayat --}}
  @if ($detail)
    <div class="card">
      <h3>Detail Riwayat</h3>
      @if ($jenis == 'presensi')
        <p>Mata kuliah: {{ $detail->sesi->perkuliahan->namaMatkul }} (Kelas {{ $detail->sesi->perkuliahan->kelas }})</p>
        <p>Pertemuan: {{ $detail->pertemuan }}</p>
        <p>Waktu: {{ $detail->waktu->format('d/m/Y H:i') }}</p>
        <p>Status: <span class="badge st-{{ $detail->status }}">{{ $detail->status }}</span></p>
        <p>Keterangan: {{ $detail->keterangan ?? '-' }}</p>
      @else
        <p>Mata kuliah: {{ $detail->perkuliahan->namaMatkul }} (Kelas {{ $detail->perkuliahan->kelas }})</p>
        <p>Pertemuan: {{ $detail->pertemuan }}</p>
        <p>Diajukan: {{ $detail->tanggalPengajuan->format('d/m/Y H:i') }}</p>
        <p>Alasan: {{ $detail->alasan }}</p>
        <p>Status: <span class="badge st-{{ $detail->status }}">{{ $detail->status }}</span></p>
        @if ($detail->fileBukti)
          <p><a href="{{ route('izin.bukti', $detail->idIzin) }}" target="_blank">Lihat bukti</a></p>
        @endif
      @endif
    </div>
  @endif
@endsection