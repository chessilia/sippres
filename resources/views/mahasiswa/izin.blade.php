@extends('layouts.app')

@section('judul', 'Ajukan Izin')

@section('isi')
  <p>Dosen pengampu akan memeriksa pengajuan izinmu.</p>

  <div class="card">
    <h3>{{ $ubah ? 'Ubah Pengajuan Izin' : 'Form Pengajuan Izin' }}</h3>

    @if ($daftarPerkuliahan->isEmpty())
      <p>Kamu belum terdaftar pada perkuliahan mana pun.</p>
    @else
      <form method="POST" enctype="multipart/form-data"
            action="{{ $ubah ? route('izin.ubah', $ubah->idIzin) : route('izin.kirim') }}">
        @csrf

        <div class="row">
          <label for="idPerkuliahan">Mata kuliah</label>
          <select name="idPerkuliahan" id="idPerkuliahan" required>
            @foreach ($daftarPerkuliahan as $p)
              <option value="{{ $p->idPerkuliahan }}"
                @selected(old('idPerkuliahan', $ubah?->idPerkuliahan) == $p->idPerkuliahan)>
                {{ $p->kodeMatkul }} - {{ $p->namaMatkul }} (Kelas {{ $p->kelas }})
              </option>
            @endforeach
          </select>
        </div>

        <div class="row">
          <label for="pertemuan">Pertemuan ke-</label>
          <select name="pertemuan" id="pertemuan" required>
            @for ($i = 1; $i <= 16; $i++)
              <option value="{{ $i }}" @selected(old('pertemuan', $ubah?->pertemuan) == $i)>{{ $i }}</option>
            @endfor
          </select>
        </div>

        <div class="row">
          <label for="alasan">Alasan ketidakhadiran</label>
          <textarea name="alasan" id="alasan" rows="3" maxlength="255" required
                    style="width:100%; padding:8px; box-sizing:border-box;">{{ old('alasan', $ubah?->alasan) }}</textarea>
        </div>

        <div class="row">
          <label for="fileBukti">Bukti pendukung (opsional, PDF/JPG/PNG, maks. 2 MB)</label>
          <input type="file" name="fileBukti" id="fileBukti" accept=".pdf,.jpg,.jpeg,.png">
          @if ($ubah && $ubah->fileBukti)
            <small>Sudah ada bukti. Pilih file baru hanya jika ingin menggantinya.</small>
          @endif
        </div>

        @if ($ubah)
          <a class="btn btn-batal" href="{{ route('izin.mahasiswa') }}">Batal</a>
        @else
          <button type="submit" name="aksi" value="Batal" class="btn-batal" formnovalidate>Batal</button>
        @endif
        <button type="submit" name="aksi" value="Kirim" class="btn-primary">
          {{ $ubah ? 'Simpan Perubahan' : 'Kirim Pengajuan' }}
        </button>
      </form>
    @endif
  </div>

  {{-- Daftar pengajuan saya --}}
  <div class="card">
    <h3>Pengajuan Saya</h3>
    @if ($daftarPengajuan->isEmpty())
      <p>Belum ada pengajuan izin.</p>
    @else
      <table style="width:100%; border-collapse:collapse;">
        <tr>
          <th style="text-align:left">Mata Kuliah</th>
          <th style="text-align:left">Pertemuan</th>
          <th style="text-align:left">Tanggal</th>
          <th style="text-align:left">Status</th>
          <th style="text-align:left">Aksi</th>
        </tr>
        @foreach ($daftarPengajuan as $z)
          <tr>
            <td>{{ $z->perkuliahan->namaMatkul }}</td>
            <td>{{ $z->pertemuan }}</td>
            <td>{{ $z->tanggalPengajuan->format('d/m/Y') }}</td>
            <td><span class="badge st-{{ $z->status }}">{{ $z->status }}</span></td>
            <td>
              @if ($z->fileBukti)
                <a href="{{ route('izin.bukti', $z->idIzin) }}" target="_blank">Lihat bukti</a>
              @endif
              @if ($z->status === 'Menunggu')
                <a href="{{ route('izin.mahasiswa', ['ubah' => $z->idIzin]) }}">Ubah</a>
                <form method="POST" action="{{ route('izin.batal', $z->idIzin) }}" style="display:inline"
                      onsubmit="return confirm('Batalkan pengajuan izin ini?')">
                  @csrf
                  <button type="submit" class="btn-hapus">Batalkan</button>
                </form>
              @endif
            </td>
          </tr>
        @endforeach
      </table>
    @endif
  </div>
@endsection