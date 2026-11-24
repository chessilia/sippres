@extends('layouts.app')

@section('judul', 'Rekap Kehadiran')

@section('isi')
  <p>Ringkasan kehadiran mahasiswa per perkuliahan.</p>

  @if (!$perkuliahan)
    <div class="card"><p>Belum ada perkuliahan yang kamu ampu.</p></div>
  @else
    <div class="card">
      <form method="GET" action="{{ route('rekap.dosen') }}">
        <div class="row">
          <label for="perkuliahan">Perkuliahan</label>
          <select name="perkuliahan" id="perkuliahan" onchange="this.form.submit()">
            @foreach ($daftar as $p)
              <option value="{{ $p->idPerkuliahan }}" @selected($perkuliahan->idPerkuliahan == $p->idPerkuliahan)>
                {{ $p->kodeMatkul }} - {{ $p->namaMatkul }} (Kelas {{ $p->kelas }})
              </option>
            @endforeach
          </select>
        </div>
      </form>
      <a class="btn btn-primary" href="{{ route('rekap.ekspor', ['perkuliahan' => $perkuliahan->idPerkuliahan]) }}">
        Ekspor ke Excel (CSV)
      </a>
    </div>

    @if ($pertemuan->isEmpty() || $mahasiswa->isEmpty())
      <div class="card"><p>Data kehadiran belum tersedia.</p></div>
    @else
      {{-- Kartu ringkasan --}}
      <div class="card">
        <strong>Total mahasiswa:</strong> {{ $ringkasan['totalMahasiswa'] }} |
        <strong>Rata-rata kehadiran:</strong> {{ $ringkasan['rataRata'] }}% |
        <strong>Total izin dan sakit:</strong> {{ $ringkasan['izinSakit'] }} |
        <strong>Total alpa:</strong> {{ $ringkasan['alpa'] }}
      </div>

      {{-- Kehadiran per mahasiswa --}}
      <div class="card">
        <table style="width:100%; border-collapse:collapse;">
          <tr>
            <th style="text-align:left">Nama</th>
            <th>Hadir</th><th>Izin</th><th>Sakit</th><th>Alpa</th>
            <th style="text-align:left">Persentase</th>
            <th></th>
          </tr>
          @foreach ($mahasiswa as $m)
            @php $h = $rekap[$m->idUser]; @endphp
            <tr>
              <td>{{ $m->nama }}</td>
              <td style="text-align:center">{{ $h['Hadir'] }}</td>
              <td style="text-align:center">{{ $h['Izin'] }}</td>
              <td style="text-align:center">{{ $h['Sakit'] }}</td>
              <td style="text-align:center">{{ $h['Alpa'] }}</td>
              <td>
                <progress value="{{ $h['persen'] }}" max="100"></progress> {{ $h['persen'] }}%
              </td>
              <td>
                <a href="{{ route('rekap.dosen', ['perkuliahan' => $perkuliahan->idPerkuliahan, 'mahasiswa' => $m->idUser]) }}">Detail</a>
              </td>
            </tr>
          @endforeach
        </table>
      </div>

      {{-- Detail per pertemuan --}}
      @if ($detail)
        <div class="card">
          <h3>{{ $detail->nama }} - {{ $rekap[$detail->idUser]['persen'] }}%</h3>
          @foreach ($pertemuan as $s)
            @php $st = $matriks[$detail->idUser][$s->pertemuan]['status']; @endphp
            <span class="badge st-{{ $st }}">P{{ $s->pertemuan }}: {{ $st }}</span>
          @endforeach
          <p><small>Hadir, Izin, Sakit, Alpa. "Belum" berarti sesi masih berlangsung.</small></p>
        </div>
      @endif
    @endif
  @endif
@endsection