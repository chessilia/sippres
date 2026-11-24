@extends('layouts.app')

@section('judul', 'Data Presensi')

@section('isi')
  <p>Pilih perkuliahan dan pertemuan, lalu klik Koreksi pada mahasiswa yang statusnya perlu diubah.</p>

  @if (!$perkuliahan)
    <div class="card"><p>Belum ada perkuliahan yang kamu ampu.</p></div>
  @else
    {{-- Pilihan perkuliahan dan pertemuan --}}
    <div class="card">
      <form method="GET" action="{{ route('koreksi.dosen') }}">
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

        @if ($pertemuan->isNotEmpty())
          <div class="row">
            <label for="pertemuan">Pertemuan</label>
            <select name="pertemuan" id="pertemuan" onchange="this.form.submit()">
              @foreach ($pertemuan as $s)
                <option value="{{ $s->pertemuan }}" @selected($nomor == $s->pertemuan)>
                  Pertemuan {{ $s->pertemuan }} - {{ $s->waktuMulai->format('d/m/Y H:i') }}
                </option>
              @endforeach
            </select>
          </div>
        @endif
      </form>
    </div>

    {{-- Tabel presensi --}}
    <div class="card">
      @if ($pertemuan->isEmpty())
        <p>Data kehadiran belum tersedia. Buat sesi presensi terlebih dahulu.</p>
      @else
        <table style="width:100%; border-collapse:collapse;">
          <tr>
            <th style="text-align:left">Nama</th>
            <th style="text-align:left">Username</th>
            <th style="text-align:left">Waktu</th>
            <th style="text-align:left">Status</th>
            <th style="text-align:left">Keterangan</th>
            <th></th>
          </tr>
          @foreach ($mahasiswa as $m)
            @php $sel = $matriks[$m->idUser][$nomor]; @endphp
            <tr>
              <td>{{ $m->nama }}</td>
              <td>{{ $m->username }}</td>
              <td>{{ $sel['presensi'] ? $sel['presensi']->waktu->format('H:i') : '-' }}</td>
              <td><span class="badge st-{{ $sel['status'] }}">{{ $sel['status'] }}</span></td>
              <td>{{ $sel['presensi']->keterangan ?? '-' }}</td>
              <td>
                <a href="{{ route('koreksi.dosen', ['perkuliahan' => $perkuliahan->idPerkuliahan, 'pertemuan' => $nomor, 'mahasiswa' => $m->idUser]) }}">Koreksi</a>
              </td>
            </tr>
          @endforeach
        </table>
      @endif
    </div>

    {{-- Panel koreksi --}}
    @if ($detail)
      <div class="card">
        <h3>Koreksi Presensi</h3>
        <p>{{ $detail['mahasiswa']->nama }} ({{ $detail['mahasiswa']->username }}) - Pertemuan {{ $nomor }}</p>

        <form method="POST" action="{{ route('koreksi.simpan') }}">
          @csrf
          <input type="hidden" name="idPerkuliahan" value="{{ $perkuliahan->idPerkuliahan }}">
          <input type="hidden" name="pertemuan" value="{{ $nomor }}">
          <input type="hidden" name="idMahasiswa" value="{{ $detail['mahasiswa']->idUser }}">

          <div class="row">
            <label>Status kehadiran</label>
            @foreach (['Hadir', 'Izin', 'Sakit', 'Alpa'] as $st)
              <label style="display:inline; font-weight:normal; margin-right:12px">
                <input type="radio" name="status" value="{{ $st }}"
                  @checked(old('status', $detail['sel']['status']) == $st)> {{ $st }}
              </label>
            @endforeach
          </div>

          <div class="row">
            <label for="keterangan">Keterangan (alasan koreksi)</label>
            <input type="text" name="keterangan" id="keterangan" maxlength="255" required
                   value="{{ old('keterangan', $detail['sel']['presensi']->keterangan ?? '') }}">
          </div>

          <button type="submit" name="aksi" value="Batal" class="btn-batal" formnovalidate>Batal</button>
          <button type="submit" name="aksi" value="Simpan" class="btn-primary">Simpan Perubahan</button>
        </form>
      </div>
    @endif
  @endif
@endsection