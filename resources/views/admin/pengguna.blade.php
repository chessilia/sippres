@extends('layouts.app')

@section('judul', 'Data Pengguna')

@section('isi')
  <p>Kelola akun mahasiswa, dosen, dan admin yang terdaftar.</p>

  {{-- Cari --}}
  <div class="card">
    <form method="GET" action="{{ route('pengguna.daftar') }}">
      <input type="hidden" name="peran" value="{{ $peran }}">
      <input type="text" name="cari" value="{{ $cari }}" placeholder="Cari nama, username, atau email"
             style="width:70%; padding:8px;">
      <button type="submit" class="btn-primary">Cari</button>
    </form>
  </div>

  {{-- Filter peran --}}
  <div>
    <a class="tab {{ !$peran ? 'aktif' : '' }}" href="{{ route('pengguna.daftar', ['cari' => $cari]) }}">Semua ({{ $total }})</a>
    <a class="tab {{ $peran == 'Mahasiswa' ? 'aktif' : '' }}" href="{{ route('pengguna.daftar', ['peran' => 'Mahasiswa', 'cari' => $cari]) }}">Mahasiswa ({{ $jumlah['Mahasiswa'] ?? 0 }})</a>
    <a class="tab {{ $peran == 'Dosen' ? 'aktif' : '' }}" href="{{ route('pengguna.daftar', ['peran' => 'Dosen', 'cari' => $cari]) }}">Dosen ({{ $jumlah['Dosen'] ?? 0 }})</a>
    <a class="tab {{ $peran == 'Admin' ? 'aktif' : '' }}" href="{{ route('pengguna.daftar', ['peran' => 'Admin', 'cari' => $cari]) }}">Admin ({{ $jumlah['Admin'] ?? 0 }})</a>
  </div>

  {{-- Tabel pengguna --}}
  <div class="card" style="margin-top:0">
    @if ($daftar->isEmpty())
      <p>Tidak ada pengguna yang cocok.</p>
    @else
      <table style="width:100%; border-collapse:collapse;">
        <tr>
          <th style="text-align:left">Pengguna</th>
          <th style="text-align:left">Peran</th>
          <th style="text-align:left">Aksi</th>
        </tr>
        @foreach ($daftar as $u)
          <tr>
            <td>{{ $u->nama }}<br><small>{{ $u->username }} | {{ $u->email }}</small></td>
            <td><span class="badge st-{{ $u->role }}">{{ $u->role }}</span></td>
            <td>
              <a class="btn btn-batal" href="{{ route('pengguna.daftar', ['peran' => $peran, 'cari' => $cari, 'edit' => $u->idUser]) }}">Edit</a>
              @if ($u->idUser !== Auth::id())
                <form method="POST" action="{{ route('pengguna.hapus', $u->idUser) }}" style="display:inline"
                      onsubmit="return confirm('Hapus pengguna {{ $u->nama }}?')">
                  @csrf
                  <button type="submit" class="btn-hapus">Hapus</button>
                </form>
              @endif
            </td>
          </tr>
        @endforeach
      </table>
    @endif
  </div>

  {{-- Peringatan: pengguna masih punya data terkait --}}
  @if ($hapus && count($terkait) > 0)
    <div class="card" style="border-color:#dc3545">
      <h3>Pengguna masih memiliki data terkait</h3>
      <p>{{ $hapus->nama }} ({{ $hapus->username }}) memiliki:</p>
      <ul>
        @foreach ($terkait as $nama => $jumlahData)
          <li>{{ $jumlahData }} {{ $nama }}</li>
        @endforeach
      </ul>
      <p>Jika dilanjutkan, semua data di atas ikut terhapus dan tidak dapat dikembalikan.</p>

      <a class="btn btn-batal" href="{{ route('pengguna.daftar') }}">Batal</a>
      <form method="POST" action="{{ route('pengguna.hapus', $hapus->idUser) }}" style="display:inline"
            onsubmit="return confirm('Yakin hapus pengguna beserta semua data terkaitnya?')">
        @csrf
        <input type="hidden" name="lanjut" value="1">
        <button type="submit" class="btn-hapus">Tetap Hapus</button>
      </form>
    </div>
  @endif

  {{-- Panel edit --}}
  @if ($detail)
    <div class="card">
      <h3>Edit Pengguna</h3>
      <p>{{ $detail->nama }} <span class="badge st-{{ $detail->role }}">{{ $detail->role }}</span></p>

      <form method="POST" action="{{ route('pengguna.perbarui', $detail->idUser) }}">
        @csrf
        <div class="row">
          <label for="nama">Nama lengkap</label>
          <input type="text" name="nama" id="nama" maxlength="100" required value="{{ old('nama', $detail->nama) }}">
        </div>
        <div class="row">
          <label for="username">Username</label>
          <input type="text" name="username" id="username" maxlength="30" required value="{{ old('username', $detail->username) }}">
        </div>
        <div class="row">
          <label for="email">Email</label>
          <input type="text" name="email" id="email" maxlength="100" required value="{{ old('email', $detail->email) }}">
        </div>

        @if ($detail->role === 'Admin')
          <p><small>Peran admin tidak dapat diubah dari halaman ini.</small></p>
        @else
          <div class="row">
            <label>Peran</label>
            @foreach (['Mahasiswa', 'Dosen'] as $r)
              <label style="display:inline; font-weight:normal; margin-right:12px">
                <input type="radio" name="role" value="{{ $r }}" @checked(old('role', $detail->role) == $r)> {{ $r }}
              </label>
            @endforeach
          </div>
        @endif

        <a class="btn btn-batal" href="{{ route('pengguna.daftar') }}">Batal</a>
        <button type="submit" class="btn-primary">Simpan Perubahan</button>
      </form>
    </div>
  @endif
@endsection