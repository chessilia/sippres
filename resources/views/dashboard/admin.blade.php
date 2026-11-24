@extends('layouts.app')

@section('judul', 'Dashboard Admin')

@section('isi')
  <div class="card">
    <h3>Hi, {{ Auth::user()->nama }}!</h3>
    <p>{{ $laporanBaru }} laporan kendala baru menunggu penanganan.</p>
  </div>

  <div class="grid4">
    <div class="stat">Crisis Center <b>{{ $laporanBaru }} baru</b>
      <progress value="{{ $ditindak }}" max="{{ max($totalLaporan, 1) }}"></progress>
      <small>{{ $ditindak }} dari {{ $totalLaporan }} sudah ditindaklanjuti</small><br>
      <a href="{{ route('crisis.admin') }}">Tangani Laporan</a></div>

    <div class="stat">Total pengguna <b>{{ $pengguna->sum() }}</b>
      <small>{{ $pengguna['Mahasiswa'] ?? 0 }} mahasiswa, {{ $pengguna['Dosen'] ?? 0 }} dosen, {{ $pengguna['Admin'] ?? 0 }} admin</small><br>
      <a href="{{ route('pengguna.daftar') }}">Kelola pengguna</a></div>

    <div class="stat">Rata-rata kehadiran <b>{{ $rata === null ? '-' : $rata . '%' }}</b>
      @if ($rata !== null) <progress value="{{ $rata }}" max="100"></progress> @endif
      <a href="{{ route('statistik.admin') }}">Lihat statistik</a></div>
  </div>

  <div class="card">
    <a class="btn btn-primary" href="{{ route('perkuliahan.daftar') }}">Kelola Data Perkuliahan</a>
  </div>

  {{-- Laporan terbaru --}}
  <div class="card">
    <h3>Laporan Crisis Center Terbaru</h3>
    @forelse ($terbaru as $l)
      <p>
        <strong>{{ $l->pelapor->nama }}</strong> | {{ $l->kategori }} | {{ $l->tanggalLapor->format('d/m/Y') }}
        <span class="badge st-{{ $l->kelas_status }}">{{ $l->label_status }}</span>
        <a href="{{ route('crisis.admin', ['id' => $l->idLaporan]) }}">Tangani</a>
      </p>
    @empty
      <p>Belum ada laporan.</p>
    @endforelse
  </div>
@endsection