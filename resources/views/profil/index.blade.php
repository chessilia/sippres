@extends('layouts.app')

@section('judul', 'Profil')

@section('isi')
  <div class="card">
    <div style="display:flex; gap:16px; align-items:center;">
      @if ($user->fotoProfil)
        <img class="avatar" src="{{ route('profil.foto', $user->idUser) }}?v={{ md5($user->fotoProfil) }}" alt="Foto profil">
      @else
        <div class="avatar">{{ strtoupper(substr($user->nama, 0, 1)) }}</div>
      @endif
      <div>
        <h3 style="margin:0">{{ $user->nama }}</h3>
        <span class="badge st-{{ $user->role }}">{{ $user->role }}</span>
        <p style="margin:4px 0 0">{{ $user->username }}</p>
      </div>
    </div>
  </div>

  {{-- Ringkasan --}}
  <div class="grid4">
    @foreach ($ringkasan as $label => $nilai)
      <div class="stat">{{ $label }} <b>{{ $nilai }}</b></div>
    @endforeach
  </div>

  {{-- Ubah profil --}}
  <div class="card">
    <h3>Ubah Profil</h3>
    <form method="POST" action="{{ route('profil.update') }}" enctype="multipart/form-data">
      @csrf
      <div class="row">
        <label for="nama">Nama lengkap</label>
        <input type="text" name="nama" id="nama" maxlength="100" required value="{{ old('nama', $user->nama) }}">
      </div>
      <div class="row">
        <label for="email">Email</label>
        <input type="text" name="email" id="email" maxlength="100" required value="{{ old('email', $user->email) }}">
      </div>
      <div class="row">
        <label for="fotoProfil">Ganti foto (JPG atau PNG, maks. 2 MB)</label>
        <input type="file" name="fotoProfil" id="fotoProfil" accept=".jpg,.jpeg,.png">
      </div>

      <button type="submit" name="aksi" value="Batal" class="btn-batal" formnovalidate>Batal</button>
      <button type="submit" name="aksi" value="Simpan" class="btn-primary">Simpan Perubahan</button>
    </form>
  </div>

  <div class="card">
    <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Yakin ingin keluar?')">
      @csrf
      <button type="submit" class="btn-hapus">Keluar</button>
    </form>
  </div>
@endsection