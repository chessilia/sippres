<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - SIPPRES</title>
  <style>
    body { font-family: Arial, sans-serif; background:#f4f6f8; margin:0; }
    .container { max-width:380px; margin:60px auto; padding:24px; background:#fff;
                 border:1px solid #ddd; border-radius:8px; }
    .row { margin-bottom:14px; }
    label { display:block; font-weight:bold; margin-bottom:4px; }
    input[type=email], input[type=password] { width:100%; padding:8px; box-sizing:border-box; }
    button { width:100%; padding:10px; border:none; border-radius:5px; cursor:pointer; margin-bottom:8px; }
    .btn-primary { background:#007bff; color:#fff; }
    .btn-google { background:#fff; border:1px solid #aaa; }
    .alert-error { background:#fde2e2; padding:8px; border-radius:4px; margin-bottom:12px; }
    .alert-success { background:#dff5e1; padding:8px; border-radius:4px; margin-bottom:12px; }
  </style>
</head>
<body>
  <div class="container">
    <h3>Masuk Portal SIPPRES</h3>

    @if (session('success')) <div class="alert-success">{{ session('success') }}</div> @endif
    @if (session('error'))   <div class="alert-error">{{ session('error') }}</div>   @endif

    <form method="POST" action="{{ route('login.submit') }}">
      @csrf
      <div class="row">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
      </div>
      <div class="row">
        <label for="password">Kata Sandi</label>
        <input type="password" id="password" name="password" required>
      </div>
      <div class="row">
        <label><input type="checkbox" name="remember"> Ingat Saya di Perangkat Ini</label>
        <a href="#">Lupa Kata Sandi?</a>
      </div>

      <button type="submit" class="btn-primary">Masuk Portal SIPPRES</button>
      <button type="button" class="btn-google">Masuk dengan Google Workspace Kampus</button>
    </form>

    <p>Belum punya akun? <a href="{{ route('registrasi') }}">Daftar Akun Baru</a></p>
  </div>
</body>
</html>