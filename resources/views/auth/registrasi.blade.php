<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrasi - SIPPRES</title>
  <style>
    body { font-family: Arial, sans-serif; background:#f4f6f8; margin:0; }
    nav { background:#fff; padding:10px 20px; display:flex; gap:16px; }
    .container { max-width:420px; margin:30px auto; padding:24px; background:#fff;
                 border:1px solid #ddd; border-radius:8px; }
    .row { margin-bottom:14px; }
    label { display:block; font-weight:bold; margin-bottom:4px; }
    input, select { width:100%; padding:8px; box-sizing:border-box; }
    .alert-error { background:#fde2e2; padding:8px; border-radius:4px; margin-bottom:12px; }
    button { padding:10px 16px; border:none; border-radius:5px; cursor:pointer; }
    .btn-primary { background:#007bff; color:#fff; }
    .btn-batal { background:#ccc; }
  </style>
</head>
<body>
  <nav>
    <a href="{{ route('login') }}">Masuk</a>
    <strong>Registrasi</strong>
    <a href="#">Bantuan</a>
  </nav>

  <div class="container">
    <h3>Daftar Akun</h3>

    @if (session('error'))
      <div class="alert-error">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('registrasi.submit') }}">
      @csrf

      <div class="row">
        <label for="role">Daftar Sebagai</label>
        <select name="role" id="role">
          <option value="Mahasiswa" @selected(old('role') == 'Mahasiswa')>Mahasiswa</option>
          <option value="Dosen" @selected(old('role') == 'Dosen')>Dosen</option>
        </select>
      </div>

      <div class="row">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="{{ old('username') }}" required>
      </div>

      <div class="row">
        <label for="nama">Nama Lengkap</label>
        <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required>
      </div>

      <div class="row">
        <label for="email">Email Kampus</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
      </div>

      <div class="row">
        <label for="password">Kata Sandi (min. 8 karakter)</label>
        <input type="password" id="password" name="password" minlength="8" required>
        <button type="button" onclick="toggleSandi()">Tampilkan/Sembunyikan</button>
      </div>

      <button type="submit" name="aksi" value="Batal" class="btn-batal" formnovalidate>Batal</button>
      <button type="submit" name="aksi" value="Submit" class="btn-primary">Submit Registrasi</button>
    </form>

    <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
  </div>

  <script>
    function toggleSandi() {
      const f = document.getElementById('password');
      f.type = f.type === 'password' ? 'text' : 'password';
    }
  </script>
</body>
</html>