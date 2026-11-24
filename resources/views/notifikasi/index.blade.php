@extends('layouts.app')

@section('judul', 'Notifikasi')

@section('isi')
  <p>{{ $belumDibaca }} notifikasi belum dibaca.</p>

  <div class="card">
    @if ($daftar->isEmpty())
      <p>Belum terdapat notifikasi terbaru</p>
    @else
      @foreach ($daftar as $n)
        <p>
          @unless ($n->statusBaca) <span class="badge st-Baru">Baru</span> @endunless
          <a href="{{ route('notifikasi', ['id' => $n->idNotifikasi]) }}">
            <strong>{{ $n->jenis }}</strong> - {{ \Illuminate\Support\Str::limit($n->isi, 70) }}
          </a>
          <small>({{ $n->waktu->format('d/m/Y H:i') }})</small>
        </p>
      @endforeach
    @endif
  </div>

  @if ($detail)
    <div class="card">
      <h3>{{ $detail->jenis }}</h3>
      <p><small>{{ $detail->waktu->format('d/m/Y H:i') }}</small></p>
      <p>{{ $detail->isi }}</p>
      @if ($detail->jenis === 'Izin' && Auth::user()->role === 'Mahasiswa')
        <a class="btn btn-primary" href="{{ route('riwayat', ['jenis' => 'izin']) }}">Lihat Riwayat Izin</a>
      @endif
    </div>
  @endif
@endsection