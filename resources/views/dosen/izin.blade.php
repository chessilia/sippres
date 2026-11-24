@extends('layouts.app')

@section('judul', 'Pengajuan Izin')

@section('isi')
  <p>Periksa pengajuan izin mahasiswa, lalu setujui atau tolak.
     <span class="badge" style="background:#dc3545">{{ $jumlahMenunggu }} menunggu</span></p>

  {{-- Daftar Pengajuan --}}
  <div class="card">
    @if ($daftar->isEmpty())
      <p>Belum ada pengajuan izin pada mata kuliah yang kamu ampu.</p>
    @else
      <table style="width:100%; border-collapse:collapse;">
        <tr>
          <th style="text-align:left">Mahasiswa</th>
          <th style="text-align:left">Mata Kuliah</th>
          <th style="text-align:left">Pertemuan</th>
          <th style="text-align:left">Status</th>
          <th></th>
        </tr>
        @foreach ($daftar as $z)
          <tr>
            <td>{{ $z->mahasiswa->nama }}</td>
            <td>{{ $z->perkuliahan->namaMatkul }}</td>
            <td>{{ $z->pertemuan }}</td>
            <td><span class="badge st-{{ $z->status }}">{{ $z->status }}</span></td>
            <td><a href="{{ route('izin.dosen', ['id' => $z->idIzin]) }}">Tinjau</a></td>
          </tr>
        @endforeach
      </table>
    @endif
  </div>

  {{-- Detail Pengajuan --}}
  @if ($detail)
    <div class="card">
      <h3>{{ $detail->mahasiswa->nama }}
        <span class="badge st-{{ $detail->status }}">{{ $detail->status }}</span></h3>
      <p>{{ $detail->mahasiswa->username }} | {{ $detail->mahasiswa->email }}</p>

      <p><strong>Mata kuliah:</strong> {{ $detail->perkuliahan->namaMatkul }}
         (Kelas {{ $detail->perkuliahan->kelas }})</p>
      <p><strong>Pertemuan:</strong> {{ $detail->pertemuan }}</p>
      <p><strong>Tanggal pengajuan:</strong> {{ $detail->tanggalPengajuan->format('d/m/Y H:i') }}</p>
      <p><strong>Alasan ketidakhadiran:</strong><br>{{ $detail->alasan }}</p>

      <p><strong>Bukti pendukung:</strong>
        @if ($detail->fileBukti)
          <a href="{{ route('izin.bukti', $detail->idIzin) }}" target="_blank">Lihat</a>
        @else
          tidak ada
        @endif
      </p>

      @if ($detail->status === 'Menunggu')
        <form method="POST" action="{{ route('izin.proses', $detail->idIzin) }}" style="display:inline"
              onsubmit="return confirm('Tolak pengajuan izin ini?')">
          @csrf
          <input type="hidden" name="keputusan" value="Tolak">
          <button type="submit" class="btn-hapus">Tolak Pengajuan</button>
        </form>

        <form method="POST" action="{{ route('izin.proses', $detail->idIzin) }}" style="display:inline"
              onsubmit="return confirm('Setujui pengajuan izin ini?')">
          @csrf
          <input type="hidden" name="keputusan" value="Setujui">
          <button type="submit" class="btn-primary">Setujui Pengajuan</button>
        </form>
      @else
        <p><em>Pengajuan ini sudah diproses.</em></p>
      @endif
    </div>
  @endif
@endsection