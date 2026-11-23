@extends('layouts.app')

@section('judul', 'Dashboard Dosen')

@section('isi')
  <div class="card">
    <h3>Hi, {{ Auth::user()->nama }}!</h3>
    <p>Gunakan menu di samping untuk melihat jadwal mengajar dan membuat sesi presensi QR.</p>
    <a class="btn btn-primary" href="{{ route('presensi.dosen') }}">Buat Sesi Presensi</a>
    <a class="btn btn-batal" href="{{ route('jadwal') }}">Lihat Jadwal Mengajar</a>
  </div>
@endsection