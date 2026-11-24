@extends('layouts.app')

@section('judul', 'Lapor Kendala')

@section('isi')
  <p>Laporan kendalamu akan ditangani oleh admin.</p>

  <div class="card">
    <h3>Form Lapor Kendala</h3>
    <form method="POST" action="{{ route('crisis.kirim') }}">
      @csrf

      <div class="row">
        <label for="kategori">Kategori</label>
        <select name="kategori" id="kategori" required>
          @foreach ($kategori as $k)
            <option value="{{ $k }}" @selected(old('kategori') == $k)>{{ $k }}</option>
          @endforeach
        </select>
      </div>

      <div class="row">
        <label for="deskripsi">Deskripsi kendala (min. 10 karakter)</label>
        <textarea name="deskripsi" id="deskripsi" rows="4" maxlength="1000" required
                  style="width:100%; padding:8px; box-sizing:border-box;">{{ old('deskripsi') }}</textarea>
      </div>

      <button type="submit" name="aksi" value="Batal" class="btn-batal" formnovalidate>Batal</button>
      <button type="submit" name="aksi" value="Submit" class="btn-primary">Submit Laporan</button>
    </form>
  </div>

  <div class="card">
    <h3>Laporan Saya</h3>
    @if ($daftar->isEmpty())
      <p>Belum ada laporan.</p>
    @else
      @foreach ($daftar as $l)
        <p>
          <span class="badge st-{{ $l->kelas_status }}">{{ $l->label_status }}</span>
          <a href="{{ route('crisis.pengguna', ['id' => $l->idLaporan]) }}">
            {{ $l->kategori }} - {{ \Illuminate\Support\Str::limit($l->deskripsi, 60) }}
          </a>
          <small>({{ $l->tanggalLapor->format('d/m/Y') }})</small>
        </p>
      @endforeach
    @endif
  </div>

  @if ($detail)
    <div class="card">
      <h3>{{ $detail->kategori }}
        <span class="badge st-{{ $detail->kelas_status }}">{{ $detail->label_status }}</span></h3>
      <p><small>Dilaporkan {{ $detail->tanggalLapor->format('d/m/Y H:i') }}</small></p>
      <p>{{ $detail->deskripsi }}</p>

      <h4>Riwayat penanganan</h4>
      @if ($detail->tindakLanjut)
        <p>{!! nl2br(e($detail->tindakLanjut)) !!}</p>
      @else
        <p><em>Belum ada tindak lanjut dari admin.</em></p>
      @endif
    </div>
  @endif
@endsection