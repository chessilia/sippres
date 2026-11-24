@extends('layouts.app')

@section('judul', 'Dashboard Mahasiswa')

@section('isi')
  <div class="card">
    <h3>Hi, {{ Auth::user()->nama }}!</h3>
    <p>Gunakan menu di samping untuk melihat jadwal kuliah dan melakukan presensi.</p>
    <a class="btn btn-primary" href="{{ route('presensi.mahasiswa') }}">Presensi Sekarang</a>
    <a class="btn btn-batal" href="{{ route('jadwal') }}">Lihat Jadwal Kuliah</a>
  </div>
@endsection