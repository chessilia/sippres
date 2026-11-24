@extends('layouts.app')

@section('judul', 'Crisis Center')

@section('isi')
  <p>Periksa laporan, beri tindak lanjut, lalu perbarui statusnya.
     <span class="badge st-Baru">{{ $jumlah['Baru'] ?? 0 }} laporan baru</span></p>

  {{-- Filter status --}}
  <div>
    <a class="tab {{ !$filter ? 'aktif' : '' }}" href="{{ route('crisis.admin') }}">Semua ({{ $total }})</a>
    <a class="tab {{ $filter == 'Baru' ? 'aktif' : '' }}" href="{{ route('crisis.admin', ['status' => 'Baru']) }}">Baru ({{ $jumlah['Baru'] ?? 0 }})</a>
    <a class="tab {{ $filter == 'Diproses' ? 'aktif' : '' }}" href="{{ route('crisis.admin', ['status' => 'Diproses']) }}">Ditangani ({{ $jumlah['Diproses'] ?? 0 }})</a>
    <a class="tab {{ $filter == 'Selesai' ? 'aktif' : '' }}" href="{{ route('crisis.admin', ['status' => 'Selesai']) }}">Selesai ({{ $jumlah['Selesai'] ?? 0 }})</a>
    <a class="tab {{ $filter == 'Tidak Valid' ? 'aktif' : '' }}" href="{{ route('crisis.admin', ['status' => 'Tidak Valid']) }}">Tidak valid ({{ $jumlah['Tidak Valid'] ?? 0 }})</a>
  </div>

  {{-- Daftar laporan --}}
  <div class="card" style="margin-top:0">
    @if ($daftar->isEmpty())
      <p>Tidak ada laporan.</p>
    @else
      <table style="width:100%; border-collapse:collapse;">
        <tr>
          <th style="text-align:left">Pelapor</th>
          <th style="text-align:left">Kategori</th>
          <th style="text-align:left">Tanggal</th>
          <th style="text-align:left">Ringkasan</th>
          <th style="text-align:left">Status</th>
          <th></th>
        </tr>
        @foreach ($daftar as $l)
          <tr>
            <td>{{ $l->pelapor->nama }}</td>
            <td>{{ $l->kategori }}</td>
            <td>{{ $l->tanggalLapor->format('d/m/Y') }}</td>
            <td>{{ \Illuminate\Support\Str::limit($l->deskripsi, 50) }}</td>
            <td><span class="badge st-{{ $l->kelas_status }}">{{ $l->label_status }}</span></td>
            <td><a href="{{ route('crisis.admin', ['status' => $filter, 'id' => $l->idLaporan]) }}">Tinjau</a></td>
          </tr>
        @endforeach
      </table>
    @endif
  </div>

  {{-- Detail laporan --}}
  @if ($detail)
    <div class="card">
      <h3>{{ $detail->pelapor->nama }}
        <span class="badge st-{{ $detail->kelas_status }}">{{ $detail->label_status }}</span></h3>
      <p>{{ $detail->pelapor->role }} | {{ $detail->pelapor->username }}</p>
      <p><strong>Kategori:</strong> {{ $detail->kategori }} |
         <strong>Tanggal lapor:</strong> {{ $detail->tanggalLapor->format('d/m/Y H:i') }}</p>
      <p><strong>Deskripsi kendala:</strong><br>{{ $detail->deskripsi }}</p>

      <p><strong>Riwayat penanganan:</strong><br>
        @if ($detail->tindakLanjut) {!! nl2br(e($detail->tindakLanjut)) !!} @else <em>belum ada</em> @endif
      </p>

      @if ($detail->status === 'Tidak Valid')
        <p><em>Laporan ini sudah ditandai tidak valid.</em></p>
      @else
        <form method="POST" action="{{ route('crisis.tindaklanjut', $detail->idLaporan) }}">
          @csrf
          <div class="row">
            <label>Ubah status</label>
            @foreach (['Baru' => 'Baru', 'Diproses' => 'Ditangani', 'Selesai' => 'Selesai'] as $nilai => $label)
              <label style="display:inline; font-weight:normal; margin-right:12px">
                <input type="radio" name="status" value="{{ $nilai }}"
                  @checked(old('status', $detail->status) == $nilai)> {{ $label }}
              </label>
            @endforeach
          </div>

          <div class="row">
            <label for="tindakLanjut">Tindak lanjut (wajib diisi)</label>
            <textarea name="tindakLanjut" id="tindakLanjut" rows="3" maxlength="1000" required
                      style="width:100%; padding:8px; box-sizing:border-box;">{{ old('tindakLanjut') }}</textarea>
          </div>

          <p><small>Pelapor akan menerima notifikasi setelah tindak lanjut disimpan.</small></p>
          <button type="submit" class="btn-primary">Simpan Tindak Lanjut</button>
        </form>
        <br>
        <form method="POST" action="{{ route('crisis.tidakvalid', $detail->idLaporan) }}"
              onsubmit="return confirm('Tandai laporan ini sebagai tidak valid?')">
          @csrf
          <button type="submit" class="btn-hapus">Tandai Tidak Valid</button>
        </form>
      @endif
    </div>
  @endif
@endsection