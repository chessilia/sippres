@extends('layouts.app')

@section('judul', 'Jadwal Perkuliahan')

@section('isi')
  <p>Pilih hari untuk melihat jadwal, lalu klik jadwal untuk melihat detailnya.</p>

  {{-- Pemilih Hari --}}
  <div>
    @foreach (['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
      <a class="tab {{ $hari == $h ? 'aktif' : '' }}" href="{{ route('jadwal', ['hari' => $h]) }}">{{ $h }}</a>
    @endforeach
  </div>

  <div class="card" style="margin-top:0">
    @if ($daftarJadwal->isEmpty())
      <p>Jadwal perkuliahan belum tersedia</p>
    @elseif ($jadwalHari->isEmpty())
      <p>Tidak ada perkuliahan pada hari {{ $hari }}.</p>
    @else
      @foreach ($jadwalHari as $j)
        <p>
          <a href="{{ route('jadwal', ['hari' => $hari, 'id' => $j->idPerkuliahan]) }}">
            <strong>{{ substr($j->jamMulai, 0, 5) }} - {{ substr($j->jamSelesai, 0, 5) }}</strong>
            {{ $j->namaMatkul }} (Kelas {{ $j->kelas }}, Ruang {{ $j->ruang }})
          </a>
        </p>
      @endforeach
    @endif
  </div>

  {{-- Detail Perkuliahan --}}
  @if ($detail)
    <div class="card">
      <h3>Detail Perkuliahan</h3>
      <p>Kode: {{ $detail->kodeMatkul }}</p>
      <p>Mata kuliah: {{ $detail->namaMatkul }}</p>
      <p>Kelas: {{ $detail->kelas }}</p>
      <p>Hari dan jam: {{ $detail->hari }}, {{ substr($detail->jamMulai, 0, 5) }} - {{ substr($detail->jamSelesai, 0, 5) }}</p>
      <p>Ruang: {{ $detail->ruang }}</p>
      <p>Dosen pengampu: {{ $detail->dosen->nama ?? '-' }}</p>
    </div>
  @endif
@endsection