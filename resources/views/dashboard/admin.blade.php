@extends('layouts.app')

@section('judul', 'Dashboard Admin')

@section('isi')
  <div class="card">
    <h3>Hi, {{ Auth::user()->nama }}!</h3>
    <p>Gunakan menu di samping untuk mengelola data perkuliahan dan menangani laporan kendala.</p>
    <a class="btn btn-primary" href="{{ route('perkuliahan.daftar') }}">Kelola Data Perkuliahan</a>
    <a class="btn btn-batal" href="{{ route('crisis.admin') }}">Crisis Center</a>
  </div>
@endsection